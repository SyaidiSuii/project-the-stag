<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\HappyHourDeal;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\Promotions\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
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

        return view('admin.promotions.index', compact('promotions'));
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
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Base validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'promotion_type' => 'required|in:combo_deal,item_discount,buy_x_free_y,promo_code,seasonal,bundle',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'badge_text' => 'nullable|string|max:50',
                'banner_color' => 'nullable|string|max:7',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'applicable_days' => 'nullable|array',
                'applicable_start_time' => 'nullable|date_format:H:i',
                'applicable_end_time' => 'nullable|date_format:H:i',
                'terms_conditions' => 'nullable|string',
                'usage_limit_per_customer' => 'nullable|integer|min:1',
                'total_usage_limit' => 'nullable|integer|min:1',
                'display_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
            ];

            // Type-specific validation
            $type = $request->input('promotion_type');

            if ($type === Promotion::TYPE_PROMO_CODE) {
                $rules['promo_code'] = 'required|string|max:50|unique:promotions,promo_code';
                $rules['discount_type'] = 'required|in:percentage,fixed';
                $rules['discount_value'] = 'required|numeric|min:0';
                $rules['max_discount_amount'] = 'nullable|numeric|min:0';
                $rules['minimum_order_value'] = 'nullable|numeric|min:0';
            }

            if (in_array($type, [Promotion::TYPE_COMBO_DEAL, Promotion::TYPE_BUNDLE, Promotion::TYPE_SEASONAL])) {
                $rules['menu_items'] = 'required|array|min:1';
                $rules['menu_items.*'] = 'exists:menu_items,id';
                $rules['combo_price'] = 'required|numeric|min:0';
                $rules['original_price'] = 'nullable|numeric|min:0';
            }

            if ($type === Promotion::TYPE_ITEM_DISCOUNT) {
                $rules['discount_type'] = 'required|in:percentage,fixed';
                $rules['discount_value'] = 'required|numeric|min:0';
                $rules['max_discount_amount'] = 'nullable|numeric|min:0';
                $rules['discount_items'] = 'nullable|array';
                $rules['discount_categories'] = 'nullable|array';
            }

            if ($type === Promotion::TYPE_BUY_X_FREE_Y) {
                $rules['buy_quantity'] = 'required|integer|min:1';
                $rules['free_quantity'] = 'required|integer|min:1';
                $rules['buy_items'] = 'required|array|min:1';
                $rules['free_items'] = 'nullable|array';
            }

            $validated = $request->validate($rules);

            // Process booleans
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image_path'] = $this->storePromotionImage(
                    $request->file('image'),
                    $validated['name']
                );
            }

            // Build promo_config based on type
            $validated['promo_config'] = $this->buildPromoConfig($type, $request);

            // Auto-generate promo code for promo_code type if not provided
            if ($type === Promotion::TYPE_PROMO_CODE && empty($validated['promo_code'])) {
                $validated['promo_code'] = strtoupper(Str::random(8));
            }

            // Remove fields that shouldn't be in promotion table
            unset($validated['image'], $validated['menu_items'], $validated['discount_items'],
                  $validated['discount_categories'], $validated['buy_items'], $validated['free_items'],
                  $validated['combo_price'], $validated['original_price'], $validated['buy_quantity'],
                  $validated['free_quantity']);

            // Create promotion
            $promotion = Promotion::create($validated);

            // Attach menu items for combo/bundle types
            if (in_array($type, [Promotion::TYPE_COMBO_DEAL, Promotion::TYPE_BUNDLE, Promotion::TYPE_SEASONAL])) {
                $this->attachMenuItems($promotion, $request);
            }

            // Attach items/categories for item discount
            if ($type === Promotion::TYPE_ITEM_DISCOUNT) {
                $this->attachDiscountTargets($promotion, $request);
            }

            // Attach items for Buy X Free Y
            if ($type === Promotion::TYPE_BUY_X_FREE_Y) {
                $this->attachBuyXFreeYItems($promotion, $request);
            }

            DB::commit();

            return redirect()
                ->route('admin.promotions.index')
                ->with('success', 'Promotion created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
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
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified promotion
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'promo_code' => 'nullable|string|max:50|unique:promotions,promo_code,' . $promotion->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean'  // ✅ Add nullable!
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            $this->deletePromotionImage($promotion->image_path);

            // Store new image
            $validated['image_path'] = $this->storePromotionImage(
                $request->file('image'),
                $validated['name']
            );
        }

        // Remove 'image' from validated data as it's not a database column
        unset($validated['image']);

        $promotion->update($validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Promotion updated successfully!');
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

    // ===== Happy Hour Deals Management =====

    /**
     * Show the form for creating a new happy hour deal
     */
    public function createHappyHour()
    {
        $menuItems = MenuItem::where('is_available', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('admin.promotions.create-happy-hour', compact('menuItems'));
    }

    /**
     * Store a newly created happy hour deal
     */
    public function storeHappyHour(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'menu_items' => 'required|array|min:1',
            'menu_items.*' => 'exists:menu_items,id',
            'is_active' => 'nullable|boolean'  // ✅ Add nullable!
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';

        $menuItems = $validated['menu_items'];
        unset($validated['menu_items']);

        $happyHourDeal = HappyHourDeal::create($validated);
        $happyHourDeal->menuItems()->attach($menuItems);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Happy Hour Deal created successfully!');
    }

    /**
     * Show the form for editing a happy hour deal
     */
    public function editHappyHour(HappyHourDeal $happyHourDeal)
    {
        $menuItems = MenuItem::where('is_available', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        $selectedMenuItems = $happyHourDeal->menuItems->pluck('id')->toArray();

        return view('admin.promotions.edit-happy-hour', compact(
            'happyHourDeal',
            'menuItems',
            'selectedMenuItems'
        ));
    }

    /**
     * Update the specified happy hour deal
     */
    public function updateHappyHour(Request $request, HappyHourDeal $happyHourDeal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'menu_items' => 'required|array|min:1',
            'menu_items.*' => 'exists:menu_items,id',
            'is_active' => 'nullable|boolean'  // ✅ Add nullable!
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';

        $menuItems = $validated['menu_items'];
        unset($validated['menu_items']);

        $happyHourDeal->update($validated);
        $happyHourDeal->menuItems()->sync($menuItems);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Happy Hour Deal updated successfully!');
    }

    /**
     * Remove the specified happy hour deal
     */
    public function destroyHappyHour(HappyHourDeal $happyHourDeal)
    {
        $happyHourDeal->menuItems()->detach();
        $happyHourDeal->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Happy Hour Deal deleted successfully!');
    }

    /**
     * Toggle happy hour deal active status
     */
    public function toggleHappyHourStatus(HappyHourDeal $happyHourDeal)
    {
        $happyHourDeal->update(['is_active' => !$happyHourDeal->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $happyHourDeal->is_active,
            'message' => 'Happy Hour Deal status updated!'
        ]);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Build promo_config JSON based on promotion type
     */
    private function buildPromoConfig(string $type, Request $request): array
    {
        $config = [];

        switch($type) {
            case Promotion::TYPE_COMBO_DEAL:
            case Promotion::TYPE_BUNDLE:
            case Promotion::TYPE_SEASONAL:
                $config = [
                    'combo_price' => $request->input('combo_price'),
                    'original_price' => $request->input('original_price'),
                    'allow_customization' => $request->has('allow_customization')
                ];
                break;

            case Promotion::TYPE_BUY_X_FREE_Y:
                $config = [
                    'buy_quantity' => $request->input('buy_quantity'),
                    'buy_item_ids' => $request->input('buy_items', []),
                    'free_quantity' => $request->input('free_quantity'),
                    'free_item_ids' => $request->input('free_items') ?? $request->input('buy_items', []),
                    'max_free_items' => $request->input('max_free_items', 999),
                    'same_item' => $request->has('same_item')
                ];
                break;

            case Promotion::TYPE_ITEM_DISCOUNT:
                $config = [
                    'apply_to' => $request->input('apply_to', 'all'), // 'all', 'specific_items', 'categories'
                    'item_ids' => $request->input('discount_items', []),
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
     */
    private function attachMenuItems(Promotion $promotion, Request $request)
    {
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

        $promotion->menuItems()->attach($attachData);
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
        $buyItems = $request->input('buy_items', []);
        $freeItems = $request->input('free_items', $buyItems);

        // Attach buy items
        foreach($buyItems as $itemId) {
            $promotion->menuItems()->attach($itemId, [
                'is_free' => false,
                'quantity' => 1
            ]);
        }

        // Attach free items (if different)
        if ($freeItems != $buyItems) {
            foreach($freeItems as $itemId) {
                if (!in_array($itemId, $buyItems)) {
                    $promotion->menuItems()->attach($itemId, [
                        'is_free' => true,
                        'quantity' => 1
                    ]);
                }
            }
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
}
