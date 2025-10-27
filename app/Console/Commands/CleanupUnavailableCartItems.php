<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCart;
use Carbon\Carbon;

class CleanupUnavailableCartItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup-unavailable {--days=7 : Number of days after which unavailable items are deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove cart items that have been unavailable for more than specified days (default: 7 days, Shopee-style)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Starting cleanup of cart items unavailable for more than {$days} days...");

        // Get cart items that have been marked unavailable for X+ days
        $itemsToDelete = UserCart::unavailableForDays($days)->get();

        $deleteCount = 0;

        if ($itemsToDelete->isEmpty()) {
            $this->info('No cart items found that need cleanup.');
            return Command::SUCCESS;
        }

        $this->info("Found {$itemsToDelete->count()} cart items to cleanup.");

        // Delete each item with logging
        foreach ($itemsToDelete as $cartItem) {
            $userId = $cartItem->user_id;
            $menuItemId = $cartItem->menu_item_id;
            $unavailableSince = $cartItem->unavailable_since;

            // Log the deletion
            \Log::info('Auto-cleaning unavailable cart item', [
                'user_id' => $userId,
                'menu_item_id' => $menuItemId,
                'unavailable_since' => $unavailableSince,
                'days_unavailable' => $unavailableSince ? $unavailableSince->diffInDays(now()) : null,
            ]);

            $cartItem->delete();
            $deleteCount++;
        }

        $this->info("Successfully cleaned up {$deleteCount} unavailable cart items.");

        return Command::SUCCESS;
    }
}
