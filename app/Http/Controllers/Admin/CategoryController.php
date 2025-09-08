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
        $categories = Category::with(['parent', 'subCategories'])
            ->whereNull('parent_id') // Hanya ambil kategori utama
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $mainCategories = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('mainCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'type' => 'required|in:main,sub',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Validasi tambahan: jika type = sub, parent_id harus ada
        if ($validated['type'] === 'sub' && empty($validated['parent_id'])) {
            return back()->withErrors(['parent_id' => 'Sub kategori harus memiliki parent kategori.']);
        }

        // Validasi tambahan: jika type = main, parent_id harus null
        if ($validated['type'] === 'main') {
            $validated['parent_id'] = null;
        }

        // Set default sort_order jika tidak diisi
        if (empty($validated['sort_order'])) {
            $maxSortOrder = Category::where('parent_id', $validated['parent_id'])->max('sort_order');
            $validated['sort_order'] = ($maxSortOrder ?? 0) + 1;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'subCategories', 'menuItems']);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $mainCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id) // Exclude kategori yang sedang diedit
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'mainCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'type' => 'required|in:main,sub',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Validasi tambahan: jika type = sub, parent_id harus ada
        if ($validated['type'] === 'sub' && empty($validated['parent_id'])) {
            return back()->withErrors(['parent_id' => 'Sub kategori harus memiliki parent kategori.']);
        }

        // Validasi tambahan: jika type = main, parent_id harus null
        if ($validated['type'] === 'main') {
            $validated['parent_id'] = null;
        }

        // Validasi: kategori tidak bisa menjadi parent dari dirinya sendiri
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Kategori tidak bisa menjadi parent dari dirinya sendiri.']);
        }

        // Validasi: kategori tidak bisa menjadi child dari sub kategorinya
        if ($validated['parent_id'] && $category->subCategories()->where('id', $validated['parent_id'])->exists()) {
            return back()->withErrors(['parent_id' => 'Kategori tidak bisa menjadi child dari sub kategorinya.']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Cek apakah kategori memiliki sub kategori
        if ($category->subCategories()->count() > 0) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus kategori yang memiliki sub kategori.']);
        }

        // Cek apakah kategori memiliki menu items
        if ($category->menuItems()->count() > 0) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus kategori yang memiliki menu items.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
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