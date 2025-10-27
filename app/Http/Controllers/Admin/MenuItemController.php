<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class MenuItemController extends Controller
{
    /**
     * Display a listing of menu items
     */
    public function index(Request $request)
    {
        // Get all categories
        $categories = Category::orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get parent categories for filtering
        $foodCategory = $categories->where('type', 'food')->whereNull('parent_id')->first();
        $drinkCategory = $categories->where('type', 'drink')->whereNull('parent_id')->first();
        $setMealCategory = $categories->where('type', 'set-meal')->whereNull('parent_id')->first();

        // Determine which tab is active (default to food)
        $activeTab = $request->get('tab', 'food');

        // Base query with category relationship
        $baseQuery = MenuItem::with('category');

        // Apply filters based on active tab
        if ($activeTab === 'food' && $foodCategory) {
            // Get all food category IDs (parent + subcategories)
            $foodCategoryIds = $categories->where('type', 'food')->pluck('id');
            $baseQuery->whereIn('category_id', $foodCategoryIds);
        } elseif ($activeTab === 'drinks' && $drinkCategory) {
            // Get all drink category IDs
            $drinkCategoryIds = $categories->where('type', 'drink')->pluck('id');
            $baseQuery->whereIn('category_id', $drinkCategoryIds);
        } elseif ($activeTab === 'set-meals' && $setMealCategory) {
            // Get all set meal category IDs
            $setMealCategoryIds = $categories->where('type', 'set-meal')->pluck('id');
            $baseQuery->whereIn('category_id', $setMealCategoryIds);
        }

        // Filter by specific category
        if ($request->has('category_id') && $request->category_id != '') {
            $baseQuery->where('category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->has('availability') && $request->availability != '') {
            $baseQuery->where('availability', $request->boolean('availability'));
        }

        // Filter featured items
        if ($request->has('is_featured') && $request->is_featured != '') {
            $baseQuery->where('is_featured', $request->boolean('is_featured'));
        }

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSortFields = ['name', 'price', 'rating_average', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $baseQuery->orderBy($sortBy, $sortOrder);
        }

        // Paginate results
        $menuItems = $baseQuery->paginate(
            $request->get('per_page', 15)
        )->appends($request->query());

        // Get statistics for dashboard cards
        $totalItems = MenuItem::count();
        $availableItems = MenuItem::where('availability', true)->count();
        $unavailableItems = MenuItem::where('availability', false)->count();
        $categoriesCount = Category::count();

        return view('admin.menu-items.index', compact(
            'menuItems',
            'categories',
            'totalItems',
            'availableItems',
            'unavailableItems',
            'categoriesCount',
            'activeTab'
        ));
    }

    /**
     * Show the form for creating a new menu item
     */
    public function create(Request $request)
    {
        $menuItem = new MenuItem();

        // Get only subcategories for dropdown (exclude parent categories)
        $categories = Category::whereNotNull('parent_id')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Pre-select category if provided
        $selectedCategoryId = $request->get('category_id');

        return view('admin.menu-items.form', compact('menuItem', 'categories', 'selectedCategoryId'));
    }

    /**
     * Store a newly created menu item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menu_items', 'name')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string|max:100',
            'preparation_time' => 'nullable|integer|min:1|max:180',
            'availability' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Set defaults if not provided
        $validated['availability'] = $validated['availability'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['preparation_time'] = $validated['preparation_time'] ?? 15;

        // upload image kalau ada
        if ($request->hasFile('image')) {
            $category = Category::find($validated['category_id']);
            $validated['image'] = $this->storeImageToStoragePublic(
                $request->file('image'),
                'menu_items',
                $validated['name'],
                $category->name ?? null
            );
        }

        $menuItem = MenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')
                        ->with('message', 'Menu item created successfully');
    }

    /**
     * Display the specified menu item
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('category');
        return view('admin.menu-items.show', compact('menuItem'));
    }

    /**
     * Show the form for editing the specified menu item
     */
    public function edit(MenuItem $menuItem)
    {
        // Get only subcategories for dropdown (exclude parent categories)
        $categories = Category::whereNotNull('parent_id')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $menuItem->load('category');
        $selectedCategoryId = $menuItem->category_id;

        return view('admin.menu-items.form', compact('menuItem', 'categories', 'selectedCategoryId'));
    }

    /**
     * Update the specified menu item
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('menu_items', 'name')
                    ->ignore($menuItem->id)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string|max:100',
            'preparation_time' => 'nullable|integer|min:1|max:180',
            'availability' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle checkboxes that might not be sent if unchecked
        $validated['availability'] = $request->has('availability');
        $validated['is_featured'] = $request->has('is_featured');


        // handle new image
        if ($request->hasFile('image')) {
            // delete old file
            if ($menuItem->image) {
                $oldFull = storage_path('app/public/' . $menuItem->image);
                if (file_exists($oldFull)) {
                    @unlink($oldFull);
                }
            }

            // dapatkan nama category
            $category = Category::find($validated['category_id'] ?? $menuItem->category_id);

            // upload baru
            $validated['image'] = $this->storeImageToStoragePublic(
                $request->file('image'),
                'menu_items',
                $validated['name'] ?? $menuItem->name,
                $category->name ?? null
            );
        }

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items.index')
                        ->with('message', 'Menu item updated successfully');
    }

    /**
     * Remove the specified menu item (soft delete)
     */
    public function destroy(MenuItem $menuItem)
    {
        // Check how many users have this item in their cart
        $cartCount = \App\Models\UserCart::where('menu_item_id', $menuItem->id)->count();

        // Soft delete the menu item
        $menuItem->delete();

        $message = 'Menu item deleted successfully';

        // Add warning if item was in carts
        if ($cartCount > 0) {
            $message .= ". Note: {$cartCount} user(s) had this item in their cart. The item will appear as unavailable in their carts.";
        }

        return redirect()->route('admin.menu-items.index')
                        ->with('message', $message)
                        ->with('warning', $cartCount > 0 ? "Item was in {$cartCount} user cart(s)" : null);
    }

    /**
     * Get featured menu items
     */
    // public function getFeatured()
    // {
    //     $featuredItems = MenuItem::with('category')
    //                             ->where('is_featured', true)
    //                             ->orderBy('rating_average', 'desc')
    //                             ->get();

    //     return view('admin.menu-items.featured', compact('featuredItems'));
    // }

    /**
     * Get menu statistics
     */
    // public function getStatistics()
    // {
    //     $stats = [
    //         'total_items' => MenuItem::count(),
    //         'available_items' => MenuItem::where('availability', true)->count(),
    //         'featured_items' => MenuItem::where('is_featured', true)->count(),
    //         'by_category' => MenuItem::join('categories', 'menu_items.category_id', '=', 'categories.id')
    //                                ->select('categories.name as category_name')
    //                                ->selectRaw('count(*) as count')
    //                                ->groupBy('categories.id', 'categories.name')
    //                                ->get()
    //                                ->pluck('count', 'category_name')
    //                                ->toArray(),
    //         'average_rating' => MenuItem::where('rating_count', '>', 0)
    //                                   ->avg('rating_average'),
    //         'average_price' => MenuItem::avg('price'),
    //         'price_range' => [
    //             'min' => MenuItem::min('price') ?? 0,
    //             'max' => MenuItem::max('price') ?? 0
    //         ]
    //     ];

    //     return view('admin.menu-items.statistic', compact('stats'));
    // }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update([
            'availability' => !$menuItem->availability
        ]);

        return back()->with('message', 'Menu item availability updated');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(MenuItem $menuItem)
    {
        $menuItem->update([
            'is_featured' => !$menuItem->is_featured
        ]);

        return back()->with('message', 'Menu item featured status updated');
    }

    /**
     * Update menu item rating
     */
    public function updateRating(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $newRatingCount = $menuItem->rating_count + 1;
        $newRatingAverage = (($menuItem->rating_average * $menuItem->rating_count) + $validated['rating']) / $newRatingCount;

        $menuItem->update([
            'rating_average' => round($newRatingAverage, 2),
            'rating_count' => $newRatingCount
        ]);

        return back()->with('message', 'Rating updated successfully');
    }


    private function storeImageToStoragePublic(\Illuminate\Http\UploadedFile $file, string $subfolder = 'images', ?string $itemName = null, ?string $categoryName = null): string
    {
        if (!$file->isValid()) {
            throw new \RuntimeException('Uploaded file is not valid');
        }

        // buat folder penuh: storage/app/public/{subfolder}
        $dir = storage_path('app/public/' . trim($subfolder, '/'));
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // extension asal
        $extension = $file->getClientOriginalExtension() ?: $file->extension();

        // slug untuk item dan kategori
        $itemSlug = $itemName ? Str::slug($itemName) : 'item';
        $categorySlug = $categoryName ? Str::slug($categoryName) : 'general';

        // nama fail: item-kategori-tarikh-random.ext
        $filename = $itemSlug
            . '_' . $categorySlug
            . '_' . date('Ymd_His')
            . '_' . Str::random(6)
            . '.' . $extension;

        // move file
        $file->move($dir, $filename);

        // return relative path untuk DB
        return trim($subfolder, '/') . '/' . $filename;
    }

    /**
     * Show the form for creating a new set meal.
     */
    public function createSetMeal()
    {
        // Ambil semua item yang BUKAN set meal untuk dijadikan komponen
        $menuItems = MenuItem::where('is_set_meal', false)->orderBy('name')->get();
        
        // Ambil semua kategori untuk dropdown
        $categories = Category::orderBy('name')->get();
        
        return view('admin.menu-items.create-set-meal', compact('menuItems', 'categories'));
    }

    /**
     * Store a newly created set meal in storage.
     */
    public function storeSetMeal(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'components' => 'required|array|min:1',
            'components.*' => 'exists:menu_items,id',
            'is_set_meal' => 'required|boolean',
        ]);

        // Cipta item utama untuk set meal
        $setMeal = MenuItem::create($request->except(['components', 'image']));

        // Handle image upload jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-items', 'public');
            $setMeal->image = $path;
            $setMeal->save();
        }

        // Attach komponen-komponen yang dipilih
        $setMeal->components()->attach($request->components);

        return redirect()->route('admin.menu-items.index')
                         ->with('success', 'Set meal created successfully.');
    }
}