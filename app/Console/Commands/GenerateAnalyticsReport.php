<?php

namespace App\Console\Commands;

use App\Models\SaleAnalytics;
use App\Services\AnalyticsRecalculationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsReport extends Command
{
    /**
     * The analytics recalculation service instance.
     *
     * @var AnalyticsRecalculationService
     */
    protected $analyticsService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:generate {--date= : The date to generate analytics for (YYYY-MM-DD). Defaults to yesterday.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate comprehensive daily analytics report for sales, orders, menu items, bookings, QR usage, promotions, and rewards';

    /**
     * Create a new command instance.
     */
    public function __construct(AnalyticsRecalculationService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting comprehensive analytics generation...');
        Log::info('GenerateAnalyticsReport command started.');

        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        $this->info('📅 Generating analytics for: ' . $date->toDateString());

        try {
            // 🔥 USE THE SHARED SERVICE FOR CALCULATION AND SAVING
            $analytics = $this->analyticsService->recalculateAndSave($date);

            $this->info('✅ Analytics generated successfully!');
            $this->displaySummary($analytics);

            Log::info('GenerateAnalyticsReport completed successfully.', [
                'date' => $date->toDateString(),
                'total_sales' => $analytics->total_sales,
                'total_orders' => $analytics->total_orders,
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Error generating analytics: ' . $e->getMessage());
            Log::error('GenerateAnalyticsReport failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Display summary in console
     */
    private function displaySummary($analytics)
    {
        $this->newLine();
        $this->info('📈 ANALYTICS SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("💰 Total Sales: RM " . number_format($analytics->total_sales, 2));
        $this->line("📦 Total Orders: {$analytics->total_orders}");
        $this->line("📊 Average Order Value: RM " . number_format($analytics->average_order_value, 2));
        $this->line("👥 Unique Customers: {$analytics->unique_customers}");
        $this->line("🆕 New Customers: {$analytics->new_customers}");
        $this->line("🔁 Returning Customers: {$analytics->returning_customers}");
        $this->line("📱 QR Orders: {$analytics->qr_orders}");
        $this->line("📅 Table Bookings: " . ($analytics->table_booking_count ?? 0));
        $this->line("🎁 Promotions Used: " . ($analytics->promotion_usage_count ?? 0));
        $this->line("⭐ Rewards Redeemed: " . ($analytics->rewards_redeemed_count ?? 0));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
    }
}
