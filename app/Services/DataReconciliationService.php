<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SaleAnalytics;
use App\Models\TableReservation;
use App\Models\TableQrcode;
use App\Models\PromotionUsageLog;
use App\Models\CustomerReward;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Data Reconciliation Service
 * Validates analytics data accuracy and detects discrepancies
 */
class DataReconciliationService
{
    /**
     * Reconcile analytics data for a specific date
     * Compares stored analytics with real-time calculations
     *
     * @param Carbon|string $date
     * @return array
     */
    public function reconcileDate($date): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Get stored analytics
        $storedAnalytics = SaleAnalytics::whereDate('date', $date)->first();

        if (!$storedAnalytics) {
            return [
                'status' => 'missing',
                'message' => 'No analytics data found for this date',
                'date' => $date->toDateString(),
                'discrepancies' => [],
            ];
        }

        // Calculate real-time analytics
        $realTimeData = $this->calculateRealTimeAnalytics($date);

        // Compare and detect discrepancies
        $discrepancies = $this->detectDiscrepancies($storedAnalytics->toArray(), $realTimeData);

        return [
            'status' => empty($discrepancies) ? 'accurate' : 'discrepancy_found',
            'date' => $date->toDateString(),
            'stored_analytics' => $storedAnalytics->toArray(),
            'real_time_analytics' => $realTimeData,
            'discrepancies' => $discrepancies,
            'accuracy_percentage' => $this->calculateAccuracyPercentage($discrepancies),
        ];
    }

    /**
     * Reconcile analytics for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function reconcileDateRange(Carbon $startDate, Carbon $endDate): array
    {
        $results = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $results[] = $this->reconcileDate($date);
        }

        return [
            'total_days_checked' => count($results),
            'accurate_days' => collect($results)->where('status', 'accurate')->count(),
            'days_with_discrepancies' => collect($results)->where('status', 'discrepancy_found')->count(),
            'missing_data_days' => collect($results)->where('status', 'missing')->count(),
            'details' => $results,
        ];
    }

    /**
     * Calculate real-time analytics from raw data
     *
     * @param Carbon $date
     * @return array
     */
    private function calculateRealTimeAnalytics(Carbon $date): array
    {
        // Get qualified orders
        $orders = Order::whereIn('order_status', ['completed', 'served'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', $date)
            ->with(['items.menuItem', 'user'])
            ->get();

        if ($orders->isEmpty()) {
            return $this->getZeroAnalytics();
        }

        // Calculate metrics
        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Customer metrics
        $uniqueCustomers = $orders->whereNotNull('user_id')->pluck('user_id')->unique()->count();

        // Order types
        $dineInOrders = $orders->where('order_type', 'dine_in')->count();
        $takeawayOrders = $orders->where('order_type', 'takeaway')->count();
        $deliveryOrders = $orders->where('order_type', 'delivery')->count();
        $qrOrders = $orders->where('order_source', 'qr_scan')->count();

        // Revenue by type
        $revenueDineIn = $orders->where('order_type', 'dine_in')->sum('total_amount');
        $revenueTakeaway = $orders->where('order_type', 'takeaway')->sum('total_amount');
        $revenueDelivery = $orders->where('order_type', 'delivery')->sum('total_amount');

        // QR Stats
        $qrRevenue = Order::where('order_source', 'qr_scan')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', $date)
            ->sum('total_amount');

        $qrSessionCount = TableQrcode::whereDate('started_at', $date)
            ->where('status', 'completed')
            ->count();

        // Table bookings
        $tableBookingCount = TableReservation::whereDate('booking_date', $date)
            ->whereIn('status', ['confirmed', 'seated', 'completed'])
            ->count();

        // Promotions
        $promotionUsageCount = PromotionUsageLog::whereDate('used_at', $date)->count();
        $promotionDiscountTotal = PromotionUsageLog::whereDate('used_at', $date)
            ->sum('discount_amount') ?? 0;

        // Rewards
        $rewardsRedeemedCount = CustomerReward::whereDate('redeemed_at', $date)
            ->where('status', 'redeemed')
            ->count();

        return [
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'unique_customers' => $uniqueCustomers,
            'dine_in_orders' => $dineInOrders,
            'takeaway_orders' => $takeawayOrders,
            'delivery_orders' => $deliveryOrders,
            'qr_orders' => $qrOrders,
            'total_revenue_dine_in' => round($revenueDineIn, 2),
            'total_revenue_takeaway' => round($revenueTakeaway, 2),
            'total_revenue_delivery' => round($revenueDelivery, 2),
            'qr_revenue' => round($qrRevenue, 2),
            'qr_session_count' => $qrSessionCount,
            'table_booking_count' => $tableBookingCount,
            'promotion_usage_count' => $promotionUsageCount,
            'promotion_discount_total' => round($promotionDiscountTotal, 2),
            'rewards_redeemed_count' => $rewardsRedeemedCount,
        ];
    }

    /**
     * Detect discrepancies between stored and real-time data
     *
     * @param array $stored
     * @param array $realTime
     * @return array
     */
    private function detectDiscrepancies(array $stored, array $realTime): array
    {
        $discrepancies = [];
        $thresholdPercentage = 0.01; // 1% tolerance for rounding differences

        $fieldsToCheck = [
            'total_sales',
            'total_orders',
            'average_order_value',
            'unique_customers',
            'dine_in_orders',
            'takeaway_orders',
            'delivery_orders',
            'qr_orders',
            'total_revenue_dine_in',
            'total_revenue_takeaway',
            'total_revenue_delivery',
            'qr_revenue',
            'qr_session_count',
            'table_booking_count',
            'promotion_usage_count',
            'promotion_discount_total',
            'rewards_redeemed_count',
        ];

        foreach ($fieldsToCheck as $field) {
            $storedValue = $stored[$field] ?? 0;
            $realTimeValue = $realTime[$field] ?? 0;

            // Skip if both are zero
            if ($storedValue == 0 && $realTimeValue == 0) {
                continue;
            }

            // Calculate percentage difference
            $maxValue = max(abs($storedValue), abs($realTimeValue));
            $difference = abs($storedValue - $realTimeValue);
            $percentageDiff = $maxValue > 0 ? ($difference / $maxValue) * 100 : 0;

            // Flag if difference exceeds threshold
            if ($percentageDiff > $thresholdPercentage) {
                $discrepancies[] = [
                    'field' => $field,
                    'stored_value' => $storedValue,
                    'real_time_value' => $realTimeValue,
                    'difference' => $difference,
                    'percentage_diff' => round($percentageDiff, 2),
                    'severity' => $this->getSeverityLevel($percentageDiff),
                ];
            }
        }

        return $discrepancies;
    }

    /**
     * Calculate overall accuracy percentage
     *
     * @param array $discrepancies
     * @return float
     */
    private function calculateAccuracyPercentage(array $discrepancies): float
    {
        if (empty($discrepancies)) {
            return 100.0;
        }

        $totalFields = 17; // Number of fields checked
        $fieldsWithDiscrepancies = count($discrepancies);
        $accurateFields = $totalFields - $fieldsWithDiscrepancies;

        return round(($accurateFields / $totalFields) * 100, 2);
    }

    /**
     * Get severity level based on percentage difference
     *
     * @param float $percentageDiff
     * @return string
     */
    private function getSeverityLevel(float $percentageDiff): string
    {
        if ($percentageDiff > 20) {
            return 'critical';
        } elseif ($percentageDiff > 10) {
            return 'high';
        } elseif ($percentageDiff > 5) {
            return 'medium';
        } else {
            return 'low';
        }
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
            'unique_customers' => 0,
            'dine_in_orders' => 0,
            'takeaway_orders' => 0,
            'delivery_orders' => 0,
            'qr_orders' => 0,
            'total_revenue_dine_in' => 0,
            'total_revenue_takeaway' => 0,
            'total_revenue_delivery' => 0,
            'qr_revenue' => 0,
            'qr_session_count' => 0,
            'table_booking_count' => 0,
            'promotion_usage_count' => 0,
            'promotion_discount_total' => 0,
            'rewards_redeemed_count' => 0,
        ];
    }

    /**
     * Auto-fix discrepancies by recalculating and updating stored analytics
     *
     * @param Carbon|string $date
     * @return array
     */
    public function autoFixDiscrepancies($date): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        $reconciliationResult = $this->reconcileDate($date);

        if ($reconciliationResult['status'] === 'accurate') {
            return [
                'success' => true,
                'message' => 'No discrepancies found. Data is already accurate.',
            ];
        }

        if ($reconciliationResult['status'] === 'missing') {
            return [
                'success' => false,
                'message' => 'No analytics data to fix. Please run analytics generation first.',
            ];
        }

        // Update with real-time data
        $updated = SaleAnalytics::whereDate('date', $date)->update(
            $reconciliationResult['real_time_analytics']
        );

        Log::info('Analytics data auto-fixed', [
            'date' => $date->toDateString(),
            'discrepancies_fixed' => count($reconciliationResult['discrepancies']),
        ]);

        return [
            'success' => true,
            'message' => 'Discrepancies fixed successfully',
            'fixed_fields' => count($reconciliationResult['discrepancies']),
            'discrepancies' => $reconciliationResult['discrepancies'],
        ];
    }
}
