<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Business Insight Generator
 * Automatically generates actionable business insights from analytics data
 */
class BusinessInsightGenerator
{
    protected $biService;
    protected $menuService;
    protected $menuRecommendationService;
    protected $qualityService;

    public function __construct(
        BusinessIntelligenceService $biService,
        MenuIntelligenceService $menuService,
        MenuRecommendationService $menuRecommendationService,
        DataQualityCheckService $qualityService
    ) {
        $this->biService = $biService;
        $this->menuService = $menuService;
        $this->menuRecommendationService = $menuRecommendationService;
        $this->qualityService = $qualityService;
    }

    /**
     * Generate comprehensive business insights
     *
     * @param Carbon|null $date
     * @return array
     */
    public function generateInsights(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();
        $endDate = $date;
        $startDate = $date->copy()->subDays(30);

        $insights = [];

        // 1. Revenue Insights
        $insights['revenue'] = $this->generateRevenueInsights();

        // 2. Menu Performance Insights
        $insights['menu'] = $this->generateMenuInsights($startDate, $endDate);

        // 3. Customer Behavior Insights
        $insights['customers'] = $this->generateCustomerInsights();

        // 4. Operational Insights
        $insights['operations'] = $this->generateOperationalInsights();

        // 5. Risk & Alert Insights
        $insights['risks'] = $this->generateRiskInsights($date);

        // 6. Opportunity Insights
        $insights['opportunities'] = $this->generateOpportunityInsights($startDate, $endDate);

        // 7. Executive Summary
        $insights['executive_summary'] = $this->generateExecutiveSummary($insights);

        return [
            'insights' => $insights,
            'generated_at' => now()->toDateTimeString(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Generate revenue-focused insights
     *
     * @return array
     */
    private function generateRevenueInsights(): array
    {
        $insights = [];
        $mom = $this->biService->getMonthOverMonthComparison(Carbon::today());

        // Revenue trend insight
        $revenueChange = $mom['changes']['revenue'];
        if ($revenueChange['direction'] === 'up') {
            if ($revenueChange['percentage'] > 50) {
                $insights[] = [
                    'type' => 'positive',
                    'priority' => 'high',
                    'title' => 'Exceptional Revenue Growth',
                    'message' => "Revenue increased by {$revenueChange['percentage']}% from last month (RM {$revenueChange['previous']} → RM {$revenueChange['current']})",
                    'action' => 'Analyze what worked well and replicate success factors',
                ];
            } elseif ($revenueChange['percentage'] > 10) {
                $insights[] = [
                    'type' => 'positive',
                    'priority' => 'medium',
                    'title' => 'Steady Revenue Growth',
                    'message' => "Revenue grew {$revenueChange['percentage']}% month-over-month",
                    'action' => 'Continue current strategies while exploring expansion opportunities',
                ];
            }
        } elseif ($revenueChange['direction'] === 'down') {
            $severity = $revenueChange['percentage'] < -20 ? 'critical' : 'high';
            $insights[] = [
                'type' => 'negative',
                'priority' => $severity,
                'title' => 'Revenue Decline Alert',
                'message' => "Revenue decreased by {$revenueChange['percentage']}% compared to last month",
                'action' => 'Immediate action required: Review pricing, promotions, and customer feedback',
            ];
        }

        // Average order value insight
        $aovChange = $mom['changes']['avg_order_value'];
        if ($aovChange['percentage'] < -10) {
            $insights[] = [
                'type' => 'warning',
                'priority' => 'medium',
                'title' => 'Declining Average Order Value',
                'message' => "AOV dropped by {$aovChange['percentage']}%",
                'action' => 'Consider upselling strategies and combo deals',
            ];
        } elseif ($aovChange['percentage'] > 15) {
            $insights[] = [
                'type' => 'positive',
                'priority' => 'low',
                'title' => 'Improved Customer Spend',
                'message' => "Average order value increased by {$aovChange['percentage']}%",
                'action' => 'Current upselling strategies are working well',
            ];
        }

        return $insights;
    }

    /**
     * Generate menu-focused insights
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function generateMenuInsights(Carbon $startDate, Carbon $endDate): array
    {
        $insights = [];
        $analysis = $this->menuService->getMenuPerformanceAnalysis($startDate, $endDate);

        // Menu health check
        $needingAttention = $analysis['summary']['items_needing_attention'];
        $totalItems = $analysis['total_items_analyzed'];
        $healthPercentage = (($totalItems - $needingAttention) / $totalItems) * 100;

        if ($healthPercentage < 50) {
            $underperformingPercentage = round(($needingAttention / $totalItems) * 100, 1);
            $insights[] = [
                'type' => 'warning',
                'priority' => 'high',
                'title' => 'Menu Needs Optimization',
                'message' => "{$needingAttention} out of {$totalItems} items ({$underperformingPercentage}%) are underperforming",
                'action' => 'Review and consider removing or revamping underperforming items',
            ];
        }

        // Top performer insight
        if (!empty($analysis['top_performers'])) {
            $topItem = $analysis['top_performers'][0];
            $insights[] = [
                'type' => 'positive',
                'priority' => 'medium',
                'title' => 'Star Menu Item',
                'message' => "{$topItem['name']} is your top performer with RM {$topItem['metrics']['total_revenue']} revenue",
                'action' => 'Feature this item in promotions and consider creating variations',
            ];
        }

        // Pricing opportunities
        $pricing = $this->menuService->getPricingOpportunities($startDate, $endDate);
        if ($pricing['count'] > 0) {
            $insights[] = [
                'type' => 'opportunity',
                'priority' => 'medium',
                'title' => 'Pricing Optimization Opportunity',
                'message' => "Found {$pricing['count']} items with pricing optimization potential",
                'action' => 'Review suggested price adjustments for revenue optimization',
            ];
        }

        return $insights;
    }

    /**
     * Generate customer behavior insights
     *
     * @return array
     */
    private function generateCustomerInsights(): array
    {
        $insights = [];
        $mom = $this->biService->getMonthOverMonthComparison(Carbon::today());

        // Customer count trend
        $customerChange = $mom['changes']['customers'];
        if ($customerChange['direction'] === 'down' && abs($customerChange['percentage']) > 15) {
            $insights[] = [
                'type' => 'warning',
                'priority' => 'high',
                'title' => 'Customer Base Declining',
                'message' => "Customer count decreased by {$customerChange['percentage']}%",
                'action' => 'Launch customer retention campaign and gather feedback',
            ];
        } elseif ($customerChange['direction'] === 'up' && $customerChange['percentage'] > 20) {
            $insights[] = [
                'type' => 'positive',
                'priority' => 'medium',
                'title' => 'Growing Customer Base',
                'message' => "Gained {$customerChange['percentage']}% more customers",
                'action' => 'Capitalize on growth with loyalty programs',
            ];
        }

        return $insights;
    }

    /**
     * Generate operational insights
     *
     * @return array
     */
    private function generateOperationalInsights(): array
    {
        $insights = [];

        // Peak hours analysis
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);
        $peakHours = $this->biService->getPeakHoursAnalysis($startDate, $endDate);

        if (!empty($peakHours['peak_hours'])) {
            $topHour = array_key_first($peakHours['peak_hours']);
            $orderCount = $peakHours['peak_hours'][$topHour];

            $insights[] = [
                'type' => 'info',
                'priority' => 'medium',
                'title' => 'Peak Hours Identified',
                'message' => "Busiest time: {$peakHours['peak_hours_formatted'][0]} with {$orderCount} orders",
                'action' => 'Ensure adequate staffing during peak hours',
            ];
        }

        // Trending items
        $trending = $this->menuRecommendationService->getTrendingItems(7, 5);
        if (!empty($trending['trending_items'])) {
            $insights[] = [
                'type' => 'opportunity',
                'priority' => 'low',
                'title' => 'Trending Items Detected',
                'message' => count($trending['trending_items']) . " items showing upward trend",
                'action' => 'Ensure stock availability and promote trending items',
            ];
        }

        return $insights;
    }

    /**
     * Generate risk and alert insights
     *
     * @param Carbon $date
     * @return array
     */
    private function generateRiskInsights(Carbon $date): array
    {
        $insights = [];

        // Data quality check
        $qualityResult = $this->qualityService->runQualityChecks($date, false);
        if ($qualityResult['overall_status'] === 'failed') {
            $insights[] = [
                'type' => 'critical',
                'priority' => 'critical',
                'title' => 'Data Quality Issues Detected',
                'message' => "{$qualityResult['issues_found']} data quality issues found",
                'action' => 'Run data reconciliation and auto-fix immediately',
            ];
        }

        // Anomaly detection
        $anomalies = $this->biService->detectAnomalies($date, 30);
        if (!empty($anomalies['anomalies'])) {
            foreach ($anomalies['anomalies'] as $anomaly) {
                if ($anomaly['severity'] === 'critical' || $anomaly['severity'] === 'high') {
                    $insights[] = [
                        'type' => 'alert',
                        'priority' => $anomaly['severity'],
                        'title' => ucfirst($anomaly['metric']) . ' Anomaly Detected',
                        'message' => "Unusual {$anomaly['metric']}: {$anomaly['current_value']} (expected: {$anomaly['expected_range']['mean']})",
                        'action' => 'Investigate cause of anomaly',
                    ];
                }
            }
        }

        return $insights;
    }

    /**
     * Generate opportunity insights
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function generateOpportunityInsights(Carbon $startDate, Carbon $endDate): array
    {
        $insights = [];

        // Bundle opportunities
        $bundles = $this->menuService->getBundleOpportunities($startDate, $endDate);
        if ($bundles['count'] > 0) {
            $topBundle = $bundles['bundle_opportunities'][0];
            $insights[] = [
                'type' => 'opportunity',
                'priority' => 'high',
                'title' => 'High-Value Bundle Opportunity',
                'message' => "{$topBundle['items'][0]} + {$topBundle['items'][1]} ordered together {$topBundle['frequency']} times",
                'action' => "Create combo deal at RM {$topBundle['suggested_bundle_price']} (potential RM {$topBundle['potential_revenue']} revenue)",
            ];
        }

        // Forecast-based opportunity
        $forecast = $this->biService->forecastRevenue(7, 30);
        if (isset($forecast['forecast'])) {
            $avgForecast = array_sum($forecast['forecast']) / count($forecast['forecast']);
            $insights[] = [
                'type' => 'info',
                'priority' => 'low',
                'title' => '7-Day Revenue Forecast',
                'message' => "Expected daily revenue: RM " . round($avgForecast, 2),
                'action' => "Plan inventory and staffing accordingly (confidence: {$forecast['confidence']})",
            ];
        }

        // Menu improvement suggestions
        $improvements = $this->menuRecommendationService->getMenuImprovementSuggestions();
        if ($improvements['total_suggestions'] > 0) {
            $highPriority = collect($improvements['suggestions'])
                ->where('priority', 'high')
                ->count();

            if ($highPriority > 0) {
                $insights[] = [
                    'type' => 'opportunity',
                    'priority' => 'high',
                    'title' => 'Menu Optimization Opportunities',
                    'message' => "Found {$highPriority} high-priority menu improvements",
                    'action' => 'Review menu improvement suggestions',
                ];
            }
        }

        return $insights;
    }

    /**
     * Generate executive summary
     *
     * @param array $allInsights
     * @return array
     */
    private function generateExecutiveSummary(array $allInsights): array
    {
        $allItems = [];
        foreach ($allInsights as $category => $items) {
            $allItems = array_merge($allItems, $items);
        }

        // Count by priority
        $priorityCounts = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
        ];

        $topInsights = [];
        foreach ($allItems as $insight) {
            $priority = $insight['priority'] ?? 'low';
            $priorityCounts[$priority]++;

            // Collect top 5 high-priority insights
            if (in_array($priority, ['critical', 'high']) && count($topInsights) < 5) {
                $topInsights[] = $insight;
            }
        }

        return [
            'total_insights' => count($allItems),
            'priority_breakdown' => $priorityCounts,
            'critical_items' => $priorityCounts['critical'],
            'requires_immediate_attention' => $priorityCounts['critical'] + $priorityCounts['high'],
            'top_priority_insights' => $topInsights,
            'health_score' => $this->calculateHealthScore($priorityCounts),
        ];
    }

    /**
     * Calculate overall business health score
     *
     * @param array $priorityCounts
     * @return array
     */
    private function calculateHealthScore(array $priorityCounts): array
    {
        // Scoring: critical = -10, high = -5, medium = -2, low = 0
        $score = 100;
        $score -= $priorityCounts['critical'] * 10;
        $score -= $priorityCounts['high'] * 5;
        $score -= $priorityCounts['medium'] * 2;

        $score = max(0, min(100, $score));

        $grade = 'A+';
        if ($score < 95) $grade = 'A';
        if ($score < 90) $grade = 'B+';
        if ($score < 85) $grade = 'B';
        if ($score < 80) $grade = 'C+';
        if ($score < 75) $grade = 'C';
        if ($score < 70) $grade = 'D';
        if ($score < 60) $grade = 'F';

        $status = $score >= 80 ? 'healthy' : ($score >= 60 ? 'needs_attention' : 'critical');

        return [
            'score' => round($score, 1),
            'grade' => $grade,
            'status' => $status,
        ];
    }

    /**
     * Get actionable recommendations based on insights
     *
     * @param array|null $insights
     * @return array
     */
    public function getActionableRecommendations(?array $insights = null): array
    {
        if ($insights === null) {
            $insights = $this->generateInsights();
        }

        $recommendations = [];

        // Extract all insights
        $allInsights = [];
        foreach ($insights['insights'] as $category => $items) {
            if ($category === 'executive_summary' || !is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (is_array($item) && isset($item['priority'])) {
                    $item['category'] = $category;
                    $allInsights[] = $item;
                }
            }
        }

        // Sort by priority
        usort($allInsights, function($a, $b) {
            $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            return ($priorityOrder[$a['priority']] ?? 99) <=> ($priorityOrder[$b['priority']] ?? 99);
        });

        // Group recommendations
        foreach (array_slice($allInsights, 0, 10) as $insight) {
            if (isset($insight['action'])) {
                $recommendations[] = [
                    'category' => $insight['category'],
                    'priority' => $insight['priority'],
                    'title' => $insight['title'],
                    'action' => $insight['action'],
                    'type' => $insight['type'],
                ];
            }
        }

        return $recommendations;
    }
}
