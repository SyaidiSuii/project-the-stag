<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DetectUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:detect-unpaid-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect completed orders that remain unpaid after 4-5 hours and flag them for admin attention';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for unpaid orders...');

        // Get completed/served orders that are still unpaid
        // and were completed more than 5 hours ago
        $fiveHoursAgo = Carbon::now()->subHours(5);

        $unpaidOrders = Order::where('payment_status', 'unpaid')
            ->whereIn('order_status', ['completed', 'served'])
            ->where(function ($query) use ($fiveHoursAgo) {
                $query->where('actual_completion_time', '<=', $fiveHoursAgo)
                      ->orWhere(function ($q) use ($fiveHoursAgo) {
                          // If no actual_completion_time, use updated_at as fallback
                          $q->whereNull('actual_completion_time')
                            ->where('updated_at', '<=', $fiveHoursAgo);
                      });
            })
            ->where('is_flagged_unpaid', false) // Only flag once
            ->get();

        if ($unpaidOrders->isEmpty()) {
            $this->info('✅ No unpaid orders found.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Found {$unpaidOrders->count()} unpaid order(s):");

        foreach ($unpaidOrders as $order) {
            // Flag the order as unpaid
            $order->update([
                'is_flagged_unpaid' => true,
                'unpaid_alert_sent_at' => now(),
            ]);

            // Calculate how long it's been unpaid
            $completionTime = $order->actual_completion_time ?? $order->updated_at;
            $hoursUnpaid = $completionTime->diffInHours(now());

            $this->line("  📋 Order #{$order->confirmation_code}");
            $this->line("     Customer: {$order->customer_name}");
            $this->line("     Amount: RM {$order->total_amount}");
            $this->line("     Completed: {$completionTime->format('Y-m-d H:i:s')} ({$hoursUnpaid}h ago)");
            $this->line("     Payment Method: {$order->payment_method}");

            // Log the unpaid order
            Log::warning('Unpaid order detected', [
                'order_id' => $order->id,
                'confirmation_code' => $order->confirmation_code,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'hours_unpaid' => $hoursUnpaid,
                'completed_at' => $completionTime->toDateTimeString(),
            ]);

            // TODO: Send notification to admin (email, SMS, or dashboard alert)
            // This can be implemented later based on your notification preferences
        }

        $this->info("✅ Flagged {$unpaidOrders->count()} unpaid order(s) for admin review.");

        return Command::SUCCESS;
    }
}
