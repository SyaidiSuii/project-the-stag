<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleAnalytics;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SaleAnalyticsController extends Controller
{
    /**
     * Display a listing of the analytics.
     */
    public function index(Request $request)
    {
        $query = SaleAnalytics::query();
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        
        // Default to current month if no filter
        if (!$request->has('start_date')) {
            $query->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
        }
        
        $analytics = $query->orderBy('date', 'desc')->paginate(31);
        
        return view('admin.sale-analytics.index', compact('analytics'));
    }

    /**
     * Show the form for creating a new analytics record.
     */
    public function create()
    {
        return view('admin.sale-analytics.create');
    }

    /**
     * Store a newly created analytics record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|unique:sale_analytics,date',
            'total_sales' => 'required|numeric|min:0',
            'total_orders' => 'required|integer|min:0',
            'average_order_value' => 'required|numeric|min:0',
            'unique_customers' => 'required|integer|min:0',
            'new_customers' => 'integer|min:0',
            'returning_customers' => 'integer|min:0',
            'dine_in_orders' => 'integer|min:0',
            'takeaway_orders' => 'integer|min:0',
            'delivery_orders' => 'integer|min:0',
            'mobile_orders' => 'integer|min:0',
            'qr_orders' => 'integer|min:0',
            'total_revenue_dine_in' => 'numeric|min:0',
            'total_revenue_takeaway' => 'numeric|min:0',
            'total_revenue_delivery' => 'numeric|min:0',
            'average_preparation_time' => 'nullable|numeric|min:0',
            'customer_satisfaction_avg' => 'nullable|numeric|between:0,5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $analytics = SaleAnalytics::create($request->all());

        return redirect()->route('admin.sale-analytics.index')
                        ->with('success', 'Analytics record created successfully.');
    }

    /**
     * Display the specified analytics record.
     */
    public function show(SaleAnalytics $saleAnalytics)
    {
        return view('admin.sale-analytics.show', compact('saleAnalytics'));
    }

    /**
     * Show the form for editing the specified analytics record.
     */
    public function edit(SaleAnalytics $saleAnalytics)
    {
        return view('admin.sale-analytics.edit', compact('saleAnalytics'));
    }

    /**
     * Update the specified analytics record.
     */
    public function update(Request $request, SaleAnalytics $saleAnalytics)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|unique:sale_analytics,date,' . $saleAnalytics->id,
            'total_sales' => 'required|numeric|min:0',
            'total_orders' => 'required|integer|min:0',
            'average_order_value' => 'required|numeric|min:0',
            'unique_customers' => 'required|integer|min:0',
            'new_customers' => 'integer|min:0',
            'returning_customers' => 'integer|min:0',
            'dine_in_orders' => 'integer|min:0',
            'takeaway_orders' => 'integer|min:0',
            'delivery_orders' => 'integer|min:0',
            'mobile_orders' => 'integer|min:0',
            'qr_orders' => 'integer|min:0',
            'total_revenue_dine_in' => 'numeric|min:0',
            'total_revenue_takeaway' => 'numeric|min:0',
            'total_revenue_delivery' => 'numeric|min:0',
            'average_preparation_time' => 'nullable|numeric|min:0',
            'customer_satisfaction_avg' => 'nullable|numeric|between:0,5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $saleAnalytics->update($request->all());

        return redirect()->route('admin.sale-analytics.index')
                        ->with('success', 'Analytics record updated successfully.');
    }

    /**
     * Remove the specified analytics record.
     */
    public function destroy(SaleAnalytics $saleAnalytics)
    {
        $saleAnalytics->delete();

        return redirect()->route('admin.sale-analytics.index')
                        ->with('success', 'Analytics record deleted successfully.');
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year
        
        $query = SaleAnalytics::query();
        
        switch ($period) {
            case 'today':
                $query->whereDate('date', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('date', Carbon::now()->year);
                break;
        }
        
        $stats = $query->selectRaw('
            SUM(total_sales) as total_sales,
            SUM(total_orders) as total_orders,
            AVG(average_order_value) as avg_order_value,
            SUM(unique_customers) as total_customers,
            SUM(new_customers) as new_customers,
            SUM(returning_customers) as returning_customers,
            SUM(dine_in_orders) as dine_in_orders,
            SUM(takeaway_orders) as takeaway_orders,
            SUM(delivery_orders) as delivery_orders,
            SUM(mobile_orders) as mobile_orders,
            SUM(qr_orders) as qr_orders,
            AVG(average_preparation_time) as avg_prep_time,
            AVG(customer_satisfaction_avg) as avg_satisfaction
        ')->first();
        
        return response()->json($stats);
    }

    /**
     * Get analytics for specific date range.
     */
    public function getDateRangeAnalytics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $analytics = SaleAnalytics::whereBetween('date', [$request->start_date, $request->end_date])
                                 ->orderBy('date', 'asc')
                                 ->get();

        return response()->json($analytics);
    }

    /**
     * Generate daily analytics from existing orders.
     */
    public function generateDailyAnalytics($date = null)
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        
        // Check if analytics already exist for this date
        $existing = SaleAnalytics::whereDate('date', $targetDate)->first();
        if ($existing) {
            return response()->json(['message' => 'Analytics already exist for this date'], 409);
        }
        
        // Get orders for the target date
        $orders = Order::whereDate('created_at', $targetDate)
                      ->where('status', '!=', 'cancelled')
                      ->get();
        
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this date'], 404);
        }
        
        // Calculate analytics
        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Count unique customers
        $uniqueCustomers = $orders->pluck('customer_id')->unique()->count();
        
        // Count order types
        $dineInOrders = $orders->where('order_type', 'dine_in')->count();
        $takeawayOrders = $orders->where('order_type', 'takeaway')->count();
        $deliveryOrders = $orders->where('order_type', 'delivery')->count();
        $mobileOrders = $orders->where('platform', 'mobile')->count();
        $qrOrders = $orders->where('platform', 'qr')->count();
        
        // Calculate revenue by order type
        $totalRevenueDineIn = $orders->where('order_type', 'dine_in')->sum('total_amount');
        $totalRevenueTakeaway = $orders->where('order_type', 'takeaway')->sum('total_amount');
        $totalRevenueDelivery = $orders->where('order_type', 'delivery')->sum('total_amount');
        
        // Get popular items
        $popularItems = OrderItem::whereHas('order', function($query) use ($targetDate) {
                                    $query->whereDate('created_at', $targetDate)
                                          ->where('status', '!=', 'cancelled');
                                })
                                ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'))
                                ->groupBy('menu_item_id')
                                ->orderBy('total_quantity', 'desc')
                                ->limit(10)
                                ->with('menuItem:id,name')
                                ->get()
                                ->map(function($item) {
                                    return [
                                        'id' => $item->menu_item_id,
                                        'name' => $item->menuItem->name ?? 'Unknown',
                                        'quantity' => $item->total_quantity
                                    ];
                                });
        
        // Calculate peak hours
        $hourlyOrders = $orders->groupBy(function($order) {
            return Carbon::parse($order->created_at)->format('H');
        })->map(function($orders) {
            return $orders->count();
        })->sortKeys();
        
        $peakHours = [
            'breakfast' => $hourlyOrders->slice(6, 5)->keys()->first() ?? 8, // 6-11 AM
            'lunch' => $hourlyOrders->slice(11, 4)->keys()->first() ?? 13,   // 11 AM-3 PM
            'dinner' => $hourlyOrders->slice(17, 5)->keys()->first() ?? 19   // 5-10 PM
        ];
        
        // Create analytics record
        $analytics = SaleAnalytics::create([
            'date' => $targetDate,
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'peak_hours' => $hourlyOrders->toArray(),
            'popular_items' => $popularItems->toArray(),
            'unique_customers' => $uniqueCustomers,
            'new_customers' => 0, // This would need additional logic to determine
            'returning_customers' => 0, // This would need additional logic to determine
            'dine_in_orders' => $dineInOrders,
            'takeaway_orders' => $takeawayOrders,
            'delivery_orders' => $deliveryOrders,
            'mobile_orders' => $mobileOrders,
            'qr_orders' => $qrOrders,
            'total_revenue_dine_in' => $totalRevenueDineIn,
            'total_revenue_takeaway' => $totalRevenueTakeaway,
            'total_revenue_delivery' => $totalRevenueDelivery,
            'average_preparation_time' => null, // Would need to calculate from order timings
            'customer_satisfaction_avg' => null, // Would need to integrate with rating system
        ]);
        
        return response()->json([
            'message' => 'Analytics generated successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get popular items analytics.
     */
    public function getPopularItems(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $limit = $request->get('limit', 10);
        
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
                                 ->whereNotNull('popular_items')
                                 ->get();
        
        $itemCounts = [];
        foreach ($analytics as $analytic) {
            $items = $analytic->popular_items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $key = $item['id'] ?? $item['name'];
                    if (!isset($itemCounts[$key])) {
                        $itemCounts[$key] = [
                            'name' => $item['name'],
                            'total_quantity' => 0
                        ];
                    }
                    $itemCounts[$key]['total_quantity'] += $item['quantity'];
                }
            }
        }
        
        // Sort by quantity and limit results
        uasort($itemCounts, function($a, $b) {
            return $b['total_quantity'] - $a['total_quantity'];
        });
        
        $popularItems = array_slice($itemCounts, 0, $limit, true);
        
        return response()->json($popularItems);
    }

    /**
     * Get peak hours analytics.
     */
    public function getPeakHours(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
                                 ->whereNotNull('peak_hours')
                                 ->get();
        
        $peakHoursData = [
            'breakfast' => [],
            'lunch' => [],
            'dinner' => []
        ];
        
        foreach ($analytics as $analytic) {
            $peakHours = $analytic->peak_hours;
            if (is_array($peakHours)) {
                foreach ($peakHours as $period => $hour) {
                    if (isset($peakHoursData[$period])) {
                        $peakHoursData[$period][] = $hour;
                    }
                }
            }
        }
        
        // Calculate average peak hours
        $averagePeakHours = [];
        foreach ($peakHoursData as $period => $hours) {
            $averagePeakHours[$period] = !empty($hours) ? round(array_sum($hours) / count($hours)) : null;
        }
        
        return response()->json($averagePeakHours);
    }

    /**
     * Get customer analytics.
     */
    public function getCustomerAnalytics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
                                 ->selectRaw('
                                     SUM(unique_customers) as total_unique_customers,
                                     SUM(new_customers) as total_new_customers,
                                     SUM(returning_customers) as total_returning_customers,
                                     AVG(customer_satisfaction_avg) as avg_satisfaction
                                 ')
                                 ->first();
        
        return response()->json($analytics);
    }

    /**
     * Get trends analytics.
     */
    public function getTrends(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $compare = $request->get('compare', false); // Compare with previous period
        
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $previousStart = Carbon::now()->startOfWeek()->subWeek();
                $previousEnd = Carbon::now()->startOfWeek()->subDay();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $previousStart = Carbon::now()->startOfMonth()->subMonth();
                $previousEnd = Carbon::now()->startOfMonth()->subDay();
                break;
            case 'quarter':
                $startDate = Carbon::now()->startOfQuarter();
                $previousStart = Carbon::now()->startOfQuarter()->subQuarter();
                $previousEnd = Carbon::now()->startOfQuarter()->subDay();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $previousStart = Carbon::now()->startOfYear()->subYear();
                $previousEnd = Carbon::now()->startOfYear()->subDay();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $previousStart = Carbon::now()->startOfMonth()->subMonth();
                $previousEnd = Carbon::now()->startOfMonth()->subDay();
        }
        
        $currentData = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
                                   ->selectRaw('
                                       SUM(total_sales) as total_sales,
                                       SUM(total_orders) as total_orders,
                                       AVG(average_order_value) as avg_order_value,
                                       SUM(unique_customers) as total_customers
                                   ')
                                   ->first();
        
        $result = [
            'current' => $currentData,
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
        
        if ($compare) {
            $previousData = SaleAnalytics::whereBetween('date', [$previousStart, $previousEnd])
                                       ->selectRaw('
                                           SUM(total_sales) as total_sales,
                                           SUM(total_orders) as total_orders,
                                           AVG(average_order_value) as avg_order_value,
                                           SUM(unique_customers) as total_customers
                                       ')
                                       ->first();
            
            $result['previous'] = $previousData;
            $result['comparison'] = [
                'sales_growth' => $this->calculateGrowthPercentage($currentData->total_sales, $previousData->total_sales),
                'orders_growth' => $this->calculateGrowthPercentage($currentData->total_orders, $previousData->total_orders),
                'aov_growth' => $this->calculateGrowthPercentage($currentData->avg_order_value, $previousData->avg_order_value),
                'customers_growth' => $this->calculateGrowthPercentage($currentData->total_customers, $previousData->total_customers),
            ];
        }
        
        return response()->json($result);
    }

    /**
     * Calculate growth percentage between two values.
     */
    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
}