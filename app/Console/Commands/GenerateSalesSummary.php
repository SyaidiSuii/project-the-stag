<?php

namespace App\Console\Commands;

use App\Models\DailySalesSummary;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSalesSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sales-summary {--date= : The date to generate the summary for (YYYY-MM-DD). Defaults to yesterday.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a summary of daily sales and store it in the daily_sales_summary table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily sales summary generation...');
        Log::info('GenerateSalesSummary command is running.');

        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();

        $this->info('Generating summary for date: ' . $date->toDateString());

        // Get all completed or served orders for the specified date
        $orders = Order::whereIn('order_status', ['completed', 'served'])
            ->whereDate('created_at', $date)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No completed orders found for ' . $date->toDateString() . '.');
            // Still create a summary with zero values
            DailySalesSummary::updateOrCreate(
                ['date' => $date->toDateString()],
                [
                    'total_revenue' => 0,
                    'total_orders' => 0,
                    'total_items_sold' => 0,
                ]
            );
            return;
        }

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $totalItemsSold = $orders->reduce(function ($carry, $order) {
            return $carry + $order->items->sum('quantity');
        }, 0);

        // Store the summary
        $summary = DailySalesSummary::updateOrCreate(
            ['date' => $date->toDateString()],
            [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_items_sold' => $totalItemsSold,
            ]
        );

        $this->info('Successfully generated sales summary for ' . $date->toDateString());
        $this->info("Total Revenue: {$summary->total_revenue}");
        $this->info("Total Orders: {$summary->total_orders}");
        $this->info("Total Items Sold: {$summary->total_items_sold}");

        Log::info('GenerateSalesSummary command finished successfully.');
    }
}
