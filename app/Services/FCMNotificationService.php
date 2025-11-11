<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFcmDevice;
use App\Models\PushNotification;
use App\Models\Order;
use App\Models\TableReservation;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\FirebaseProjectManager;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class FCMNotificationService
{
    protected Messaging $messaging;

    public function __construct(FirebaseProjectManager $firebaseManager)
    {
        $this->messaging = $firebaseManager->project()->messaging();
    }

    /**
     * Register a device token for a user
     */
    public function registerDeviceToken(
        int $userId,
        string $deviceToken,
        string $deviceType = 'web',
        ?string $platform = null,
        ?string $browser = null,
        ?string $version = null
    ): UserFcmDevice {
        // Check if token already exists
        $existingDevice = UserFcmDevice::where('device_token', $deviceToken)->first();

        if ($existingDevice) {
            // Update existing device
            $existingDevice->update([
                'user_id' => $userId,
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
                'version' => $version,
                'is_active' => true,
                'last_used_at' => now(),
            ]);

            return $existingDevice;
        }

        // Create new device
        return UserFcmDevice::create([
            'user_id' => $userId,
            'device_token' => $deviceToken,
            'device_type' => $deviceType,
            'platform' => $platform,
            'browser' => $browser,
            'version' => $version,
            'is_active' => true,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(int $userId, array $notificationData, ?int $orderId = null, ?int $reservationId = null): bool
    {
        try {
            // Get user's active devices
            $devices = UserFcmDevice::where('user_id', $userId)->where('is_active', true)->get();

            Log::info('FCM: Starting send to user', [
                'user_id' => $userId,
                'devices_count' => $devices->count(),
                'notification_title' => $notificationData['title']
            ]);

            if ($devices->isEmpty()) {
                Log::warning('No active devices found for user', ['user_id' => $userId]);
                return false;
            }

            $successCount = 0;
            $failureCount = 0;

            // Send to all devices
            foreach ($devices as $device) {
                try {
                    Log::info('FCM: Attempting send', [
                        'device_id' => $device->id,
                        'token_prefix' => substr($device->device_token, 0, 20) . '...'
                    ]);

                    // Create message with target token for each device
                    $message = CloudMessage::withTarget('token', $device->device_token)
                        ->withNotification(Notification::create(
                            $notificationData['title'],
                            $notificationData['body']
                        ))
                        ->withData($notificationData['data'] ?? []);

                    $result = $this->messaging->send($message);
                    $device->markAsUsed();
                    $successCount++;

                    Log::info('FCM: Message sent successfully', [
                        'device_id' => $device->id,
                        'result' => $result
                    ]);

                    // Log notification
                    $this->logNotification($userId, $orderId, $reservationId, $notificationData, 'sent');

                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error('Failed to send FCM notification', [
                        'user_id' => $userId,
                        'device_id' => $device->id,
                        'device_token' => substr($device->device_token, 0, 30) . '...',
                        'error_message' => $e->getMessage(),
                        'error_class' => get_class($e),
                        'error_trace' => $e->getTraceAsString()
                    ]);

                    // If token is invalid, deactivate device
                    if (str_contains($e->getMessage(), 'invalid-registration-token') ||
                        str_contains($e->getMessage(), 'registration-token-not-registered') ||
                        str_contains($e->getMessage(), 'Requested entity was not found')) {
                        $device->deactivate();
                        Log::info('FCM: Device deactivated due to invalid token', [
                            'user_id' => $userId,
                            'device_id' => $device->id
                        ]);
                    }
                }
            }

            if ($successCount > 0) {
                Log::info('FCM notifications sent successfully', [
                    'user_id' => $userId,
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('FCM Notification Error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers(array $userIds, array $notificationData, ?int $orderId = null, ?int $reservationId = null): array
    {
        $results = [];

        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendToUser($userId, $notificationData, $orderId, $reservationId);
        }

        return $results;
    }

    /**
     * Send notification to all users (for promotional campaigns)
     */
    public function sendToAllUsers(array $notificationData, ?string $userType = null): int
    {
        try {
            // Build query based on user type
            $query = User::where('is_active', true);

            // You can extend this to filter by user type/role if needed
            if ($userType === 'customers') {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'customer');
                });
            }

            $users = $query->get();
            $successCount = 0;

            foreach ($users as $user) {
                if ($this->sendToUser($user->id, $notificationData)) {
                    $successCount++;
                }
            }

            Log::info('Bulk FCM notifications sent', [
                'total_users' => $users->count(),
                'success_count' => $successCount,
                'user_type' => $userType,
            ]);

            return $successCount;

        } catch (\Exception $e) {
            Log::error('Bulk FCM Notification Error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
            ]);
            return 0;
        }
    }

    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification(Order $order): bool
    {
        $user = $order->user;

        if (!$user) {
            Log::warning('Order has no user', ['order_id' => $order->id]);
            return false;
        }

        $statusMessages = [
            'confirmed' => [
                'title' => 'Order Confirmed! 🍽️',
                'body' => "Your order #{$order->order_number} has been confirmed and is being prepared.",
            ],
            'preparing' => [
                'title' => 'Preparing Your Food 👨‍🍳',
                'body' => "Your order #{$order->order_number} is now being prepared by our kitchen.",
            ],
            'ready' => [
                'title' => 'Order Ready! ✨',
                'body' => "Your order #{$order->order_number} is ready for pickup!",
            ],
            'completed' => [
                'title' => 'Order Completed! 🎉',
                'body' => "Your order #{$order->order_number} has been completed. Thank you!",
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'body' => "Your order #{$order->order_number} has been cancelled.",
            ],
        ];

        $statusMessage = $statusMessages[$order->order_status] ?? null;

        if (!$statusMessage) {
            return false;
        }

        $notificationData = [
            'title' => $statusMessage['title'],
            'body' => $statusMessage['body'],
            'data' => [
                'type' => 'order_status',
                'order_id' => (string) $order->id,
                'order_number' => $order->confirmation_code ?? $order->id,
                'status' => $order->order_status,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendToUser($user->id, $notificationData, $order->id, null);
    }

    /**
     * Send new order notification to admin users
     */
    public function sendNewOrderNotificationToAdmin(Order $order): bool
    {
        try {
            // Get all admin users (admin and manager roles)
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager', 'super-admin']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('No admin users found to send new order notification');
                return false;
            }

            Log::info('Sending new order notification to admins', [
                'order_id' => $order->id,
                'order_number' => $order->confirmation_code,
                'admin_count' => $adminUsers->count()
            ]);

            // Prepare notification data
            $orderNumber = $order->confirmation_code ?? "ORD-{$order->id}";
            $customerName = $order->user ? $order->user->name : 'Guest';
            $itemCount = $order->items->count();
            $total = 'RM ' . number_format($order->total_amount, 2);

            $notificationData = [
                'title' => '🔔 New Order Received!',
                'body' => "{$customerName} placed a new order ({$orderNumber}) - {$itemCount} items, Total: {$total}",
                'data' => [
                    'type' => 'new_order',
                    'order_id' => (string) $order->id,
                    'order_number' => $orderNumber,
                    'customer_name' => $customerName,
                    'item_count' => (string) $itemCount,
                    'total_amount' => $total,
                    'click_action' => '/admin/orders/' . $order->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->sendToUser($admin->id, $notificationData, $order->id, null)) {
                    $successCount++;
                }
            }

            Log::info('New order notification sent to admins', [
                'order_id' => $order->id,
                'success_count' => $successCount,
                'total_admins' => $adminUsers->count()
            ]);

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send new order notification to admin', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send new reservation notification to admin users
     */
    public function sendNewReservationNotificationToAdmin(TableReservation $reservation): bool
    {
        try {
            // Get all admin users (admin and manager roles)
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('No admin users found to send new reservation notification');
                return false;
            }

            Log::info('Sending new reservation notification to admins', [
                'reservation_id' => $reservation->id,
                'confirmation_code' => $reservation->confirmation_code,
                'admin_count' => $adminUsers->count()
            ]);

            // Prepare notification data
            $reservationCode = $reservation->confirmation_code ?? "BK-{$reservation->id}";
            $customerName = $reservation->user ? $reservation->user->name : $reservation->guest_name;
            $tableNumber = $reservation->table ? $reservation->table->table_number : 'N/A';
            $bookingDate = $reservation->booking_date->format('d/m/Y');
            $bookingTime = $reservation->booking_time->format('h:i A');
            $partySize = $reservation->party_size;

            $notificationData = [
                'title' => '📅 New Table Reservation!',
                'body' => "{$customerName} booked Table {$tableNumber} for {$partySize} guests on {$bookingDate} at {$bookingTime} ({$reservationCode})",
                'data' => [
                    'type' => 'new_reservation',
                    'reservation_id' => (string) $reservation->id,
                    'reservation_code' => $reservationCode,
                    'customer_name' => $customerName,
                    'table_number' => $tableNumber,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'party_size' => (string) $partySize,
                    'click_action' => '/admin/table-reservation',
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->sendToUser($admin->id, $notificationData, null, $reservation->id)) {
                    $successCount++;
                }
            }

            Log::info('New reservation notification sent to admins', [
                'reservation_id' => $reservation->id,
                'success_count' => $successCount,
                'total_admins' => $adminUsers->count()
            ]);

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send new reservation notification to admin', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send booking confirmed notification to the customer.
     * This is a wrapper for clarity.
     */
    public function sendBookingConfirmedNotificationToCustomer(TableReservation $reservation): bool
    {
        return $this->sendReservationNotification($reservation, 'confirmed');
    }

    /**
     * Send a notification to admins to confirm that a booking has been successfully confirmed by them.
     */
    public function sendBookingConfirmedNotificationToAdmin(TableReservation $reservation): bool
    {
        try {
            $adminUsers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'manager']))
                              ->where('is_active', true)->get();

            if ($adminUsers->isEmpty()) return false;

            $reservationCode = $reservation->confirmation_code ?? "BK-{$reservation->id}";
            $customerName = $reservation->guest_name;

            $notificationData = [
                'title' => '✅ Reservation Confirmed',
                'body' => "You have confirmed booking {$reservationCode} for {$customerName}.",
                'data' => [
                    'type' => 'reservation_confirmed_admin',
                    'reservation_id' => (string) $reservation->id,
                    'click_action' => '/admin/table-reservation/' . $reservation->id,
                ],
            ];

            $successCount = 0;
            foreach ($adminUsers as $admin) {
                if ($this->sendToUser($admin->id, $notificationData, null, $reservation->id)) {
                    $successCount++;
                }
            }
            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error(__FUNCTION__, ['error' => $e->getMessage(), 'reservation_id' => $reservation->id]);
            return false;
        }
    }

    /**
     * Send reservation notification
     */
    public function sendReservationNotification(TableReservation $reservation, string $type = 'confirmed'): bool
    {
        $user = $reservation->user;

        if (!$user) {
            Log::warning('Reservation has no user', ['reservation_id' => $reservation->id]);
            return false;
        }

        $typeMessages = [
            'confirmed' => [
                'title' => 'Reservation Confirmed! 📅',
                'body' => "Your reservation for Table {$reservation->table->table_number} on " .
                         $reservation->booking_date->format('d/m/Y') . " at " .
                         $reservation->booking_time->format('h:i A') . " has been confirmed.",
            ],
            'cancelled' => [
                'title' => 'Reservation Cancelled',
                'body' => "Your reservation for Table {$reservation->table->table_number} has been cancelled.",
            ],
            'reminder' => [
                'title' => 'Reservation Reminder ⏰',
                'body' => "Reminder: You have a reservation today at " .
                         $reservation->booking_time->format('h:i A') . " for Table {$reservation->table->table_number}.",
            ],
        ];

        $typeMessage = $typeMessages[$type] ?? null;

        if (!$typeMessage) {
            return false;
        }

        $notificationData = [
            'title' => $typeMessage['title'],
            'body' => $typeMessage['body'],
            'data' => [
                'type' => 'reservation',
                'reservation_id' => (string) $reservation->id,
                'table_number' => (string) $reservation->table->table_number,
                'reservation_date' => $reservation->booking_date->format('Y-m-d'),
                'reservation_time' => $reservation->booking_time->format('H:i:s'),
                'notification_type' => $type,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendToUser($user->id, $notificationData, null, $reservation->id);
    }

    /**
     * Send promotional notification
     */
    public function sendPromotionalNotification(string $title, string $message, ?string $userType = null): int
    {
        $notificationData = [
            'title' => $title,
            'body' => $message,
            'data' => [
                'type' => 'promotion',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendToAllUsers($notificationData, $userType);
    }

    /**
     * Log notification to database
     */
    private function logNotification(int $userId, ?int $orderId, ?int $reservationId, array $notificationData, string $status): void
    {
        try {
            PushNotification::create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'reservation_id' => $reservationId,
                'title' => $notificationData['title'],
                'message' => $notificationData['body'],
                'type' => $notificationData['data']['type'] ?? 'general',
                'data' => $notificationData['data'] ?? [],
                'is_sent' => $status === 'sent',
                'sent_at' => $status === 'sent' ? now() : null,
                'delivery_status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log notification', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Clean up invalid/expired tokens
     */
    public function cleanupInvalidTokens(): void
    {
        // This would typically be called via a scheduled job
        $invalidDevices = UserFcmDevice::where('is_active', true)
            ->where('updated_at', '<', now()->subDays(30)) // Tokens older than 30 days
            ->get();

        foreach ($invalidDevices as $device) {
            try {
                // Try to validate token
                $this->messaging->validateRegistrationTokens([$device->device_token]);

                // If validation passes, mark as used
                $device->markAsUsed();
            } catch (\Exception $e) {
                // Invalid token, deactivate
                $device->deactivate();
                Log::info('Invalid FCM token deactivated', [
                    'device_id' => $device->id,
                    'user_id' => $device->user_id,
                ]);
            }
        }
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_devices' => UserFcmDevice::count(),
            'active_devices' => UserFcmDevice::where('is_active', true)->count(),
            'device_types' => UserFcmDevice::where('is_active', true)
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type')
                ->toArray(),
            'recent_notifications' => PushNotification::where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
    }
}
