<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KitchenStation;

class KitchenStationController extends Controller
{
    /**
     * Display a listing of kitchen stations
     */
    public function index()
    {
        $stations = KitchenStation::withCount(['activeLoads', 'pendingAssignments'])
            ->ordered()
            ->get();

        return view('admin.kitchen.stations.index', compact('stations'));
    }

    /**
     * Show the form for creating a new kitchen station
     */
    public function create()
    {
        $station = null;
        return view('admin.kitchen.stations.form', compact('station'));
    }

    /**
     * Store a newly created kitchen station in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:kitchen_stations,name',
            'icon' => 'required|string',
            'max_capacity' => 'required|integer|min:1',
            'operating_hours' => 'nullable|array',
            'operating_hours.start' => 'nullable|date_format:H:i',
            'operating_hours.end' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        // Convert checkbox to boolean
        $validated['is_active'] = $request->has('is_active');

        KitchenStation::create($validated);

        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', 'Kitchen station created successfully');
    }

    /**
     * Show the form for editing the specified kitchen station
     */
    public function edit(KitchenStation $station)
    {
        return view('admin.kitchen.stations.form', compact('station'));
    }

    /**
     * Update the specified kitchen station in database
     */
    public function update(Request $request, KitchenStation $station)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:kitchen_stations,name,' . $station->id,
            'icon' => 'required|string',
            'max_capacity' => 'required|integer|min:1',
            'operating_hours' => 'nullable|array',
            'operating_hours.start' => 'nullable|date_format:H:i',
            'operating_hours.end' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        // Convert checkbox to boolean
        $validated['is_active'] = $request->has('is_active');

        $station->update($validated);

        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', 'Kitchen station updated successfully');
    }

    /**
     * Display the specified kitchen station
     */
    public function show(KitchenStation $station)
    {
        $station->load([
            'activeLoads.order.items.menuItem',
            'pendingAssignments.order',
            'logs' => function ($q) {
                $q->latest()->limit(20);
            }
        ]);

        return view('admin.kitchen.station-detail', compact('station'));
    }

    /**
     * Toggle the active status of a kitchen station
     */
    public function toggleStatus(KitchenStation $station)
    {
        $station->update(['is_active' => !$station->is_active]);

        return redirect()->back()
            ->with('success', 'Station status updated');
    }

    /**
     * Delete the specified kitchen station
     */
    public function destroy(KitchenStation $station)
    {
        // Check if station has active assignments
        if ($station->activeLoads()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete station with active orders');
        }

        $station->delete();

        return redirect()->route('admin.kitchen.stations.index')
            ->with('success', 'Kitchen station deleted successfully');
    }
}
