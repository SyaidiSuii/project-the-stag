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

        // Comprehensive analytics generation (replaces generate-sales-summary)
        $schedule->command('analytics:generate')->dailyAt('01:00');

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
