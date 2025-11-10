<?php

namespace App\Services;

use App\Models\SaleAnalytics;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Data Quality Check Service
 * Automated validation and quality assurance for analytics data
 */
class DataQualityCheckService
{
    protected $reconciliationService;
    protected $auditService;

    public function __construct(
        DataReconciliationService $reconciliationService,
        ReportAuditService $auditService
    ) {
        $this->reconciliationService = $reconciliationService;
        $this->auditService = $auditService;
    }

    /**
     * Run comprehensive data quality checks for a date
     *
     * @param Carbon|string $date
     * @param bool $autoFix
     * @return array
     */
    public function runQualityChecks($date, bool $autoFix = false): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        $checks = [
            'data_exists' => $this->checkDataExists($date),
            'data_completeness' => $this->checkDataCompleteness($date),
            'data_accuracy' => $this->checkDataAccuracy($date),
            'data_consistency' => $this->checkDataConsistency($date),
            'anomaly_detection' => $this->detectAnomalies($date),
        ];

        $overallStatus = $this->determineOverallStatus($checks);
        $issues = $this->collectIssues($checks);

        // Auto-fix if enabled and issues found
        if ($autoFix && !empty($issues)) {
            $fixResult = $this->reconciliationService->autoFixDiscrepancies($date);
            $checks['auto_fix_applied'] = $fixResult;
        }

        // Log results
        if (!empty($issues)) {
            $this->auditService->logDiscrepancy($date, $issues);
        }

        return [
            'date' => $date->toDateString(),
            'overall_status' => $overallStatus,
            'checks' => $checks,
            'issues_found' => count($issues),
            'issues' => $issues,
            'recommendations' => $this->generateRecommendations($checks),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Run quality checks for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param bool $autoFix
     * @return array
     */
    public function runQualityChecksForRange(Carbon $startDate, Carbon $endDate, bool $autoFix = false): array
    {
        $results = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $results[] = $this->runQualityChecks($date, $autoFix);
        }

        return [
            'date_range' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'total_days_checked' => count($results),
            'days_passed' => collect($results)->where('overall_status', 'passed')->count(),
            'days_failed' => collect($results)->where('overall_status', 'failed')->count(),
            'days_with_warnings' => collect($results)->where('overall_status', 'warning')->count(),
            'total_issues' => collect($results)->sum('issues_found'),
            'details' => $results,
        ];
    }

    /**
     * Check if analytics data exists for date
     *
     * @param Carbon $date
     * @return array
     */
    private function checkDataExists(Carbon $date): array
    {
        $exists = SaleAnalytics::whereDate('date', $date)->exists();

        return [
            'status' => $exists ? 'passed' : 'failed',
            'message' => $exists ? 'Data exists' : 'No analytics data found',
            'severity' => $exists ? null : 'critical',
        ];
    }

    /**
     * Check data completeness (all required fields populated)
     *
     * @param Carbon $date
     * @return array
     */
    private function checkDataCompleteness(Carbon $date): array
    {
        $analytics = SaleAnalytics::whereDate('date', $date)->first();

        if (!$analytics) {
            return [
                'status' => 'failed',
                'message' => 'No data to check',
                'severity' => 'critical',
            ];
        }

        $requiredFields = [
            'total_sales',
            'total_orders',
            'average_order_value',
            'unique_customers',
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if ($analytics->$field === null) {
                $missingFields[] = $field;
            }
        }

        $status = empty($missingFields) ? 'passed' : 'failed';

        return [
            'status' => $status,
            'message' => $status === 'passed'
                ? 'All required fields populated'
                : 'Missing required fields: ' . implode(', ', $missingFields),
            'severity' => $status === 'passed' ? null : 'high',
            'missing_fields' => $missingFields,
        ];
    }

