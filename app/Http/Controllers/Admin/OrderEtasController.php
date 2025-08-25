<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderEtas;
use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderEtasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('admin.order-etas.index');
        }

        $orderEtas = OrderEtas::with(['order.user', 'order.table'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.order-etas.index', compact('orderEtas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orderEta = new OrderEtas;
        $orders = Order::whereDoesntHave('eta')
            ->with(['user', 'table'])
            ->where('order_status', '!=', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->select('id', 'user_id', 'table_id', 'order_status', 'total_amount')
            ->get();
        
        return view('admin.order-etas.form', compact('orderEta', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id|unique:order_etas,order_id',
            'initial_estimate' => 'required|integer|min:1|max:480', // max 8 jam
            'current_estimate' => 'required|integer|min:1|max:480',
            'delay_reason' => 'nullable|string|max:255',
            'is_delayed' => 'nullable|boolean',
            'delay_duration' => 'nullable|integer|min:0|max:240', // max 4 jam delay
            'customer_notified' => 'nullable|boolean',
        ], [
            'order_id.required' => 'Order is required.',
            'order_id.exists' => 'Selected order does not exist.',
            'order_id.unique' => 'ETA for this order already exists.',
            'initial_estimate.required' => 'Initial estimate is required.',
            'initial_estimate.integer' => 'Initial estimate must be a number.',
            'initial_estimate.min' => 'Initial estimate must be at least 1 minute.',
            'initial_estimate.max' => 'Initial estimate cannot exceed 8 hours (480 minutes).',
            'current_estimate.required' => 'Current estimate is required.',
            'current_estimate.integer' => 'Current estimate must be a number.',
            'current_estimate.min' => 'Current estimate must be at least 1 minute.',
            'current_estimate.max' => 'Current estimate cannot exceed 8 hours (480 minutes).',
            'delay_duration.max' => 'Delay duration cannot exceed 4 hours (240 minutes).',
        ]);

        $orderEta = new OrderEtas;
        $orderEta->fill($request->all());

        // Handle boolean fields
        $orderEta->is_delayed = $request->has('is_delayed');
        $orderEta->customer_notified = $request->has('customer_notified');

        // Auto-calculate delay if current estimate is higher than initial
        if ($orderEta->current_estimate > $orderEta->initial_estimate) {
            $orderEta->is_delayed = true;
            $orderEta->delay_duration = $orderEta->current_estimate - $orderEta->initial_estimate;
        }

        // Set last_updated timestamp
        $orderEta->last_updated = now();

        $orderEta->save();

        return redirect()->route('admin.order-etas.index')->with('message', 'Order ETA has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderEtas $orderEta)
    {
        $orderEta->load(['order.user', 'order.table', 'order.items']);
        return view('admin.order-etas.show', compact('orderEta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderEtas $orderEta)
    {
        $orders = Order::with(['user', 'table'])
            ->where('order_status', '!=', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->select('id', 'user_id', 'table_id', 'order_status', 'total_amount')
            ->get();
        
        return view('admin.order-etas.form', compact('orderEta', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderEtas $orderEta)
    {
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id|unique:order_etas,order_id,' . $orderEta->id,
            'initial_estimate' => 'required|integer|min:1|max:480',
            'current_estimate' => 'required|integer|min:1|max:480',
            'actual_completion_time' => 'nullable|integer|min:1|max:600', // max 10 jam untuk actual
            'delay_reason' => 'nullable|string|max:255',
            'is_delayed' => 'nullable|boolean',
            'delay_duration' => 'nullable|integer|min:0|max:240',
            'customer_notified' => 'nullable|boolean',
        ], [
            'order_id.required' => 'Order is required.',
            'order_id.exists' => 'Selected order does not exist.',
            'order_id.unique' => 'ETA for this order already exists.',
            'initial_estimate.required' => 'Initial estimate is required.',
            'initial_estimate.integer' => 'Initial estimate must be a number.',
            'initial_estimate.min' => 'Initial estimate must be at least 1 minute.',
            'initial_estimate.max' => 'Initial estimate cannot exceed 8 hours (480 minutes).',
            'current_estimate.required' => 'Current estimate is required.',
            'current_estimate.integer' => 'Current estimate must be a number.',
            'current_estimate.min' => 'Current estimate must be at least 1 minute.',
            'current_estimate.max' => 'Current estimate cannot exceed 8 hours (480 minutes).',
            'actual_completion_time.max' => 'Actual completion time cannot exceed 10 hours (600 minutes).',
            'delay_duration.max' => 'Delay duration cannot exceed 4 hours (240 minutes).',
        ]);

        $orderEta->fill($request->all());

        // Handle boolean fields
        $orderEta->is_delayed = $request->has('is_delayed');
        $orderEta->customer_notified = $request->has('customer_notified');

        // Auto-calculate delay if current estimate is higher than initial
        if ($orderEta->current_estimate > $orderEta->initial_estimate) {
            $orderEta->is_delayed = true;
            $orderEta->delay_duration = $orderEta->current_estimate - $orderEta->initial_estimate;
        } else {
            // Reset delay if current estimate is back to normal
            if (!$request->has('is_delayed')) {
                $orderEta->is_delayed = false;
                $orderEta->delay_duration = 0;
                $orderEta->delay_reason = null;
            }
        }

        // Set last_updated timestamp
        $orderEta->last_updated = now();

        $orderEta->save();

        return redirect()->route('admin.order-etas.index')->with('message', 'Order ETA has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderEtas $orderEta)
    {
        $orderEta->delete();
        return redirect()->route('admin.order-etas.index')->with('message', 'Order ETA has been deleted successfully!');
    }

    /**
     * Update current estimate
     */
    public function updateEstimate(Request $request, OrderEtas $orderEta)
    {
        $this->validate($request, [
            'current_estimate' => 'required|integer|min:1|max:480',
            'delay_reason' => 'nullable|string|max:255',
        ]);

        $oldEstimate = $orderEta->current_estimate;
        $orderEta->current_estimate = $request->current_estimate;

        // Auto-calculate delay
        if ($orderEta->current_estimate > $orderEta->initial_estimate) {
            $orderEta->is_delayed = true;
            $orderEta->delay_duration = $orderEta->current_estimate - $orderEta->initial_estimate;
            $orderEta->delay_reason = $request->delay_reason;
        } else {
            $orderEta->is_delayed = false;
            $orderEta->delay_duration = 0;
            $orderEta->delay_reason = null;
        }

        $orderEta->last_updated = now();
        $orderEta->save();

        return response()->json([
            'success' => true,
            'message' => 'Estimate updated successfully!',
            'orderEta' => $orderEta,
            'changed' => $oldEstimate !== $orderEta->current_estimate
        ]);
    }

    /**
     * Mark order as completed and set actual completion time
     */
    public function markCompleted(Request $request, OrderEtas $orderEta)
    {
        $this->validate($request, [
            'actual_completion_time' => 'nullable|integer|min:1|max:600',
        ]);

        // If actual completion time is not provided, calculate from order creation time
        if (!$request->actual_completion_time) {
            $orderCreatedAt = $orderEta->order->created_at;
            $actualMinutes = now()->diffInMinutes($orderCreatedAt);
            $orderEta->actual_completion_time = $actualMinutes;
        } else {
            $orderEta->actual_completion_time = $request->actual_completion_time;
        }

        $orderEta->last_updated = now();
        $orderEta->save();

        // Also update the order status to completed
        $orderEta->order->update([
            'order_status' => 'completed',
            'actual_completion_time' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order marked as completed!',
            'orderEta' => $orderEta
        ]);
    }

    /**
     * Notify customer about delay
     */
    public function notifyCustomer(OrderEtas $orderEta)
    {
        if (!$orderEta->is_delayed) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not delayed.'
            ]);
        }

        // Here you would implement the actual notification logic
        // For example, send email, SMS, or push notification

        $orderEta->customer_notified = true;
        $orderEta->last_updated = now();
        $orderEta->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer has been notified about the delay!',
            'orderEta' => $orderEta
        ]);
    }

    /**
     * Get delayed orders
     */
    public function getDelayedOrders()
    {
        $delayedOrders = OrderEtas::with(['order.user', 'order.table'])
            ->where('is_delayed', true)
            ->orderBy('delay_duration', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'delayed_orders' => $delayedOrders
        ]);
    }

    /**
     * Get ETA statistics
     */
    public function getStatistics()
    {
        $totalOrders = OrderEtas::count();
        $delayedOrders = OrderEtas::where('is_delayed', true)->count();
        $completedOrders = OrderEtas::whereNotNull('actual_completion_time')->count();
        $averageEstimate = OrderEtas::avg('initial_estimate');
        $averageActual = OrderEtas::whereNotNull('actual_completion_time')->avg('actual_completion_time');
        $averageDelay = OrderEtas::where('is_delayed', true)->avg('delay_duration');

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_orders' => $totalOrders,
                'delayed_orders' => $delayedOrders,
                'completed_orders' => $completedOrders,
                'delay_percentage' => $totalOrders > 0 ? round(($delayedOrders / $totalOrders) * 100, 2) : 0,
                'average_estimate' => round($averageEstimate ?? 0, 2),
                'average_actual' => round($averageActual ?? 0, 2),
                'average_delay' => round($averageDelay ?? 0, 2),
            ]
        ]);
    }

    /**
     * Get orders needing attention (delayed and not notified)
     */
    public function getNeedingAttention()
    {
        $ordersNeedingAttention = OrderEtas::with(['order.user', 'order.table'])
            ->where('is_delayed', true)
            ->where('customer_notified', false)
            ->orderBy('delay_duration', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $ordersNeedingAttention,
            'count' => $ordersNeedingAttention->count()
        ]);
    }
}