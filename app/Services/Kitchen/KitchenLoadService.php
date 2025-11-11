<?php

namespace App\Services\Kitchen;

use App\Models\KitchenStation;
use App\Models\KitchenLoad;
use App\Models\LoadBalancingLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KitchenLoadService
{
    const OVERLOAD_THRESHOLD = 85; // 85% capacity triggers overload alert

    /**
     * Add load to a station when order is assigned
     * @param int $loadPoints - Now represents the actual item quantity
     */
    public function addLoad($stationId, $orderId, $loadPoints, $estimatedCompletionTime = null)
    {
        $station = KitchenStation::findOrFail($stationId);

        // Create kitchen load record
        $kitchenLoad = KitchenLoad::create([
            'station_id' => $stationId,
            'order_id' => $orderId,
            'load_points' => $loadPoints,
            'estimated_completion_time' => $estimatedCompletionTime ?? now()->addMinutes(15),
            'status' => 'pending',
        ]);

        // Increment station load counter by actual item quantity (not just 1)
        $station->increment('current_load', $loadPoints);

        // Check for overload
        $this->checkOverload($station);

        // Clear cache
        $this->clearStationCache($stationId);

        Log::info("Load added to station", [
            'station' => $station->name,
            'order_id' => $orderId,
            'load_points' => $loadPoints,
            'current_load' => $station->fresh()->current_load,
            'load_percentage' => $station->fresh()->load_percentage,
        ]);

        return $kitchenLoad;
    }

    /**
     * Release load when order is completed
     */
    public function releaseLoad($stationId, $orderId)
    {
        $station = KitchenStation::findOrFail($stationId);

        // Update kitchen load status
        $kitchenLoad = KitchenLoad::where('station_id', $stationId)
            ->where('order_id', $orderId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($kitchenLoad) {
            $kitchenLoad->update([
                'status' => 'completed',
                'actual_completion_time' => now(),
            ]);

            // Decrement station load counter by the actual load points (item quantity)
            $loadPoints = $kitchenLoad->load_points;
            if ($station->current_load > 0) {
                $newLoad = max(0, $station->current_load - $loadPoints);
                $station->update(['current_load' => $newLoad]);
            }

            // Log the completion
            LoadBalancingLog::create([
                'order_id' => $orderId,
                'station_id' => $stationId,
                'action_type' => 'completion',
                'old_load' => $station->current_load + $loadPoints,
                'new_load' => $station->current_load,
                'reason' => 'Order completed successfully',
                'metadata' => [
                    'duration_minutes' => $kitchenLoad->duration,
                    'load_points' => $loadPoints,
                ]
            ]);

            // Clear cache
            $this->clearStationCache($stationId);

            Log::info("Load released from station", [
                'station' => $station->name,
                'order_id' => $orderId,
                'load_points_released' => $loadPoints,
                'current_load' => $station->fresh()->current_load,
            ]);
        }

        return true;
    }

    /**
     * Update load status (e.g., from pending to in_progress)
     */
    public function updateLoadStatus($stationId, $orderId, $status)
    {
        $kitchenLoad = KitchenLoad::where('station_id', $stationId)
            ->where('order_id', $orderId)
            ->first();

        if ($kitchenLoad) {
            $kitchenLoad->update(['status' => $status]);

            $this->clearStationCache($stationId);

            return true;
        }

        return false;
    }

    /**
     * Check if station is overloaded and trigger alert
     */
    protected function checkOverload(KitchenStation $station)
    {
        $station = $station->fresh();

        if ($station->isOverloaded()) {
            // Log overload alert
            LoadBalancingLog::create([
                'station_id' => $station->id,
                'action_type' => 'overload_alert',
                'old_load' => $station->current_load,
                'new_load' => $station->current_load,
                'reason' => 'Station reached 85% capacity',
                'metadata' => [
                    'load_percentage' => $station->load_percentage,
                    'max_capacity' => $station->max_capacity,
                    'current_load' => $station->current_load,
                ]
            ]);

            Log::warning("Station overloaded!", [
                'station' => $station->name,
                'load_percentage' => $station->load_percentage,
                'current_load' => $station->current_load,
                'max_capacity' => $station->max_capacity,
            ]);

            // TODO: Trigger notification (will be implemented in notification service)
            // event(new StationOverloadEvent($station));
        }
    }

    /**
     * Detect bottlenecks across all stations (TODAY ONLY)
     */
    public function detectBottlenecks()
    {
        return KitchenStation::where('is_active', true)
            ->get()
            ->map(function ($station) {
                // Calculate today's load
                $todayLoad = $station->today_load;
                $loadPercentage = $station->load_percentage;
                
                return [
                    'station' => $station,
                    'load_percentage' => $loadPercentage,
                    'current_load' => $todayLoad,
                    'max_capacity' => $station->max_capacity,
                    'pending_orders' => $station->pendingAssignments()->count(),
                    'suggested_action' => $loadPercentage >= 85 ? $this->suggestAction($station) : null,
                ];
            })
            ->filter(function ($data) {
                return $data['load_percentage'] >= 85;
            })
            ->values();
    }

    /**
     * Suggest action for overloaded station
     */
    protected function suggestAction(KitchenStation $station)
    {
        // Find alternative stations with lower load
        $alternatives = KitchenStation::where('is_active', true)
            ->where('id', '!=', $station->id)
            ->get()
            ->filter(function ($alt) {
                return $alt->load_percentage < 70;
            });

        if ($alternatives->isNotEmpty()) {
            $bestAlternative = $alternatives->sortBy('load_percentage')->first();

            return "Redistribute orders to {$bestAlternative->name} ({$bestAlternative->load_percentage}% load)";
        }

        return "Consider increasing capacity or adding staff";
    }

    /**
     * Get real-time status for all stations (TODAY ONLY)
     */
    public function getStationsStatus()
    {
        return Cache::remember('kitchen_stations_status', 10, function () {
            return KitchenStation::with([
                    'activeLoads.order',
                    'pendingAssignments.order',
                ])
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->map(function ($station) {
                    $todayLoad = $station->today_load;
                    $loadPercentage = $station->load_percentage;
                    
                    return [
                        'id' => $station->id,
                        'name' => $station->name,
                        'icon' => $station->icon,
                        'current_load' => $todayLoad,
                        'max_capacity' => $station->max_capacity,
                        'load_percentage' => $loadPercentage,
                        'status' => $this->getStationStatusByPercentage($loadPercentage),
                        'pending_orders' => $station->pendingAssignments()->count(),
                        'avg_completion_time' => $station->getAverageCompletionTime(),
                        'is_overloaded' => $loadPercentage >= 85,
                        'is_approaching_capacity' => $loadPercentage >= 70 && $loadPercentage < 85,
                    ];
                });
        });
    }

    /**
     * Get station status label based on load percentage
     */
    protected function getStationStatusByPercentage($loadPercentage)
    {
        if ($loadPercentage >= 85) {
            return 'overloaded';
        }

        if ($loadPercentage >= 70) {
            return 'approaching_capacity';
        }

        if ($loadPercentage >= 50) {
            return 'busy';
        }

        if ($loadPercentage >= 25) {
            return 'normal';
        }

        return 'idle';
    }

    /**
     * Clear station cache
     */
    protected function clearStationCache($stationId = null)
    {
        Cache::forget('kitchen_stations_status');

        if ($stationId) {
            Cache::forget("station_status_{$stationId}");
        }
    }

    /**
     * Get today's statistics (TODAY ONLY)
     */
    public function getTodayStats()
    {
        $completedLoads = KitchenLoad::whereDate('created_at', today())
            ->where('status', 'completed')
            ->with('station')
            ->get();

        $totalOrders = $completedLoads->unique('order_id')->count();
        $avgCompletionTime = $completedLoads->avg('duration') ?? 0;

        $overloadAlerts = LoadBalancingLog::today()
            ->overloadAlerts()
            ->count();

        // Count active orders from today's loads only
        $activeOrders = KitchenLoad::whereIn('status', ['pending', 'in_progress'])
            ->whereDate('created_at', today())
            ->count();

        return [
            'total_orders_completed' => $totalOrders,
            'avg_completion_time' => round($avgCompletionTime, 1),
            'overload_alerts' => $overloadAlerts,
            'active_orders' => $activeOrders,
            'stations_performance' => $this->getStationsPerformance($completedLoads),
        ];
    }

    /**
     * Get performance metrics per station
     */
    protected function getStationsPerformance($completedLoads)
    {
        return $completedLoads->groupBy('station_id')
            ->map(function ($loads) {
                $station = $loads->first()->station;

                return [
                    'station_name' => $station->name,
                    'orders_completed' => $loads->unique('order_id')->count(),
                    'avg_time' => round($loads->avg('duration'), 1),
                    'efficiency' => $this->calculateEfficiency($loads),
                ];
            })
            ->values();
    }

    /**
     * Calculate station efficiency percentage
     */
    protected function calculateEfficiency($loads)
    {
        if ($loads->isEmpty()) {
            return 100;
        }

        $totalActual = $loads->sum('duration');
        $totalEstimated = $loads->sum(function ($load) {
            return $load->created_at->diffInMinutes($load->estimated_completion_time);
        });

        if ($totalEstimated == 0) {
            return 100;
        }

        // If completed faster than estimated = higher efficiency
        $efficiency = ($totalEstimated / $totalActual) * 100;

        return min(round($efficiency, 1), 100);
    }
}
