<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemUnavailableEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $unavailableItems;
    public $reportedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, array $unavailableItems, ?int $reportedBy = null)
    {
        $this->order = $order;
        $this->unavailableItems = $unavailableItems;
        $this->reportedBy = $reportedBy;
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
        return 'order.item.unavailable';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'confirmation_code' => $this->order->confirmation_code,
            'unavailable_items' => $this->unavailableItems,
            'reported_by' => $this->reportedBy,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
