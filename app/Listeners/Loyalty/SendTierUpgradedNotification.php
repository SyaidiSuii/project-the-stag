<?php

namespace App\Listeners\Loyalty;

use App\Events\Loyalty\TierUpgraded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 7: Tier Upgraded Notification Listener
 *
 * Sends congratulations notifications when customers upgrade to higher tiers.
 * Implements ShouldQueue for background processing.
 *
 * Responsibilities:
 * - Send congratulations email/notification
 * - Log tier upgrade for analytics
 * - Unlock tier-exclusive rewards (future: Phase 8)
 * - Award upgrade bonus points (future: Phase 8)
 *
 * Industry Pattern: Similar to Marriott Bonvoy, Starbucks Rewards
 * tier upgrade notifications.
 */
class SendTierUpgradedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Maximum retry attempts if job fails
     */
    public $tries = 3;

    /**
     * Seconds to wait before retrying
     */
    public $backoff = 60;

    /**
     * Handle the event.
     */
    public function handle(TierUpgraded $event): void
    {
        $user = $event->user;
        $oldTier = $event->oldTier;
        $newTier = $event->newTier;

        // Log tier upgrade for analytics
        Log::info('Customer tier upgraded', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'old_tier' => $oldTier?->name ?? 'None',
            'new_tier' => $newTier->name,
            'upgrade_level' => $event->getUpgradeLevel(),
            'is_first_tier' => $event->isFirstTier(),
        ]);

        // Send congratulations notification
        if ($event->isFirstTier()) {
            $this->sendFirstTierWelcome($event);
        } else {
            $this->sendTierUpgradeNotification($event);
        }

        // Log new benefits available
        $benefits = $event->getNewBenefits();
        if (!empty($benefits)) {
            Log::info('New tier benefits unlocked', [
                'user_id' => $user->id,
                'tier' => $newTier->name,
                'benefits' => $benefits,
            ]);
        }
    }

    /**
     * Send welcome notification for first tier assignment
     */
    protected function sendFirstTierWelcome(TierUpgraded $event): void
    {
        $user = $event->user;
        $tier = $event->newTier;

        Log::info('First tier welcome notification', [
            'user_id' => $user->id,
            'tier' => $tier->name,
            'message' => "Welcome to {$tier->name}! You've joined our loyalty program.",
        ]);

        // TODO: Send actual email notification
        // Mail::to($user->email)->queue(new TierWelcomeEmail($user, $tier));

        // TODO: Send push notification
        // $user->notify(new TierWelcomeNotification($tier));
    }

    /**
     * Send tier upgrade congratulations notification
     */
    protected function sendTierUpgradeNotification(TierUpgraded $event): void
    {
        $user = $event->user;
        $oldTier = $event->oldTier;
        $newTier = $event->newTier;

        Log::info('Tier upgrade congratulations notification', [
            'user_id' => $user->id,
            'message' => "Congratulations! You've been upgraded from {$oldTier->name} to {$newTier->name}!",
            'upgrade_level' => $event->getUpgradeLevel(),
        ]);

        // TODO: Send actual email notification
        // Mail::to($user->email)->queue(new TierUpgradeEmail($user, $oldTier, $newTier));

        // TODO: Send push notification
        // $user->notify(new TierUpgradeNotification($oldTier, $newTier));

        // Special handling for multi-tier jumps
        if ($event->getUpgradeLevel() > 1) {
            Log::info('Multi-tier jump detected - sending special notification', [
                'user_id' => $user->id,
                'tiers_jumped' => $event->getUpgradeLevel(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(TierUpgraded $event, \Throwable $exception): void
    {
        Log::error('Failed to send tier upgrade notification', [
            'user_id' => $event->user->id,
            'new_tier' => $event->newTier->name,
            'error' => $exception->getMessage(),
        ]);

        // TODO: Send alert to admin about failed notification
    }
}
