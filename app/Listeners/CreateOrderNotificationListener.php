<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Models\AdminNotification;
use App\Models\User;
use App\Notifications\AdminRealtimeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

use App\Models\Order;

class CreateOrderNotificationListener
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
    public function handle(OrderCreatedEvent $event): void
    {
        $order = $event->order;

        if (!$order) {
            return; // or log an error
        }

        // Ensure relationships are loaded, for consistency.
        $order->load(['user', 'items']);

        $adminNotification = AdminNotification::create([
            'type' => 'new_order',
            'title' => 'New Order Received',
            'message' => 'Order #' . $order->confirmation_code . ' for RM ' . number_format($order->total_amount, 2) . ' has been placed.',
            'link' => route('admin.order.show', $order->id),
        ]);

        // Get all admins and managers
        $admins = User::role(['admin', 'manager', 'super-admin'])->get();

        // DEBUG: Log how many admins were found
        Log::info('Found ' . $admins->count() . ' admins/managers to notify for new order.', [
            'order_id' => $order->id,
            'admin_ids' => $admins->pluck('id')->toArray()
        ]);

        // Send real-time notification
        Notification::send($admins, new AdminRealtimeNotification($adminNotification));
    }
}
