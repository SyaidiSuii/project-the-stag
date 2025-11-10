<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataReconciliationService;
use App\Services\DataQualityCheckService;
use App\Services\BusinessIntelligenceService;
use App\Services\MenuIntelligenceService;
use Carbon\Carbon;

class TestAnalyticsServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:test {--date= : Date to test (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and demonstrate new analytics services with real data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        $this->info('');
        $this->info('========================================');
        $this->info('  ANALYTICS SERVICES DEMONSTRATION');
        $this->info('========================================');
        $this->info('  Testing Date: ' . $date->toDateString());
        $this->info('========================================');
        $this->info('');

        // 1. Data Quality Check
        $this->testDataQuality($date);

        // 2. Data Reconciliation
        $this->testDataReconciliation($date);

        // 3. Business Intelligence
        $this->testBusinessIntelligence();

        // 4. Menu Intelligence
        $this->testMenuIntelligence();

        $this->info('');
        $this->info('========================================');
        $this->info('✅ ALL TESTS COMPLETED SUCCESSFULLY');
        $this->info('========================================');
        $this->info('');

        return 0;
    }

    private function testDataQuality($date)
    {
        $this->info('📊 [1/4] Data Quality Check');
        $this->info('─────────────────────────────────────');

        $service = app(DataQualityCheckService::class);
        $result = $service->runQualityChecks($date, false);

        $statusColor = match($result['overall_status']) {
            'passed' => 'green',
            'warning' => 'yellow',
            'failed' => 'red',
            default => 'white',
        };

        $this->line("Status: <fg={$statusColor}>{$result['overall_status']}</>");
        $this->line("Issues Found: {$result['issues_found']}");

        if (!empty($result['recommendations'])) {
            $this->warn('Recommendations:');
            foreach ($result['recommendations'] as $rec) {
                $this->line("  • {$rec}");
            }
        }

        $this->info('');
    }

    private function testDataReconciliation($date)
    {
        $this->info('🔄 [2/4] Data Reconciliation');
        $this->info('─────────────────────────────────────');

        $service = app(DataReconciliationService::class);
        $result = $service->reconcileDate($date);

        $this->line("Status: {$result['status']}");

        if (isset($result['accuracy_percentage'])) {
            $accuracy = $result['accuracy_percentage'];
            $color = $accuracy >= 99 ? 'green' : ($accuracy >= 95 ? 'yellow' : 'red');
            $this->line("Accuracy: <fg={$color}>{$accuracy}%</>");
            $this->line("Discrepancies: " . count($result['discrepancies']));

            if (!empty($result['discrepancies'])) {
                $this->warn('Top Discrepancies:');
                foreach (array_slice($result['discrepancies'], 0, 3) as $disc) {
                    $this->line("  • {$disc['field']}: {$disc['severity']} ({$disc['percentage_diff']}% diff)");
                }
            }
        }

        $this->info('');
    }

    private function testBusinessIntelligence()
    {
        $this->info('📈 [3/4] Business Intelligence Analysis');
        $this->info('─────────────────────────────────────');

        $service = app(BusinessIntelligenceService::class);

        // Trend Analysis
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);
        $trends = $service->getTrendAnalysis($startDate, $endDate);

        if (isset($trends['revenue_trend'])) {
            $direction = $trends['revenue_trend']['direction'];
            $percentage = $trends['revenue_trend']['percentage'];

            $icon = match($direction) {
                'increasing' => '📈',
                'decreasing' => '📉',
                'stable' => '➡️',
                default => '•',
            };

            $this->line("{$icon} Revenue Trend: {$direction} ({$percentage}%)");
        }

        // Month-over-Month
        $mom = $service->getMonthOverMonthComparison(Carbon::today());
        $this->line('');
        $this->line('Month-over-Month Comparison:');
        $this->line("  Current: RM {$mom['current_month']['revenue']}");
        $this->line("  Previous: RM {$mom['previous_month']['revenue']}");

        $changeIcon = $mom['changes']['revenue']['direction'] === 'up' ? '⬆' : '⬇';
        $changeColor = $mom['changes']['revenue']['direction'] === 'up' ? 'green' : 'red';
        $this->line("  Change: <fg={$changeColor}>{$changeIcon} {$mom['changes']['revenue']['percentage']}%</>");

        // Forecast
        $forecast = $service->forecastRevenue(7, 30);
        if (isset($forecast['forecast'])) {
            $avgForecast = round(array_sum($forecast['forecast']) / count($forecast['forecast']), 2);
            $this->line('');
            $this->line("7-Day Revenue Forecast: RM {$avgForecast}/day");
            $this->line("Confidence: {$forecast['confidence']}");
        }

        $this->info('');
    }

    private function testMenuIntelligence()
    {
        $this->info('🍽️  [4/4] Menu Intelligence Analysis');
        $this->info('─────────────────────────────────────');

        $service = app(MenuIntelligenceService::class);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        $analysis = $service->getMenuPerformanceAnalysis($startDate, $endDate);

        $this->line("Total Items Analyzed: {$analysis['total_items_analyzed']}");
        $this->line("Average Score: {$analysis['summary']['average_score']}");
        $this->line("Items Needing Attention: {$analysis['summary']['items_needing_attention']}");

        // Top Performers
        if (!empty($analysis['top_performers'])) {
            $this->line('');
            $this->info('🌟 Top 3 Performers:');
            foreach (array_slice($analysis['top_performers'], 0, 3) as $i => $item) {
                $badge = ['🥇', '🥈', '🥉'][$i];
                $this->line("  {$badge} {$item['name']}");
                $this->line("     Score: {$item['performance_score']} | Grade: {$item['performance_grade']}");
                $this->line("     Revenue: RM {$item['metrics']['total_revenue']} | Orders: {$item['metrics']['order_count']}");
            }
        }

        // Underperformers
        if (!empty($analysis['underperformers']) && $analysis['underperformers'][0]['performance_score'] < 50) {
            $this->line('');
            $this->warn('⚠️  Items Needing Attention:');
            foreach (array_slice($analysis['underperformers'], 0, 3) as $item) {
                $this->line("  • {$item['name']} (Score: {$item['performance_score']})");
                if (!empty($item['recommendations'])) {
                    $this->line("    → " . $item['recommendations'][0]);
                }
            }
        }

        // Pricing Opportunities
        $pricing = $service->getPricingOpportunities($startDate, $endDate);
        if ($pricing['count'] > 0) {
            $this->line('');
            $this->info("💰 Pricing Opportunities: {$pricing['count']} found");
            foreach (array_slice($pricing['opportunities'], 0, 2) as $opp) {
                $this->line("  • {$opp['item']}: RM {$opp['current_price']} → RM {$opp['suggested_price']}");
                $this->line("    {$opp['reason']}");
            }
        }

        // Bundle Opportunities
        $bundles = $service->getBundleOpportunities($startDate, $endDate);
        if ($bundles['count'] > 0) {
            $this->line('');
            $this->info("🎁 Bundle Opportunities: {$bundles['count']} found");
            foreach (array_slice($bundles['bundle_opportunities'], 0, 2) as $bundle) {
                $this->line("  • {$bundle['items'][0]} + {$bundle['items'][1]}");
                $this->line("    Bundle Price: RM {$bundle['suggested_bundle_price']} (save {$bundle['discount_percentage']}%)");
                $this->line("    Ordered together {$bundle['frequency']} times");
            }
        }

        $this->info('');
    }
}
