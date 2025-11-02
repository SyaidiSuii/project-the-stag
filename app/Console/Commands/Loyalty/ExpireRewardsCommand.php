<?php

namespace App\Console\Commands\Loyalty;

use App\Services\Loyalty\RewardRedemptionService;
use Illuminate\Console\Command;

/**
 * PHASE 6: Expire Rewards Command
 *
 * Manually expire old customer rewards.
 * Also scheduled to run daily via Kernel.
 */
class ExpireRewardsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:expire-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire customer rewards that have passed their expiry date';

    /**
     * Execute the console command.
     */
    public function handle(RewardRedemptionService $redemptionService): int
    {
        $this->info('Starting to expire old rewards...');

        try {
            $expiredCount = $redemptionService->expireOldRewards();

            $this->info("✓ Successfully expired {$expiredCount} rewards");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to expire rewards: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
