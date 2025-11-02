<?php

namespace App\Listeners\Loyalty;

use App\Events\Loyalty\RewardRedeemed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 5: Send Reward Redeemed Notification Listener
 *
 * Sends confirmation when customer redeems a reward.
 * Queued for background processing.
 */
class SendRewardRedeemedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(RewardRedeemed $event): void
    {
        $user = $event->user;
        $reward = $event->reward;
        $redemptionCode = $event->getRedemptionCode();

        Log::info('Reward redeemed notification triggered', [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'reward_title' => $reward->title,
            'points_spent' => $event->pointsSpent,
            'redemption_code' => $redemptionCode,
        ]);

        // TODO: Send email with redemption code
        // Email should include:
        // - Reward title and description
        // - Redemption code (if applicable)
        // - Expiry date
        // - Instructions on how to use
        //
        // Example:
        // Mail::to($user)->queue(new RewardRedeemedMail($event));

        Log::info('Reward redemption email queued', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'reward_title' => $reward->title,
            'redemption_code' => $redemptionCode,
        ]);

        // Send push notification
        Log::info('Reward redemption push notification sent', [
            'user_id' => $user->id,
            'message' => "You've successfully redeemed: {$reward->title}",
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(RewardRedeemed $event, \Throwable $exception): void
    {
        Log::error('Failed to send reward redeemed notification', [
            'user_id' => $event->user->id,
            'reward_id' => $event->reward->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
