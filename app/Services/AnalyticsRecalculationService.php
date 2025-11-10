<?php

namespace App\Services;

use App\Models\SaleAnalytics;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableReservation;
use App\Models\TableQrcode;
use App\Models\PromotionUsageLog;
use App\Models\CustomerReward;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsRecalculationService
{
    protected $auditService;

    public function __construct(ReportAuditService $auditService = null)
    {
        $this->auditService = $auditService ?? app(ReportAuditService::class);
    }
    /**
     * Recalculate analytics for a specific date
     * Returns calculated data (does NOT save to database)
     *
     * @param Carbon|string $date
     * @return array
     */
    public function calculateForDate($date): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Get all qualified orders for the date
        // Only completed/served AND paid orders count towards revenue
        $orders = Order::whereIn('order_status', ['completed', 'served'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', $date)
            ->with(['items.menuItem', 'user'])
            ->get();

        if ($orders->isEmpty()) {
            return $this->getZeroAnalytics();
        }

        // === REVENUE & ORDERS ===
        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // === ORDER TYPES ===
        $dineInOrders = $orders->where('order_type', 'dine_in')->count();
        $takeawayOrders = $orders->where('order_type', 'takeaway')->count();
        $deliveryOrders = $orders->where('order_type', 'delivery')->count();
        $qrOrders = $orders->where('order_source', 'qr_scan')->count();
        $mobileOrders = $orders->where('order_source', 'mobile')->count();

        // === REVENUE BY TYPE ===
        $revenueDineIn = $orders->where('order_type', 'dine_in')->sum('total_amount');
        $revenueTakeaway = $orders->where('order_type', 'takeaway')->sum('total_amount');
        $revenueDelivery = $orders->where('order_type', 'delivery')->sum('total_amount');

        // === CUSTOMER METRICS ===
        $uniqueCustomers = $orders->whereNotNull('user_id')->pluck('user_id')->unique()->count();

        // New customers (first order is on this date)
        $newCustomers = User::whereHas('orders', function ($query) use ($date) {
            $query->where('payment_status', 'paid')
                ->whereDate('created_at', $date);
        })->whereDoesntHave('orders', function ($query) use ($date) {
            $query->where('payment_status', 'paid')
                ->whereDate('created_at', '<', $date);
        })->count();

        $returningCustomers = $uniqueCustomers - $newCustomers;

        // === POPULAR ITEMS ===
        $popularItems = OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('menu_item_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('menuItem:id,name,price')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->menu_item_id,
                    'name' => $item->menuItem->name ?? 'Unknown',
                    'quantity' => $item->total_quantity,
                ];
            })
            ->toArray();

        // === PEAK HOURS ===
        $peakHours = $this->calculatePeakHours($orders);

        // === PREPARATION TIME ===
        $avgPrepTime = $orders->whereNotNull('estimated_completion_time')
            ->whereNotNull('order_time')
            ->map(function ($order) {
                return $order->order_time->diffInMinutes($order->estimated_completion_time);
            })
            ->average() ?? 0;

        // === TABLE BOOKINGS ===
        $bookingStats = $this->getBookingStats($date);

        // === QR CODE SESSIONS ===
        $qrStats = $this->getQrStats($date);

        // === PROMOTIONS USAGE ===
        $promotionStats = $this->getPromotionStats($date);

        // === REWARDS REDEEMED ===
        $rewardsRedeemed = CustomerReward::whereDate('redeemed_at', $date)
            ->where('status', 'redeemed')
            ->count();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'peak_hours' => $peakHours,
            'popular_items' => $popularItems,
            'unique_customers' => $uniqueCustomers,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'dine_in_orders' => $dineInOrders,
            'takeaway_orders' => $takeawayOrders,
            'delivery_orders' => $deliveryOrders,
            'mobile_orders' => $mobileOrders,
            'qr_orders' => $qrOrders,
            'total_revenue_dine_in' => $revenueDineIn,
            'total_revenue_takeaway' => $revenueTakeaway,
            'total_revenue_delivery' => $revenueDelivery,
            'average_preparation_time' => $avgPrepTime,
            'qr_session_count' => $qrStats['session_count'],
            'qr_revenue' => $qrStats['revenue'],
            'table_booking_count' => $bookingStats['total_bookings'],
            'table_utilization_rate' => $bookingStats['utilization_rate'],
            'promotion_usage_count' => $promotionStats['usage_count'],
            'promotion_discount_total' => $promotionStats['discount_total'],
            'rewards_redeemed_count' => $rewardsRedeemed,
        ];
    }

    /**
     * Recalculate and save analytics for a specific date
     *
     * @param Carbon|string $date
     * @return SaleAnalytics
     */
    public function recalculateAndSave($date): SaleAnalytics
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Get old data for audit trail
        $oldAnalytics = SaleAnalytics::whereDate('date', $date)->first();
        $oldData = $oldAnalytics ? $oldAnalytics->toArray() : [];

        // Calculate new analytics
        $analyticsData = $this->calculateForDate($date);

        // Log calculation
        $this->auditService->logCalculation($date, $analyticsData, 'recalculation');

        // Save to database
        $analytics = SaleAnalytics::updateOrCreate(
            ['date' => $date->toDateString()],
            $analyticsData
        );

        // Log update if there were changes
        if (!empty($oldData)) {
            $this->auditService->logUpdate($date, $oldData, $analyticsData, 'recalculation');
        }

        Log::info('Analytics recalculated', [
            'date' => $date->toDateString(),
            'total_sales' => $analyticsData['total_sales'],
            'total_orders' => $analyticsData['total_orders'],
        ]);

        return $analytics;
    }

    /**
     * Get zero analytics structure
     *
     * @return array
     */
    private function getZeroAnalytics(): array
    {
        return [
            'total_sales' => 0,
            'total_orders' => 0,
            'average_order_value' => 0,
            'peak_hours' => [],
            'popular_items' => [],
            'unique_customers' => 0,
            'new_customers' => 0,
            'returning_customers' => 0,
            'dine_in_orders' => 0,
            'takeaway_orders' => 0,
            'delivery_orders' => 0,
            'mobile_orders' => 0,
            'qr_orders' => 0,
            'total_revenue_dine_in' => 0,
            'total_revenue_takeaway' => 0,
            'total_revenue_delivery' => 0,
            'average_preparation_time' => 0,
            'qr_session_count' => 0,
            'qr_revenue' => 0,
            'table_booking_count' => 0,
            'table_utilization_rate' => 0,
            'promotion_usage_count' => 0,
            'promotion_discount_total' => 0,
            'rewards_redeemed_count' => 0,
        ];
    }

    /**
     * Calculate peak hours from orders
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function calculatePeakHours($orders): array
    {
        $hourCounts = [];

        foreach ($orders as $order) {
            $hour = $order->order_time ? $order->order_time->format('H') : $order->created_at->format('H');

            if (!isset($hourCounts[$hour])) {
                $hourCounts[$hour] = 0;
            }
            $hourCounts[$hour]++;
        }

        arsort($hourCounts);

        return array_slice($hourCounts, 0, 5, true); // Top 5 peak hours
    }

    /**
     * Get table booking statistics
     *
     * @param Carbon $date
     * @return array
     */
    private function getBookingStats(Carbon $date): array
    {
        $totalBookings = TableReservation::whereDate('booking_date', $date)
            ->whereIn('status', ['confirmed', 'seated', 'completed'])
            ->count();

        $completedBookings = TableReservation::whereDate('booking_date', $date)
            ->where('status', 'completed')
            ->count();

        $utilizationRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;

        return [
            'total_bookings' => $totalBookings,
            'utilization_rate' => round($utilizationRate, 2),
        ];
    }

    /**
     * Get QR code session statistics
     *
     * @param Carbon $date
     * @return array
     */
    private function getQrStats(Carbon $date): array
    {
        $sessions = TableQrcode::whereDate('started_at', $date)
            ->where('status', 'completed')
            ->get();

        $sessionCount = $sessions->count();

        $qrRevenue = Order::where('order_source', 'qr_scan')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', $date)
            ->sum('total_amount');

        return [
            'session_count' => $sessionCount,
            'revenue' => $qrRevenue,
        ];
    }

    /**
     * Get promotion usage statistics
     *
     * @param Carbon $date
     * @return array
     */
    private function getPromotionStats(Carbon $date): array
    {
        $usageLogs = PromotionUsageLog::whereDate('used_at', $date)->get();

        return [
            'usage_count' => $usageLogs->count(),
            'discount_total' => $usageLogs->sum('discount_amount') ?? 0,
        ];
    }
}
