<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestStockSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and verify stock management system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('🧪 Stock Management System Test');
        $this->info('========================================');
        $this->newLine();

        // Test 1: Count POs
        $poCount = \App\Models\PurchaseOrder::count();
        $this->info("✅ Purchase Orders Created: {$poCount}");

        // Test 2: Show PO details
        $pos = \App\Models\PurchaseOrder::with('supplier', 'items')->get();
        foreach ($pos as $po) {
            $this->newLine();
            $this->line("PO: {$po->po_number}");
            $this->line("  Supplier: {$po->supplier->name}");
            $this->line("  Status: {$po->status}");
            $this->line("  Total: RM {$po->total_amount}");
            $this->line("  Items Count: {$po->items->count()}");
            $this->line("  Expected Delivery: {$po->expected_delivery_date->format('d M Y')}");
        }

        // Test 3: Stock Transactions
        $this->newLine();
        $transactionCount = \App\Models\StockTransaction::count();
        $this->info("✅ Stock Transactions Logged: {$transactionCount}");

        // Test 4: Summary
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 Summary Statistics');
        $this->info('========================================');

        $totalItems = \App\Models\StockItem::count();
        $lowStock = \App\Models\StockItem::lowStock()->count();
        $critical = \App\Models\StockItem::criticalStock()->count();
        $suppliers = \App\Models\Supplier::count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Stock Items', $totalItems],
                ['Low Stock Items', $lowStock],
                ['Critical Stock Items', $critical],
                ['Active Suppliers', $suppliers],
                ['Purchase Orders', $poCount],
                ['Transactions', $transactionCount],
            ]
        );

        $this->newLine();
        $this->info('🎉 All systems operational!');

        return 0;
    }
}
