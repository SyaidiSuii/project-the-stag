<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesAnalyticsService;
use App\Services\BusinessIntelligenceService;
use App\Services\MenuIntelligenceService;
use App\Services\MenuRecommendationService;
use App\Services\BusinessInsightGenerator;
use App\Services\DataQualityCheckService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\MenuItem;
use App\Models\OrderActivityLog;
use App\Models\Order; // Import the Order model
use Carbon\Carbon;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Import DB facade

class ReportController extends Controller
{
    protected $salesAnalyticsService;
    protected $biService;
    protected $menuIntelligenceService;
    protected $menuRecommendationService;
    protected $insightGenerator;
    protected $qualityService;

    public function __construct(
        SalesAnalyticsService $salesAnalyticsService,
        BusinessIntelligenceService $biService,
        MenuIntelligenceService $menuIntelligenceService,
        MenuRecommendationService $menuRecommendationService,
        BusinessInsightGenerator $insightGenerator,
        DataQualityCheckService $qualityService
    ) {
        $this->salesAnalyticsService = $salesAnalyticsService;
        $this->biService = $biService;
        $this->menuIntelligenceService = $menuIntelligenceService;
        $this->menuRecommendationService = $menuRecommendationService;
        $this->insightGenerator = $insightGenerator;
        $this->qualityService = $qualityService;
    }

    /**
     * Display the main reporting dashboard with comprehensive analytics.
     * This now redirects to the monthly view.
     */
    public function index(): View
    {
        return $this->monthly();
    }

