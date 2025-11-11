<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdatedEvent;
use App\Services\FCMNotificationService;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderStatusNotification implements ShouldQueue
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
    public function handle(OrderStatusUpdatedEvent $event): void
    {
        try {
            // Get the order from the event
            $order = $event->order;

            if (!$order) {
                // This case should ideally not happen if the event is constructed correctly
                Log::error('Order object not found in OrderStatusUpdatedEvent');
                return;
            }

            // Check if notifications are enabled
            if (!config('services.fcm.enabled', true)) {
                Log::info('FCM notifications disabled', ['order_id' => $order->id]);
                return;
            }

            // Only send notification for specific statuses: preparing, ready, completed
            $notifiableStatuses = ['preparing', 'ready', 'completed'];

            if (!in_array($event->newStatus, $notifiableStatuses)) {
                Log::info('FCM notification skipped (status not in notifiable list)', [
                    'order_id' => $order->id,
                    'status' => $event->newStatus,
                    'notifiable_statuses' => implode(', ', $notifiableStatuses)
                ]);
                return;
            }

            // Send FCM notification
            $result = $this->fcmService->sendOrderStatusNotification($order);

            if ($result) {
                Log::info('✓ Order status notification sent via FCM', [
                    'order_id' => $order->id,
                    'order_number' => $order->confirmation_code ?? $order->id,
                    'old_status' => $event->oldStatus,
                    'new_status' => $event->newStatus,
                ]);
            } else {
                Log::warning('✗ Order status notification FAILED to send', [
                    'order_id' => $order->id,
                    'order_status' => $order->order_status,
                    'old_status' => $event->oldStatus,
                    'new_status' => $event->newStatus,
                    'reason' => 'sendOrderStatusNotification returned false'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order status notification', [
                'order_id' => $event->order->id ?? 'N/A',
                'error' => $e->getMessage(),
            ]);
        }
    }
}

