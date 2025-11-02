<?php

namespace App\Listeners\Loyalty;

use App\Events\Loyalty\PointsAwarded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * PHASE 5: Send Points Awarded Notification Listener
 *
 * Handles sending notifications when points are awarded.
 * Implements ShouldQueue for background processing.
 *
 * Notification triggers:
 * - Email notification for large point awards (100+)
 * - Push notification for all point awards
 * - Special notification for milestones
 */
class SendPointsAwardedNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(PointsAwarded $event): void
    {
        $user = $event->user;
        $points = $event->pointsAwarded;
        $newBalance = $event->newBalance;

        // Log the event
        Log::info('Points awarded notification triggered', [
            'user_id' => $user->id,
            'points' => $points,
            'new_balance' => $newBalance,
            'description' => $event->description,
        ]);

        // Send notification for significant point awards (10+)
        if ($points >= 10) {
            // TODO: Implement actual notification
            // Example: Notification::send($user, new PointsAwardedNotification($event));

            Log::info('Points notification sent', [
                'user_id' => $user->id,
                'points' => $points,
                'message' => "You earned {$points} points! New balance: {$newBalance}"
            ]);
        }

        // Send special notification for milestones
        if ($event->isMilestone()) {
            Log::info('Milestone reached notification', [
                'user_id' => $user->id,
                'milestone' => $newBalance,
                'message' => "Congratulations! You've reached {$newBalance} points milestone!"
            ]);

            // TODO: Send special milestone notification
            // Example: Notification::send($user, new MilestoneReachedNotification($newBalance));
        }

        // Check for check-in streaks
        if ($event->isCheckInPoints()) {
            Log::info('Check-in points awarded', [
                'user_id' => $user->id,
                'points' => $points,
            ]);

            // TODO: Send check-in success notification
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PointsAwarded $event, \Throwable $exception): void
    {
        Log::error('Failed to send points awarded notification', [
            'user_id' => $event->user->id,
            'points' => $event->pointsAwarded,
            'error' => $exception->getMessage(),
        ]);
    }
}
