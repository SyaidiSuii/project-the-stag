<?php

namespace App\Jobs\Loyalty;

use App\Services\Loyalty\RewardRedemptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 6: Expire Old Rewards Job
 *
 * Scheduled job to expire customer rewards that have passed their expiry date.
 * Runs daily at 2:00 AM.
 *
 * Industry Pattern: Automated cleanup jobs prevent data inconsistencies
 * and ensure business rules are enforced even if manual processes fail.
 */
class ExpireRewardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(RewardRedemptionService $redemptionService): void
    {
        Log::info('Starting ExpireRewardsJob');

        try {
            $expiredCount = $redemptionService->expireOldRewards();

            Log::info('ExpireRewardsJob completed successfully', [
                'expired_count' => $expiredCount,
            ]);

        } catch (\Exception $e) {
            Log::error('ExpireRewardsJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ExpireRewardsJob failed permanently', [
            'error' => $exception->getMessage(),
        ]);

        // TODO: Send alert to admin
        // Example: Notification::route('mail', config('mail.admin'))
        //     ->notify(new JobFailedNotification('ExpireRewardsJob', $exception));
    }
}
