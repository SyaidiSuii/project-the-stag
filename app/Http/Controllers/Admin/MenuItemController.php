<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuItem;
use Illuminate\Http\Request;
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
        $query = MenuItem::query();

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->has('availability') && $request->availability != '') {
            $query->where('availability', $request->boolean('availability'));
        }

        // Filter featured items
        if ($request->has('featured') && $request->featured != '') {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSortFields = ['name', 'price', 'category', 'rating_average', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $menuItems = $query->paginate(
            $request->get('per_page', 15)
        );

        return view('admin.menu-items.index', compact('menuItems'));
    }

    /**
     * Show the form for creating a new menu item
     */
    public function create()
    {
        $menuItem = new MenuItem();
        return view('admin.menu-items.form', compact('menuItem'));
    }

    /**
     * Store a newly created menu item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_items,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => ['required', Rule::in(['western', 'local', 'drink', 'dessert', 'appetizer'])],
            'image_url' => 'nullable|url',
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

        $menuItem = MenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')
                        ->with('message', 'Menu item created successfully');
    }

    /**
     * Display the specified menu item
     */
    public function show(MenuItem $menuItem)
    {
        return view('admin.menu-items.show', compact('menuItem'));
    }

    /**
     * Show the form for editing the specified menu item
     */
    public function edit(MenuItem $menuItem)
    {
        return view('admin.menu-items.form', compact('menuItem'));
    }

    /**
     * Update the specified menu item
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:menu_items,name,' . $menuItem->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category' => ['sometimes', Rule::in(['western', 'local', 'drink', 'dessert', 'appetizer'])],
            'image_url' => 'nullable|url',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string|max:100',
            'preparation_time' => 'nullable|integer|min:1|max:180',
            'availability' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle checkboxes that might not be sent if unchecked
        $validated['availability'] = $request->has('availability');
        $validated['is_featured'] = $request->has('is_featured');

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items.index')
                        ->with('message', 'Menu item updated successfully');
    }

    /**
     * Remove the specified menu item (soft delete)
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')
                        ->with('message', 'Menu item deleted successfully');
    }

    /**
     * Get featured menu items
     */
    public function getFeatured()
    {
        $featuredItems = MenuItem::where('is_featured', true)
                                ->orderBy('rating_average', 'desc')
                                ->get();

        return view('admin.menu-items.featured', compact('featuredItems'));
    }

    /**
     * Get menu statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_items' => MenuItem::count(),
            'available_items' => MenuItem::where('availability', true)->count(),
            'featured_items' => MenuItem::where('is_featured', true)->count(),
            'by_category' => MenuItem::select('category')
                                   ->selectRaw('count(*) as count')
                                   ->groupBy('category')
                                   ->get()
                                   ->pluck('count', 'category')
                                   ->toArray(),
            'average_rating' => MenuItem::where('rating_count', '>', 0)
                                      ->avg('rating_average'),
            'average_price' => MenuItem::avg('price'),
            'price_range' => [
                'min' => MenuItem::min('price') ?? 0,
                'max' => MenuItem::max('price') ?? 0
            ]
        ];

        return view('admin.menu-items.statistic', compact('stats'));
    }

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
}