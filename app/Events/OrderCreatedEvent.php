<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $confirmationCode;
    public $customerName;
    public $itemCount;
    public $total;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->orderId = $order->id;
        $this->confirmationCode = $order->confirmation_code;
        $this->customerName = $order->user ? $order->user->name : 'Guest';
        $this->itemCount = $order->orderItems->count();
        $this->total = $order->final_total;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'confirmation_code' => $this->confirmationCode,
            'customer_name' => $this->customerName,
            'item_count' => $this->itemCount,
            'total' => $this->total,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
