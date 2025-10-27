<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Kitchen\OrderDistributionService;

class RedistributeOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kitchen:redistribute-orders {--all : Redistribute all orders} {--pending : Only redistribute pending orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redistribute orders to kitchen stations (for orders without station assignments)';

    protected $distributionService;

    public function __construct(OrderDistributionService $distributionService)
    {
        parent::__construct();
        $this->distributionService = $distributionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Redistributing orders to kitchen stations...');

        // Get orders without station assignments
        $query = Order::whereDoesntHave('stationAssignments');

        if ($this->option('pending')) {
            $query->where('order_status', 'pending');
        }

        $orders = $query->with('items.menuItem.category')->get();

        if ($orders->isEmpty()) {
            $this->info('✅ No orders need redistribution!');
            return 0;
        }

        $this->info("📦 Found {$orders->count()} orders without station assignments");

        $successful = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        foreach ($orders as $order) {
            try {
                $this->distributionService->distributeOrder($order);
                $successful++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ✗ Order #{$order->confirmation_code}: {$e->getMessage()}");
                $failed++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('✨ Distribution complete!');
        $this->info("  ✓ Successfully distributed: {$successful} orders");
        if ($failed > 0) {
            $this->warn("  ✗ Failed: {$failed} orders");
        }

        return 0;
    }
}
