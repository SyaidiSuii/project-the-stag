<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuCustomization;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class MenuCustomizationController extends Controller
{
    /**
     * Display a listing of menu customizations
     */
    public function index(Request $request)
    {
        $query = MenuCustomization::with(['orderItem.menuItem', 'orderItem.order']);

        // Filter by customization type
        if ($request->filled('customization_type')) {
            $query->where('customization_type', $request->customization_type);
        }

        // Filter by additional price range
        if ($request->filled('min_price')) {
            $query->where('additional_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('additional_price', '<=', $request->max_price);
        }

        // Search by customization value
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('customization_value', 'LIKE', "%{$search}%");
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Get statistics for dashboard cards
        $allowedSortFields = ['customization_type', 'customization_value', 'additional_price', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $customizations = $query->paginate(
            $request->get('per_page', 15)
        )->appends($request->query());

        // Get unique customization types for filter dropdown
        $customizationTypes = MenuCustomization::select('customization_type')
            ->distinct()
            ->orderBy('customization_type')
            ->pluck('customization_type');

        return view('admin.menu-customizations.index', compact('customizations', 'customizationTypes'));
    }

    /**
     * Show the form for creating a new menu customization
     */
    public function create()
    {
        $menuCustomization = new MenuCustomization();
        $orderItems = OrderItem::with(['menuItem', 'order'])
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['pending', 'confirmed', 'preparing']);
            })
            ->get();
            
        return view('admin.menu-customizations.form', compact('menuCustomization', 'orderItems'));
    }

    /**
     * Store a newly created menu customization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'customization_type' => 'required|string|max:100',
            'customization_value' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0|max:999999.99',
        ], [
            'order_item_id.required' => 'Order item is required.',
            'order_item_id.exists' => 'Selected order item does not exist.',
            'customization_type.required' => 'Customization type is required.',
            'customization_type.max' => 'Customization type cannot exceed 100 characters.',
            'customization_value.required' => 'Customization value is required.',
            'customization_value.max' => 'Customization value cannot exceed 255 characters.',
            'additional_price.required' => 'Additional price is required.',
            'additional_price.numeric' => 'Additional price must be a valid number.',
            'additional_price.min' => 'Additional price cannot be negative.',
            'additional_price.max' => 'Additional price exceeds maximum limit.',
        ]);

        $customization = MenuCustomization::create($validated);

        // Update order item total if there's additional price
        if ($customization->additional_price > 0) {
            $this->updateOrderItemTotal($customization->order_item_id);
        }

        return redirect()->route('admin.menu-customizations.index')
                        ->with('message', 'Menu customization created successfully');
    }

    /**
     * Display the specified menu customization
     */
    public function show(MenuCustomization $menuCustomization)
    {
        $menuCustomization->load(['orderItem.menuItem', 'orderItem.order.user']);
        return view('admin.menu-customizations.show', compact('menuCustomization'));
    }

    /**
     * Show the form for editing the specified menu customization
     */
    public function edit(MenuCustomization $menuCustomization)
    {
        $orderItems = OrderItem::with(['menuItem', 'order'])
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['pending', 'confirmed', 'preparing']);
            })
            ->get();
            
        return view('admin.menu-customizations.form', compact('menuCustomization', 'orderItems'));
    }

    /**
     * Update the specified menu customization
     */
    public function update(Request $request, MenuCustomization $menuCustomization)
    {
        $validated = $request->validate([
            'order_item_id' => 'sometimes|exists:order_items,id',
            'customization_type' => 'sometimes|string|max:100',
            'customization_value' => 'sometimes|string|max:255',
            'additional_price' => 'sometimes|numeric|min:0|max:999999.99',
        ], [
            'order_item_id.exists' => 'Selected order item does not exist.',
            'customization_type.max' => 'Customization type cannot exceed 100 characters.',
            'customization_value.max' => 'Customization value cannot exceed 255 characters.',
            'additional_price.numeric' => 'Additional price must be a valid number.',
            'additional_price.min' => 'Additional price cannot be negative.',
            'additional_price.max' => 'Additional price exceeds maximum limit.',
        ]);

        $oldOrderItemId = $menuCustomization->order_item_id;
        
        $menuCustomization->update($validated);

        // Update order item totals for affected order items
        if (isset($validated['order_item_id']) && $oldOrderItemId != $validated['order_item_id']) {
            $this->updateOrderItemTotal($oldOrderItemId);
        }
        $this->updateOrderItemTotal($menuCustomization->order_item_id);

        return redirect()->route('admin.menu-customizations.index')
                        ->with('message', 'Menu customization updated successfully');
    }

    /**
     * Remove the specified menu customization (soft delete)
     */
    public function destroy(MenuCustomization $menuCustomization)
    {
        $orderItemId = $menuCustomization->order_item_id;
        
        $menuCustomization->delete();

        // Update order item total after deletion
        $this->updateOrderItemTotal($orderItemId);

        return redirect()->route('admin.menu-customizations.index')
                        ->with('message', 'Menu customization deleted successfully');
    }


    /**
     * Get customizations by order item
     */
    public function getByOrderItem(OrderItem $orderItem)
    {
        $customizations = MenuCustomization::where('order_item_id', $orderItem->id)
                                         ->orderBy('created_at', 'desc')
                                         ->get();

        return view('admin.menu-customizations.by-order-item', compact('customizations', 'orderItem'));
    }

    /**
     * Bulk delete customizations
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'customization_ids' => 'required|array',
            'customization_ids.*' => 'exists:menu_customizations,id',
        ]);

        // Get affected order items before deletion
        $affectedOrderItems = MenuCustomization::whereIn('id', $validated['customization_ids'])
            ->pluck('order_item_id')
            ->unique();

        MenuCustomization::whereIn('id', $validated['customization_ids'])->delete();

        // Update order item totals for all affected items
        foreach ($affectedOrderItems as $orderItemId) {
            $this->updateOrderItemTotal($orderItemId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected customizations deleted successfully'
        ]);
    }

    /**
     * Get customizations data for AJAX requests
     */
    public function getCustomizationsByOrderItem(Request $request)
    {
        $orderItemId = $request->get('order_item_id');
        
        if (!$orderItemId) {
            return response()->json(['error' => 'Order item ID is required'], 400);
        }

        $customizations = MenuCustomization::where('order_item_id', $orderItemId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAdditionalPrice = $customizations->sum('additional_price');

        return response()->json([
            'customizations' => $customizations,
            'total_additional_price' => number_format($totalAdditionalPrice, 2)
        ]);
    }

    /**
     * Update order item total price including customizations
     */
    private function updateOrderItemTotal($orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);
        
        if (!$orderItem) {
            return;
        }

        // Calculate total customization price
        $customizationTotal = MenuCustomization::where('order_item_id', $orderItemId)
            ->sum('additional_price');

        // Update order item total price
        $baseTotal = $orderItem->unit_price * $orderItem->quantity;
        $orderItem->total_price = $baseTotal + $customizationTotal;
        $orderItem->save();

        // Update order total
        $this->updateOrderTotal($orderItem->order_id);
    }

    /**
     * Update order total amount
     */
    private function updateOrderTotal($orderId)
    {
        $order = Order::find($orderId);
        
        if ($order) {
            $totalAmount = OrderItem::where('order_id', $orderId)
                ->sum('total_price');
            
            $order->total_amount = $totalAmount;
            $order->save();
        }
    }
}