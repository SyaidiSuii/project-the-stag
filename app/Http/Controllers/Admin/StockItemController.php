<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockItemController extends Controller
{
    /**
     * Display a listing of stock items with filters
     */
    public function index(Request $request)
    {
        $query = StockItem::with('supplier');

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'critical':
                    $query->criticalStock();
                    break;
                case 'good':
                    $query->whereRaw('current_quantity > reorder_point');
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                default:
                    $query->active();
            }
        } else {
            $query->active();
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $stockItems = $query->latest()->paginate(15);

        // Get categories for filter
        $categories = StockItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        // Get suppliers for filter
        $suppliers = Supplier::active()->get();

        return view('admin.stock.items.index', compact('stockItems', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new stock item
     */
    public function create()
    {
        $suppliers = Supplier::active()->get();
        return view('admin.stock.items.form', compact('suppliers'));
    }

    /**
     * Store a newly created stock item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:stock_items,sku|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'current_quantity' => 'required|numeric|min:0',
            'minimum_threshold' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'storage_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        $stockItem = StockItem::create($validated);

        // Log initial stock as transaction
        $stockItem->transactions()->create([
            'transaction_type' => 'initial',
            'quantity' => $validated['current_quantity'],
            'previous_quantity' => 0,
            'new_quantity' => $validated['current_quantity'],
            'notes' => 'Initial stock entry',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.stock.items.index')
            ->with('success', 'Stock item created successfully!');
    }

    /**
     * Display the specified stock item with transaction history
     */
    public function show(StockItem $item)
    {
        $item->load(['supplier', 'transactions' => function ($query) {
            $query->with('creator')->latest()->limit(20);
        }]);

        return view('admin.stock.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified stock item
     */
    public function edit(StockItem $item)
    {
        $suppliers = Supplier::active()->get();
        return view('admin.stock.items.form', compact('item', 'suppliers'));
    }

    /**
     * Update the specified stock item
     */
    public function update(Request $request, StockItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:stock_items,sku,' . $item->id . '|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'minimum_threshold' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'storage_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('admin.stock.items.index')
            ->with('success', 'Stock item updated successfully!');
    }

    /**
     * Remove the specified stock item (soft delete)
     */
    public function destroy(StockItem $item)
    {
        $item->delete();

        return redirect()->route('admin.stock.items.index')
            ->with('success', 'Stock item deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(StockItem $item)
    {
        $item->is_active = !$item->is_active;
        $item->save();

        return response()->json([
            'success' => true,
            'is_active' => $item->is_active,
            'message' => 'Status updated successfully!'
        ]);
    }

    /**
     * Adjust stock quantity (manual adjustment)
     */
    public function adjustStock(Request $request, StockItem $item)
    {
        $validated = $request->validate([
            'new_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item->adjustStock(
            $validated['new_quantity'],
            $validated['notes'] ?? 'Manual stock adjustment',
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully!',
            'new_quantity' => $item->fresh()->current_quantity,
        ]);
    }
}
