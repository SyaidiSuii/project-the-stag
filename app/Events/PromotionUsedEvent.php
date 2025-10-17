<?php

namespace App\Events;

use App\Models\PromotionUsageLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromotionUsedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $promotionUsage;
    public $analyticsUpdate;

    /**
     * Create a new event instance.
     */
    public function __construct(PromotionUsageLog $promotionUsage, array $analyticsUpdate = [])
    {
        $this->promotionUsage = $promotionUsage;
        $this->analyticsUpdate = $analyticsUpdate;
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
        return 'promotion.used';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'promotion_id' => $this->promotionUsage->promotion_id,
            'discount_amount' => $this->promotionUsage->discount_amount,
            'analytics' => $this->analyticsUpdate,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
