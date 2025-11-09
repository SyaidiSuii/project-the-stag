<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOrderDelays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-delays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for delayed orders and send notifications';

    /**
     * Delay threshold in minutes
     */
    const DELAY_THRESHOLD = 10;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for delayed orders...');

        try {
            // Get all active orders (pending, confirmed, preparing, ready)
            $activeOrders = \App\Models\Order::whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
                ->whereNotNull('estimated_completion_time')
                ->with(['user', 'items'])
                ->get();

            $this->info("Found {$activeOrders->count()} active orders to check.");

            $delayedCount = 0;
            $now = \Carbon\Carbon::now();

            foreach ($activeOrders as $order) {
                // Calculate if order is delayed
                $estimatedTime = \Carbon\Carbon::parse($order->estimated_completion_time);

                if ($now->gt($estimatedTime)) {
                    $delayMinutes = $now->diffInMinutes($estimatedTime);

                    // Only trigger alert if delay exceeds threshold
                    if ($delayMinutes >= self::DELAY_THRESHOLD) {
                        // Check if we already sent a delay notification
                        // (to avoid spamming customer every 5 minutes)
                        $lastDelayAlert = $order->activityLogs()
                            ->where('activity_type', 'warning')
                            ->where('title', 'Order Delayed')
                            ->latest()
                            ->first();

                        // Only send alert if:
                        // 1. No previous delay alert OR
                        // 2. Last delay alert was sent more than 15 minutes ago (escalation)
                        $shouldAlert = !$lastDelayAlert ||
                                      $lastDelayAlert->created_at->lt(now()->subMinutes(15));

                        if ($shouldAlert) {
                            \Illuminate\Support\Facades\Log::info('Order delay detected', [
                                'order_id' => $order->id,
                                'confirmation_code' => $order->confirmation_code,
                                'delay_minutes' => $delayMinutes,
                                'estimated_time' => $estimatedTime->toDateTimeString(),
                                'current_time' => $now->toDateTimeString(),
                            ]);

                            // Fire event
                            event(new \App\Events\OrderDelayedEvent($order, $delayMinutes));

                            $delayedCount++;
                            $this->warn("Order #{$order->confirmation_code} delayed by {$delayMinutes} minutes");
                        }
                    }
                }
            }

            $this->info("Delay check completed. {$delayedCount} delayed orders detected.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to check order delays', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to check order delays: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
