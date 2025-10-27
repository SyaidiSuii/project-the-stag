<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $newStatus;
    public $oldStatus;
    public $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $oldStatus, string $updatedBy = 'system')
    {
        $this->orderId = $order->id;
        $this->newStatus = $order->order_status;
        $this->oldStatus = $oldStatus;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen-display'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'new_status' => $this->newStatus,
            'old_status' => $this->oldStatus,
            'updated_by' => $this->updatedBy,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