    /**
     * Check data accuracy using reconciliation
     *
     * @param Carbon $date
     * @return array
     */
    private function checkDataAccuracy(Carbon $date): array
    {
        $reconciliationResult = $this->reconciliationService->reconcileDate($date);

        if ($reconciliationResult['status'] === 'missing') {
            return [
                'status' => 'skipped',
                'message' => 'No data to validate',
            ];
        }

        $accuracyPercentage = $reconciliationResult['accuracy_percentage'];
        $status = $accuracyPercentage >= 99 ? 'passed' : ($accuracyPercentage >= 95 ? 'warning' : 'failed');

        return [
            'status' => $status,
            'message' => "Data accuracy: {$accuracyPercentage}%",
            'accuracy_percentage' => $accuracyPercentage,
            'discrepancies_count' => count($reconciliationResult['discrepancies']),
            'discrepancies' => $reconciliationResult['discrepancies'],
            'severity' => $status === 'failed' ? 'high' : ($status === 'warning' ? 'medium' : null),
        ];
    }

    /**
     * Check data consistency (logical consistency between fields)
     *
     * @param Carbon $date
     * @return array
     */
    private function checkDataConsistency(Carbon $date): array
    {
        $analytics = SaleAnalytics::whereDate('date', $date)->first();

        if (!$analytics) {
            return [
                'status' => 'skipped',
                'message' => 'No data to check',
            ];
        }

        $inconsistencies = [];

        // Check: Total orders should match sum of order types
        $sumOrderTypes = $analytics->dine_in_orders + $analytics->takeaway_orders + $analytics->delivery_orders;
        if ($analytics->total_orders > 0 && abs($sumOrderTypes - $analytics->total_orders) > 1) {
            $inconsistencies[] = [
                'check' => 'order_types_sum',
                'issue' => "Order types sum ({$sumOrderTypes}) doesn't match total orders ({$analytics->total_orders})",
            ];
        }

        // Check: Total revenue should match sum of revenue types
        $sumRevenueTypes = $analytics->total_revenue_dine_in + $analytics->total_revenue_takeaway + $analytics->total_revenue_delivery;
        $revenueDiff = abs($sumRevenueTypes - $analytics->total_sales);
        if ($analytics->total_sales > 0 && $revenueDiff > 0.01) {
            $inconsistencies[] = [
                'check' => 'revenue_types_sum',
                'issue' => "Revenue types sum (RM{$sumRevenueTypes}) doesn't match total sales (RM{$analytics->total_sales})",
            ];
        }

        // Check: Average order value calculation
        if ($analytics->total_orders > 0) {
            $expectedAvg = $analytics->total_sales / $analytics->total_orders;
            $avgDiff = abs($expectedAvg - $analytics->average_order_value);
            if ($avgDiff > 0.01) {
                $inconsistencies[] = [
                    'check' => 'average_order_value',
                    'issue' => "Average order value (RM{$analytics->average_order_value}) incorrect. Expected: RM" . round($expectedAvg, 2),
                ];
            }
        }

        // Check: Negative values
        $fieldsToCheck = ['total_sales', 'total_orders', 'unique_customers'];
        foreach ($fieldsToCheck as $field) {
            if ($analytics->$field < 0) {
                $inconsistencies[] = [
                    'check' => 'negative_value',
                    'issue' => "{$field} has negative value: {$analytics->$field}",
                ];
            }
        }

        $status = empty($inconsistencies) ? 'passed' : 'failed';

        return [
            'status' => $status,
            'message' => $status === 'passed'
                ? 'Data is logically consistent'
                : count($inconsistencies) . ' consistency issues found',
            'inconsistencies' => $inconsistencies,
            'severity' => $status === 'passed' ? null : 'medium',
        ];
    }

