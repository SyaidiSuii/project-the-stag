<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalyticsRefreshEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $date;
    public $analyticsData;
    public $reason;

    /**
     * Create a new event instance.
     *
     * @param Carbon|string $date The date to recalculate analytics for
     * @param array $analyticsData Fresh analytics data (will be set by listener)
     * @param string $reason Reason for refresh (for logging)
     */
    public function __construct($date = null, array $analyticsData = [], string $reason = 'generic')
    {
        $this->date = $date instanceof Carbon ? $date : ($date ? Carbon::parse($date) : today());
        $this->analyticsData = $analyticsData;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('analytics-updates'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'analytics.refresh';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'date' => $this->date->toDateString(),
            'reason' => $this->reason,
            'analytics' => [
                'total_revenue' => (float) ($this->analyticsData['total_sales'] ?? 0),
                'total_orders' => (int) ($this->analyticsData['total_orders'] ?? 0),
                'avg_order_value' => (float) ($this->analyticsData['average_order_value'] ?? 0),
                'new_customers' => (int) ($this->analyticsData['new_customers'] ?? 0),
                'returning_customers' => (int) ($this->analyticsData['returning_customers'] ?? 0),
                'qr_orders' => (int) ($this->analyticsData['qr_orders'] ?? 0),
                'qr_revenue' => (float) ($this->analyticsData['qr_revenue'] ?? 0),
                'table_bookings' => (int) ($this->analyticsData['table_booking_count'] ?? 0),
                'promotions_used' => (int) ($this->analyticsData['promotion_usage_count'] ?? 0),
                'promotion_discounts' => (float) ($this->analyticsData['promotion_discount_total'] ?? 0),
                'rewards_redeemed' => (int) ($this->analyticsData['rewards_redeemed_count'] ?? 0),
            ],
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
