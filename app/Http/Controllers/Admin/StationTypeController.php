<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StationType;
use App\Models\KitchenStation;

class StationTypeController extends Controller
{
    /**
     * Display a listing of station types OR kitchen stations
     * (Handles both /admin/kitchen/stations and /admin/kitchen/station-types)
     */
    public function index()
    {
        // Check if this is for kitchen stations or station types based on the URL
        if (request()->is('admin/kitchen/stations')) {
            // Kitchen Stations Index
            $stations = KitchenStation::with('stationType')->get();
            return view('admin.kitchen.stations.index', compact('stations'));
        }

        // Station Types Index
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
     * Show the form for editing a kitchen station
     */
    public function edit($id)
    {
        // Check if this is for kitchen station or station type
        if (request()->is('admin/kitchen/stations/*')) {
            // Kitchen Station Edit
            $station = KitchenStation::with('stationType')->findOrFail($id);
            $stationTypes = StationType::all();
            return view('admin.kitchen.stations.form', compact('station', 'stationTypes'));
        }

        // Station Type Edit
        $stationType = StationType::findOrFail($id);
        return view('admin.kitchen.station-types.edit', compact('stationType'));
    }

    /**
     * Toggle the active status of a kitchen station
     */
    public function toggleStatus($id)
    {
        $station = KitchenStation::findOrFail($id);
        $station->is_active = !$station->is_active;
        $station->save();

        return redirect()->back()
            ->with('success', 'Station status updated successfully');
    }

    /**
     * Show the form for creating a new station type
     */
    public function create()
    {
        return view('admin.kitchen.station-types.create');
    }

    /**
     * Store a newly created station type OR kitchen station
     */
    public function store(Request $request)
    {
        // Check if this is for kitchen station or station type
        if ($request->has('name')) {
            // Kitchen Station Store
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'station_type_id' => 'required|exists:station_types,id',
                'max_capacity' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active') ? true : false;
            KitchenStation::create($validated);

            return redirect()->route('admin.kitchen.stations.index')
                ->with('success', 'Kitchen station created successfully');
        }

        // Station Type Store
        $validated = $request->validate([
            'station_type' => 'required|string|max:255|unique:station_types,station_type',
            'icon' => 'nullable|string|max:255',
        ]);

        StationType::create($validated);

        return redirect()->route('admin.kitchen.station-types.index')
            ->with('success', 'Station type created successfully');
    }

    /**
     * Update the specified station type OR kitchen station
     */
    public function update(Request $request, $id)
    {
        // Check if this is for kitchen station or station type
        if (request()->is('admin/kitchen/stations/*')) {
            // Kitchen Station Update
            $station = KitchenStation::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'station_type_id' => 'required|exists:station_types,id',
                'max_capacity' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active') ? true : false;
            $station->update($validated);

            return redirect()->route('admin.kitchen.stations.index')
                ->with('success', 'Kitchen station updated successfully');
        }

        // Station Type Update
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
