<?php

namespace App\Console\Commands\Loyalty;

use App\Models\User;
use App\Models\LoyaltyTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * PHASE 6: Verify Points Balance Command
 *
 * Verifies that users.points_balance matches the sum of loyalty_transactions.
 * Reports discrepancies for admin review.
 *
 * Industry Standard: Banking systems perform balance reconciliation daily.
 */
class VerifyPointsBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:verify-balance
                            {--fix : Automatically fix discrepancies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that user points balances match loyalty transactions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Verifying points balances...');

        $discrepancies = [];
        $fixMode = $this->option('fix');

        // Get all users with points
        $users = User::whereNotNull('points_balance')
            ->with('customerProfile')
            ->get();

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            $customerProfile = $user->customerProfile;

            if (!$customerProfile) {
                $this->warn("\nUser {$user->id} has points but no customer profile");
                continue;
            }

            // Calculate expected balance from transactions
            $expectedBalance = LoyaltyTransaction::where('customer_profile_id', $customerProfile->id)
                ->sum('points_change');

            $currentBalance = $user->points_balance ?? 0;

            // Check for discrepancy
            if ($expectedBalance != $currentBalance) {
                $discrepancy = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'current_balance' => $currentBalance,
                    'expected_balance' => $expectedBalance,
                    'difference' => $currentBalance - $expectedBalance,
                ];

                $discrepancies[] = $discrepancy;

                // Fix if requested
                if ($fixMode) {
                    $user->update(['points_balance' => $expectedBalance]);
                    $this->warn("\n✓ Fixed user {$user->id}: {$currentBalance} → {$expectedBalance}");
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Report results
        if (empty($discrepancies)) {
            $this->info('✓ All points balances are correct!');
            return Command::SUCCESS;
        }

        $this->error('Found ' . count($discrepancies) . ' discrepancies:');
        $this->table(
            ['User ID', 'Name', 'Current', 'Expected', 'Difference'],
            array_map(function ($d) {
                return [
                    $d['user_id'],
                    $d['user_name'],
                    $d['current_balance'],
                    $d['expected_balance'],
                    $d['difference'],
                ];
            }, $discrepancies)
        );

        if ($fixMode) {
            $this->info("\n✓ All discrepancies have been fixed!");
            return Command::SUCCESS;
        } else {
            $this->warn("\nRun with --fix to automatically correct discrepancies");
            return Command::FAILURE;
        }
    }
}
