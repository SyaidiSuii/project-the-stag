<?php

namespace App\Listeners;

use App\Events\RewardRedeemedEvent;
use App\Models\SaleAnalytics;
use Illuminate\Support\Facades\Log;

class UpdateAnalyticsOnRewardRedeemed
{
    /**
     * Handle the event.
     */
    public function handle(RewardRedeemedEvent $event): void
    {
        try {
            $today = now()->toDateString();

            $analytics = SaleAnalytics::firstOrCreate(
                ['date' => $today],
                $this->getDefaultAnalytics($today)
            );

            $analytics->rewards_redeemed += 1;
            $analytics->save();

            Log::info('Real-time analytics updated for reward redemption', [
                'reward_id' => $event->redemption->reward_id,
                'points_used' => $event->redemption->points_used,
                'analytics_date' => $today,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update analytics on reward redeemed', [
                'reward_id' => $event->redemption->reward_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getDefaultAnalytics(string $date): array
    {
        return [
            'total_revenue' => 0,
            'total_orders' => 0,
            'average_order_value' => 0,
            'dine_in_orders' => 0,
            'takeaway_orders' => 0,
            'delivery_orders' => 0,
            'qr_orders' => 0,
            'qr_revenue' => 0,
            'table_bookings' => 0,
            'promotions_used' => 0,
            'total_discounts' => 0,
            'rewards_redeemed' => 0,
            'new_customers' => 0,
            'returning_customers' => 0,
            'customer_retention_rate' => 0,
            'paid_orders' => 0,
            'pending_orders' => 0,
            'cancelled_orders' => 0,
        ];
    }
}
