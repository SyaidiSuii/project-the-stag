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
                    // Get the order model from the event.
                    // It might be a "hollow" model after being unserialized from the queue.
                    $order = $event->order;
        
                    if (!$order || !$order->id) {
                        Log::warning('SendNewOrderNotification: Event was fired without a valid order object.');
                        return;
                    }
        
                    // Eager load the relationships we need, just in case they were lost during queueing.
                    $order->load(['user', 'items']);
        
                    Log::info('SendNewOrderNotification: Processing new order notification', [
                        'order_id' => $order->id,
                        'confirmation_code' => $order->confirmation_code
                    ]);
        
                    // Send FCM notification to admin users
                    $this->fcmService->sendNewOrderNotificationToAdmin($order);
        
                    Log::info('SendNewOrderNotification: Notification sent successfully', [
                        'order_id' => $order->id
                    ]);
        
                } catch (\Exception $e) {
                    Log::error('SendNewOrderNotification: Failed to send notification', [
                        'order_id' => $event->order->id ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Re-throw the exception to ensure the job is marked as failed
                    throw $e;
                }
            }}
