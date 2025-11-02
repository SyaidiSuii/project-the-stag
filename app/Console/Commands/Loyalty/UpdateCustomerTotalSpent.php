<?php

namespace App\Console\Commands\Loyalty;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Order;

class UpdateCustomerTotalSpent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:update-total-spent
                            {--user_id= : Update specific user only}
                            {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update customer_profiles.total_spent from paid orders (fix for missing total_spent)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Get users with customer profiles
        $query = User::whereHas('customerProfile');

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->with('customerProfile')->get();

        if ($users->isEmpty()) {
            $this->warn('No users with customer profiles found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} users with customer profiles");
        $this->newLine();

        $updatedCount = 0;
        $unchangedCount = 0;
        $totalAdded = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            $customerProfile = $user->customerProfile;

            // Calculate total from paid orders
            $actualTotalSpent = Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $currentTotalSpent = $customerProfile->total_spent ?? 0;

            if ($actualTotalSpent != $currentTotalSpent) {
                $difference = $actualTotalSpent - $currentTotalSpent;

                $this->newLine();
                $this->line("  User #{$user->id} - {$user->name}");
                $this->line("    Current: RM " . number_format($currentTotalSpent, 2));
                $this->line("    Should be: RM " . number_format($actualTotalSpent, 2));
                $this->line("    Difference: RM " . number_format($difference, 2));

                if (!$dryRun) {
                    $customerProfile->update(['total_spent' => $actualTotalSpent]);

                    // Also update loyalty tier
                    $customerProfile->updateTotalSpendingAndTier();

                    $this->info("    ✅ Updated!");
                }

                $updatedCount++;
                $totalAdded += $difference;
            } else {
                $unchangedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Summary:');
        $this->info("  Total users checked: {$users->count()}");
        $this->info("  Updated: {$updatedCount}");
        $this->info("  Unchanged: {$unchangedCount}");
        $this->info("  Total spending added: RM " . number_format($totalAdded, 2));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  This was a dry run. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }
}
