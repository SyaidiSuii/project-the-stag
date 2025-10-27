<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Kitchen\OrderDistributionService;

class DistributeExistingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kitchen:distribute-existing-orders {--force : Force distribution even if already distributed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute existing orders to kitchen stations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to distribute existing orders to kitchen stations...');

        $distributionService = app(OrderDistributionService::class);

        // Get orders that need distribution (active orders without station assignments)
        $query = Order::whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with('items.menuItem');

        if (!$this->option('force')) {
            // Only get orders without station assignments
            $query->whereDoesntHave('stationAssignments');
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found that need distribution.');
            return 0;
        }

        $this->info("Found {$orders->count()} orders to distribute.");

        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($orders as $order) {
            try {
                // Clear existing assignments if forcing redistribution
                if ($this->option('force')) {
                    $order->stationAssignments()->delete();
                }

                $distributionService->distributeOrder($order);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("\nFailed to distribute order #{$order->confirmation_code}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Distribution complete!");
        $this->info("Successfully distributed: {$successCount} orders");

        if ($errorCount > 0) {
            $this->error("Failed to distribute: {$errorCount} orders");
        }

        return 0;
    }
}
