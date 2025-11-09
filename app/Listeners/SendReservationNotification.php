<?php

namespace App\Listeners;

use App\Events\TableBookingCreatedEvent;
use App\Services\FCMNotificationService;
use App\Models\TableReservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReservationNotification implements ShouldQueue
{
    protected FCMNotificationService $fcmService;

    /**
     * Create the event listener.
     */
    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Handle the event.
     */
    public function handle(TableBookingCreatedEvent $event): void
    {
        try {
            // Get the reservation
            $reservation = TableReservation::find($event->reservationId);

            if (!$reservation) {
                Log::error('Reservation not found for notification', ['reservation_id' => $event->reservationId]);
                return;
            }

            // Check if notifications are enabled
            if (!config('services.fcm.enabled', true)) {
                Log::info('FCM notifications disabled', ['reservation_id' => $reservation->id]);
                return;
            }

            // Send FCM notification for reservation confirmation
            $this->fcmService->sendReservationNotification($reservation, 'confirmed');

            Log::info('Reservation notification sent via FCM', [
                'reservation_id' => $reservation->id,
                'table_id' => $reservation->table_id,
                'booking_date' => $reservation->booking_date,
                'booking_time' => $reservation->booking_time,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send reservation notification', [
                'reservation_id' => $event->reservationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

