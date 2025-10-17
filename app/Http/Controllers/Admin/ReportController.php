<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\MenuItem;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $salesAnalyticsService;

    public function __construct(SalesAnalyticsService $salesAnalyticsService)
    {
        $this->salesAnalyticsService = $salesAnalyticsService;
    }

    /**
     * Display the main reporting dashboard with comprehensive analytics.
     */
    public function index(): View
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

        return view('admin.reports.index', [
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

    private function calculateChangePercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
