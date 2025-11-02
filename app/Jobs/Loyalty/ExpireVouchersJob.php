<?php

namespace App\Jobs\Loyalty;

use App\Services\Loyalty\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 6: Expire Old Vouchers Job
 *
 * Scheduled job to expire customer vouchers that have passed their expiry date.
 * Runs daily at 1:00 AM.
 */
class ExpireVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(VoucherService $voucherService): void
    {
        Log::info('Starting ExpireVouchersJob');

        try {
            $expiredCount = $voucherService->expireOldVouchers();

            Log::info('ExpireVouchersJob completed successfully', [
                'expired_count' => $expiredCount,
            ]);

        } catch (\Exception $e) {
            Log::error('ExpireVouchersJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ExpireVouchersJob failed permanently', [
            'error' => $exception->getMessage(),
        ]);

        // TODO: Send alert to admin
    }
}
