<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StockReplenishmentService;

class GenerateAutoPurchaseOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:auto-order
                            {--approve : Automatically approve generated purchase orders}
                            {--dry-run : Show what would be ordered without creating POs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate purchase orders for low stock items';

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
        $this->info('🛒 Auto Stock Replenishment - Purchase Order Generation');
        $this->newLine();

        // Dry run mode
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No purchase orders will be created');
            $this->newLine();

            $lowStockItems = $this->replenishmentService->checkStockLevels();

            if ($lowStockItems->isEmpty()) {
                $this->info('✅ No low stock items found. No purchase orders needed.');
                return 0;
            }

            $this->info('The following items would be ordered:');
            $this->newLine();

            $tableData = $lowStockItems->map(function ($item) {
                return [
                    'Item' => $item->name,
                    'Current' => number_format($item->current_quantity, 2) . ' ' . $item->unit_of_measure,
                    'Order Qty' => number_format($item->reorder_quantity, 2),
                    'Unit Price' => 'RM ' . number_format($item->unit_price, 2),
                    'Total' => 'RM ' . number_format($item->reorder_quantity * $item->unit_price, 2),
                    'Supplier' => $item->supplier ? $item->supplier->name : 'N/A',
                ];
            })->toArray();

            $this->table(
                ['Item', 'Current', 'Order Qty', 'Unit Price', 'Total', 'Supplier'],
                $tableData
            );

            $totalValue = $lowStockItems->sum(function ($item) {
                return $item->reorder_quantity * $item->unit_price;
            });

            $this->newLine();
            $this->info('Total Order Value: RM ' . number_format($totalValue, 2));

            return 0;
        }

        // Generate actual purchase orders
        $this->info('Generating purchase orders...');

        $autoApprove = $this->option('approve');
        if ($autoApprove) {
            $this->warn('Auto-approve is ON - POs will be automatically approved');
        }

        $result = $this->replenishmentService->generateAutoPurchaseOrders($autoApprove);

        $this->newLine();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);

            if (!empty($result['purchase_orders'])) {
                $this->newLine();
                $this->info('Generated Purchase Orders:');

                $poData = collect($result['purchase_orders'])->map(function ($po) {
                    return [
                        'PO Number' => $po->po_number,
                        'Supplier' => $po->supplier->name,
                        'Items' => $po->items->count(),
                        'Total Amount' => 'RM ' . number_format($po->total_amount, 2),
                        'Status' => strtoupper($po->status),
                        'Delivery Date' => $po->expected_delivery_date->format('Y-m-d'),
                    ];
                })->toArray();

                $this->table(
                    ['PO Number', 'Supplier', 'Items', 'Total Amount', 'Status', 'Delivery Date'],
                    $poData
                );
            }
        } else {
            $this->error('❌ ' . $result['message']);
            return 1;
        }

        return 0;
    }
}