    /**
     * Display monthly analytics dashboard.
     */
    public function monthly(): View
    {
        // === CHARTS DATA ===
        $salesSummary = $this->salesAnalyticsService->getSalesSummary(30);
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();

        // === DATE RANGES ===
        $startOfCurrentMonth = Carbon::now()->startOfMonth();
        $endOfCurrentMonth = Carbon::now()->endOfMonth();
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();

        // === CURRENT MONTH ANALYTICS ===
        $currentMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfCurrentMonth,
            $endOfCurrentMonth
        );
        $previousMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfPreviousMonth,
            $endOfPreviousMonth
        );

        // === REVENUE METRICS ===
        $currentMonthRevenue = $currentMonthAnalytics['total_revenue'];
        $previousMonthRevenue = $previousMonthAnalytics['total_revenue'];
        $revenueChangePercentage = $this->calculateChangePercentage($currentMonthRevenue, $previousMonthRevenue);

        // === ORDER METRICS ===
        $currentMonthOrders = $currentMonthAnalytics['total_orders'];
        $previousMonthOrders = $previousMonthAnalytics['total_orders'];
        $ordersChangePercentage = $this->calculateChangePercentage($currentMonthOrders, $previousMonthOrders);

        // === AVG ORDER VALUE ===
        $currentMonthAvgOrderValue = $currentMonthAnalytics['avg_order_value'];
        $previousMonthAvgOrderValue = $previousMonthAnalytics['avg_order_value'];
        $avgOrderValueChangePercentage = $this->calculateChangePercentage($currentMonthAvgOrderValue, $previousMonthAvgOrderValue);

        // === CUSTOMER METRICS ===
        $customerRetention = $this->salesAnalyticsService->getCustomerRetention(30);

        // === ORDER TYPE BREAKDOWN ===
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown(30);

        // === QR VS WEB COMPARISON ===
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders(30);

        // === PROMOTION EFFECTIVENESS ===
        $promotionStats = $this->salesAnalyticsService->getPromotionEffectiveness(30);

        // === MENU ITEMS ===
        $activeItems = MenuItem::where('is_available', 1)->count();
        $newItemsThisMonth = MenuItem::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();

        // === QR & TABLE STATS ===
        $qrOrders = $currentMonthAnalytics['qr_orders'];
        $qrRevenue = $currentMonthAnalytics['qr_revenue'];
        $tableBookings = $currentMonthAnalytics['table_bookings'];

        // === PROMOTIONS & REWARDS ===
        $promotionsUsed = $currentMonthAnalytics['promotions_used'];
        $promotionDiscounts = $currentMonthAnalytics['promotion_discounts'];
        $rewardsRedeemed = $currentMonthAnalytics['rewards_redeemed'];

        return view('admin.reports.monthly', [
            // Charts
            'salesSummary' => $salesSummary,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'orderTypeBreakdown' => $orderTypeBreakdown,
            'qrVsWeb' => $qrVsWeb,

            // Core Metrics
            'currentMonthRevenue' => $currentMonthRevenue,
            'revenueChangePercentage' => $revenueChangePercentage,
            'currentMonthOrders' => $currentMonthOrders,
            'ordersChangePercentage' => $ordersChangePercentage,
            'currentMonthAvgOrderValue' => $currentMonthAvgOrderValue,
            'avgOrderValueChangePercentage' => $avgOrderValueChangePercentage,

            // Customer Metrics
            'customerRetention' => $customerRetention,
            'newCustomers' => $currentMonthAnalytics['new_customers'],
            'returningCustomers' => $currentMonthAnalytics['returning_customers'],

            // Menu Items
            'activeItems' => $activeItems,
            'newItemsThisMonth' => $newItemsThisMonth,

            // QR & Table Analytics
            'qrOrders' => $qrOrders,
            'qrRevenue' => $qrRevenue,
            'tableBookings' => $tableBookings,

            // Promotions & Rewards
            'promotionsUsed' => $promotionsUsed,
            'promotionDiscounts' => $promotionDiscounts,
            'rewardsRedeemed' => $rewardsRedeemed,
            'promotionStats' => $promotionStats,
        ]);
    }

    /**
     * Display all-time analytics dashboard.
     */
    public function allTime(): View
    {
        // All-time date range (from beginning of business)
        $startOfBusiness = Carbon::createFromDate(2020, 1, 1); // Assuming business started in 2020
        $endOfNow = Carbon::now();

        // === CHARTS DATA (All Time) ===
        // Use monthly Jan-Dec view for current year
        $salesSummary = $this->salesAnalyticsService->getMonthlyYearSummary();
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown(3650); // ~10 years
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders(3650);

        // === ALL-TIME ANALYTICS ===
        $allTimeAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfBusiness,
            $endOfNow
        );

        // === CUSTOMER METRICS ===
        $customerRetention = $this->salesAnalyticsService->getCustomerRetention(3650);

        // === PROMOTION EFFECTIVENESS ===
        $promotionStats = $this->salesAnalyticsService->getPromotionEffectiveness(3650);

        // === TOTAL CUSTOMERS (All Time) ===
        $totalCustomers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'customer');
        })->count();

        return view('admin.reports.all-time', [
            // Charts
            'salesSummary' => $salesSummary,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'orderTypeBreakdown' => $orderTypeBreakdown,
            'qrVsWeb' => $qrVsWeb,

            // Core Metrics
            'totalRevenue' => $allTimeAnalytics['total_revenue'],
            'totalOrders' => $allTimeAnalytics['total_orders'],
            'avgOrderValue' => $allTimeAnalytics['avg_order_value'],
            'totalCustomers' => $totalCustomers,

            // Customer Metrics
            'customerRetention' => $customerRetention,
            'newCustomers' => $allTimeAnalytics['new_customers'],
            'returningCustomers' => $allTimeAnalytics['returning_customers'],

            // QR & Table Analytics
            'qrOrders' => $allTimeAnalytics['qr_orders'],
            'qrRevenue' => $allTimeAnalytics['qr_revenue'],
            'tableBookings' => $allTimeAnalytics['table_bookings'],

            // Promotions & Rewards
            'promotionsUsed' => $allTimeAnalytics['promotions_used'],
            'promotionDiscounts' => $allTimeAnalytics['promotion_discounts'],
            'rewardsRedeemed' => $allTimeAnalytics['rewards_redeemed'],
            'promotionStats' => $promotionStats,

            // Metadata
            'dateRange' => [
                'start' => $startOfBusiness->toDateString(),
                'end' => $endOfNow->toDateString(),
                'label' => 'All Time (Since 2020) - Yearly View'
            ],
        ]);
    }

    /**
     * Generate and download a PDF report of the analytics dashboard.
     */
    public function generatePDF(): Response
    {
        // === CHARTS DATA ===
        $salesSummary = $this->salesAnalyticsService->getSalesSummary(30);
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();

        // === DATE RANGES ===
        $startOfCurrentMonth = Carbon::now()->startOfMonth();
        $endOfCurrentMonth = Carbon::now()->endOfMonth();
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();

        // === CURRENT MONTH ANALYTICS ===
        $currentMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfCurrentMonth,
            $endOfCurrentMonth
        );
        $previousMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfPreviousMonth,
            $endOfPreviousMonth
        );

        // === REVENUE METRICS ===
        $currentMonthRevenue = $currentMonthAnalytics['total_revenue'];
        $previousMonthRevenue = $previousMonthAnalytics['total_revenue'];
        $revenueChangePercentage = $this->calculateChangePercentage($currentMonthRevenue, $previousMonthRevenue);

        // === ORDER METRICS ===
        $currentMonthOrders = $currentMonthAnalytics['total_orders'];
        $previousMonthOrders = $previousMonthAnalytics['total_orders'];
        $ordersChangePercentage = $this->calculateChangePercentage($currentMonthOrders, $previousMonthOrders);

        // === AVG ORDER VALUE ===
        $currentMonthAvgOrderValue = $currentMonthAnalytics['avg_order_value'];
        $previousMonthAvgOrderValue = $previousMonthAnalytics['avg_order_value'];
        $avgOrderValueChangePercentage = $this->calculateChangePercentage($currentMonthAvgOrderValue, $previousMonthAvgOrderValue);

        // === CUSTOMER METRICS ===
        $customerRetention = $this->salesAnalyticsService->getCustomerRetention(30);

        // === ORDER TYPE BREAKDOWN ===
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown(30);

        // === QR VS WEB COMPARISON ===
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders(30);

        // === PROMOTION EFFECTIVENESS ===
        $promotionStats = $this->salesAnalyticsService->getPromotionEffectiveness(30);

        // === MENU ITEMS ===
        $activeItems = MenuItem::where('is_available', 1)->count();
        $newItemsThisMonth = MenuItem::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();

        // === QR & TABLE STATS ===
        $qrOrders = $currentMonthAnalytics['qr_orders'];
        $qrRevenue = $currentMonthAnalytics['qr_revenue'];
        $tableBookings = $currentMonthAnalytics['table_bookings'];

        // === PROMOTIONS & REWARDS ===
        $promotionsUsed = $currentMonthAnalytics['promotions_used'];
        $promotionDiscounts = $currentMonthAnalytics['promotion_discounts'];
        $rewardsRedeemed = $currentMonthAnalytics['rewards_redeemed'];

        // Prepare data for PDF
        $data = [
            // Charts
            'salesSummary' => $salesSummary,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'orderTypeBreakdown' => $orderTypeBreakdown,
            'qrVsWeb' => $qrVsWeb,

            // Core Metrics
            'currentMonthRevenue' => $currentMonthRevenue,
            'revenueChangePercentage' => $revenueChangePercentage,
            'currentMonthOrders' => $currentMonthOrders,
            'ordersChangePercentage' => $ordersChangePercentage,
            'currentMonthAvgOrderValue' => $currentMonthAvgOrderValue,
            'avgOrderValueChangePercentage' => $avgOrderValueChangePercentage,

            // Customer Metrics
            'customerRetention' => $customerRetention,
            'newCustomers' => $currentMonthAnalytics['new_customers'],
            'returningCustomers' => $currentMonthAnalytics['returning_customers'],

            // Menu Items
            'activeItems' => $activeItems,
            'newItemsThisMonth' => $newItemsThisMonth,

            // QR & Table Analytics
            'qrOrders' => $qrOrders,
            'qrRevenue' => $qrRevenue,
            'tableBookings' => $tableBookings,

            // Promotions & Rewards
            'promotionsUsed' => $promotionsUsed,
            'promotionDiscounts' => $promotionDiscounts,
            'rewardsRedeemed' => $rewardsRedeemed,
            'promotionStats' => $promotionStats,
            
            // Report metadata
            'reportDate' => now()->format('F j, Y'),
            'reportPeriod' => 'Last 30 Days'
        ];

        // Load the PDF view with data
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Stream the PDF to the browser (view in browser instead of downloading)
        return $pdf->stream('analytics-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate and download a PDF report of the analytics dashboard.
     */
    public function downloadPDF(): Response
    {
        // Use the same logic as the executive summary for consistency
        $mom = $this->biService->getMonthOverMonthComparison(Carbon::today());

        // Core Metrics
        $currentMonthRevenue = $mom['current_month']['revenue'];
        $currentMonthOrders = $mom['current_month']['orders'];
        $currentMonthAvgOrderValue = $mom['current_month']['avg_order_value'];

        // --- Additional Data for PDF ---
        $days = 30;
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        // Order Distribution
        $trends = $this->biService->getTrendAnalysis($startDate, $endDate);
        $orderDistribution = $trends['order_status_distribution'];

        // Menu Performance
        $menuPerformance = $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate);
        $top10Items = array_slice($menuPerformance['top_performers'] ?? [], 0, 10);
        
        // Sort by revenue for Top 5 Revenue Generators
        $topPerformers = $menuPerformance['top_performers'] ?? [];
        usort($topPerformers, function ($a, $b) {
            return ($b['metrics']['total_revenue'] ?? 0) <=> ($a['metrics']['total_revenue'] ?? 0);
        });
        $top5Revenue = array_slice($topPerformers, 0, 5);

        // Most Booked Tables
        $mostBookedTables = $this->biService->getMostBookedTables($startDate, $endDate);

        // Peak Hours
        $peakHoursData = $this->biService->getPeakHoursAnalysis($startDate, $endDate);
        $peakHours = $peakHoursData['peak_hours'] ?? [];
        arsort($peakHours); // Sort by order count descending

        // Prepare data for PDF
        $data = [
            // Core Metrics
            'currentMonthRevenue' => $currentMonthRevenue,
            'currentMonthOrders' => $currentMonthOrders,
            'currentMonthAvgOrderValue' => $currentMonthAvgOrderValue,
            
            // Additional Analytics
            'orderDistribution' => $orderDistribution,
            'top10Items' => $top10Items,
            'top5Revenue' => $top5Revenue,
            'mostBookedTables' => $mostBookedTables,
            'peakHours' => $peakHours,

            // Report metadata
            'reportDate' => now()->format('F j, Y'),
            'reportPeriod' => 'Current Month (' . now()->format('F') . ')'
        ];

        // Load the PDF view with data
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Return the PDF as a download
        return $pdf->download('full-analytics-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Get real-time analytics data for live dashboard updates.
     * This endpoint is called by JavaScript to refresh dashboard stats.
     */
    public function getLiveAnalytics(): JsonResponse
    {
        $startOfCurrentMonth = Carbon::now()->startOfMonth();
        $endOfCurrentMonth = Carbon::now()->endOfMonth();

        $currentMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfCurrentMonth,
            $endOfCurrentMonth
        );

        $activeItems = MenuItem::where('is_available', 1)->count();
        $newItemsThisMonth = MenuItem::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();

        return response()->json([
            'success' => true,
            'data' => [
                // Core metrics
                'total_revenue' => $currentMonthAnalytics['total_revenue'],
                'total_orders' => $currentMonthAnalytics['total_orders'],
                'avg_order_value' => $currentMonthAnalytics['avg_order_value'],

                // Menu items
                'active_items' => $activeItems,
                'new_items' => $newItemsThisMonth,

                // QR & Table
                'qr_orders' => $currentMonthAnalytics['qr_orders'],
                'qr_revenue' => $currentMonthAnalytics['qr_revenue'],
                'table_bookings' => $currentMonthAnalytics['table_bookings'],

                // Promotions & Rewards
                'promotions_used' => $currentMonthAnalytics['promotions_used'],
                'promotion_discounts' => $currentMonthAnalytics['promotion_discounts'],
                'rewards_redeemed' => $currentMonthAnalytics['rewards_redeemed'],

                // Customer metrics
                'new_customers' => $currentMonthAnalytics['new_customers'],
                'returning_customers' => $currentMonthAnalytics['returning_customers'],
                'customer_retention_rate' => $currentMonthAnalytics['customer_retention_rate'],
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get updated chart data for real-time dashboard.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);

        $salesSummary = $this->salesAnalyticsService->getSalesSummary($days);
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown($days);
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders($days);

        return response()->json([
            'success' => true,
            'charts' => [
                'sales_summary' => $salesSummary,
                'top_products' => $topSellingProducts,
                'sales_by_category' => $salesByCategory,
                'order_types' => $orderTypeBreakdown,
                'qr_vs_web' => $qrVsWeb,
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get all-time analytics data for the "All Time" tab view.
     * This provides comprehensive historical data since the business started.
     */
    public function getAllTimeAnalytics(): JsonResponse
    {
        // All-time date range (from beginning of business)
        $startOfBusiness = Carbon::createFromDate(2020, 1, 1); // Assuming business started in 2020
        $endOfNow = Carbon::now();

        // === CHARTS DATA (All Time) ===
        // Use smart date range method to automatically select best granularity
        $salesSummary = $this->salesAnalyticsService->getSalesSummaryByDateRange($startOfBusiness, $endOfNow);
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown(3650);
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders(3650);

        // === ALL-TIME ANALYTICS ===
        $allTimeAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfBusiness,
            $endOfNow
        );

        // === CUSTOMER METRICS ===
        $customerRetention = $this->salesAnalyticsService->getCustomerRetention(3650);

        // === PROMOTION EFFECTIVENESS ===
        $promotionStats = $this->salesAnalyticsService->getPromotionEffectiveness(3650);

        // === MENU ITEMS (All Time) ===
        $activeItems = MenuItem::where('is_available', 1)->count();
        $totalItemsEver = MenuItem::count();

        // === BUILD RESPONSE DATA ===
        return response()->json([
            'success' => true,

            // KPI Data for dashboard cards
            'kpi' => [
                'total_revenue' => $allTimeAnalytics['total_revenue'],
                'total_orders' => $allTimeAnalytics['total_orders'],
                'avg_order_value' => $allTimeAnalytics['avg_order_value'],
                'active_items' => $activeItems,
                'qr_orders' => $allTimeAnalytics['qr_orders'],
                'table_bookings' => $allTimeAnalytics['table_bookings'],
                'new_customers' => $allTimeAnalytics['new_customers'],
                'returning_customers' => $allTimeAnalytics['returning_customers'],
                'customer_retention_rate' => $allTimeAnalytics['customer_retention_rate'],
            ],

            // Chart Data
            'salesSummary' => $salesSummary,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'orderTypeBreakdown' => $orderTypeBreakdown,
            'qrVsWeb' => $qrVsWeb,

            // Additional metrics
            'promotionStats' => $promotionStats,
            'promotionsUsed' => $allTimeAnalytics['promotions_used'],
            'promotionDiscounts' => $allTimeAnalytics['promotion_discounts'],
            'rewardsRedeemed' => $allTimeAnalytics['rewards_redeemed'],
            'customerRetention' => $customerRetention,

            // Metadata
            'dateRange' => [
                'start' => $startOfBusiness->toDateString(),
                'end' => $endOfNow->toDateString(),
                'label' => 'All Time (Since 2020) - Yearly View'
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Display Order Activity Logs report
     */
    public function orderActivityLogs(Request $request): View
    {
        $query = OrderActivityLog::with(['order', 'triggeredBy'])
            ->latest('created_at');

        // Filter by activity type
        if ($request->filled('activity_type') && $request->activity_type !== 'all') {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by order number (search)
        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('confirmation_code', 'like', '%' . $request->search . '%')
                  ->orWhere('id', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $activityLogs = $query->paginate(50);

        // Statistics
        $totalLogs = OrderActivityLog::count();
        $criticalCount = OrderActivityLog::where('activity_type', 'critical')->count();
        $errorCount = OrderActivityLog::where('activity_type', 'error')->count();
        $warningCount = OrderActivityLog::where('activity_type', 'warning')->count();
        $infoCount = OrderActivityLog::where('activity_type', 'info')->count();

        // Recent problems (last 24 hours)
        $recentProblems = OrderActivityLog::problems()
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return view('admin.reports.activity-logs', compact(
            'activityLogs',
            'totalLogs',
            'criticalCount',
            'errorCount',
            'warningCount',
            'infoCount',
            'recentProblems'
        ));
    }

    private function calculateChangePercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    // ==========================================
    // NEW ENHANCED ANALYTICS ENDPOINTS
    // ==========================================

    /**
     * Get business intelligence insights
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBusinessIntelligence(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = [
            'trends' => $this->biService->getTrendAnalysis($startDate, $endDate),
            'mom_comparison' => $this->biService->getMonthOverMonthComparison(Carbon::today()),
            'yoy_comparison' => $this->biService->getYearOverYearComparison(Carbon::today()),
            'forecast' => $this->biService->forecastRevenue(7, $days),
            'peak_hours' => $this->biService->getPeakHoursAnalysis($startDate, $endDate),
            'anomalies' => $this->biService->detectAnomalies(Carbon::today(), $days),
            'most_booked_tables' => $this->biService->getMostBookedTables($startDate, $endDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $days,
            ],
        ]);
    }

    /**
     * Get menu intelligence analysis
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMenuIntelligence(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = [
            'performance_analysis' => $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate),
            'underperformers' => $this->menuIntelligenceService->getUnderperformingItems($startDate, $endDate, 10),
            'pricing_opportunities' => $this->menuIntelligenceService->getPricingOpportunities($startDate, $endDate),
            'bundle_opportunities' => $this->menuIntelligenceService->getBundleOpportunities($startDate, $endDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ]);
    }

    /**
     * Get menu recommendations and improvements
     *
     * @return JsonResponse
     */
    public function getMenuRecommendations(): JsonResponse
    {
        $data = [
            'improvements' => $this->menuRecommendationService->getMenuImprovementSuggestions(),
            'trending_items' => $this->menuRecommendationService->getTrendingItems(7, 10),
            'seasonal' => $this->menuRecommendationService->getSeasonalRecommendations(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get automated business insights with executive summary
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBusinessInsights(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $insights = $this->insightGenerator->generateInsights($date);
        $recommendations = $this->insightGenerator->getActionableRecommendations($insights);

        return response()->json([
            'success' => true,
            'insights' => $insights,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Get executive summary
     *
     * @return JsonResponse
     */
    public function getExecutiveSummary(): JsonResponse
    {
        $insights = $this->insightGenerator->generateInsights();
        $mom = $this->biService->getMonthOverMonthComparison(Carbon::today());

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);
        $menuAnalysis = $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate);

        $summary = [
            'executive_summary' => $insights['insights']['executive_summary'],
            'key_metrics' => [
                'revenue' => [
                    'current' => $mom['current_month']['revenue'],
                    'previous' => $mom['previous_month']['revenue'],
                    'change_percentage' => $mom['changes']['revenue']['percentage'],
                    'direction' => $mom['changes']['revenue']['direction'],
                ],
                'orders' => [
                    'current' => $mom['current_month']['orders'],
                    'previous' => $mom['previous_month']['orders'],
                    'change_percentage' => $mom['changes']['orders']['percentage'],
                ],
                'aov' => [
                    'current' => $mom['current_month']['avg_order_value'],
                    'previous' => $mom['previous_month']['avg_order_value'],
                    'change_percentage' => $mom['changes']['avg_order_value']['percentage'],
                ],
            ],
            'menu_health' => [
                'total_items' => $menuAnalysis['total_items_analyzed'],
                'average_score' => $menuAnalysis['summary']['average_score'],
                'items_needing_attention' => $menuAnalysis['summary']['items_needing_attention'],
            ],
            'top_insights' => $insights['insights']['executive_summary']['top_priority_insights'],
            'health_score' => $insights['insights']['executive_summary']['health_score'],
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
            'generated_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get data quality report
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDataQualityReport(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $qualityCheck = $this->qualityService->runQualityChecks($date, false);

        return response()->json([
            'success' => true,
            'data' => $qualityCheck,
        ]);
    }

    /**
     * Enhanced monthly view with new analytics
     *
     * @return View
     */
    public function enhancedMonthly(): View
    {
        // Get existing monthly data
        $monthlyData = $this->getMonthlyData();

        // Get enhanced analytics
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        $enhancedData = [
            'business_intelligence' => [
                'trends' => $this->biService->getTrendAnalysis($startDate, $endDate),
                'mom' => $this->biService->getMonthOverMonthComparison(Carbon::today()),
                'forecast' => $this->biService->forecastRevenue(7, 30),
                'peak_hours' => $this->biService->getPeakHoursAnalysis($startDate, $endDate),
            ],
            'menu_intelligence' => $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate),
            'insights' => $this->insightGenerator->generateInsights(),
            'recommendations' => $this->menuRecommendationService->getMenuImprovementSuggestions(),
        ];

        return view('admin.reports.enhanced-monthly', array_merge($monthlyData, $enhancedData));
    }

    /**
     * Helper: Get monthly data (extracted from existing monthly method)
     *
     * @return array
     */
    private function getMonthlyData(): array
    {
        $salesSummary = $this->salesAnalyticsService->getSalesSummary(30);
        $topSellingProducts = $this->salesAnalyticsService->getTopSellingProducts(10);
        $salesByCategory = $this->salesAnalyticsService->getSalesByCategory();

        $startOfCurrentMonth = Carbon::now()->startOfMonth();
        $endOfCurrentMonth = Carbon::now()->endOfMonth();
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfCurrentMonth,
            $endOfCurrentMonth
        );
        $previousMonthAnalytics = $this->salesAnalyticsService->getComprehensiveAnalytics(
            $startOfPreviousMonth,
            $endOfPreviousMonth
        );

        $currentMonthRevenue = $currentMonthAnalytics['total_revenue'];
        $previousMonthRevenue = $previousMonthAnalytics['total_revenue'];
        $revenueChangePercentage = $this->calculateChangePercentage($currentMonthRevenue, $previousMonthRevenue);

        $currentMonthOrders = $currentMonthAnalytics['total_orders'];
        $previousMonthOrders = $previousMonthAnalytics['total_orders'];
        $ordersChangePercentage = $this->calculateChangePercentage($currentMonthOrders, $previousMonthOrders);

        $currentMonthAvgOrderValue = $currentMonthAnalytics['avg_order_value'];
        $previousMonthAvgOrderValue = $previousMonthAnalytics['avg_order_value'];
        $avgOrderValueChangePercentage = $this->calculateChangePercentage($currentMonthAvgOrderValue, $previousMonthAvgOrderValue);

        $customerRetention = $this->salesAnalyticsService->getCustomerRetention(30);
        $orderTypeBreakdown = $this->salesAnalyticsService->getOrderTypeBreakdown(30);
        $qrVsWeb = $this->salesAnalyticsService->getQrVsWebOrders(30);
        $promotionStats = $this->salesAnalyticsService->getPromotionEffectiveness(30);

        $activeItems = MenuItem::where('is_available', 1)->count();
        $newItemsThisMonth = MenuItem::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();

        return [
            'salesSummary' => $salesSummary,
            'topSellingProducts' => $topSellingProducts,
            'salesByCategory' => $salesByCategory,
            'orderTypeBreakdown' => $orderTypeBreakdown,
            'qrVsWeb' => $qrVsWeb,
            'currentMonthRevenue' => $currentMonthRevenue,
            'revenueChangePercentage' => $revenueChangePercentage,
            'currentMonthOrders' => $currentMonthOrders,
            'ordersChangePercentage' => $ordersChangePercentage,
            'currentMonthAvgOrderValue' => $currentMonthAvgOrderValue,
            'avgOrderValueChangePercentage' => $avgOrderValueChangePercentage,
            'customerRetention' => $customerRetention,
            'newCustomers' => $currentMonthAnalytics['new_customers'],
            'returningCustomers' => $currentMonthAnalytics['returning_customers'],
            'activeItems' => $activeItems,
            'newItemsThisMonth' => $newItemsThisMonth,
            'qrOrders' => $currentMonthAnalytics['qr_orders'],
            'qrRevenue' => $currentMonthAnalytics['qr_revenue'],
            'tableBookings' => $currentMonthAnalytics['table_bookings'],
            'promotionsUsed' => $currentMonthAnalytics['promotions_used'],
            'promotionDiscounts' => $currentMonthAnalytics['promotion_discounts'],
            'rewardsRedeemed' => $currentMonthAnalytics['rewards_redeemed'],
            'promotionStats' => $promotionStats,
        ];
    }
}