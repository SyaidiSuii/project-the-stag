<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OrderTrackingController extends Controller
{
    /**
     * Display a listing of order trackings.
     */
    public function index(Request $request)
    {
        $query = OrderTracking::with(['order', 'staff']);

        // Filter by order_id if provided
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by station if provided
        if ($request->has('station_name')) {
            $query->where('station_name', $request->station_name);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        $trackings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Return view for web or JSON for AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $trackings,
            ]);
        }

        return view('admin.order-trackings.index', compact('trackings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::whereNotIn('order_status', ['completed', 'cancelled'])
            ->with('table')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.order-trackings.create', compact('orders'));
    }

    /**
     * Store a newly created order tracking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => [
                'required',
                Rule::in([
                    'received', 'confirmed', 'preparing', 'cooking',
                    'plating', 'ready', 'served', 'completed'
                ])
            ],
            'station_name' => 'nullable|string|max:100',
            'started_at' => 'nullable|date',
            'estimated_time' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'staff_id' => 'nullable|exists:users,id',
        ]);

        // Set started_at to current time if not provided
        if (!isset($validated['started_at'])) {
            $validated['started_at'] = Carbon::now();
        }

        // Set staff_id to current user if not provided
        if (!isset($validated['staff_id'])) {
            $validated['staff_id'] = auth()->id();
        }

        $tracking = OrderTracking::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order tracking created successfully',
                'data' => $tracking->load(['order', 'staff']),
            ], 201);
        }

        return redirect()
            ->route('admin.order-trackings.index')
            ->with('success', 'Order tracking created successfully');
    }

    /**
     * Display the specified order tracking.
     */
    public function show(OrderTracking $orderTracking)
    {
        $orderTracking->load(['order.table', 'order.items.menuItem', 'staff']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $orderTracking,
            ]);
        }

        return view('admin.order-trackings.show', compact('orderTracking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderTracking $orderTracking)
    {
        $orderTracking->load(['order']);
        
        return view('admin.order-trackings.edit', compact('orderTracking'));
    }

    /**
     * Update the specified order tracking.
     */
    public function update(Request $request, OrderTracking $orderTracking)
    {
        $validated = $request->validate([
            'status' => [
                'sometimes',
                Rule::in([
                    'received', 'confirmed', 'preparing', 'cooking',
                    'plating', 'ready', 'served', 'completed'
                ])
            ],
            'station_name' => 'sometimes|nullable|string|max:100',
            'started_at' => 'sometimes|nullable|date',
            'completed_at' => 'sometimes|nullable|date',
            'estimated_time' => 'sometimes|nullable|integer|min:1',
            'actual_time' => 'sometimes|nullable|integer|min:1',
            'notes' => 'sometimes|nullable|string',
            'staff_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $orderTracking->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order tracking updated successfully',
                'data' => $orderTracking->load(['order', 'staff']),
            ]);
        }

        return redirect()
            ->route('admin.order-trackings.index')
            ->with('success', 'Order tracking updated successfully');
    }

    /**
     * Remove the specified order tracking.
     */
    public function destroy(OrderTracking $orderTracking)
    {
        $orderTracking->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order tracking deleted successfully',
            ]);
        }

        return redirect()
            ->route('admin.order-trackings.index')
            ->with('success', 'Order tracking deleted successfully');
    }

    /**
     * Get order tracking history for a specific order.
     */
    public function getOrderHistory($orderId)
    {
        $order = Order::with(['table', 'items.menuItem'])->findOrFail($orderId);
        
        $trackings = OrderTracking::with('staff')
            ->where('order_id', $orderId)
            ->orderBy('started_at', 'asc')
            ->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'tracking_history' => $trackings,
                ],
            ]);
        }

        return view('admin.order-trackings.history', compact('order', 'trackings'));
    }

    /**
     * Update order status and automatically set completion time.
     */
    public function updateStatus(Request $request, OrderTracking $orderTracking)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'received', 'confirmed', 'preparing', 'cooking',
                    'plating', 'ready', 'served', 'completed'
                ])
            ],
            'notes' => 'nullable|string',
        ]);

        // Automatically set completed_at if status is completed, served, or ready
        if (in_array($validated['status'], ['completed', 'served', 'ready'])) {
            $validated['completed_at'] = Carbon::now();
            
            // Calculate actual time if started_at exists
            if ($orderTracking->started_at) {
                $validated['actual_time'] = Carbon::parse($orderTracking->started_at)
                    ->diffInMinutes(Carbon::now());
            }
        }

        $orderTracking->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $orderTracking->load(['order', 'staff']),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Order status updated successfully');
    }

    /**
     * Get active orders by station.
     */
    public function getActiveOrdersByStation(Request $request)
    {
        $validated = $request->validate([
            'station_name' => 'required|string|max:100',
        ]);

        $trackings = OrderTracking::with(['order.table', 'order.items.menuItem', 'staff'])
            ->where('station_name', $validated['station_name'])
            ->whereIn('status', ['preparing', 'cooking', 'plating'])
            ->whereNull('completed_at')
            ->orderBy('started_at', 'asc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $trackings,
            ]);
        }

        return view('admin.order-trackings.station', [
            'trackings' => $trackings,
            'station_name' => $validated['station_name']
        ]);
    }

    /**
     * Get performance statistics.
     */
    public function getPerformanceStats(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'station_name' => 'nullable|string|max:100',
        ]);

        $query = OrderTracking::whereNotNull('actual_time');

        if (isset($validated['date_from'])) {
            $query->whereDate('completed_at', '>=', $validated['date_from']);
        }

        if (isset($validated['date_to'])) {
            $query->whereDate('completed_at', '<=', $validated['date_to']);
        }

        if (isset($validated['station_name'])) {
            $query->where('station_name', $validated['station_name']);
        }

        $stats = $query->selectRaw('
            AVG(actual_time) as avg_time,
            MIN(actual_time) as min_time,
            MAX(actual_time) as max_time,
            COUNT(*) as total_orders,
            station_name
        ')
        ->groupBy('station_name')
        ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        }

        return view('admin.order-trackings.stats', compact('stats'));
    }

    /**
     * Kitchen dashboard view
     */
    public function kitchen()
    {
        $activeTrackings = OrderTracking::with(['order.table', 'order.items.menuItem', 'staff'])
            ->whereIn('status', ['confirmed', 'preparing', 'cooking', 'plating'])
            ->whereNull('completed_at')
            ->orderBy('started_at', 'asc')
            ->get()
            ->groupBy('station_name');

        return view('admin.order-trackings.kitchen', compact('activeTrackings'));
    }
}