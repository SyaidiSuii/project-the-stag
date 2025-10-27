<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StationType;
use App\Models\KitchenStation;

class StationTypeController extends Controller
{
    /**
     * Display a listing of station types
     */
    public function index()
    {
        $stationTypes = StationType::withCount('kitchenStations')->get();
        return view('admin.kitchen.station-types.index', compact('stationTypes'));
    }

    /**
     * Display the specified kitchen station details
     */
    public function show($id)
    {
        $station = KitchenStation::with(['stationType', 'stationAssignments.orderItem.order', 'stationAssignments.orderItem.menuItem'])
            ->findOrFail($id);

        return view('admin.kitchen.stations.show', compact('station'));
    }

    /**
     * Show the form for creating a new station type
     */
    public function create()
    {
        return view('admin.kitchen.station-types.create');
    }

    /**
     * Store a newly created station type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_type' => 'required|string|max:255|unique:station_types,station_type',
            'icon' => 'nullable|string|max:255',
        ]);

        StationType::create($validated);

        return redirect()->route('admin.kitchen.station-types.index')
            ->with('success', 'Station type created successfully');
    }

    /**
     * Show the form for editing a station type
     */
    public function edit($id)
    {
        $stationType = StationType::findOrFail($id);
        return view('admin.kitchen.station-types.edit', compact('stationType'));
    }

    /**
     * Update the specified station type
     */
    public function update(Request $request, $id)
    {
        $stationType = StationType::findOrFail($id);

        $validated = $request->validate([
            'station_type' => 'required|string|max:255|unique:station_types,station_type,' . $id,
            'icon' => 'nullable|string|max:255',
        ]);

        $stationType->update($validated);

        return redirect()->route('admin.kitchen.station-types.index')
            ->with('success', 'Station type updated successfully');
    }

    /**
     * Remove the specified station type
     */
    public function destroy($id)
    {
        $stationType = StationType::findOrFail($id);

        // Check if any kitchen stations are using this type
        if ($stationType->kitchenStations()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete station type that is being used by kitchen stations');
        }

        $stationType->delete();

        return redirect()->route('admin.kitchen.station-types.index')
            ->with('success', 'Station type deleted successfully');
    }
}
