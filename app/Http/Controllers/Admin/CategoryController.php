<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Get only subcategories (parent categories are fixed and not editable)
        $categories = Category::with('menuItems', 'parent')
            ->whereNotNull('parent_id')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get parent categories for reference
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('type')
            ->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $type = $request->get('type', 'food');

        // Get parent category based on type
        $parentCategory = Category::whereNull('parent_id')
            ->where('type', $type)
            ->first();

        return view('admin.categories.create', compact('type', 'parentCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,deleted_at,NULL',
            'type' => 'required|in:food,drink,set-meal',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Set default sort_order if not provided
        if (empty($validated['sort_order'])) {
            $query = Category::where('type', $validated['type']);
            if (!empty($validated['parent_id'])) {
                $query->where('parent_id', $validated['parent_id']);
            } else {
                $query->whereNull('parent_id');
            }
            $maxSortOrder = $query->max('sort_order');
            $validated['sort_order'] = ($maxSortOrder ?? 0) + 1;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category->load('menuItems');

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        // Prevent editing of parent categories (Food, Drink, Set Meal)
        if ($category->parent_id === null) {
            return redirect()->route('admin.categories.index')
                ->withErrors(['edit' => 'Cannot edit main categories.']);
        }

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Prevent updating parent categories
        if ($category->parent_id === null) {
            return redirect()->route('admin.categories.index')
                ->withErrors(['update' => 'Cannot update main categories.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id . ',id,deleted_at,NULL',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Type and parent_id cannot be changed
        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Prevent deleting parent categories (Food, Drink, Set Meal)
        if ($category->parent_id === null) {
            return back()->withErrors(['delete' => 'Cannot delete main categories.']);
        }

        // Check if category has menu items
        if ($category->menuItems()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete category with existing menu items.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Get subcategories for AJAX request
     */
    public function getSubCategories(Request $request): JsonResponse
    {
        $parentId = $request->get('parent_id');
        
        $subCategories = Category::where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subCategories);
    }

    /**
     * Update sort order via AJAX
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['categories'] as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil diupdate.']);
    }

    /**
     * Get all categories in hierarchical format for API
     */
    public function getHierarchical(): JsonResponse
    {
        $categories = Category::with('subCategories')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Restore soft deleted category
     */
    public function restore($id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dipulihkan.');
    }

    /**
     * Force delete category permanently
     */
    public function forceDelete($id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        
        // Cek apakah kategori memiliki sub kategori
        if ($category->subCategories()->withTrashed()->count() > 0) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus permanen kategori yang memiliki sub kategori.']);
        }

        $category->forceDelete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus permanen.');
    }
}