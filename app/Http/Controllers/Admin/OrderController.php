<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Table;
use App\Models\TableReservation;
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
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('admin.order.index');
        }

        $orders = Order::with(['user', 'table', 'reservation', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $order = new Order;
        $users = User::select('id', 'name')->get();
        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status')->get();
        $reservations = TableReservation::with('table')->whereDate('reservation_date', '>=', now())->get();
        
        return view('admin.order.form', compact('order', 'users', 'tables', 'reservations'));
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
            'confirmation_code' => 'nullable|string|max:10|unique:orders,confirmation_code',
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
            $order->confirmation_code = $this->generateConfirmationCode();
        }

        // Set order_time to current timestamp
        $order->order_time = now();

        $order->save();

        return redirect()->route('admin.order.index')->with('message', 'Order has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'table', 'reservation', 'items']);
        return view('admin.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $users = User::select('id', 'name')->get();
        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status')->get();
        $reservations = TableReservation::with('table')->whereDate('reservation_date', '>=', now())->get();
        
        return view('admin.order.form', compact('order', 'users', 'tables', 'reservations'));
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
            'confirmation_code' => 'nullable|string|max:10|unique:orders,confirmation_code,' . $order->id,
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

        $order->save();

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
     * Generate unique confirmation code
     */
    private function generateConfirmationCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (Order::where('confirmation_code', $code)->exists());

        return $code;
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
}