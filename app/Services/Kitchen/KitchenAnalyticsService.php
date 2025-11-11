<?php

namespace App\Services\Kitchen;

use App\Models\KitchenStation;
use App\Models\KitchenLoad;
use App\Models\LoadBalancingLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KitchenAnalyticsService
{
    /**
     * Get performance analytics for a date range
     */
    public function getPerformanceAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? today()->subDays(7);
        $endDate = $endDate ?? today();

        return [
            'summary' => $this->getSummaryMetrics($startDate, $endDate),
            'station_performance' => $this->getStationPerformance($startDate, $endDate),
            'hourly_distribution' => $this->getHourlyDistribution($startDate, $endDate),
            'bottleneck_events' => $this->getBottleneckEvents($startDate, $endDate),
        ];
    }

    /**
     * Get summary metrics
     */
    protected function getSummaryMetrics($startDate, $endDate)
    {
        $loads = KitchenLoad::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalOrders = $loads->unique('order_id')->count();
        $avgCompletionTime = $loads->avg('duration') ?? 0;
        $onTimePercentage = $this->calculateOnTimePercentage($loads);

        $overloadAlerts = LoadBalancingLog::whereBetween('created_at', [$startDate, $endDate])
            ->where('action_type', 'overload_alert')
            ->count();

        return [
            'total_orders' => $totalOrders,
            'avg_completion_time' => round($avgCompletionTime, 1),
            'on_time_percentage' => $onTimePercentage,
            'overload_alerts' => $overloadAlerts,
        ];
    }

    /**
     * Calculate on-time delivery percentage
     */
    protected function calculateOnTimePercentage($loads)
    {
        if ($loads->isEmpty()) {
            return 100;
        }

        $onTime = $loads->filter(function ($load) {
            if (!$load->estimated_completion_time || !$load->actual_completion_time) {
                return false;
            }

            return $load->actual_completion_time->lte($load->estimated_completion_time);
        })->count();

        return round(($onTime / $loads->count()) * 100, 1);
    }

    /**
     * Get performance by station
     */
    protected function getStationPerformance($startDate, $endDate)
    {
        return KitchenStation::with([
                'kitchenLoads' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'completed');
                }
            ])
            ->get()
            ->map(function ($station) {
                $loads = $station->kitchenLoads;

                return [
                    'station_name' => $station->name,
                    'icon' => $station->icon,
                    'orders_completed' => $loads->unique('order_id')->count(),
                    'avg_completion_time' => round($loads->avg('duration') ?? 0, 1),
                    'total_load_points' => round($loads->sum('load_points'), 2),
                    'efficiency_score' => $this->calculateStationEfficiency($loads),
                ];
            })
            ->sortByDesc('orders_completed')
            ->values();
    }

    /**
     * Calculate station efficiency score (0-100)
     */
    protected function calculateStationEfficiency($loads)
    {
        if ($loads->isEmpty()) {
            return 0;
        }

        $totalActual = $loads->sum('duration');
        $totalEstimated = $loads->sum(function ($load) {
            if (!$load->estimated_completion_time) {
                return 15; // Default 15 minutes
            }
            return $load->created_at->diffInMinutes($load->estimated_completion_time);
        });

        if ($totalEstimated == 0) {
            return 100;
        }

        // Efficiency = (Estimated / Actual) * 100
        // If actual < estimated = good (>100%), if actual > estimated = bad (<100%)
        $efficiency = ($totalEstimated / $totalActual) * 100;

        return min(round($efficiency, 1), 100);
    }

    /**
     * Get hourly distribution of orders
     */
    protected function getHourlyDistribution($startDate, $endDate)
    {
        $loads = KitchenLoad::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $distribution = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $distribution[$hour] = $loads->filter(function ($load) use ($hour) {
                return $load->created_at->hour == $hour;
            })->count();
        }

        // Find peak hours
        $peakHours = collect($distribution)->sortDesc()->take(3)->keys()->sort()->values();

        return [
            'hourly_counts' => $distribution,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get bottleneck events
     */
    protected function getBottleneckEvents($startDate, $endDate)
    {
        return LoadBalancingLog::whereBetween('created_at', [$startDate, $endDate])
            ->where('action_type', 'overload_alert')
            ->with('station')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'timestamp' => $log->created_at,
                    'station' => $log->station->name ?? 'Unknown',
                    'load_percentage' => $log->metadata['load_percentage'] ?? 0,
                    'current_load' => $log->metadata['current_load'] ?? 0,
                    'max_capacity' => $log->metadata['max_capacity'] ?? 0,
                ];
            });
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData($days = 7)
    {
        $endDate = today();
        $startDate = today()->subDays($days - 1);

        $dailyData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayLoads = KitchenLoad::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->get();

            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => $dayLoads->unique('order_id')->count(),
                'avg_time' => round($dayLoads->avg('duration') ?? 0, 1),
            ];
        }

        return $dailyData;
    }

    /**
     * Generate intelligent recommendations based on analytics data
     */
    public function generateRecommendations($analytics)
    {
        $strengths = [];
        $improvements = [];
        $suggestions = [];

        $summary = $analytics['summary'];
        $stationPerformance = collect($analytics['station_performance']);
        $bottleneckEvents = $analytics['bottleneck_events'];

        // === ANALYZE STRENGTHS ===

        // High on-time rate
        if ($summary['on_time_percentage'] >= 90) {
            $strengths[] = "Excellent on-time delivery rate ({$summary['on_time_percentage']}%)";
        } elseif ($summary['on_time_percentage'] >= 80) {
            $strengths[] = "Good on-time delivery rate ({$summary['on_time_percentage']}%)";
        }

        // Low overload alerts
        if ($summary['overload_alerts'] <= 2) {
            $strengths[] = "Well-managed kitchen load (only {$summary['overload_alerts']} overload alerts)";
        } elseif ($summary['overload_alerts'] <= 5) {
            $strengths[] = "Kitchen load under control ({$summary['overload_alerts']} overload alerts)";
        }

        // High efficiency stations
        $highEfficiencyStations = $stationPerformance->where('efficiency_score', '>=', 85)->pluck('station_name');
        if ($highEfficiencyStations->isNotEmpty()) {
            $strengths[] = "High efficiency at " . $highEfficiencyStations->take(2)->implode(', ');
        }

        // Good completion time
        if ($summary['avg_completion_time'] > 0 && $summary['avg_completion_time'] <= 20) {
            $strengths[] = "Fast average completion time ({$summary['avg_completion_time']} min)";
        }

        // Default if no strengths found
        if (empty($strengths)) {
            $strengths[] = "Kitchen operations are active";
        }

        // === ANALYZE AREAS TO IMPROVE ===

        // Low on-time rate
        if ($summary['on_time_percentage'] < 80) {
            $improvements[] = "On-time delivery rate needs improvement ({$summary['on_time_percentage']}%)";
        }

        // High overload frequency
        if ($summary['overload_alerts'] > 5) {
            $improvements[] = "Frequent bottlenecks detected ({$summary['overload_alerts']} overload alerts)";
        } elseif ($summary['overload_alerts'] > 10) {
            $improvements[] = "Critical: High frequency of station overloads ({$summary['overload_alerts']} alerts)";
        }

        // Low efficiency stations
        $lowEfficiencyStations = $stationPerformance->where('efficiency_score', '<', 70)->where('efficiency_score', '>', 0);
        if ($lowEfficiencyStations->isNotEmpty()) {
            $worstStation = $lowEfficiencyStations->sortBy('efficiency_score')->first();
            $improvements[] = "{$worstStation['station_name']} efficiency needs optimization ({$worstStation['efficiency_score']}%)";
        }

        // Slow completion time
        if ($summary['avg_completion_time'] > 30) {
            $improvements[] = "Average completion time is high ({$summary['avg_completion_time']} min)";
        }

        // Unbalanced workload
        $maxOrders = $stationPerformance->max('orders_completed');
        $minOrders = $stationPerformance->where('orders_completed', '>', 0)->min('orders_completed');
        if ($maxOrders > 0 && $minOrders > 0 && ($maxOrders / $minOrders) > 3) {
            $improvements[] = "Unbalanced workload distribution across stations";
        }

        // Default if no improvements needed
        if (empty($improvements)) {
            $improvements[] = "Continue monitoring peak hours for optimization";
        }

        // === GENERATE ACTIONABLE SUGGESTIONS ===

        // Suggestion based on slow stations
        if ($lowEfficiencyStations->isNotEmpty()) {
            $slowest = $lowEfficiencyStations->sortBy('efficiency_score')->first();
            $suggestions[] = "Add staff to {$slowest['station_name']} during peak hours";
        }

        // Suggestion based on overload alerts
        if ($summary['overload_alerts'] > 5) {
            $mostOverloaded = $this->getMostOverloadedStation($bottleneckEvents);
            if ($mostOverloaded) {
                $suggestions[] = "Increase capacity at {$mostOverloaded} or redistribute orders";
            } else {
                $suggestions[] = "Review station capacity settings during peak hours";
            }
        }

        // Suggestion based on completion time
        if ($summary['avg_completion_time'] > 25) {
            $slowStations = $stationPerformance->where('avg_completion_time', '>', 30);
            if ($slowStations->isNotEmpty()) {
                $suggestions[] = "Review menu item preparation times for accuracy";
            } else {
                $suggestions[] = "Consider prep work during off-peak times";
            }
        }

        // Suggestion based on workload imbalance
        if ($maxOrders > 0 && $minOrders > 0 && ($maxOrders / $minOrders) > 3) {
            $busiestStation = $stationPerformance->sortByDesc('orders_completed')->first();
            $suggestions[] = "Consider cross-training staff for {$busiestStation['station_name']}";
        }

        // Suggestion for peak hour management
        if (isset($analytics['hourly_distribution']['peak_hours'])) {
            $peakHours = $analytics['hourly_distribution']['peak_hours'];
            if ($peakHours->isNotEmpty()) {
                $peakTime = $peakHours->first();
                $suggestions[] = "Schedule additional staff around {$peakTime}:00 (peak hour)";
            }
        }

        // Default suggestions
        if (empty($suggestions)) {
            $suggestions[] = "Continue current operations and monitor trends";
            $suggestions[] = "Review menu item load factors for accuracy";
        }

        // Limit to 3 items each
        return [
            'strengths' => array_slice($strengths, 0, 3),
            'improvements' => array_slice($improvements, 0, 3),
            'suggestions' => array_slice($suggestions, 0, 3),
        ];
    }

    /**
     * Get the most frequently overloaded station
     */
    protected function getMostOverloadedStation($bottleneckEvents)
    {
        if (empty($bottleneckEvents) || count($bottleneckEvents) === 0) {
            return null;
        }

        $stationCounts = [];
        foreach ($bottleneckEvents as $event) {
            $station = $event['station'] ?? 'Unknown';
            $stationCounts[$station] = ($stationCounts[$station] ?? 0) + 1;
        }

        if (empty($stationCounts)) {
            return null;
        }

        arsort($stationCounts);
        return array_key_first($stationCounts);
    }
}
