<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KitchenStation;
use App\Models\Order;
use App\Models\LoadBalancingLog;
use App\Services\Kitchen\KitchenLoadService;
use App\Services\Kitchen\KitchenAnalyticsService;
use App\Services\Kitchen\OrderDistributionService;

class KitchenLoadController extends Controller
{
    protected $kitchenLoadService;
    protected $analyticsService;
    protected $distributionService;

    public function __construct(
        KitchenLoadService $kitchenLoadService,
        KitchenAnalyticsService $analyticsService,
        OrderDistributionService $distributionService
    ) {
        $this->kitchenLoadService = $kitchenLoadService;
        $this->analyticsService = $analyticsService;
        $this->distributionService = $distributionService;
    }

    /**
     * Display the kitchen dashboard (main view) - TODAY ONLY
     */
    public function index()
    {
        $stations = KitchenStation::with(['activeLoads.order', 'pendingAssignments.order'])
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(function ($station) {
                // Add today's load as attribute for display
                $station->today_load_display = $station->today_load;
                $station->load_percentage_display = $station->load_percentage;
                return $station;
            });

        $todayStats = $this->kitchenLoadService->getTodayStats();
        $bottlenecks = $this->kitchenLoadService->detectBottlenecks();

        // Get recent alerts (last 5)
        $recentAlerts = LoadBalancingLog::with('station', 'order')
            ->where('action_type', 'overload_alert')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.kitchen.dashboard', compact(
            'stations',
            'todayStats',
            'bottlenecks',
            'recentAlerts'
        ));
    }

    /**
     * Display stations management page
     */
    public function stations()
    {
        $stations = KitchenStation::withCount(['activeLoads', 'pendingAssignments'])
            ->ordered()
            ->get();

        return view('admin.kitchen.stations.index', compact('stations'));
    }

    /**
     * Display active orders by station (TODAY ONLY)
     */
    public function orders(Request $request)
    {
        $stationId = $request->get('station_id');
        $status = $request->get('status');

        // Get ALL orders for stat cards (TODAY ONLY)
        $allOrders = Order::with(['items.menuItem', 'stationAssignments.station', 'kitchenLoads'])
            ->whereHas('stationAssignments')
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->whereDate('order_time', today())
            ->orderBy('order_time', 'asc')
            ->get();

        // Get filtered orders for display (TODAY ONLY)
        $query = Order::with(['items.menuItem', 'stationAssignments.station', 'kitchenLoads'])
            ->whereHas('stationAssignments')
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->whereDate('order_time', today());

        if ($stationId) {
            $query->whereHas('stationAssignments', function ($q) use ($stationId) {
                $q->where('station_id', $stationId);
            });
        }

        $orders = $query->orderBy('order_time', 'asc')->get();

        // Total count is just the count of all orders
        $totalOrdersCount = $allOrders->count();

        // Get stations with counts filtered by order status and TODAY ONLY
        $stations = KitchenStation::where('is_active', true)
            ->ordered()
            ->withCount([
                'activeLoads' => function ($q) {
                    $q->whereHas('order', function ($orderQuery) {
                        $orderQuery->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
                            ->whereDate('order_time', today());
                    });
                },
                'pendingAssignments' => function ($q) {
                    $q->whereHas('order', function ($orderQuery) {
                        $orderQuery->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
                            ->whereDate('order_time', today());
                    });
                }
            ])
            ->get();

        return view('admin.kitchen.orders', compact('orders', 'stations', 'stationId', 'totalOrdersCount', 'allOrders'));
    }

    /**
     * Display detailed view of a specific kitchen order
     */
    public function orderDetail(Request $request, $id)
    {
        $stationId = $request->get('station_id');

        $order = Order::with([
            'items.menuItem.category',
            'stationAssignments.station',
            'kitchenLoads.station',
            'user',
            'table',
            'tableQrcode',
            'etas'
        ])->findOrFail($id);

        // Get all active stations for navigation
        $stations = KitchenStation::where('is_active', true)
            ->ordered()
            ->withCount(['activeLoads', 'pendingAssignments'])
            ->get();

        // If filtering by station, get that station's details
        $currentStation = null;
        if ($stationId) {
            $currentStation = KitchenStation::find($stationId);
        }

        return view('admin.kitchen.order-detail', compact('order', 'stations', 'stationId', 'currentStation'));
    }

    /**
     * Display analytics page
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 7);

        $analytics = $this->analyticsService->getPerformanceAnalytics(
            today()->subDays($days - 1),
            today()
        );

        $chartData = $this->analyticsService->getChartData($days);

        // Generate dynamic recommendations based on analytics data
        $recommendations = $this->analyticsService->generateRecommendations($analytics);

        return view('admin.kitchen.analytics', compact('analytics', 'chartData', 'days', 'recommendations'));
    }

    /**
     * Show station details
     */
    public function stationDetail(Request $request, $id)
    {
        $station = KitchenStation::with([
            'activeLoads.order.items.menuItem',
            'pendingAssignments.order',
            'logs' => function ($q) {
                $q->latest()->limit(20);
            }
        ])->findOrFail($id);

        // Return JSON for AJAX requests (edit modal)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($station);
        }

        // Return view for direct page access
        return view('admin.kitchen.station-detail', compact('station'));
    }

    /**
     * Update station settings
     */
    public function updateStation(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'operating_hours' => 'nullable|array',
        ]);

        $station = KitchenStation::findOrFail($id);
        $station->update($validated);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Station updated successfully',
                'station' => $station
            ]);
        }

        return redirect()->route('admin.kitchen.stations.index')->with('success', 'Station updated successfully');
    }

    /**
     * Manually redistribute an order
     */
    public function redistribute(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'from_station_id' => 'required|exists:kitchen_stations,id',
            'to_station_id' => 'required|exists:kitchen_stations,id',
            'reason' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $fromStation = KitchenStation::findOrFail($validated['from_station_id']);
        $toStation = KitchenStation::findOrFail($validated['to_station_id']);

        $this->distributionService->redistributeOrder(
            $order,
            $fromStation,
            $toStation,
            $validated['reason'] ?? 'Manual redistribution by admin'
        );

        return response()->json([
            'success' => true,
            'message' => "Order redistributed from {$fromStation->name} to {$toStation->name}"
        ]);
    }

    /**
     * Mark order as completed for a station
     */
    public function completeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'station_id' => 'required|exists:kitchen_stations,id',
        ]);

        $this->kitchenLoadService->releaseLoad(
            $validated['station_id'],
            $validated['order_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Order marked as completed'
        ]);
    }

    /**
     * Get real-time stations status (AJAX)
     */
    public function getStationsStatus()
    {
        $status = $this->kitchenLoadService->getStationsStatus();

        return response()->json([
            'success' => true,
            'stations' => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Create a new station
     */
    public function storeStation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'station_type' => 'required|in:general_kitchen,drinks,desserts',
            'max_capacity' => 'required|integer|min:1',
            'operating_hours' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $station = KitchenStation::create($validated);

        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', 'Station created successfully');
    }

    /**
     * Delete a station
     */
    public function destroyStation($id)
    {
        $station = KitchenStation::findOrFail($id);

        // Check if station has active orders
        if ($station->activeLoads()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete station with active orders. Please redistribute or complete them first.');
        }

        $station->delete();

        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', 'Station deleted successfully');
    }

    /**
     * Show the form for creating a new station
     */
    public function createStation()
    {
        $station = null; // No station for create mode
        return view('admin.kitchen.stations.form', compact('station'));
    }

    /**
     * Show the form for editing a station
     */
    public function editStation($id)
    {
        $station = KitchenStation::findOrFail($id);
        return view('admin.kitchen.stations.form', compact('station'));
    }

    /**
     * Toggle station active status
     */
    public function toggleStationStatus($id)
    {
        $station = KitchenStation::findOrFail($id);
        $station->is_active = !$station->is_active;
        $station->save();

        $status = $station->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', "Station {$status} successfully");
    }

    /**
     * Handle "Call Manager" request from kitchen staff
     */
    public function callManager(Request $request)
    {
        $this->validate($request, [
            'station_id' => 'nullable|exists:kitchen_stations,id',
            'station_name' => 'required|string',
            'chef_name' => 'required|string',
        ]);

        $stationId = $request->station_id;
        $stationName = $request->station_name;
        $chefName = $request->chef_name;

        // Log the call for help
        \Log::warning('Kitchen staff called for manager assistance', [
            'station_id' => $stationId,
            'station_name' => $stationName,
            'chef_name' => $chefName,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ]);

        // Create alert log
        if ($stationId) {
            LoadBalancingLog::create([
                'station_id' => $stationId,
                'action_type' => 'help_requested',
                'description' => "Chef {$chefName} at {$stationName} requested manager assistance",
                'metadata' => json_encode([
                    'chef_name' => $chefName,
                    'station_name' => $stationName,
                    'timestamp' => now()->toDateTimeString(),
                ]),
            ]);
        }

        // TODO: Send real-time notification to manager
        // This can be implemented with:
        // 1. Broadcasting/Pusher for real-time alerts
        // 2. SMS notification
        // 3. Email notification
        // 4. Push notification to manager's mobile app

        // For now, we'll use session flash for when manager visits dashboard
        session()->flash('manager_alert', [
            'type' => 'help_request',
            'station' => $stationName,
            'chef' => $chefName,
            'time' => now()->format('h:i A'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manager has been notified. Help is on the way!',
            'data' => [
                'station' => $stationName,
                'chef' => $chefName,
                'time' => now()->format('h:i A'),
            ],
        ]);
    }
}
