<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\MenuItem;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('order-item.index');
        }

        $orderItems = OrderItem::with(['order', 'menuItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('order-item.index', compact('orderItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orderItem = new OrderItem;
        $orders = Order::with('user')->select('id', 'confirmation_code', 'user_id', 'total_amount')->get();
        $menuItems = MenuItem::where('availability', true)->select('id', 'name', 'price')->get();
        
        return view('order-item.form', compact('orderItem', 'orders', 'menuItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:999',
            'unit_price' => 'required|numeric|min:0|max:999999.99',
            'total_price' => 'required|numeric|min:0|max:999999.99',
            'special_note' => 'nullable|string|max:1000',
            'item_status' => 'required|in:pending,preparing,ready,served',
        ], [
            'order_id.required' => 'Order is required.',
            'order_id.exists' => 'Selected order does not exist.',
            'menu_item_id.required' => 'Menu item is required.',
            'menu_item_id.exists' => 'Selected menu item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a valid number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999.',
            'unit_price.required' => 'Unit price is required.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'unit_price.max' => 'Unit price exceeds maximum limit.',
            'total_price.required' => 'Total price is required.',
            'total_price.numeric' => 'Total price must be a valid number.',
            'total_price.min' => 'Total price cannot be negative.',
            'total_price.max' => 'Total price exceeds maximum limit.',
            'item_status.required' => 'Item status is required.',
            'item_status.in' => 'Invalid item status selected.',
            'special_note.max' => 'Special note cannot exceed 1000 characters.',
        ]);

        $orderItem = new OrderItem;
        $orderItem->fill($request->all());
        $orderItem->save();

        // Update order total amount
        $this->updateOrderTotal($orderItem->order_id);

        return redirect()->route('order-item.index')->with('message', 'Order item has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem)
    {
        $orderItem->load(['order.user', 'menuItem']);
        return view('order-item.show', compact('orderItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderItem $orderItem)
    {
        $orders = Order::with('user')->select('id', 'confirmation_code', 'user_id', 'total_amount')->get();
        $menuItems = MenuItem::where('availability', true)->select('id', 'name', 'price')->get();
        
        return view('order-item.form', compact('orderItem', 'orders', 'menuItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:999',
            'unit_price' => 'required|numeric|min:0|max:999999.99',
            'total_price' => 'required|numeric|min:0|max:999999.99',
            'special_note' => 'nullable|string|max:1000',
            'item_status' => 'required|in:pending,preparing,ready,served',
        ], [
            'order_id.required' => 'Order is required.',
            'order_id.exists' => 'Selected order does not exist.',
            'menu_item_id.required' => 'Menu item is required.',
            'menu_item_id.exists' => 'Selected menu item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a valid number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999.',
            'unit_price.required' => 'Unit price is required.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'unit_price.max' => 'Unit price exceeds maximum limit.',
            'total_price.required' => 'Total price is required.',
            'total_price.numeric' => 'Total price must be a valid number.',
            'total_price.min' => 'Total price cannot be negative.',
            'total_price.max' => 'Total price exceeds maximum limit.',
            'item_status.required' => 'Item status is required.',
            'item_status.in' => 'Invalid item status selected.',
            'special_note.max' => 'Special note cannot exceed 1000 characters.',
        ]);

        $oldOrderId = $orderItem->order_id;
        
        $orderItem->fill($request->all());
        $orderItem->save();

        // Update order total amount for both old and new orders if order changed
        if ($oldOrderId != $orderItem->order_id) {
            $this->updateOrderTotal($oldOrderId);
        }
        $this->updateOrderTotal($orderItem->order_id);

        return redirect()->route('order-item.index')->with('message', 'Order item has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderId = $orderItem->order_id;
        
        $orderItem->delete();
        
        // Update order total amount
        $this->updateOrderTotal($orderId);

        return redirect()->route('order-item.index')->with('message', 'Order item has been deleted successfully!');
    }

    /**
     * Update item status
     */
    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        $this->validate($request, [
            'item_status' => 'required|in:pending,preparing,ready,served',
        ]);

        $orderItem->item_status = $request->item_status;
        $orderItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Item status updated successfully!',
            'orderItem' => $orderItem
        ]);
    }

    /**
     * Get order items by order
     */
    public function getByOrder(Request $request)
    {
        $orderId = $request->get('order_id');
        
        $orderItems = OrderItem::with(['menuItem'])
            ->when($orderId, function ($query) use ($orderId) {
                return $query->where('order_id', $orderId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Get order items by status
     */
    public function getByStatus(Request $request)
    {
        $status = $request->get('status');
        
        $orderItems = OrderItem::with(['order', 'menuItem'])
            ->when($status, function ($query) use ($status) {
                return $query->where('item_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Bulk update item status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $this->validate($request, [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:order_items,id',
            'item_status' => 'required|in:pending,preparing,ready,served',
        ]);

        OrderItem::whereIn('id', $request->item_ids)
            ->update(['item_status' => $request->item_status]);

        return response()->json([
            'success' => true,
            'message' => 'Item status updated successfully for ' . count($request->item_ids) . ' items!'
        ]);
    }

    /**
     * Calculate total price based on quantity and unit price
     */
    public function calculateTotal(Request $request)
    {
        $quantity = $request->get('quantity', 0);
        $unitPrice = $request->get('unit_price', 0);
        
        $totalPrice = $quantity * $unitPrice;

        return response()->json([
            'success' => true,
            'total_price' => number_format($totalPrice, 2, '.', '')
        ]);
    }

    /**
     * Get menu item price
     */
    public function getMenuItemPrice(Request $request)
    {
        $menuItemId = $request->get('menu_item_id');
        
        $menuItem = MenuItem::find($menuItemId);
        
        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'price' => $menuItem->price,
            'name' => $menuItem->name
        ]);
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

    /**
     * Duplicate order item
     */
    public function duplicate(OrderItem $orderItem)
    {
        $newOrderItem = $orderItem->replicate();
        $newOrderItem->item_status = 'pending';
        $newOrderItem->created_at = now();
        $newOrderItem->updated_at = now();
        
        $newOrderItem->save();

        // Update order total amount
        $this->updateOrderTotal($newOrderItem->order_id);

        return redirect()->route('order-item.edit', $newOrderItem)->with('message', 'Order item has been duplicated successfully!');
    }

    /**
     * Show kitchen view for preparing items
     */
    public function kitchen()
    {
        $orderItems = OrderItem::with(['order.user', 'menuItem'])
            ->whereIn('item_status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('item_status');

        return view('order-item.kitchen', compact('orderItems'));
    }
}