<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Kitchen\KitchenLoadService;

class KitchenStatusController extends Controller
{
    protected $kitchenLoadService;

    public function __construct(KitchenLoadService $kitchenLoadService)
    {
        $this->kitchenLoadService = $kitchenLoadService;
    }

    /**
     * Get real-time kitchen status for all stations
     * Used by frontend for auto-refresh
     */
    public function index()
    {
        $status = $this->kitchenLoadService->getStationsStatus();

        return response()->json([
            'success' => true,
            'data' => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get status for a specific station
     */
    public function show($stationId)
    {
        $allStatus = $this->kitchenLoadService->getStationsStatus();
        $station = $allStatus->firstWhere('id', $stationId);

        if (!$station) {
            return response()->json([
                'success' => false,
                'message' => 'Station not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $station,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
