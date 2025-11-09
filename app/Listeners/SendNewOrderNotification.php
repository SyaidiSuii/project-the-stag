<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use App\Services\FCMNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewOrderNotification implements ShouldQueue
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
    public function handle(OrderCreatedEvent $event): void
    {
        try {
            Log::info('SendNewOrderNotification: Processing new order notification', [
                'order_id' => $event->orderId,
                'confirmation_code' => $event->confirmationCode
            ]);

            // Find the order
            $order = Order::with(['user', 'orderItems'])->find($event->orderId);

            if (!$order) {
                Log::warning('SendNewOrderNotification: Order not found', [
                    'order_id' => $event->orderId
                ]);
                return;
            }

            // Send FCM notification to admin users
            $this->fcmService->sendNewOrderNotificationToAdmin($order);

            Log::info('SendNewOrderNotification: Notification sent successfully', [
                'order_id' => $event->orderId
            ]);

        } catch (\Exception $e) {
            Log::error('SendNewOrderNotification: Failed to send notification', [
                'order_id' => $event->orderId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
