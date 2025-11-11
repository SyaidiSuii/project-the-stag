<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $oldPaymentStatus;
    public string $newPaymentStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $oldPaymentStatus, string $updatedBy = 'system')
    {
        // Load necessary relationships for the broadcast
        $this->order = $order->load(['items.menuItem', 'table']);
        $this->oldPaymentStatus = $oldPaymentStatus;
        $this->newPaymentStatus = $order->payment_status;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('kitchen-display'), // For the kitchen
        ];

        // Add a channel for the specific order tracking page if it has a session token
        if ($this->order->session_token) {
            $channels[] = new Channel('order-track.' . $this->order->session_token);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        // Broadcast the entire order object with its relationships
        // Plus payment status change data for frontend listeners
        return [
            'order' => $this->order->toArray(),
            'table_number' => $this->order->table ? $this->order->table->table_number : null,
            'total_amount' => $this->order->total_amount,
            'estimated_time' => $this->order->estimated_ready_time,
            'items' => $this->order->items,
            // Payment status change tracking for frontend
            'old_payment_status' => $this->oldPaymentStatus,
            'new_payment_status' => $this->newPaymentStatus,
            'order_id' => $this->order->id, // Also add at root level for easier access
        ];
    }
}
