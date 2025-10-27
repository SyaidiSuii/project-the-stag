<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearOrderHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:clear {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all order history and reset kitchen loads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ask for confirmation unless --force flag is used
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will DELETE ALL orders and related data. Are you sure?')) {
                $this->info('❌ Operation cancelled.');
                return 0;
            }
        }

        $this->info('🗑️  Clearing order history...');

        DB::beginTransaction();

        try {
            // Delete in correct order to avoid foreign key constraints
            $stationAssignments = DB::table('station_assignments')->count();
            DB::table('station_assignments')->delete();
            $this->line("   ✓ Deleted {$stationAssignments} station assignments");

            $kitchenLoads = DB::table('kitchen_loads')->count();
            DB::table('kitchen_loads')->delete();
            $this->line("   ✓ Deleted {$kitchenLoads} kitchen loads");

            $orderItems = DB::table('order_items')->count();
            DB::table('order_items')->delete();
            $this->line("   ✓ Deleted {$orderItems} order items");

            $orders = DB::table('orders')->count();
            DB::table('orders')->delete();
            $this->line("   ✓ Deleted {$orders} orders");

            // Reset kitchen station loads
            DB::table('kitchen_stations')->update(['current_load' => 0]);
            $this->line("   ✓ Reset all kitchen station loads to 0");

            DB::commit();

            $this->info('✅ Order history cleared successfully!');
            $this->newLine();
            $this->table(
                ['Item', 'Count'],
                [
                    ['Orders', $orders],
                    ['Order Items', $orderItems],
                    ['Station Assignments', $stationAssignments],
                    ['Kitchen Loads', $kitchenLoads],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Failed to clear order history: ' . $e->getMessage());
            return 1;
        }
    }
}
