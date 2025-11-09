<?php

namespace App\Listeners;

use App\Events\TableBookingCreatedEvent;
use App\Models\TableReservation;
use App\Services\FCMNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewReservationNotificationToAdmin implements ShouldQueue
{
    use InteractsWithQueue;

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
            Log::info('SendNewReservationNotificationToAdmin: Processing new reservation notification', [
                'reservation_id' => $event->reservationId
            ]);

            // Find the reservation
            $reservation = TableReservation::with(['user', 'table'])->find($event->reservationId);

            if (!$reservation) {
                Log::warning('SendNewReservationNotificationToAdmin: Reservation not found', [
                    'reservation_id' => $event->reservationId
                ]);
                return;
            }

            // Send FCM notification to admin users
            $this->fcmService->sendNewReservationNotificationToAdmin($reservation);

            Log::info('SendNewReservationNotificationToAdmin: Notification sent successfully', [
                'reservation_id' => $event->reservationId
            ]);

        } catch (\Exception $e) {
            Log::error('SendNewReservationNotificationToAdmin: Failed to send notification', [
                'reservation_id' => $event->reservationId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
