<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Kitchen\OrderDistributionService;

class DistributeUnassignedOrders extends Command
{
    protected $signature = 'kitchen:distribute-unassigned {--dry-run : Preview changes without applying them}';
    protected $description = 'Distribute orders that have no station assignments';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔎 Searching for orders without station assignments...');
        
        // Get orders that have no station assignments but have items
        $orders = Order::has('items')
            ->doesntHave('stationAssignments')
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('✅ All orders have station assignments! Nothing to do.');
            return 0;
        }

        $this->warn("Found {$orders->count()} orders without station assignments");
        
        if (!$isDryRun) {
            if (!$this->confirm('Do you want to distribute these orders to kitchen stations?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $distributionService = app(OrderDistributionService::class);
        $successCount = 0;
        $failCount = 0;

        foreach ($orders as $order) {
            $this->line("\nProcessing Order #{$order->id} ({$order->confirmation_code})");
            $this->line("  Status: {$order->order_status} | Items: {$order->items->count()}");
            
            // Show what items will be distributed
            foreach ($order->items as $item) {
                $station = $item->menuItem->getEffectiveStation();
                $stationName = $station ? $station->name : 'NO STATION DEFINED';
                $this->line("  → {$item->menuItem->name} x{$item->quantity} → {$stationName}");
            }

            if ($isDryRun) {
                $this->info("  [DRY RUN] Would distribute this order");
                $successCount++;
                continue;
            }

            try {
                // Load necessary relationships
                $order->load('items.menuItem.category.defaultStation', 'items.menuItem.stationOverride');
                
                // Distribute to stations
                $distributionService->distributeOrder($order);
                
                $this->info("  ✅ Successfully distributed");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$e->getMessage()}");
                $failCount++;
            }
        }

        $this->info("\n═══════════════════════════════════════");
        $this->info("📊 SUMMARY");
        $this->info("═══════════════════════════════════════");
        $this->info("Total orders processed: {$orders->count()}");
        
        if ($isDryRun) {
            $this->warn("Would distribute: {$successCount}");
        } else {
            $this->info("✅ Successfully distributed: {$successCount}");
            if ($failCount > 0) {
                $this->error("❌ Failed: {$failCount}");
            }
        }

        return 0;
    }
}
