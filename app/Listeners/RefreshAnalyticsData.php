<?php

namespace App\Listeners;

use App\Events\AnalyticsRefreshEvent;
use App\Services\AnalyticsRecalculationService;
use Illuminate\Support\Facades\Log;

class RefreshAnalyticsData
{
    protected $analyticsService;

    /**
     * Create the event listener.
     */
    public function __construct(AnalyticsRecalculationService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle the event.
     *
     * @param AnalyticsRefreshEvent $event
     * @return void
     */
    public function handle(AnalyticsRefreshEvent $event): void
    {
        try {
            // Recalculate analytics for the date
            $analytics = $this->analyticsService->recalculateAndSave($event->date);

            // Update event with fresh data for broadcasting
            $event->analyticsData = [
                'total_sales' => $analytics->total_sales,
                'total_orders' => $analytics->total_orders,
                'average_order_value' => $analytics->average_order_value,
                'new_customers' => $analytics->new_customers,
                'returning_customers' => $analytics->returning_customers,
                'qr_orders' => $analytics->qr_orders,
                'qr_revenue' => $analytics->qr_revenue,
                'table_booking_count' => $analytics->table_booking_count,
                'promotion_usage_count' => $analytics->promotion_usage_count,
                'promotion_discount_total' => $analytics->promotion_discount_total,
                'rewards_redeemed_count' => $analytics->rewards_redeemed_count,
            ];

            Log::info('Analytics refreshed successfully', [
                'date' => $event->date->toDateString(),
                'reason' => $event->reason,
                'total_sales' => $analytics->total_sales,
                'total_orders' => $analytics->total_orders,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to refresh analytics', [
                'date' => $event->date->toDateString(),
                'reason' => $event->reason,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
