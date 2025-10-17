<?php

namespace App\Events;

use App\Models\RewardRedemption;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RewardRedeemedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $redemption;
    public $analyticsUpdate;

    /**
     * Create a new event instance.
     */
    public function __construct(RewardRedemption $redemption, array $analyticsUpdate = [])
    {
        $this->redemption = $redemption;
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
        return 'reward.redeemed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'reward_id' => $this->redemption->reward_id,
            'points_used' => $this->redemption->points_used,
            'analytics' => $this->analyticsUpdate,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
