<?php

namespace App\Listeners;

use App\Events\TableBookingCreatedEvent;
use App\Models\SaleAnalytics;
use Illuminate\Support\Facades\Log;

class UpdateAnalyticsOnTableBooking
{
    /**
     * Handle the event.
     */
    public function handle(TableBookingCreatedEvent $event): void
    {
        try {
            $today = now()->toDateString();

            $analytics = SaleAnalytics::firstOrCreate(
                ['date' => $today],
                $this->getDefaultAnalytics($today)
            );

            $analytics->table_booking_count += 1;
            $analytics->save();

            Log::info('Real-time analytics updated for table booking', [
                'reservation_id' => $event->reservation->id,
                'table_id' => $event->reservation->table_id,
                'analytics_date' => $today,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update analytics on table booking', [
                'reservation_id' => $event->reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getDefaultAnalytics(string $date): array
    {
        return [
            'total_sales' => 0,
            'total_orders' => 0,
            'average_order_value' => 0,
            'unique_customers' => 0,
            'new_customers' => 0,
            'returning_customers' => 0,
            'dine_in_orders' => 0,
            'takeaway_orders' => 0,
            'delivery_orders' => 0,
            'mobile_orders' => 0,
            'qr_orders' => 0,
            'qr_session_count' => 0,
            'qr_revenue' => 0,
            'table_booking_count' => 0,
            'table_utilization_rate' => 0,
            'promotion_usage_count' => 0,
            'promotion_discount_total' => 0,
            'rewards_redeemed_count' => 0,
            'total_revenue_dine_in' => 0,
            'total_revenue_takeaway' => 0,
            'total_revenue_delivery' => 0,
        ];
    }
}
