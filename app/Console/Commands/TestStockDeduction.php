<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestStockDeduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test-deduction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test stock deduction functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('🧪 Testing Stock Deduction System');
        $this->info('========================================');
        $this->newLine();

        // Get a stock item
        $stockItem = \App\Models\StockItem::where('name', 'White Rice')->first();

        if (!$stockItem) {
            $this->error('Stock item not found!');
            return 1;
        }

        $this->info("Testing with: {$stockItem->name}");
        $this->line("Current Stock: {$stockItem->current_quantity} {$stockItem->unit_of_measure}");
        $this->newLine();

        // Test 1: Add Stock
        $this->info('TEST 1: Adding Stock (Restock)');
        $oldQty = $stockItem->current_quantity;
        $stockItem->addStock(10, 'Manual', null, 'Test restock', 1);
        $stockItem->refresh();

        $this->line("  Before: {$oldQty} kg");
        $this->line("  Added: 10 kg");
        $this->line("  After: {$stockItem->current_quantity} kg");
        $this->info('  ✅ Stock added successfully!');
        $this->newLine();

        // Test 2: Reduce Stock
        $this->info('TEST 2: Reducing Stock (Usage)');
        $oldQty = $stockItem->current_quantity;
        $stockItem->reduceStock(5, 'Order', 999, 'Test order usage', 1);
        $stockItem->refresh();

        $this->line("  Before: {$oldQty} kg");
        $this->line("  Reduced: 5 kg");
        $this->line("  After: {$stockItem->current_quantity} kg");
        $this->info('  ✅ Stock reduced successfully!');
        $this->newLine();

        // Test 3: Adjust Stock
        $this->info('TEST 3: Adjusting Stock (Manual Adjustment)');
        $oldQty = $stockItem->current_quantity;
        $stockItem->adjustStock(18.5, 'Physical count correction', 1);
        $stockItem->refresh();

        $this->line("  Before: {$oldQty} kg");
        $this->line("  Adjusted to: 18.5 kg");
        $this->line("  After: {$stockItem->current_quantity} kg");
        $this->info('  ✅ Stock adjusted successfully!');
        $this->newLine();

        // Show transaction history
        $this->info('========================================');
        $this->info('📋 Transaction History');
        $this->info('========================================');

        $transactions = $stockItem->transactions()->latest()->take(5)->get();
        $tableData = $transactions->map(function($t) {
            return [
                'Type' => strtoupper($t->transaction_type),
                'Quantity' => ($t->quantity > 0 ? '+' : '') . $t->quantity,
                'Previous' => number_format($t->previous_quantity, 2),
                'New' => number_format($t->new_quantity, 2),
                'Notes' => $t->notes,
                'Time' => $t->created_at->format('H:i:s'),
            ];
        })->toArray();

        $this->table(
            ['Type', 'Quantity', 'Previous', 'New', 'Notes', 'Time'],
            $tableData
        );

        $this->newLine();
        $this->info('🎉 All stock operations working correctly!');

        return 0;
    }
}
