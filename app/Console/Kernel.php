<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->command('app:send-happy-birthday-email')->everyTwoMinutes();
        $schedule->command('app:cleanup-abandoned-orders')->everyFiveMinutes();

        // Detect unpaid orders after 4 hours - run every hour
        $schedule->command('app:detect-unpaid-orders')->hourly();

        // Comprehensive analytics generation (replaces generate-sales-summary)
        $schedule->command('analytics:generate')->dailyAt('01:00');

        // Cleanup unavailable cart items (Shopee-style: 7 days)
        $schedule->command('cart:cleanup-unavailable')->dailyAt('02:00');

        // Check for delayed orders every 5 minutes
        $schedule->command('orders:check-delays')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('Order delay check completed successfully');
            })
            ->onFailure(function () {
                \Log::error('Order delay check failed');
            });

        // PHASE 6: Loyalty Program Scheduled Tasks
        // Expire old vouchers daily at 1:00 AM
        $schedule->command('loyalty:expire-vouchers')
            ->dailyAt('01:00')
            ->onSuccess(function () {
                \Log::info('Voucher expiry job completed successfully');
            })
            ->onFailure(function () {
                \Log::error('Voucher expiry job failed');
            });

        // Expire old rewards daily at 2:00 AM
        $schedule->command('loyalty:expire-rewards')
            ->dailyAt('02:00')
            ->onSuccess(function () {
                \Log::info('Reward expiry job completed successfully');
            })
            ->onFailure(function () {
                \Log::error('Reward expiry job failed');
            });

        // Verify points balance integrity weekly on Sunday at 3:00 AM
        $schedule->command('loyalty:verify-balance')
            ->weekly()
            ->sundays()
            ->at('03:00')
            ->onSuccess(function () {
                \Log::info('Points balance verification completed');
            })
            ->onFailure(function () {
                \Log::error('Points balance verification found discrepancies');
                // TODO: Send alert to admin
            });

        // AI Recommendation Model Retraining
        // Retrain AI model daily at 3:00 AM to keep CF matrix current
        // Runs after analytics generation (01:00) and cart cleanup (02:00)
        $schedule->command('ai:retrain')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('AI recommendation model retrained successfully');
            })
            ->onFailure(function () {
                \Log::error('AI recommendation model retrain failed');
                // TODO: Send alert to admin
            });

        // Keep old command for backward compatibility (will be deprecated)
        // $schedule->command('app:generate-sales-summary')->daily();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