    /**
     * Detect anomalies in data patterns
     *
     * @param Carbon $date
     * @return array
     */
    private function detectAnomalies(Carbon $date): array
    {
        $analytics = SaleAnalytics::whereDate('date', $date)->first();

        if (!$analytics) {
            return [
                'status' => 'skipped',
                'message' => 'No data to check',
            ];
        }

        // Get historical average for comparison (last 30 days excluding today)
        $historicalData = SaleAnalytics::whereBetween('date', [
                $date->copy()->subDays(30),
                $date->copy()->subDay()
            ])
            ->get();

        if ($historicalData->count() < 7) {
            return [
                'status' => 'skipped',
                'message' => 'Insufficient historical data for anomaly detection',
            ];
        }

        $anomalies = [];

        // Check revenue anomaly (>200% or <20% of average)
        $avgRevenue = $historicalData->avg('total_sales');
        if ($avgRevenue > 0) {
            $revenueRatio = $analytics->total_sales / $avgRevenue;
            if ($revenueRatio > 2) {
                $anomalies[] = [
                    'metric' => 'revenue',
                    'type' => 'spike',
                    'current' => $analytics->total_sales,
                    'average' => round($avgRevenue, 2),
                    'ratio' => round($revenueRatio, 2),
                ];
            } elseif ($revenueRatio < 0.2 && $analytics->total_sales > 0) {
                $anomalies[] = [
                    'metric' => 'revenue',
                    'type' => 'drop',
                    'current' => $analytics->total_sales,
                    'average' => round($avgRevenue, 2),
                    'ratio' => round($revenueRatio, 2),
                ];
            }
        }

        // Check orders anomaly
        $avgOrders = $historicalData->avg('total_orders');
        if ($avgOrders > 0) {
            $ordersRatio = $analytics->total_orders / $avgOrders;
            if ($ordersRatio > 2) {
                $anomalies[] = [
                    'metric' => 'orders',
                    'type' => 'spike',
                    'current' => $analytics->total_orders,
                    'average' => round($avgOrders, 2),
                    'ratio' => round($ordersRatio, 2),
                ];
            } elseif ($ordersRatio < 0.2 && $analytics->total_orders > 0) {
                $anomalies[] = [
                    'metric' => 'orders',
                    'type' => 'drop',
                    'current' => $analytics->total_orders,
                    'average' => round($avgOrders, 2),
                    'ratio' => round($ordersRatio, 2),
                ];
            }
        }

        $status = empty($anomalies) ? 'passed' : 'warning';

        return [
            'status' => $status,
            'message' => $status === 'passed'
                ? 'No anomalies detected'
                : count($anomalies) . ' potential anomalies detected',
            'anomalies' => $anomalies,
            'severity' => $status === 'passed' ? null : 'low',
        ];
    }

    /**
     * Determine overall status from all checks
     *
     * @param array $checks
     * @return string
     */
    private function determineOverallStatus(array $checks): string
    {
        $statuses = array_column($checks, 'status');

        if (in_array('failed', $statuses)) {
            return 'failed';
        } elseif (in_array('warning', $statuses)) {
            return 'warning';
        } else {
            return 'passed';
        }
    }

    /**
     * Collect all issues from checks
     *
     * @param array $checks
     * @return array
     */
    private function collectIssues(array $checks): array
    {
        $issues = [];

        foreach ($checks as $checkName => $checkResult) {
            if (isset($checkResult['status']) && in_array($checkResult['status'], ['failed', 'warning'])) {
                $issues[] = [
                    'check' => $checkName,
                    'status' => $checkResult['status'],
                    'message' => $checkResult['message'],
                    'severity' => $checkResult['severity'] ?? 'low',
                ];
            }
        }

        return $issues;
    }

    /**
     * Generate recommendations based on check results
     *
     * @param array $checks
     * @return array
     */
    private function generateRecommendations(array $checks): array
    {
        $recommendations = [];

        if ($checks['data_exists']['status'] === 'failed') {
            $recommendations[] = 'Run analytics generation command to create missing data';
        }

        if (isset($checks['data_accuracy']) && $checks['data_accuracy']['status'] !== 'passed') {
            $recommendations[] = 'Run reconciliation and auto-fix to correct data accuracy';
        }

        if (isset($checks['data_consistency']) && $checks['data_consistency']['status'] === 'failed') {
            $recommendations[] = 'Recalculate analytics to fix consistency issues';
        }

        if (isset($checks['anomaly_detection']) && !empty($checks['anomaly_detection']['anomalies'])) {
            $recommendations[] = 'Review detected anomalies for potential data issues or unusual business activity';
        }

        return $recommendations;
    }
}
