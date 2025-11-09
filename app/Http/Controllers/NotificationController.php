<?php

namespace App\Http\Controllers;

use App\Services\FCMNotificationService;
use App\Models\User;
use App\Models\UserFcmDevice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Register a device token for FCM
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'platform' => 'nullable|string',
            'browser' => 'nullable|string',
            'version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $device = $this->fcmService->registerDeviceToken(
                $user->id,
                $request->device_token,
                $request->device_type,
                $request->platform,
                $request->browser,
                $request->version
            );

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'data' => [
                    'device_id' => $device->id,
                    'device_type' => $device->device_type,
                    'platform' => $device->platform,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send notification to authenticated user
     */
    public function sendToSelf(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $notificationData = [
                'title' => $request->title,
                'body' => $request->body,
                'data' => $request->data ?? [],
            ];

            $result = $this->fcmService->sendToUser($user->id, $notificationData);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Notification sent successfully' : 'Failed to send notification',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification history for authenticated user
     */
    public function getHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $notifications = \App\Models\PushNotification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification history',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get FCM statistics (admin only)
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user is admin
            if (!$user || !$user->hasAnyRole(['admin', 'manager', 'super-admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $statistics = $this->fcmService->getStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test FCM connection (admin only)
     */
    public function testConnection(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->hasAnyRole(['admin', 'manager', 'super-admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            // Try to send a test notification to all devices
            $notificationData = [
                'title' => 'Test Notification',
                'body' => 'This is a test notification from The Stag SmartDine',
                'data' => [
                    'type' => 'test',
                    'timestamp' => now()->timestamp,
                ],
            ];

            $successCount = $this->fcmService->sendToAllUsers($notificationData);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent',
                'data' => [
                    'sent_to' => $successCount . ' devices',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's registered devices
     */
    public function getUserDevices(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $devices = $user->fcmDevices()
                ->select('id', 'device_type', 'platform', 'browser', 'version', 'is_active', 'last_used_at', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $devices,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch devices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate a device
     */
    public function deactivateDevice(Request $request, int $deviceId): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $device = UserFcmDevice::where('user_id', $user->id)->where('id', $deviceId)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found',
                ], 404);
            }

            $device->deactivate();

            return response()->json([
                'success' => true,
                'message' => 'Device deactivated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate device',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
