<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $failureReason;
    public $errorDetails;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $failureReason, ?array $errorDetails = null)
    {
        $this->order = $order;
        $this->failureReason = $failureReason;
        $this->errorDetails = $errorDetails;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-alerts'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment.failed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'confirmation_code' => $this->order->confirmation_code,
            'failure_reason' => $this->failureReason,
            'error_details' => $this->errorDetails,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
