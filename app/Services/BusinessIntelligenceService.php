<?php

namespace App\Services;

use App\Models\SaleAnalytics;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Business Intelligence Service
 * Advanced analytics with trend analysis, forecasting, and business insights
 */
class BusinessIntelligenceService
{
    /**
     * Get comprehensive trend analysis
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getTrendAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        if ($analytics->count() < 2) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 2 data points for trend analysis',
            ];
        }

        $orderStatusDistribution = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('order_status', DB::raw('count(*) as total'))
            ->groupBy('order_status')
            ->get()
            ->pluck('total', 'order_status');

        return [
            'revenue_trend' => $this->calculateTrend($analytics->pluck('total_sales')->toArray()),
            'orders_trend' => $this->calculateTrend($analytics->pluck('total_orders')->toArray()),
            'avg_order_value_trend' => $this->calculateTrend($analytics->pluck('average_order_value')->toArray()),
            'customer_trend' => $this->calculateTrend($analytics->pluck('unique_customers')->toArray()),
            'order_status_distribution' => $orderStatusDistribution,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $analytics->count(),
            ],
        ];
    }

    /**
     * Get Month-over-Month (MoM) comparison
     *
     * @param Carbon $date
     * @return array
     */
    public function getMonthOverMonthComparison(Carbon $date): array
    {
        $currentMonthStart = $date->copy()->startOfMonth();
        $currentMonthEnd = $date->copy()->endOfMonth();
        $previousMonthStart = $date->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $date->copy()->subMonth()->endOfMonth();

        $currentMonth = $this->getMonthSummary($currentMonthStart, $currentMonthEnd);
        $previousMonth = $this->getMonthSummary($previousMonthStart, $previousMonthEnd);

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'changes' => [
                'revenue' => $this->calculateChange($currentMonth['revenue'], $previousMonth['revenue']),
                'orders' => $this->calculateChange($currentMonth['orders'], $previousMonth['orders']),
                'avg_order_value' => $this->calculateChange($currentMonth['avg_order_value'], $previousMonth['avg_order_value']),
                'customers' => $this->calculateChange($currentMonth['customers'], $previousMonth['customers']),
            ],
        ];
    }

    /**
     * Get Year-over-Year (YoY) comparison
     *
     * @param Carbon $date
     * @return array
     */
    public function getYearOverYearComparison(Carbon $date): array
    {
        $currentYearStart = $date->copy()->startOfYear();
        $currentYearEnd = $date->copy()->endOfYear();
        $previousYearStart = $date->copy()->subYear()->startOfYear();
        $previousYearEnd = $date->copy()->subYear()->endOfYear();

        $currentYear = $this->getYearSummary($currentYearStart, $currentYearEnd);
        $previousYear = $this->getYearSummary($previousYearStart, $previousYearEnd);

        return [
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'changes' => [
                'revenue' => $this->calculateChange($currentYear['revenue'], $previousYear['revenue']),
                'orders' => $this->calculateChange($currentYear['orders'], $previousYear['orders']),
                'avg_order_value' => $this->calculateChange($currentYear['avg_order_value'], $previousYear['avg_order_value']),
                'customers' => $this->calculateChange($currentYear['customers'], $previousYear['customers']),
            ],
        ];
    }

    /**
     * Get Week-over-Week (WoW) comparison
     *
     * @param Carbon $date
     * @return array
     */
    public function getWeekOverWeekComparison(Carbon $date): array
    {
        $currentWeekStart = $date->copy()->startOfWeek();
        $currentWeekEnd = $date->copy()->endOfWeek();
        $previousWeekStart = $date->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $date->copy()->subWeek()->endOfWeek();

        $currentWeek = $this->getWeekSummary($currentWeekStart, $currentWeekEnd);
        $previousWeek = $this->getWeekSummary($previousWeekStart, $previousWeekEnd);

        return [
            'current_week' => $currentWeek,
            'previous_week' => $previousWeek,
            'changes' => [
                'revenue' => $this->calculateChange($currentWeek['revenue'], $previousWeek['revenue']),
                'orders' => $this->calculateChange($currentWeek['orders'], $previousWeek['orders']),
                'avg_order_value' => $this->calculateChange($currentWeek['avg_order_value'], $previousWeek['avg_order_value']),
                'customers' => $this->calculateChange($currentWeek['customers'], $previousWeek['customers']),
            ],
        ];
    }

    /**
     * Revenue forecasting using linear regression
     *
     * @param int $daysToForecast
     * @param int $historicalDays
     * @return array
     */
    public function forecastRevenue(int $daysToForecast = 7, int $historicalDays = 30): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($historicalDays - 1);

        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        if ($analytics->count() < 7) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 7 days of historical data for forecasting',
            ];
        }

        $revenues = $analytics->pluck('total_sales')->toArray();
        $forecast = $this->linearRegressionForecast($revenues, $daysToForecast);

        return [
            'historical_data' => $revenues,
            'forecast' => $forecast,
            'forecast_period' => [
                'start' => $endDate->copy()->addDay()->toDateString(),
                'end' => $endDate->copy()->addDays($daysToForecast)->toDateString(),
            ],
            'confidence' => $this->calculateForecastConfidence($revenues),
        ];
    }

    /**
     * Detect peak hours and optimize staffing
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPeakHoursAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$startDate, $endDate])->get();

        $allPeakHours = [];
        foreach ($analytics as $analytic) {
            if (!empty($analytic->peak_hours) && is_array($analytic->peak_hours)) {
                foreach ($analytic->peak_hours as $hour => $count) {
                    if (!isset($allPeakHours[$hour])) {
                        $allPeakHours[$hour] = 0;
                    }
                    $allPeakHours[$hour] += $count;
                }
            }
        }

        // Filter for operating hours (4 PM to 12 AM)
        $operatingHours = [16, 17, 18, 19, 20, 21, 22, 23, 0];
        $filteredPeakHours = array_filter(
            $allPeakHours,
            fn($hour) => in_array($hour, $operatingHours),
            ARRAY_FILTER_USE_KEY
        );

        arsort($filteredPeakHours);

        $topPeakHours = array_slice($filteredPeakHours, 0, 5, true);

        return [
            'peak_hours' => $topPeakHours,
            'peak_hours_formatted' => array_map(function($hour) {
                return sprintf('%02d:00 - %02d:00', $hour, ($hour + 1) % 24);
            }, array_keys($topPeakHours)),
            'recommendations' => $this->generatePeakHoursRecommendations($topPeakHours),
        ];
    }

    /**
     * Get anomaly detection for business metrics
     *
     * @param Carbon $date
     * @param int $historicalDays
     * @return array
     */
    public function detectAnomalies(Carbon $date, int $historicalDays = 30): array
    {
        $targetAnalytics = SaleAnalytics::whereDate('date', $date)->first();

        if (!$targetAnalytics) {
            return [
                'status' => 'no_data',
                'message' => 'No data found for the specified date',
            ];
        }

        $historicalAnalytics = SaleAnalytics::whereBetween('date', [
                $date->copy()->subDays($historicalDays),
                $date->copy()->subDay()
            ])
            ->get();

        if ($historicalAnalytics->count() < 7) {
            return [
                'status' => 'insufficient_historical_data',
                'message' => 'Need at least 7 days of historical data',
            ];
        }

        $anomalies = [];

        // Revenue anomaly
        $revenueStats = $this->calculateStats($historicalAnalytics->pluck('total_sales')->toArray());
        if ($this->isAnomaly($targetAnalytics->total_sales, $revenueStats)) {
            $anomalies[] = [
                'metric' => 'revenue',
                'current_value' => $targetAnalytics->total_sales,
                'expected_range' => [
                    'min' => round($revenueStats['min'], 2),
                    'max' => round($revenueStats['max'], 2),
                    'mean' => round($revenueStats['mean'], 2),
                ],
                'deviation' => round($this->calculateDeviation($targetAnalytics->total_sales, $revenueStats), 2),
                'severity' => $this->getAnomalySeverity($targetAnalytics->total_sales, $revenueStats),
            ];
        }

        // Orders anomaly
        $ordersStats = $this->calculateStats($historicalAnalytics->pluck('total_orders')->toArray());
        if ($this->isAnomaly($targetAnalytics->total_orders, $ordersStats)) {
            $anomalies[] = [
                'metric' => 'orders',
                'current_value' => $targetAnalytics->total_orders,
                'expected_range' => [
                    'min' => round($ordersStats['min']),
                    'max' => round($ordersStats['max']),
                    'mean' => round($ordersStats['mean']),
                ],
                'deviation' => round($this->calculateDeviation($targetAnalytics->total_orders, $ordersStats), 2),
                'severity' => $this->getAnomalySeverity($targetAnalytics->total_orders, $ordersStats),
            ];
        }

        return [
            'date' => $date->toDateString(),
            'anomalies_detected' => count($anomalies),
            'anomalies' => $anomalies,
            'status' => empty($anomalies) ? 'normal' : 'anomaly_detected',
        ];
    }

    /**
     * Calculate trend direction and strength
     *
     * @param array $values
     * @return array
     */
    private function calculateTrend(array $values): array
    {
        if (count($values) < 2) {
            return ['direction' => 'stable', 'strength' => 0, 'percentage' => 0];
        }

        $n = count($values);
        $sumX = array_sum(range(0, $n - 1));
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        // Avoid division by zero if all X values are the same (highly unlikely with range)
        $denominator = ($n * $sumX2 - $sumX * $sumX);
        if ($denominator == 0) {
            return ['direction' => 'stable', 'strength' => 0, 'percentage' => 0];
        }

        $slope = $denominator != 0 ? ($n * $sumXY - $sumX * $sumY) / $denominator : 0;
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Calculate percentage change based on the trend line's start and end points
        $trendStartValue = $intercept;
        $trendEndValue = $slope * ($n - 1) + $intercept;

        $percentageChange = $trendStartValue != 0 ? (($trendEndValue - $trendStartValue) / abs($trendStartValue)) * 100 : ($trendEndValue > 0 ? 100 : 0);

        $direction = $slope > 0.1 ? 'increasing' : ($slope < -0.1 ? 'decreasing' : 'stable');
        $strength = abs($slope);

        return [
            'direction' => $direction,
            'slope' => round($slope, 4),
            'strength' => round($strength, 4),
            'percentage' => round($percentageChange, 2),
            'first_value' => round($values[0], 2),
            'last_value' => round(end($values), 2),
        ];
    }

    /**
     * Calculate percentage change between two values
     *
     * @param float $current
     * @param float $previous
     * @return array
     */
    private function calculateChange(float $current, float $previous): array
    {
        $difference = $current - $previous;
        $percentage = $previous != 0 ? ($difference / $previous) * 100 : 0;

        return [
            'current' => round($current, 2),
            'previous' => round($previous, 2),
            'difference' => round($difference, 2),
            'percentage' => round($percentage, 2),
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'stable'),
        ];
    }

    /**
     * Get month summary
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    private function getMonthSummary(Carbon $start, Carbon $end): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$start, $end])->get();

        $revenue = $analytics->sum('total_sales');
        $orders = $analytics->sum('total_orders');

        return [
            'month' => $start->format('F Y'),
            'revenue' => round($revenue, 2),
            'orders' => $orders,
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0,
            'customers' => $analytics->sum('unique_customers'),
            'days_in_period' => $analytics->count(),
        ];
    }

    /**
     * Get year summary
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    private function getYearSummary(Carbon $start, Carbon $end): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$start, $end])->get();

        $revenue = $analytics->sum('total_sales');
        $orders = $analytics->sum('total_orders');

        return [
            'year' => $start->year,
            'revenue' => round($revenue, 2),
            'orders' => $orders,
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0,
            'customers' => $analytics->sum('unique_customers'),
            'days_in_period' => $analytics->count(),
        ];
    }

    /**
     * Get week summary
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    private function getWeekSummary(Carbon $start, Carbon $end): array
    {
        $analytics = SaleAnalytics::whereBetween('date', [$start, $end])->get();

        $revenue = $analytics->sum('total_sales');
        $orders = $analytics->sum('total_orders');

        return [
            'week' => 'Week of ' . $start->format('M d, Y'),
            'revenue' => round($revenue, 2),
            'orders' => $orders,
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0,
            'customers' => $analytics->sum('unique_customers'),
            'days_in_period' => $analytics->count(),
        ];
    }

    /**
     * Linear regression forecast
     *
     * @param array $values
     * @param int $periods
     * @return array
     */
    private function linearRegressionForecast(array $values, int $periods): array
    {
        $n = count($values);
        $sumX = array_sum(range(0, $n - 1));
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecast = [];
        for ($i = 1; $i <= $periods; $i++) {
            $x = $n + $i - 1;
            $forecast[] = round(max(0, $slope * $x + $intercept), 2);
        }

        return $forecast;
    }

    /**
     * Calculate forecast confidence
     *
     * @param array $values
     * @return string
     */
    private function calculateForecastConfidence(array $values): string
    {
        $trend = $this->calculateTrend($values);
        $strength = abs($trend['slope']);

        if ($strength > 10) {
            return 'high';
        } elseif ($strength > 5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Generate peak hours recommendations
     *
     * @param array $peakHours
     * @return array
     */
    private function generatePeakHoursRecommendations(array $peakHours): array
    {
        $recommendations = [];

        foreach ($peakHours as $hour => $count) {
            $timeRange = sprintf('%02d:00 - %02d:00', $hour, ($hour + 1) % 24);
            $recommendations[] = "Increase staffing during {$timeRange} (peak activity: {$count} orders)";
        }

        return $recommendations;
    }

    /**
     * Calculate statistics for anomaly detection
     *
     * @param array $values
     * @return array
     */
    private function calculateStats(array $values): array
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($val) use ($mean) {
            return pow($val - $mean, 2);
        }, $values)) / count($values);
        $stdDev = sqrt($variance);

        return [
            'mean' => $mean,
            'std_dev' => $stdDev,
            'min' => $mean - 2 * $stdDev,
            'max' => $mean + 2 * $stdDev,
        ];
    }

    /**
     * Check if value is anomaly (outside 2 standard deviations)
     *
     * @param float $value
     * @param array $stats
     * @return bool
     */
    private function isAnomaly(float $value, array $stats): bool
    {
        return $value < $stats['min'] || $value > $stats['max'];
    }

    /**
     * Calculate deviation from mean in standard deviations
     *
     * @param float $value
     * @param array $stats
     * @return float
     */
    private function calculateDeviation(float $value, array $stats): float
    {
        if ($stats['std_dev'] == 0) {
            return 0;
        }
        return ($value - $stats['mean']) / $stats['std_dev'];
    }

    /**
     * Get anomaly severity level
     *
     * @param float $value
     * @param array $stats
     * @return string
     */
    private function getAnomalySeverity(float $value, array $stats): string
    {
        $deviation = abs($this->calculateDeviation($value, $stats));

        if ($deviation > 3) {
            return 'critical';
        } elseif ($deviation > 2.5) {
            return 'high';
        } elseif ($deviation > 2) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get the most frequently booked tables.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getMostBookedTables(Carbon $startDate, Carbon $endDate, int $limit = 5)
    {
        return DB::table('table_reservations')
            ->join('tables', 'table_reservations.table_id', '=', 'tables.id')
            ->whereBetween('table_reservations.created_at', [$startDate, $endDate])
            ->select('tables.table_number', DB::raw('count(table_reservations.id) as booking_count'))
            ->groupBy('tables.table_number')
            ->orderByDesc('booking_count')
            ->limit($limit)
            ->get();
    }
}
