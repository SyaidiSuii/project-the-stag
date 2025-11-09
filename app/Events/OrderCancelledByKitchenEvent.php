<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByKitchenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $reason;
    public $cancelledBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $reason, ?int $cancelledBy = null)
    {
        $this->order = $order;
        $this->reason = $reason;
        $this->cancelledBy = $cancelledBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen-display'),
            new Channel('admin-alerts'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.cancelled.kitchen';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'confirmation_code' => $this->order->confirmation_code,
            'reason' => $this->reason,
            'cancelled_by' => $this->cancelledBy,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
