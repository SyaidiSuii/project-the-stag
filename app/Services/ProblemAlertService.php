<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProblemAlertService
{
    protected FCMNotificationService $fcmService;

    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send delay alert notification
     */
    public function sendDelayAlert(Order $order, int $delayMinutes): bool
    {
        try {
            Log::info('Sending order delay alert', [
                'order_id' => $order->id,
                'delay_minutes' => $delayMinutes
            ]);

            // Log activity
            OrderActivityLog::logActivity(
                $order->id,
                OrderActivityLog::TYPE_WARNING,
                'Order Delayed',
                "Order is delayed by {$delayMinutes} minutes beyond ETA",
                [
                    'delay_minutes' => $delayMinutes,
                    'estimated_time' => $order->estimated_time,
                    'current_status' => $order->order_status,
                ]
            );

            // Send notification to customer
            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerNotification = [
                'title' => 'Order Taking Longer Than Expected ⏱️',
                'body' => "We apologize! Your order {$orderNumber} is delayed by approximately {$delayMinutes} minutes. We're working hard to get it to you!",
                'data' => [
                    'type' => 'order_delay',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'delay_minutes' => (string) $delayMinutes,
                    'click_action' => '/customer/orders/' . $order->id,
                ],
            ];

            $customerSent = false;
            if ($order->user_id) {
                $customerSent = $this->fcmService->sendToUser(
                    $order->user_id,
                    $customerNotification,
                    $order->id,
                    null
                );
            }

            // Send notification to admins
            $this->sendDelayAlertToAdmins($order, $delayMinutes);

            return $customerSent;

        } catch (\Exception $e) {
            Log::error('Failed to send delay alert', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send kitchen cancellation alert
     */
    public function sendKitchenCancellationAlert(Order $order, string $reason, ?int $cancelledBy = null): bool
    {
        try {
            Log::info('Sending kitchen cancellation alert', [
                'order_id' => $order->id,
                'reason' => $reason
            ]);

            // Log activity
            OrderActivityLog::logActivity(
                $order->id,
                OrderActivityLog::TYPE_CRITICAL,
                'Order Cancelled by Kitchen',
                "Order cancelled by kitchen. Reason: {$reason}",
                [
                    'reason' => $reason,
                    'cancelled_by_user_id' => $cancelledBy,
                ],
                $cancelledBy
            );

            // Send notification to customer
            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerNotification = [
                'title' => 'Order Update Required ⚠️',
                'body' => "We're sorry! Your order {$orderNumber} needs attention. Reason: {$reason}. Please contact us.",
                'data' => [
                    'type' => 'order_cancelled_kitchen',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'reason' => $reason,
                    'click_action' => '/customer/orders/' . $order->id,
                ],
            ];

            $customerSent = false;
            if ($order->user_id) {
                $customerSent = $this->fcmService->sendToUser(
                    $order->user_id,
                    $customerNotification,
                    $order->id,
                    null
                );
            }

            // Send notification to admins
            $this->sendKitchenCancellationAlertToAdmins($order, $reason);

            return $customerSent;

        } catch (\Exception $e) {
            Log::error('Failed to send kitchen cancellation alert', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send payment failure alert
     */
    public function sendPaymentFailureAlert(Order $order, string $failureReason = 'Payment verification failed'): bool
    {
        try {
            Log::info('Sending payment failure alert', [
                'order_id' => $order->id,
                'reason' => $failureReason
            ]);

            // Log activity
            OrderActivityLog::logActivity(
                $order->id,
                OrderActivityLog::TYPE_CRITICAL,
                'Payment Failed',
                "Payment failed: {$failureReason}",
                [
                    'failure_reason' => $failureReason,
                    'payment_method' => $order->payment_method,
                    'total_amount' => $order->final_total,
                ]
            );

            // Send notification to customer
            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerNotification = [
                'title' => 'Payment Issue 💳',
                'body' => "Payment for order {$orderNumber} failed. Reason: {$failureReason}. Please retry or contact us.",
                'data' => [
                    'type' => 'payment_failed',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'failure_reason' => $failureReason,
                    'amount' => number_format($order->final_total, 2),
                    'click_action' => '/customer/orders/' . $order->id,
                ],
            ];

            $customerSent = false;
            if ($order->user_id) {
                $customerSent = $this->fcmService->sendToUser(
                    $order->user_id,
                    $customerNotification,
                    $order->id,
                    null
                );
            }

            // Send notification to admins
            $this->sendPaymentFailureAlertToAdmins($order, $failureReason);

            return $customerSent;

        } catch (\Exception $e) {
            Log::error('Failed to send payment failure alert', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send item unavailable alert
     */
    public function sendItemUnavailableAlert(Order $order, array $unavailableItems, ?int $reportedBy = null): bool
    {
        try {
            Log::info('Sending item unavailable alert', [
                'order_id' => $order->id,
                'unavailable_items' => $unavailableItems
            ]);

            $itemNames = implode(', ', array_column($unavailableItems, 'name'));

            // Log activity
            OrderActivityLog::logActivity(
                $order->id,
                OrderActivityLog::TYPE_ERROR,
                'Items Unavailable',
                "The following items are unavailable: {$itemNames}",
                [
                    'unavailable_items' => $unavailableItems,
                ],
                $reportedBy
            );

            // Send notification to customer
            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerNotification = [
                'title' => 'Order Items Unavailable 🚫',
                'body' => "Some items in order {$orderNumber} are unavailable: {$itemNames}. Please contact us for alternatives.",
                'data' => [
                    'type' => 'items_unavailable',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'unavailable_items' => json_encode($unavailableItems),
                    'click_action' => '/customer/orders/' . $order->id,
                ],
            ];

            $customerSent = false;
            if ($order->user_id) {
                $customerSent = $this->fcmService->sendToUser(
                    $order->user_id,
                    $customerNotification,
                    $order->id,
                    null
                );
            }

            // Send notification to admins
            $this->sendItemUnavailableAlertToAdmins($order, $unavailableItems);

            return $customerSent;

        } catch (\Exception $e) {
            Log::error('Failed to send item unavailable alert', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send delay alert to admin users
     */
    private function sendDelayAlertToAdmins(Order $order, int $delayMinutes): bool
    {
        try {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                return false;
            }

            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerName = $order->user ? $order->user->name : 'Guest';

            $adminNotification = [
                'title' => '⚠️ Order Delayed Alert',
                'body' => "Order {$orderNumber} from {$customerName} is delayed by {$delayMinutes} minutes!",
                'data' => [
                    'type' => 'admin_order_delay',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'delay_minutes' => (string) $delayMinutes,
                    'customer_name' => $customerName,
                    'click_action' => '/admin/orders/' . $order->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->fcmService->sendToUser($admin->id, $adminNotification, $order->id, null)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send delay alert to admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send kitchen cancellation alert to admins
     */
    private function sendKitchenCancellationAlertToAdmins(Order $order, string $reason): bool
    {
        try {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                return false;
            }

            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";

            $adminNotification = [
                'title' => '🔴 Kitchen Cancelled Order',
                'body' => "Order {$orderNumber} cancelled by kitchen. Reason: {$reason}",
                'data' => [
                    'type' => 'admin_kitchen_cancellation',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'reason' => $reason,
                    'click_action' => '/admin/orders/' . $order->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->fcmService->sendToUser($admin->id, $adminNotification, $order->id, null)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send kitchen cancellation alert to admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send payment failure alert to admins
     */
    private function sendPaymentFailureAlertToAdmins(Order $order, string $failureReason): bool
    {
        try {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                return false;
            }

            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerName = $order->user ? $order->user->name : 'Guest';
            $total = 'RM ' . number_format($order->final_total, 2);

            $adminNotification = [
                'title' => '💳 Payment Failed Alert',
                'body' => "Payment failed for order {$orderNumber} ({$customerName}) - {$total}. Reason: {$failureReason}",
                'data' => [
                    'type' => 'admin_payment_failed',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'customer_name' => $customerName,
                    'total' => $total,
                    'failure_reason' => $failureReason,
                    'click_action' => '/admin/orders/' . $order->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->fcmService->sendToUser($admin->id, $adminNotification, $order->id, null)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send payment failure alert to admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send item unavailable alert to admins
     */
    private function sendItemUnavailableAlertToAdmins(Order $order, array $unavailableItems): bool
    {
        try {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                return false;
            }

            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $itemNames = implode(', ', array_column($unavailableItems, 'name'));

            $adminNotification = [
                'title' => '🚫 Items Unavailable Alert',
                'body' => "Order {$orderNumber} has unavailable items: {$itemNames}",
                'data' => [
                    'type' => 'admin_items_unavailable',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'unavailable_items' => json_encode($unavailableItems),
                    'click_action' => '/admin/orders/' . $order->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->fcmService->sendToUser($admin->id, $adminNotification, $order->id, null)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send item unavailable alert to admins', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get all active problems for dashboard
     */
    public function getActiveProblems(int $limit = 10): array
    {
        try {
            $problems = OrderActivityLog::problems()
                ->with(['order', 'triggeredBy'])
                ->latest('created_at')
                ->limit($limit)
                ->get();

            return $problems->map(function ($log) {
                return [
                    'id' => $log->id,
                    'order_id' => $log->order_id,
                    'order_number' => $log->order->confirmation_code ?? "ORD-{$log->order_id}",
                    'activity_type' => $log->activity_type,
                    'title' => $log->title,
                    'message' => $log->message,
                    'metadata' => $log->metadata,
                    'triggered_by' => $log->triggeredBy ? $log->triggeredBy->name : 'System',
                    'created_at' => $log->created_at->diffForHumans(),
                    'created_at_formatted' => $log->created_at->format('d/m/Y h:i A'),
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Failed to get active problems', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
