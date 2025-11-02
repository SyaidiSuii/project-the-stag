<?php

namespace App\Console\Commands\Loyalty;

use App\Services\Loyalty\VoucherService;
use Illuminate\Console\Command;

/**
 * PHASE 6: Expire Vouchers Command
 *
 * Manually expire old customer vouchers.
 * Also scheduled to run daily via Kernel.
 */
class ExpireVouchersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:expire-vouchers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire customer vouchers that have passed their expiry date';

    /**
     * Execute the console command.
     */
    public function handle(VoucherService $voucherService): int
    {
        $this->info('Starting to expire old vouchers...');

        try {
            $expiredCount = $voucherService->expireOldVouchers();

            $this->info("✓ Successfully expired {$expiredCount} vouchers");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to expire vouchers: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
