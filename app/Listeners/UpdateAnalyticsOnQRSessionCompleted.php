<?php

namespace App\Listeners;

use App\Events\QRSessionCompletedEvent;
use App\Models\SaleAnalytics;
use Illuminate\Support\Facades\Log;

class UpdateAnalyticsOnQRSessionCompleted
{
    /**
     * Handle the event.
     */
    public function handle(QRSessionCompletedEvent $event): void
    {
        try {
            $completionDate = $event->completedAt->toDateString();
            
            // Get or create analytics record for the completion date
            $analytics = SaleAnalytics::firstOrCreate(
                ['date' => $completionDate],
                $this->getDefaultAnalytics($completionDate)
            );
            
            // Increment QR session count
            $analytics->qr_session_count += 1;
            $analytics->save();
            
            Log::info('Analytics updated for QR session completion', [
                'session_id' => $event->tableQrcode->id,
                'completion_date' => $completionDate,
                'qr_session_count' => $analytics->qr_session_count,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update analytics on QR session completion', [
                'session_id' => $event->tableQrcode->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Get default analytics data structure
     */
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