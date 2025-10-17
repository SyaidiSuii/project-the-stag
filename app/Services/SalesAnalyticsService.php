<?php

namespace App\Services;

use App\Models\SaleAnalytics;
use App\Models\DailySalesSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    /**
     * Get sales data for a given period.
     * Now uses SaleAnalytics for comprehensive data
     *
     * @param int $days
     * @return array
     */
    public function getSalesSummary(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        $summaries = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $data = [
            'labels' => [],
            'revenue' => [],
        ];

        // Create a period of dates to ensure we have a value for each day
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $summary = $summaries->first(function ($item) use ($formattedDate) {
                return $item->date->format('Y-m-d') === $formattedDate;
            });

            $data['labels'][] = $date->format('M d');
            $data['revenue'][] = $summary ? $summary->total_sales : 0;
        }

        return $data;
    }

    /**
     * Get top selling products.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopSellingProducts(int $limit = 5)
    {
        return \App\Models\OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('menuItem:id,name')
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['completed', 'served']);
            })
            ->groupBy('menu_item_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales by category.
     *
     * @return array
     */
    public function getSalesByCategory(): array
    {
        $sales = \App\Models\OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->whereHas('order', function ($query) {
                $query->whereIn('order_status', ['completed', 'served']);
            })
            ->groupBy('categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'labels' => $sales->pluck('category_name'),
            'revenue' => $sales->pluck('total_revenue'),
        ];
    }

    /**
     * Get comprehensive analytics for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getComprehensiveAnalytics(Carbon $startDate, Carbon $endDate): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        $newCustomers = $analytics->sum('new_customers');
        $returningCustomers = $analytics->sum('returning_customers');
        $totalCustomers = $newCustomers + $returningCustomers;

        return [
            'total_revenue' => $analytics->sum('total_sales'),
            'total_orders' => $analytics->sum('total_orders'),
            'avg_order_value' => $analytics->avg('average_order_value') ?: 0,
            'unique_customers' => $analytics->sum('unique_customers'),
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'customer_retention_rate' => $totalCustomers > 0
                ? round(($returningCustomers / $totalCustomers) * 100, 1)
                : 0,
            'qr_orders' => $analytics->sum('qr_orders'),
            'qr_revenue' => $analytics->sum('qr_revenue'),
            'table_bookings' => $analytics->sum('table_booking_count'),
            'promotions_used' => $analytics->sum('promotion_usage_count'),
            'promotion_discounts' => $analytics->sum('promotion_discount_total'),
            'rewards_redeemed' => $analytics->sum('rewards_redeemed_count'),
        ];
    }

    /**
     * Get order type breakdown
     *
     * @param int $days
     * @return array
     */
    public function getOrderTypeBreakdown(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        return [
            'labels' => ['Dine In', 'Takeaway', 'Delivery', 'QR Table'],
            'data' => [
                $analytics->sum('dine_in_orders'),
                $analytics->sum('takeaway_orders'),
                $analytics->sum('delivery_orders'),
                $analytics->sum('qr_orders'),
            ],
            'revenue' => [
                $analytics->sum('total_revenue_dine_in'),
                $analytics->sum('total_revenue_takeaway'),
                $analytics->sum('total_revenue_delivery'),
                $analytics->sum('qr_revenue'),
            ],
        ];
    }

    /**
     * Get QR vs Web orders comparison
     *
     * @param int $days
     * @return array
     */
    public function getQrVsWebOrders(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        $qrOrders = $analytics->sum('qr_orders');
        $totalOrders = $analytics->sum('total_orders');
        $webOrders = $totalOrders - $qrOrders;

        return [
            'labels' => ['Web Orders', 'QR Orders'],
            'data' => [$webOrders, $qrOrders],
            'percentage' => [
                'web' => $totalOrders > 0 ? round(($webOrders / $totalOrders) * 100, 1) : 0,
                'qr' => $totalOrders > 0 ? round(($qrOrders / $totalOrders) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get promotion effectiveness stats
     *
     * @param int $days
     * @return array
     */
    public function getPromotionEffectiveness(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        $totalRevenue = $analytics->sum('total_sales');
        $totalDiscounts = $analytics->sum('promotion_discount_total');
        $promotionUsage = $analytics->sum('promotion_usage_count');

        return [
            'total_usage' => $promotionUsage,
            'total_discounts' => $totalDiscounts,
            'revenue_impact_percentage' => $totalRevenue > 0
                ? round(($totalDiscounts / $totalRevenue) * 100, 2)
                : 0,
            'avg_discount_per_use' => $promotionUsage > 0
                ? round($totalDiscounts / $promotionUsage, 2)
                : 0,
        ];
    }

    /**
     * Get customer retention metrics
     *
     * @param int $days
     * @return array
     */
    public function getCustomerRetention(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        $newCustomers = $analytics->sum('new_customers');
        $returningCustomers = $analytics->sum('returning_customers');
        $totalCustomers = $newCustomers + $returningCustomers;

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'retention_rate' => $totalCustomers > 0
                ? round(($returningCustomers / $totalCustomers) * 100, 1)
                : 0,
        ];
    }
}
