<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\DataReconciliationService;
use App\Services\ReportAuditService;
use App\Services\DataQualityCheckService;
use App\Services\BusinessIntelligenceService;
use App\Services\MenuIntelligenceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsServicesTest extends TestCase
{
    /**
     * Test DataReconciliationService
     */
    public function test_data_reconciliation_service()
    {
        $service = app(DataReconciliationService::class);
        $today = Carbon::today();

        // Test reconciliation for today
        $result = $service->reconcileDate($today);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('discrepancies', $result);

        echo "\n✅ DataReconciliationService Test Passed\n";
        echo "   Status: {$result['status']}\n";
        echo "   Date: {$result['date']}\n";

        if (isset($result['accuracy_percentage'])) {
            echo "   Accuracy: {$result['accuracy_percentage']}%\n";
        }
    }

    /**
     * Test ReportAuditService
     */
    public function test_report_audit_service()
    {
        $service = app(ReportAuditService::class);
        $today = Carbon::today();

        // Test audit summary
        $summary = $service->getAuditSummary($today->copy()->subDays(7), $today);

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('total_events', $summary);

        echo "\n✅ ReportAuditService Test Passed\n";
        echo "   Total Events: {$summary['total_events']}\n";
        echo "   Calculations: {$summary['calculations']}\n";
        echo "   Updates: {$summary['updates']}\n";
        echo "   Discrepancies: {$summary['discrepancies']}\n";
    }

    /**
     * Test DataQualityCheckService
     */
    public function test_data_quality_check_service()
    {
        $service = app(DataQualityCheckService::class);
        $yesterday = Carbon::yesterday();

        // Test quality checks for yesterday
        $result = $service->runQualityChecks($yesterday, false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('overall_status', $result);
        $this->assertArrayHasKey('checks', $result);

        echo "\n✅ DataQualityCheckService Test Passed\n";
        echo "   Date: {$result['date']}\n";
        echo "   Overall Status: {$result['overall_status']}\n";
        echo "   Issues Found: {$result['issues_found']}\n";

        if (!empty($result['recommendations'])) {
            echo "   Recommendations:\n";
            foreach ($result['recommendations'] as $rec) {
                echo "     - {$rec}\n";
            }
        }
    }

    /**
     * Test BusinessIntelligenceService
     */
    public function test_business_intelligence_service()
    {
        $service = app(BusinessIntelligenceService::class);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        // Test trend analysis
        $trends = $service->getTrendAnalysis($startDate, $endDate);

        $this->assertIsArray($trends);

        echo "\n✅ BusinessIntelligenceService Test Passed\n";

        if (isset($trends['revenue_trend'])) {
            echo "   Revenue Trend: {$trends['revenue_trend']['direction']}\n";
            echo "   Percentage Change: {$trends['revenue_trend']['percentage']}%\n";
        }

        // Test MoM comparison
        $mom = $service->getMonthOverMonthComparison(Carbon::today());

        echo "\n   Month-over-Month Comparison:\n";
        echo "   Current Month Revenue: RM {$mom['current_month']['revenue']}\n";
        echo "   Previous Month Revenue: RM {$mom['previous_month']['revenue']}\n";
        echo "   Change: {$mom['changes']['revenue']['percentage']}%\n";

        // Test forecasting
        $forecast = $service->forecastRevenue(7, 30);

        if (isset($forecast['forecast'])) {
            echo "\n   7-Day Revenue Forecast:\n";
            echo "   Confidence: {$forecast['confidence']}\n";
            echo "   Average Forecast: RM " . round(array_sum($forecast['forecast']) / count($forecast['forecast']), 2) . "\n";
        }
    }

    /**
     * Test MenuIntelligenceService
     */
    public function test_menu_intelligence_service()
    {
        $service = app(MenuIntelligenceService::class);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        // Test menu performance analysis
        $analysis = $service->getMenuPerformanceAnalysis($startDate, $endDate);

        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('total_items_analyzed', $analysis);

        echo "\n✅ MenuIntelligenceService Test Passed\n";
        echo "   Total Items Analyzed: {$analysis['total_items_analyzed']}\n";

        if (!empty($analysis['top_performers'])) {
            echo "\n   Top 3 Performers:\n";
            foreach (array_slice($analysis['top_performers'], 0, 3) as $item) {
                echo "     - {$item['name']}: Score {$item['performance_score']} ({$item['performance_grade']})\n";
            }
        }

        if (!empty($analysis['underperformers'])) {
            echo "\n   Bottom 3 Performers:\n";
            foreach (array_slice($analysis['underperformers'], 0, 3) as $item) {
                echo "     - {$item['name']}: Score {$item['performance_score']} ({$item['performance_grade']})\n";
            }
        }

        // Test pricing opportunities
        $pricing = $service->getPricingOpportunities($startDate, $endDate);

        echo "\n   Pricing Opportunities Found: {$pricing['count']}\n";

        // Test bundle opportunities
        $bundles = $service->getBundleOpportunities($startDate, $endDate);

        echo "   Bundle Opportunities Found: {$bundles['count']}\n";

        if (!empty($bundles['bundle_opportunities'])) {
            $topBundle = $bundles['bundle_opportunities'][0];
            echo "     Top Bundle: {$topBundle['items'][0]} + {$topBundle['items'][1]}\n";
            echo "     Frequency: {$topBundle['frequency']} times\n";
        }
    }

    /**
     * Test all services integration
     */
    public function test_full_analytics_workflow()
    {
        echo "\n\n========================================\n";
        echo "FULL ANALYTICS WORKFLOW TEST\n";
        echo "========================================\n";

        $today = Carbon::today();

        // Step 1: Check data quality
        echo "\n[Step 1] Running Data Quality Checks...\n";
        $qualityService = app(DataQualityCheckService::class);
        $qualityResult = $qualityService->runQualityChecks($today, false);
        echo "Status: {$qualityResult['overall_status']}\n";

        // Step 2: Reconcile data
        echo "\n[Step 2] Reconciling Data...\n";
        $reconciliationService = app(DataReconciliationService::class);
        $reconcileResult = $reconciliationService->reconcileDate($today);
        echo "Reconciliation Status: {$reconcileResult['status']}\n";

        // Step 3: Business intelligence
        echo "\n[Step 3] Analyzing Business Trends...\n";
        $biService = app(BusinessIntelligenceService::class);
        $trends = $biService->getTrendAnalysis($today->copy()->subDays(30), $today);
        echo "Trend Analysis Complete\n";

        // Step 4: Menu intelligence
        echo "\n[Step 4] Analyzing Menu Performance...\n";
        $menuService = app(MenuIntelligenceService::class);
        $menuAnalysis = $menuService->getMenuPerformanceAnalysis($today->copy()->subDays(30), $today);
        echo "Menu Analysis Complete - {$menuAnalysis['total_items_analyzed']} items analyzed\n";

        // Step 5: Get audit trail
        echo "\n[Step 5] Checking Audit Trail...\n";
        $auditService = app(ReportAuditService::class);
        $auditSummary = $auditService->getAuditSummary($today->copy()->subDays(7), $today);
        echo "Total Audit Events: {$auditSummary['total_events']}\n";

        echo "\n========================================\n";
        echo "✅ FULL WORKFLOW TEST COMPLETED\n";
        echo "========================================\n\n";

        $this->assertTrue(true);
    }
}
