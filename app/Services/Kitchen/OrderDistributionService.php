<?php

namespace App\Services\Kitchen;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\KitchenStation;
use App\Models\StationAssignment;
use App\Models\KitchenLoad;
use App\Models\LoadBalancingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDistributionService
{
    protected $kitchenLoadService;

    public function __construct(KitchenLoadService $kitchenLoadService)
    {
        $this->kitchenLoadService = $kitchenLoadService;
    }

    /**
     * Distribute an order to optimal kitchen stations
     */
    public function distributeOrder(Order $order)
    {
        DB::beginTransaction();

        try {
            $order->load('items.menuItem.category.defaultStation', 'items.menuItem.stationOverride');

            // Group order items by station (using category/item station logic)
            $itemsByStation = $this->groupItemsByStation($order->items);

            // Assign each group to the determined station
            foreach ($itemsByStation as $stationId => $items) {
                $station = KitchenStation::find($stationId);

                if (!$station || !$station->is_active) {
                    Log::warning("Station not found or inactive: {$stationId}");
                    continue;
                }

                $this->assignItemsToStation($order, $station, $items);
            }

            DB::commit();

            Log::info("Order #{$order->confirmation_code} distributed successfully", [
                'order_id' => $order->id,
                'stations' => count($itemsByStation)
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to distribute order #{$order->confirmation_code}", [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Group order items by their effective kitchen stations
     * Uses menu item override if set, otherwise category's default station
     */
    protected function groupItemsByStation($orderItems)
    {
        $grouped = [];

        foreach ($orderItems as $item) {
            $station = $item->menuItem->getEffectiveStation();

            if (!$station) {
                Log::warning("No station assigned for menu item: {$item->menuItem->name}");
                continue;
            }

            $stationId = $station->id;

            if (!isset($grouped[$stationId])) {
                $grouped[$stationId] = [];
            }

            $grouped[$stationId][] = $item;
        }

        return $grouped;
    }

    /**
     * Find the optimal station for given station type and items
     * Uses intelligent load balancing algorithm
     */
    protected function findOptimalStation($stationType, $items)
    {
        // Get all active stations of this type
        $stations = KitchenStation::where('station_type', $stationType)
            ->where('is_active', true)
            ->get();

        if ($stations->isEmpty()) {
            return null;
        }

        // If only one station, return it
        if ($stations->count() === 1) {
            return $stations->first();
        }

        // Calculate load score for each station
        $scores = [];

        foreach ($stations as $station) {
            $scores[$station->id] = $this->calculateLoadScore($station, $items);
        }

        // Get station with lowest score (least loaded)
        $optimalStationId = array_keys($scores, min($scores))[0];

        return $stations->find($optimalStationId);
    }

    /**
     * Calculate load score for a station
     * Lower score = better choice
     */
    protected function calculateLoadScore(KitchenStation $station, $items)
    {
        // Factor 1: Current capacity ratio (0-100 points)
        $capacityRatio = $station->load_percentage;
        $capacityScore = $capacityRatio;

        // Factor 2: Queue length (0-50 points)
        $queueLength = $station->pendingAssignments()->count();
        $queueScore = min($queueLength * 5, 50);

        // Factor 3: Item complexity (0-30 points)
        $totalLoadPoints = 0;
        foreach ($items as $item) {
            $totalLoadPoints += $item->menuItem->load_points * $item->quantity;
        }
        $complexityScore = min($totalLoadPoints * 2, 30);

        // Factor 4: Average completion time (0-20 points)
        $avgTime = $station->getAverageCompletionTime();
        $timeScore = min($avgTime / 2, 20);

        // Total score
        $totalScore = $capacityScore + $queueScore + $complexityScore + $timeScore;

        Log::debug("Station load score calculated", [
            'station' => $station->name,
            'capacity' => $capacityScore,
            'queue' => $queueScore,
            'complexity' => $complexityScore,
            'time' => $timeScore,
            'total' => $totalScore
        ]);

        return $totalScore;
    }

    /**
     * Assign items to a specific station
     */
    protected function assignItemsToStation(Order $order, KitchenStation $station, $items)
    {
        $totalLoadPoints = 0;

        // Create station assignment for each item
        foreach ($items as $item) {
            $loadPoints = $item->menuItem->load_points * $item->quantity;
            $totalLoadPoints += $loadPoints;

            StationAssignment::create([
                'order_id' => $order->id,
                'station_id' => $station->id,
                'order_item_id' => $item->id,
                'assignment_priority' => 1,
                'status' => 'assigned',
            ]);
        }

        // Add kitchen load record
        $estimatedCompletion = now()->addMinutes($this->estimateCompletionTime($station, $items));

        $this->kitchenLoadService->addLoad(
            $station->id,
            $order->id,
            $totalLoadPoints,
            $estimatedCompletion
        );

        // Log the assignment
        LoadBalancingLog::create([
            'order_id' => $order->id,
            'station_id' => $station->id,
            'action_type' => 'assignment',
            'old_load' => $station->current_load,
            'new_load' => $station->current_load + 1,
            'reason' => 'Auto-distributed order to optimal station',
            'metadata' => [
                'load_points' => $totalLoadPoints,
                'items_count' => count($items),
                'load_percentage_before' => $station->load_percentage,
            ]
        ]);
    }

    /**
     * Estimate completion time for items at a station
     */
    protected function estimateCompletionTime(KitchenStation $station, $items)
    {
        // Base preparation time for all items
        $basePrepTime = 0;
        foreach ($items as $item) {
            $basePrepTime += ($item->menuItem->preparation_time ?? 15) * $item->quantity;
        }

        // Add queue wait time
        $queueTime = $station->pendingAssignments()->count() * 5;

        // Apply load multiplier
        $loadMultiplier = 1 + ($station->load_percentage / 100);

        return round(($basePrepTime + $queueTime) * $loadMultiplier);
    }

    /**
     * Manually redistribute an order from one station to another
     */
    public function redistributeOrder(Order $order, KitchenStation $fromStation, KitchenStation $toStation, $reason = null)
    {
        DB::beginTransaction();

        try {
            // Update station assignments
            $assignments = StationAssignment::where('order_id', $order->id)
                ->where('station_id', $fromStation->id)
                ->where('status', 'assigned')
                ->get();

            foreach ($assignments as $assignment) {
                $assignment->update(['station_id' => $toStation->id]);
            }

            // Update kitchen loads
            $oldLoad = KitchenLoad::where('order_id', $order->id)
                ->where('station_id', $fromStation->id)
                ->first();

            if ($oldLoad) {
                $newLoad = $oldLoad->replicate();
                $newLoad->station_id = $toStation->id;
                $newLoad->save();

                $oldLoad->update(['status' => 'cancelled']);
            }

            // Update load counters
            $fromStation->decrement('current_load');
            $toStation->increment('current_load');

            // Log the redistribution
            LoadBalancingLog::create([
                'order_id' => $order->id,
                'station_id' => $toStation->id,
                'action_type' => 'redistribution',
                'old_load' => $toStation->current_load - 1,
                'new_load' => $toStation->current_load,
                'reason' => $reason ?? 'Manual redistribution',
                'metadata' => [
                    'from_station' => $fromStation->name,
                    'to_station' => $toStation->name,
                ]
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
