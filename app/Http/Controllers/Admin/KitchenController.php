<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KitchenStation;
use App\Models\Order;
use App\Models\LoadBalancingLog;
use App\Models\StationAssignment;
use Carbon\Carbon;

class KitchenController extends Controller
{
    /**
     * Kitchen Dashboard - Overview of all kitchen operations
     */
    public function index()
    {
        // Get all kitchen stations (not mapped, return full models for blade methods)
        $stations = KitchenStation::where('is_active', true)
            ->with('stationType')
            ->get();

        // Get bottlenecks (stations at >85% capacity)
        $bottlenecks = $stations->filter(function ($station) {
            return $station->isOverloaded();
        })->map(function ($station) {
            $loadPercentage = $station->max_capacity > 0
                ? round(($station->current_load / $station->max_capacity) * 100, 1)
                : 0;

            return [
                'station' => $station,
                'load_percentage' => $loadPercentage,
                'current_load' => $station->current_load,
                'max_capacity' => $station->max_capacity,
                'suggested_action' => $loadPercentage >= 95
                    ? 'Critical: Redistribute orders immediately'
                    : 'Consider redistributing some orders to other stations'
            ];
        });

        // Get recent alerts from today
        $recentAlerts = LoadBalancingLog::whereDate('created_at', Carbon::today())
            ->where('reason', 'like', '%overload%')
            ->orWhere('reason', 'like', '%capacity%')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate today's stats
        $activeOrders = Order::whereIn('order_status', ['pending', 'preparing'])->count();
        $completedToday = Order::whereDate('order_time', Carbon::today())
            ->where('order_status', 'completed')
            ->count();

        // Calculate average completion time (in minutes)
        $avgCompletionMinutes = Order::whereDate('order_time', Carbon::today())
            ->where('order_status', 'completed')
            ->whereNotNull('actual_completion_time')
            ->get()
            ->map(function ($order) {
                $start = Carbon::parse($order->order_time);
                $end = Carbon::parse($order->actual_completion_time);
                return $start->diffInMinutes($end);
            })
            ->avg();

        // Count overload alerts today
        $overloadAlerts = LoadBalancingLog::whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->where('reason', 'like', '%overload%')
                    ->orWhere('reason', 'like', '%capacity%');
            })
            ->count();

        // Station performance today
        $stationsPerformance = $stations->map(function ($station) {
            $completedOrders = LoadBalancingLog::where('station_id', $station->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            $avgTime = $station->getAverageCompletionTime();

            // Calculate efficiency (inverse of load percentage - lower is better)
            $efficiency = $station->max_capacity > 0
                ? max(0, 100 - round(($station->current_load / $station->max_capacity) * 100))
                : 100;

            return [
                'station_name' => $station->name,
                'orders_completed' => $completedOrders,
                'avg_time' => $avgTime,
                'efficiency' => $efficiency
            ];
        });

        $todayStats = [
            'active_orders' => $activeOrders,
            'total_orders_completed' => $completedToday,
            'avg_completion_time' => round($avgCompletionMinutes ?? 0, 1),
            'overload_alerts' => $overloadAlerts,
            'stations_performance' => $stationsPerformance
        ];

        return view('admin.kitchen.dashboard', compact(
            'stations',
            'todayStats',
            'bottlenecks',
            'recentAlerts'
        ));
    }

    /**
     * Kitchen Display System - Full screen view for kitchen staff
     */
    public function kds()
    {
        // Check if user is kitchen staff
        $isKitchenStaff = auth()->user()->hasRole('kitchen_staff');

        // Get station filter (if any)
        $stationId = request('station_id');

        // If kitchen staff, get their assigned station
        if ($isKitchenStaff && !$stationId) {
            $stationId = auth()->user()->assigned_station;
        }

        // Get current station
        $currentStation = $stationId ? KitchenStation::find($stationId) : null;

        // Get all stations for filter
        $stations = KitchenStation::where('is_active', true)
            ->with('stationType')
            ->get()
            ->map(function ($station) {
                // Count active orders assigned to this station
                $station->active_loads_count = StationAssignment::where('station_id', $station->id)
                    ->whereHas('order', function ($q) {
                        $q->whereIn('order_status', ['pending', 'preparing', 'ready']);
                    })
                    ->distinct('order_id')
                    ->count('order_id');
                return $station;
            });

        // Get orders grouped by status
        $ordersQuery = Order::with([
            'items.menuItem',
            'table',
            'user',
            'stationAssignments.station.stationType',
            'stationAssignments.orderItem.menuItem'
        ])
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready', 'completed'])
            ->orderBy('order_time', 'asc');

        // Filter by station if specified
        if ($stationId) {
            $ordersQuery->whereHas('stationAssignments', function ($query) use ($stationId) {
                $query->where('station_id', $stationId);
            });
        }

        $allOrders = $ordersQuery->get();

        // Group orders by status
        $orders = collect([
            'pending' => $allOrders->where('order_status', 'pending'),
            'confirmed' => $allOrders->where('order_status', 'confirmed'),
            'preparing' => $allOrders->where('order_status', 'preparing'),
            'ready' => $allOrders->where('order_status', 'ready'),
            'completed' => $allOrders->where('order_status', 'completed')
                ->filter(function ($order) {
                    // Only show completed orders from last 30 minutes
                    return $order->actual_completion_time &&
                           Carbon::parse($order->actual_completion_time)->diffInMinutes(Carbon::now()) <= 30;
                })
        ]);

        // Calculate stats
        $todayStats = [
            'pending' => Order::whereIn('order_status', ['pending', 'confirmed'])
                ->whereDate('order_time', Carbon::today())
                ->count(),
            'preparing' => Order::where('order_status', 'preparing')
                ->whereDate('order_time', Carbon::today())
                ->count(),
            'ready' => Order::where('order_status', 'ready')
                ->whereDate('order_time', Carbon::today())
                ->count(),
            'completed_today' => Order::where('order_status', 'completed')
                ->whereDate('order_time', Carbon::today())
                ->count(),
        ];

        return view('admin.kitchen.kds', compact(
            'orders',
            'todayStats',
            'stations',
            'stationId',
            'currentStation',
            'isKitchenStaff'
        ));
    }

    /**
     * Active Orders View
     */
    public function orders()
    {
        // Get station filter (if any)
        $stationId = request('station_id');

        // Get all active orders (for stats - unfiltered)
        $allOrders = Order::whereIn('order_status', ['pending', 'preparing', 'ready'])
            ->with(['items.menuItem', 'table', 'user', 'stationAssignments.station'])
            ->orderBy('order_time', 'asc')
            ->get();

        // Get orders for display (filtered by station if specified)
        // Sort by oldest first so kitchen processes orders in FIFO order
        $ordersQuery = Order::whereIn('order_status', ['pending', 'preparing', 'ready'])
            ->with(['items.menuItem', 'table', 'user', 'stationAssignments.station.stationType'])
            ->orderBy('order_time', 'asc');

        // Filter by station if specified
        if ($stationId) {
            $ordersQuery->whereHas('stationAssignments', function ($query) use ($stationId) {
                $query->where('station_id', $stationId);
            });
        }

        $orders = $ordersQuery->paginate(20);

        // Get all stations for filter
        $stations = KitchenStation::where('is_active', true)
            ->with('stationType')
            ->get()
            ->map(function ($station) {
                // Count active orders assigned to this station
                $station->active_loads_count = StationAssignment::where('station_id', $station->id)
                    ->whereHas('order', function ($q) {
                        $q->whereIn('order_status', ['pending', 'preparing', 'ready']);
                    })
                    ->distinct('order_id')
                    ->count('order_id');
                return $station;
            });

        $totalOrdersCount = $allOrders->count();

        return view('admin.kitchen.orders', compact('orders', 'allOrders', 'stations', 'totalOrdersCount', 'stationId'));
    }

    /**
     * Order Detail View
     */
    public function orderDetail($orderId)
    {
        $order = Order::with([
            'items.menuItem.category',
            'table',
            'user',
            'stationAssignments.station.stationType',
            'stationAssignments.orderItem.menuItem'
        ])->findOrFail($orderId);

        $stationId = request('station_id');

        return view('admin.kitchen.order-detail', compact('order', 'stationId'));
    }

    /**
     * Kitchen Analytics
     */
    public function analytics()
    {
        // Get date range from request or default to last 7 days
        $startDate = request('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->format('Y-m-d'));

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Last 7 days performance
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push([
                'date' => $date->format('M d'),
                'orders' => Order::whereDate('order_time', $date)->count(),
                'completed' => Order::whereDate('order_time', $date)
                    ->where('order_status', 'completed')
                    ->count(),
            ]);
        }

        // Summary analytics for the date range
        $totalOrders = Order::whereBetween('order_time', [$startDate, $endDate])->count();

        $completedOrders = Order::whereBetween('order_time', [$startDate, $endDate])
            ->where('order_status', 'completed')
            ->whereNotNull('actual_completion_time')
            ->get();

        $avgCompletionTime = $completedOrders->map(function ($order) {
            $start = Carbon::parse($order->order_time);
            $end = Carbon::parse($order->actual_completion_time);
            return $start->diffInMinutes($end);
        })->avg();

        // Calculate on-time percentage (assume 30 min SLA)
        $onTimeOrders = $completedOrders->filter(function ($order) {
            $start = Carbon::parse($order->order_time);
            $end = Carbon::parse($order->actual_completion_time);
            return $start->diffInMinutes($end) <= 30;
        })->count();

        $onTimePercentage = $completedOrders->count() > 0
            ? round(($onTimeOrders / $completedOrders->count()) * 100, 1)
            : 100;

        $overloadAlerts = LoadBalancingLog::whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('reason', 'like', '%overload%')
                    ->orWhere('reason', 'like', '%capacity%');
            })
            ->count();

        $analytics = [
            'summary' => [
                'total_orders' => $totalOrders,
                'avg_completion_time' => round($avgCompletionTime ?? 0, 1),
                'on_time_percentage' => $onTimePercentage,
                'overload_alerts' => $overloadAlerts,
            ]
        ];

        // Station performance with efficiency scoring
        $stationPerformance = KitchenStation::where('is_active', true)
            ->get()
            ->map(function ($station) use ($startDate, $endDate) {
                $ordersCompleted = LoadBalancingLog::where('station_id', $station->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $avgLoad = LoadBalancingLog::where('station_id', $station->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->avg('new_load') ?? 0;

                // Get average completion time for this station
                $stationOrders = Order::whereHas('stationAssignments', function ($query) use ($station) {
                    $query->where('station_id', $station->id);
                })
                    ->whereBetween('order_time', [$startDate, $endDate])
                    ->where('order_status', 'completed')
                    ->whereNotNull('actual_completion_time')
                    ->get();

                $avgCompletionTime = $stationOrders->map(function ($order) {
                    $start = Carbon::parse($order->order_time);
                    $end = Carbon::parse($order->actual_completion_time);
                    return $start->diffInMinutes($end);
                })->avg() ?? 0;

                // Calculate efficiency score (based on throughput and completion time)
                // Higher orders with lower completion time = higher efficiency
                $efficiencyScore = $ordersCompleted > 0
                    ? min(100, round(($ordersCompleted / max(1, $avgCompletionTime / 10)) * 10, 1))
                    : 0;

                return [
                    'station_name' => $station->name,
                    'orders_completed' => $ordersCompleted,
                    'avg_load' => round($avgLoad, 1),
                    'avg_completion_time' => round($avgCompletionTime, 1),
                    'efficiency_score' => $efficiencyScore,
                ];
            })
            ->sortByDesc('efficiency_score')
            ->values();

        // Peak hours analysis
        $peakHours = Order::whereBetween('order_time', [$startDate, $endDate])
            ->selectRaw('HOUR(order_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return view('admin.kitchen.analytics', compact(
            'last7Days',
            'stationPerformance',
            'peakHours',
            'analytics'
        ));
    }
}
