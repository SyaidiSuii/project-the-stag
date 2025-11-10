<?php

namespace App\Events;

use App\Models\TableReservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableBookingConfirmedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;
    public $reservationId;

    /**
     * Create a new event instance.
     */
    public function __construct(TableReservation $reservation)
    {
        $this->reservation = $reservation;
        $this->reservationId = $reservation->id;
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
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.confirmed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservationId,
            'status' => $this->reservation->status,
            'customer_name' => $this->reservation->guest_name,
            'table_number' => $this->reservation->table->table_number ?? 'N/A',
            'message' => "Booking #{$this->reservationId} has been confirmed.",
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
