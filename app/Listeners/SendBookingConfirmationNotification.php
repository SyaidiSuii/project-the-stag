<?php

namespace App\Listeners;

use App\Events\TableBookingConfirmedEvent;
use App\Models\TableReservation;
use App\Services\FCMNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationNotification implements ShouldQueue
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
    public function handle(TableBookingConfirmedEvent $event): void
    {
        try {
            $reservation = $event->reservation;

            Log::info('SendBookingConfirmationNotification: Processing confirmed booking notification', [
                'reservation_id' => $reservation->id
            ]);

            // 1. Send notification to the customer
            if ($reservation->user) {
                // This method will be a wrapper around the existing sendReservationNotification
                $this->fcmService->sendBookingConfirmedNotificationToCustomer($reservation);
                Log::info('Confirmation notification sent to customer', ['user_id' => $reservation->user_id]);
            } else {
                Log::info('Customer is a guest, skipping FCM notification to customer.', ['guest_email' => $reservation->guest_email]);
            }

            // 2. Send a confirmation receipt/log notification to the admin
            // This is a lower priority notification to confirm the action was successful.
            $this->fcmService->sendBookingConfirmedNotificationToAdmin($reservation);
            Log::info('Confirmation receipt sent to admin', ['reservation_id' => $reservation->id]);

        } catch (\Exception $e) {
            Log::error('SendBookingConfirmationNotification: Failed to send notification', [
                'reservation_id' => $event->reservation->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
