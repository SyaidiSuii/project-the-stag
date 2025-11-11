<?php

namespace App\Listeners;

use App\Events\TableBookingCreatedEvent;
use App\Models\AdminNotification;
use App\Models\User;
use App\Notifications\AdminRealtimeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class CreateBookingNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TableBookingCreatedEvent $event): void
    {
        $booking = $event->reservation;

        $adminNotification = AdminNotification::create([
            'type' => 'new_booking',
            'title' => 'New Table Booking',
            'message' => 'A new booking for ' . $booking->party_size . ' people has been made by ' . $booking->guest_name . '.',
            'link' => route('admin.table-reservation.show', $booking->id),
        ]);

        // Get all admins and managers
        $admins = User::role(['admin', 'manager', 'super-admin'])->get();

        // Send real-time notification
        Notification::send($admins, new AdminRealtimeNotification($adminNotification));
    }
}
