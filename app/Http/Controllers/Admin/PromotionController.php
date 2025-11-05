<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\Promotions\PromotionService;
use App\Services\Analytics\PromotionAnalyticsService;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    protected $promotionService;
    protected $analyticsService;

    public function __construct(PromotionService $promotionService, PromotionAnalyticsService $analyticsService)
    {
        $this->promotionService = $promotionService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display a listing of promotions
     */
    public function index(Request $request)
    {
        $query = Promotion::with(['menuItems', 'categories', 'usageLogs'])
            ->withCount('usageLogs');

        // Filter by type if requested
        if ($request->has('type') && $request->type) {
            $query->where('promotion_type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'valid') {
                $query->valid();
            }
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Fetch analytics summary for dashboard cards
        $analyticsSummary = $this->analyticsService->getOverallSummary();

        return view('admin.promotions.index', compact('promotions', 'analyticsSummary'));
    }

    /**
     * Show the form for creating a new promotion
     */
    public function create(Request $request)
    {
        $type = $request->get('type', Promotion::TYPE_PROMO_CODE);

        // Get menu items and categories for combo/discount promotions
        $menuItems = MenuItem::where('availability', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.promotions.create', compact('type', 'menuItems', 'categories'));
    }

    /**
     * Store a newly created promotion
     */
    public function store(StorePromotionRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $type = $validated['promotion_type'];

            // Process booleans
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            // Initialize usage counter for new promotions
            $validated['current_usage_count'] = 0;

            // Process applicable_days as JSON
            if ($request->has('applicable_days')) {
                $validated['applicable_days'] = $request->input('applicable_days');
            }

            // Handle banner image upload
            if ($request->hasFile('banner_image')) {
                $validated['banner_image'] = $this->storeBannerImage(
                    $request->file('banner_image'),
                    $validated['name']
                );
            }

            // Handle image upload (legacy support)
            if ($request->hasFile('image')) {
                $validated['image_path'] = $this->storePromotionImage(
                    $request->file('image'),
                    $validated['name']
                );
            }

            // Build promo config based on type
            $validated['promo_config'] = $this->buildPromoConfig($type, $request);

            // Create promotion
            $promotion = Promotion::create($validated);

            // Handle type-specific relationships
            switch($type) {
                case Promotion::TYPE_COMBO_DEAL:
                case Promotion::TYPE_BUNDLE:
                    $this->attachMenuItems($promotion, $request);
                    break;

                case Promotion::TYPE_ITEM_DISCOUNT:
                    $this->attachDiscountTargets($promotion, $request);
                    break;

                case Promotion::TYPE_BUY_X_FREE_Y:
                    $this->attachBuyXFreeYItems($promotion, $request);
                    break;

                case Promotion::TYPE_SEASONAL:
                    // Seasonal promotions don't require menu items attachment
                    // They work like promo codes with optional banner
                    break;
            }

            DB::commit();

            return redirect()
                ->route('admin.promotions.index')
                ->with('success', 'Promotion created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating promotion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified promotion
     */
    public function show(Promotion $promotion)
    {
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing a promotion
     */
    public function edit(Promotion $promotion)
    {
        // Get menu items for dynamic form sections
        $menuItems = MenuItem::where('availability', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.promotions.edit', compact('promotion', 'menuItems', 'categories'));
    }

    /**
     * Update the specified promotion
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Process booleans
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            // Process applicable_days as JSON
            if ($request->has('applicable_days')) {
                $validated['applicable_days'] = $request->input('applicable_days');
            } else {
                // If no days selected, set to null (available all days)
                $validated['applicable_days'] = null;
            }

            // IMPORTANT: Do NOT reset current_usage_count on update
            // This preserves the existing usage statistics

            // Handle banner image upload
            if ($request->hasFile('banner_image')) {
                // Delete old banner image if exists
                $this->deleteBannerImage($promotion->banner_image);

                // Store new banner image
                $validated['banner_image'] = $this->storeBannerImage(
                    $request->file('banner_image'),
                    $validated['name']
                );
            }

            // Handle image upload (legacy support)
            if ($request->hasFile('image')) {
                // Delete old image if exists
                $this->deletePromotionImage($promotion->image_path);

                // Store new image
                $validated['image_path'] = $this->storePromotionImage(
                    $request->file('image'),
                    $validated['name']
                );
            }

            // Update promo config based on type
            $validated['promo_config'] = $this->buildPromoConfig($promotion->promotion_type, $request);

            // Update promotion
            $promotion->update($validated);

            // Handle type-specific relationships
            switch($promotion->promotion_type) {
                case Promotion::TYPE_COMBO_DEAL:
                case Promotion::TYPE_BUNDLE:
                    // Detach existing items
                    $promotion->menuItems()->detach();
                    $this->attachMenuItems($promotion, $request);
                    break;

                case Promotion::TYPE_ITEM_DISCOUNT:
                    // Detach existing items and categories
                    $promotion->menuItems()->detach();
                    $promotion->categories()->detach();
                    $this->attachDiscountTargets($promotion, $request);
                    break;

                case Promotion::TYPE_BUY_X_FREE_Y:
                    // Detach existing items
                    $promotion->menuItems()->detach();
                    $this->attachBuyXFreeYItems($promotion, $request);
                    break;

                case Promotion::TYPE_SEASONAL:
                    // Seasonal promotions don't require menu items attachment
                    // They work like promo codes with optional banner
                    break;
            }

            DB::commit();

            return redirect()
                ->route('admin.promotions.index')
                ->with('success', 'Promotion updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating promotion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified promotion
     */
    public function destroy(Promotion $promotion)
    {
        // Delete promotion image if exists
        $this->deletePromotionImage($promotion->image_path);

        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion deleted successfully!');
    }

    /**
     * Toggle promotion active status
     */
    public function toggleStatus(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $promotion->is_active,
            'message' => 'Promotion status updated!'
        ]);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Build promo_config JSON based on promotion type
     * Reads from promotion_data.* request fields and transforms to promo_config structure
     */
    private function buildPromoConfig(string $type, Request $request): array
    {
        $config = [];

        switch($type) {
            case Promotion::TYPE_COMBO_DEAL:
                $config = [
                    'combo_price' => $request->input('promotion_data.combo_price'),
                    'combo_items' => $request->input('promotion_data.combo_items', []),
                    'original_price' => $request->input('original_price'),
                    'allow_customization' => $request->has('allow_customization')
                ];
                break;

            case Promotion::TYPE_BUNDLE:
                $config = [
                    // Store bundle price as 'combo_price' for consistency across combo/bundle types
                    'combo_price' => $request->input('promotion_data.bundle_price'),
                    'bundle_items' => $request->input('promotion_data.bundle_items', []),
                    'original_price' => $request->input('original_price'),
                    'allow_customization' => $request->has('allow_customization')
                ];
                break;

            case Promotion::TYPE_SEASONAL:
                $config = [
                    'combo_price' => $request->input('combo_price'),
                    'original_price' => $request->input('original_price'),
                    'allow_customization' => $request->has('allow_customization')
                ];
                break;

            case Promotion::TYPE_BUY_X_FREE_Y:
                $config = [
                    'buy_item_id' => $request->input('promotion_data.buy_item_id'),
                    'buy_quantity' => $request->input('promotion_data.buy_quantity'),
                    'get_item_id' => $request->input('promotion_data.get_item_id'),
                    'free_quantity' => $request->input('promotion_data.get_quantity'),
                    'max_free_items' => $request->input('max_free_items', 999),
                    'same_item' => $request->has('same_item')
                ];
                break;

            case Promotion::TYPE_ITEM_DISCOUNT:
                $config = [
                    'apply_to' => $request->input('apply_to', 'all'), // 'all', 'specific_items', 'categories'
                    'item_ids' => $request->input('promotion_data.item_ids', []),
                    'category_ids' => $request->input('discount_categories', [])
                ];
                break;

            case Promotion::TYPE_PROMO_CODE:
                $config = [
                    'first_order_only' => $request->has('first_order_only')
                ];
                break;
        }

        return $config;
    }

    /**
     * Attach menu items to combo/bundle promotion
     * Reads from promotion_data.combo_items or promotion_data.bundle_items based on type
     */
    private function attachMenuItems(Promotion $promotion, Request $request)
    {
        // Determine which field to read based on promotion type
        $items = [];
        if ($promotion->promotion_type === Promotion::TYPE_COMBO_DEAL) {
            $items = $request->input('promotion_data.combo_items', []);
        } elseif ($promotion->promotion_type === Promotion::TYPE_BUNDLE) {
            $items = $request->input('promotion_data.bundle_items', []);
        }

        // If no items in promotion_data, try fallback to old format
        if (empty($items)) {
            $menuItems = $request->input('menu_items', []);
            $quantities = $request->input('item_quantities', []);
            $isRequired = $request->input('item_required', []);

            $attachData = [];
            foreach($menuItems as $index => $itemId) {
                $attachData[$itemId] = [
                    'quantity' => $quantities[$itemId] ?? 1,
                    'is_required' => in_array($itemId, $isRequired),
                    'sort_order' => $index
                ];
            }
        } else {
            // New format from promotion_data.combo_items or promotion_data.bundle_items
            // Structure: [['item_id' => 2, 'quantity' => 1], ['item_id' => 32, 'quantity' => 1]]
            $attachData = [];
            foreach($items as $index => $item) {
                $itemId = $item['item_id'] ?? null;
                if ($itemId) {
                    $attachData[$itemId] = [
                        'quantity' => $item['quantity'] ?? 1,
                        'is_required' => true, // All items in combo/bundle are required
                        'sort_order' => $index
                    ];
                }
            }
        }

        if (!empty($attachData)) {
            $promotion->menuItems()->attach($attachData);
        }
    }

    /**
     * Attach items/categories for item discount promotion
     */
    private function attachDiscountTargets(Promotion $promotion, Request $request)
    {
        // Attach specific items
        $items = $request->input('discount_items', []);
        if (!empty($items)) {
            $promotion->menuItems()->attach($items);
        }

        // Attach categories
        $categories = $request->input('discount_categories', []);
        if (!empty($categories)) {
            $promotion->categories()->attach($categories);
        }
    }

    /**
     * Attach Buy X Free Y items
     */
    private function attachBuyXFreeYItems(Promotion $promotion, Request $request)
    {
        // Get data from promotion_data structure
        $buyItemId = $request->input('promotion_data.buy_item_id');
        $buyQuantity = $request->input('promotion_data.buy_quantity', 1);
        $getItemId = $request->input('promotion_data.get_item_id');
        $getQuantity = $request->input('promotion_data.get_quantity', 1);

        // Attach buy item (paid item)
        if ($buyItemId) {
            $promotion->menuItems()->attach($buyItemId, [
                'is_free' => false,
                'quantity' => $buyQuantity,
                'is_required' => true,
                'sort_order' => 0
            ]);
        }

        // Attach free item (if different from buy item or if it's the same item with additional quantity)
        if ($getItemId && $getItemId != $buyItemId) {
            $promotion->menuItems()->attach($getItemId, [
                'is_free' => true,
                'quantity' => $getQuantity,
                'is_required' => true,
                'sort_order' => 1
            ]);
        } elseif ($getItemId && $getItemId == $buyItemId) {
            // If same item, attach it again but as free
            $promotion->menuItems()->attach($getItemId, [
                'is_free' => true,
                'quantity' => $getQuantity,
                'is_required' => true,
                'sort_order' => 1
            ]);
        }
    }

    /**
     * Get promotion statistics
     */
    public function stats(Promotion $promotion)
    {
        $stats = $this->promotionService->getPromotionStats($promotion);

        return view('admin.promotions.stats', compact('promotion', 'stats'));
    }

    /**
     * Display detailed analytics for specific promotion
     */
    public function analytics(Request $request, Promotion $promotion)
    {
        // Get date range from request or default to last 30 days
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Fetch detailed analytics
        $analytics = $this->analyticsService->getPromotionAnalytics(
            $promotion->id,
            $dateFrom,
            $dateTo
        );

        return view('admin.promotions.analytics', compact('promotion', 'analytics', 'dateFrom', 'dateTo'));
    }

    /**
     * Duplicate a promotion
     */
    public function duplicate(Promotion $promotion)
    {
        try {
            DB::beginTransaction();

            $newPromotion = $promotion->replicate();
            $newPromotion->name = $promotion->name . ' (Copy)';
            $newPromotion->promo_code = $newPromotion->promo_code ? strtoupper(Str::random(8)) : null;
            $newPromotion->current_usage_count = 0;
            $newPromotion->is_active = false;
            $newPromotion->save();

            // Copy relationships
            if ($promotion->menuItems->count() > 0) {
                $items = [];
                foreach($promotion->menuItems as $item) {
                    $items[$item->id] = [
                        'quantity' => $item->pivot->quantity,
                        'is_free' => $item->pivot->is_free,
                        'is_required' => $item->pivot->is_required,
                        'is_customizable' => $item->pivot->is_customizable,
                        'custom_price' => $item->pivot->custom_price,
                        'sort_order' => $item->pivot->sort_order
                    ];
                }
                $newPromotion->menuItems()->attach($items);
            }

            if ($promotion->categories->count() > 0) {
                $newPromotion->categories()->sync($promotion->categories->pluck('id'));
            }

            DB::commit();

            return redirect()
                ->route('admin.promotions.edit', $newPromotion)
                ->with('success', 'Promotion duplicated successfully! Please review and activate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error duplicating promotion: ' . $e->getMessage());
        }
    }

    /**
     * Store promotion image to storage/app/public/promotions
     * Filename format: promo-name_date_random.ext (NO category)
     */
    private function storePromotionImage(\Illuminate\Http\UploadedFile $file, string $promoName): string
    {
        if (!$file->isValid()) {
            throw new \RuntimeException('Uploaded file is not valid');
        }

        // Buat folder: storage/app/public/promotions
        $dir = storage_path('app/public/promotions');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Extension asal
        $extension = $file->getClientOriginalExtension() ?: $file->extension();

        // Slug untuk promo name
        $promoSlug = Str::slug($promoName);

        // Nama fail: promo-name_date_random.ext (NO category!)
        $filename = $promoSlug
            . '_' . date('Ymd_His')
            . '_' . Str::random(6)
            . '.' . $extension;

        // Move file
        $file->move($dir, $filename);

        // Return relative path untuk DB
        return 'promotions/' . $filename;
    }

    /**
     * Delete promotion image from storage
     */
    private function deletePromotionImage(?string $imagePath): void
    {
        if ($imagePath) {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    /**
     * Store banner image to storage/app/public/promotions/banners
     */
    private function storeBannerImage(\Illuminate\Http\UploadedFile $file, string $promoName): string
    {
        if (!$file->isValid()) {
            throw new \RuntimeException('Uploaded file is not valid');
        }

        // Create folder: storage/app/public/promotions/banners
        $dir = storage_path('app/public/promotions/banners');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Original extension
        $extension = $file->getClientOriginalExtension() ?: $file->extension();

        // Slug for promo name
        $promoSlug = Str::slug($promoName);

        // Filename: promo-name_date_random.ext
        $filename = 'banner_' . $promoSlug
            . '_' . date('Ymd_His')
            . '_' . Str::random(6)
            . '.' . $extension;

        // Move file
        $file->move($dir, $filename);

        // Return relative path for DB
        return 'promotions/banners/' . $filename;
    }

    /**
     * Delete banner image from storage
     */
    private function deleteBannerImage(?string $imagePath): void
    {
        if ($imagePath) {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
