<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderEtas;
use App\Models\OrderTracking;
use App\Models\User;
use App\Models\Table;
use App\Models\TableReservation;
use App\Models\MenuItem;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Show today's orders
     */
    public function today()
    {
        $orders = Order::with(['user', 'table', 'reservation', 'items'])
            ->whereDate('order_time', today())
            ->orderBy('order_time', 'desc')
            ->get()
            ->groupBy('order_status');

        return view('admin.order.today', compact('orders'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request('cancel')) {
            return redirect()->route('admin.order.index');
        }

        $query = Order::with(['user', 'table', 'reservation', 'items', 'etas', 'trackings']);

        // Filter by order status
        if ($request->has('order_status') && $request->order_status != '') {
            $query->where('order_status', $request->order_status);
        }

        // Filter by order type
        if ($request->has('order_type') && $request->order_type != '') {
            $query->where('order_type', $request->order_type);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('order_time', $request->date);
        }

        // Search by order ID, customer name, confirmation code
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('confirmation_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Default sort by creation time
        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate(10)->appends($request->query());
        
        // Get statistics for dashboard cards
        $totalOrders = Order::count();
        $todayRevenue = Order::whereDate('order_time', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $completedOrders = Order::where('order_status', 'completed')->count();
        
        return view('admin.order.index', compact(
            'orders', 
            'totalOrders', 
            'todayRevenue', 
            'pendingOrders', 
            'completedOrders'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $order = new Order;
        $users = User::select('id', 'name')->get();
        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status')->get();
        $reservations = TableReservation::with('table')->whereDate('booking_date', '>=', now())->get();
        $menuItems = MenuItem::where('availability', true)->select('id', 'name', 'price', 'preparation_time')->get();
        
        return view('admin.order.form', compact('order', 'users', 'tables', 'reservations', 'menuItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_id' => 'nullable|exists:table_reservations,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,event',
            'order_source' => 'required|in:counter,web,mobile,waiter,qr_scan',
            'order_status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
            'table_number' => 'nullable|string|max:10',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'special_instructions' => 'nullable|array',
            'estimated_completion_time' => 'nullable|date|after:now',
            'actual_completion_time' => 'nullable|date',
            'is_rush_order' => 'nullable|boolean',
            'confirmation_code' => 'nullable|string|max:20|unique:orders,confirmation_code',
        ], [
            'user_id.required' => 'Customer is required.',
            'user_id.exists' => 'Selected customer does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_id.exists' => 'Selected reservation does not exist.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type selected.',
            'order_source.required' => 'Order source is required.',
            'order_source.in' => 'Invalid order source selected.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status selected.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'estimated_completion_time.after' => 'Estimated completion time must be in the future.',
            'confirmation_code.unique' => 'Confirmation code already exists.',
        ]);

        $order = new Order;
        $order->fill($request->all());

        // Handle boolean fields
        $order->is_rush_order = $request->has('is_rush_order');

        // Generate confirmation code if not provided
        if (empty($request->confirmation_code)) {
            $order->confirmation_code = Order::generateConfirmationCode();
        }

        // Set order_time to current timestamp
        $order->order_time = now();

        $order->save();

        // Handle order items creation
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['menu_item_id']) && !empty($itemData['price'])) {
                    $quantity = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['price'];
                    $totalPrice = $unitPrice * $quantity;
                    
                    $order->items()->create([
                        'menu_item_id' => $itemData['menu_item_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'special_note' => $itemData['notes'] ?? null,
                    ]);
                }
            }
        }

        // Auto-create ETA based on order items
        $order->load('items.menuItem'); // Load items with menu item data
        if ($order->items->count() > 0) {
            $order->autoCreateETA();
        }

        return redirect()->route('admin.order.index')->with('message', 'Order has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'table', 'reservation', 'items', 'etas', 'trackings.updatedBy']);
        return view('admin.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $users = User::select('id', 'name')->get();
        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status')->get();
        $reservations = TableReservation::with('table')->whereDate('booking_date', '>=', now())->get();
        $menuItems = MenuItem::where('availability', true)->select('id', 'name', 'price', 'preparation_time')->get();
        
        return view('admin.order.form', compact('order', 'users', 'tables', 'reservations', 'menuItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_id' => 'nullable|exists:table_reservations,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,event',
            'order_source' => 'required|in:counter,web,mobile,waiter,qr_scan',
            'order_status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
            'table_number' => 'nullable|string|max:10',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'special_instructions' => 'nullable|array',
            'estimated_completion_time' => 'nullable|date',
            'actual_completion_time' => 'nullable|date',
            'is_rush_order' => 'nullable|boolean',
            'confirmation_code' => 'nullable|string|max:20|unique:orders,confirmation_code,' . $order->id,
        ], [
            'user_id.required' => 'Customer is required.',
            'user_id.exists' => 'Selected customer does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_id.exists' => 'Selected reservation does not exist.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type selected.',
            'order_source.required' => 'Order source is required.',
            'order_source.in' => 'Invalid order source selected.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status selected.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'confirmation_code.unique' => 'Confirmation code already exists.',
        ]);

        $order->fill($request->all());

        // Handle boolean fields
        $order->is_rush_order = $request->has('is_rush_order');

        // Handle JSON fields
        if ($request->has('special_instructions')) {
            $order->special_instructions = $request->special_instructions;
        }

        // Set actual completion time if status is completed
        if ($request->order_status === 'completed' && !$order->actual_completion_time) {
            $order->actual_completion_time = now();
        }

        // Generate confirmation code if payment status is paid and no code exists
        if ($request->payment_status === 'paid' && empty($order->confirmation_code)) {
            $order->confirmation_code = Order::generateConfirmationCode();
        }

        $order->save();

        // Handle order items updates
        if ($request->has('items') && is_array($request->items)) {
            // Delete existing items
            $order->items()->delete();
            
            // Create new items
            foreach ($request->items as $itemData) {
                if (!empty($itemData['menu_item_id']) && !empty($itemData['price'])) {
                    $quantity = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['price'];
                    $totalPrice = $unitPrice * $quantity;
                    
                    $order->items()->create([
                        'menu_item_id' => $itemData['menu_item_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'special_note' => $itemData['notes'] ?? null,
                    ]);
                }
            }
            
            // Recalculate ETA if items changed
            $order->load('items.menuItem');
            if ($order->items->count() > 0) {
                $order->updateAutoETA();
            }
        }


        return redirect()->route('admin.order.index')->with('message', 'Order has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.order.index')->with('message', 'Order has been deleted successfully!');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->validate($request, [
            'order_status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
        ]);

        $order->order_status = $request->order_status;

        // Set actual completion time if status is completed
        if ($request->order_status === 'completed' && !$order->actual_completion_time) {
            $order->actual_completion_time = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
            'order' => $order
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $this->validate($request, [
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
        ]);

        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully!',
            'order' => $order
        ]);
    }

    /**
     * Get orders by status
     */
    public function getByStatus(Request $request)
    {
        $status = $request->get('status');
        
        $orders = Order::with(['user', 'table', 'items'])
            ->when($status, function ($query) use ($status) {
                return $query->where('order_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Cannot cancel this order.');
        }

        $order->order_status = 'cancelled';
        $order->save();

        return redirect()->back()->with('message', 'Order has been cancelled successfully!');
    }

    /**
     * Duplicate order
     */
    public function duplicate(Order $order)
    {
        $newOrder = $order->replicate();
        $newOrder->order_status = 'pending';
        $newOrder->payment_status = 'unpaid';
        $newOrder->confirmation_code = $this->generateConfirmationCode();
        $newOrder->order_time = now();
        $newOrder->actual_completion_time = null;
        $newOrder->created_at = now();
        $newOrder->updated_at = now();
        
        $newOrder->save();

        // Copy order items if they exist
        foreach ($order->items as $item) {
            $newItem = $item->replicate();
            $newItem->order_id = $newOrder->id;
            $newItem->save();
        }

        return redirect()->route('admin.order.edit', $newOrder)->with('message', 'Order has been duplicated successfully!');
    }

    /**
     * Calculate ETA for given order items (used for AJAX calls)
     */
    public function calculateETA(Request $request)
    {
        $this->validate($request, [
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrepTime = 0;
        
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem) {
                $prepTime = $menuItem->preparation_time ?? 15;
                $totalPrepTime += ($prepTime * $item['quantity']);
            }
        }
        
        // Add buffer time (10% or minimum 5 minutes)
        $bufferTime = max(5, round($totalPrepTime * 0.1));
        $totalTime = $totalPrepTime + $bufferTime;
        
        $estimatedTime = now()->addMinutes($totalTime);
        
        return response()->json([
            'success' => true,
            'total_prep_time' => $totalTime,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s'),
            'estimated_time_formatted' => $estimatedTime->format('M d, Y h:i A'),
            'human_readable' => "Estimated {$totalTime} minutes from now"
        ]);
    }

    /**
     * Get menu item preparation time (used for AJAX calls)
     */
    public function getMenuItemPrepTime(Request $request)
    {
        $this->validate($request, [
            'menu_item_id' => 'required|exists:menu_items,id'
        ]);

        $menuItem = MenuItem::find($request->menu_item_id);
        
        return response()->json([
            'success' => true,
            'preparation_time' => $menuItem->preparation_time ?? 15,
            'name' => $menuItem->name,
            'price' => $menuItem->price
        ]);
    }
}