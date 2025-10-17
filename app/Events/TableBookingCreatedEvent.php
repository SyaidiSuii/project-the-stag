<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableBookingCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;
    public $analyticsUpdate;

    /**
     * Create a new event instance.
     */
    public function __construct(Reservation $reservation, array $analyticsUpdate = [])
    {
        $this->reservation = $reservation;
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
        return 'booking.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'table_id' => $this->reservation->table_id,
            'guest_count' => $this->reservation->number_of_guests,
            'analytics' => $this->analyticsUpdate,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
