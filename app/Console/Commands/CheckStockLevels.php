<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StockReplenishmentService;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check
                            {--critical : Show only critical stock items}
                            {--alert : Send low stock alerts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock levels and display low/critical stock items';

    protected $replenishmentService;

    /**
     * Create a new command instance.
     */
    public function __construct(StockReplenishmentService $replenishmentService)
    {
        parent::__construct();
        $this->replenishmentService = $replenishmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking stock levels...');
        $this->newLine();

        // Get stock items based on flags
        if ($this->option('critical')) {
            $stockItems = $this->replenishmentService->getCriticalStockItems();
            $title = 'CRITICAL Stock Items (Below Minimum Threshold)';
        } else {
            $stockItems = $this->replenishmentService->checkStockLevels();
            $title = 'Low Stock Items (Below Reorder Point)';
        }

        if ($stockItems->isEmpty()) {
            $this->info('✅ All stock levels are good!');
            return 0;
        }

        $this->warn($title);
        $this->newLine();

        // Display table
        $tableData = $stockItems->map(function ($item) {
            return [
                'ID' => $item->id,
                'Name' => $item->name,
                'Current' => number_format($item->current_quantity, 2) . ' ' . $item->unit_of_measure,
                'Reorder Point' => number_format($item->reorder_point, 2),
                'Min Threshold' => number_format($item->minimum_threshold, 2),
                'Status' => $item->stock_status_text,
                'Supplier' => $item->supplier ? $item->supplier->name : 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Current', 'Reorder Point', 'Min Threshold', 'Status', 'Supplier'],
            $tableData
        );

        $this->newLine();
        $this->info('Total items needing attention: ' . count($stockItems));

        // Send alerts if requested
        if ($this->option('alert')) {
            $this->newLine();
            $this->info('📧 Sending low stock alerts...');
            $result = $this->replenishmentService->sendLowStockAlerts();
            $this->info($result['message']);
        }

        return 0;
    }
}
