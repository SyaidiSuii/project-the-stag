<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class CleanupAbandonedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-abandoned-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Soft delete orders with unpaid online payments that are older than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up abandoned orders...');

        // Find orders that are:
        // 1. Older than 30 minutes
        // 2. Have payment_status = 'unpaid'
        // 3. Have payment_method = 'online'
        // 4. Not already soft deleted
        $cutoffTime = Carbon::now()->subMinutes(30);

        $abandonedOrders = Order::where('payment_status', 'unpaid')
            ->where('payment_method', 'online')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $count = 0;

        foreach ($abandonedOrders as $order) {
            // Mark order as cancelled before soft deleting
            $order->update(['order_status' => 'cancelled']);

            // Soft delete associated payment records
            Payment::where('order_id', $order->id)->delete();

            // Soft delete the order (Order model uses SoftDeletes trait)
            $order->delete();

            $count++;
        }

        $this->info("Soft deleted {$count} abandoned order(s).");

        return 0;
    }
}
