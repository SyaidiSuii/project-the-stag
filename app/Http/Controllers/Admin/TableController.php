<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Validation\Rule;


class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request('cancel')) {
            return redirect()->route('admin.table.index');
        }

        $query = Table::query();

        // Filter by search (table number, type, location)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                  ->orWhere('table_type', 'like', "%{$search}%")
                  ->orWhere('location_description', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by table type
        if ($request->filled('table_type')) {
            $query->where('table_type', $request->table_type);
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->active == '1');
        }

        $tables = $query->orderBy('table_number')
                       ->paginate(10)
                       ->appends($request->query());

        return view('admin.table.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $table = new Table;
        return view('admin.table.form', compact('table'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define a list of valid amenities for validation
        $amenitiesList = ['wifi_available', 'power_outlet', 'window_view', 'air_conditioning', 'heating', 'wheelchair_accessible', 'high_chair_available', 'privacy_screen', 'soundproof', 'tv_screen'];
        
        $this->validate($request, [
            'table_number' => 'required|string|max:10|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'qr_code' => 'nullable|string|max:255|unique:tables,qr_code',
            'nfc_tag_id' => 'nullable|string|max:100|unique:tables,nfc_tag_id',
            'location_description' => 'nullable|string|max:255',
            'coordinates' => 'nullable|array', // Ensures coordinates is an array if present
            'coordinates.lat' => 'nullable|numeric|between:-90,90', // Validates latitude
            'coordinates.lng' => 'nullable|numeric|between:-180,180', // Validates longitude
            'table_type' => 'required|in:indoor,outdoor,vip',
            'amenities' => 'nullable|array', // Ensures amenities is an array if present
            'amenities.*' => ['string', Rule::in($amenitiesList)], // Validates each amenity
            'is_active' => 'nullable|boolean',
        ], [
            'table_number.required' => 'Table number is required.',
            'table_number.unique' => 'Table number already exists.',
            'capacity.required' => 'Table capacity is required.',
            'capacity.min' => 'Table capacity must be at least 1.',
            'capacity.max' => 'Table capacity cannot exceed 50.',
            'qr_code.required' => 'QR code is required.',
            'qr_code.unique' => 'QR code already exists.',
            'nfc_tag_id.unique' => 'NFC tag ID already exists.',
            'status.in' => 'Invalid status selected.',
            'table_type.in' => 'Invalid table type selected.',
            'coordinates.array' => 'The coordinates must be an array.',
            'amenities.array' => 'The amenities must be an array.',
        ]);

        $table = new Table;
        $table->fill($request->all());

        // Correctly handle the 'is_active' checkbox
        $table->is_active = $request->has('is_active');

        // Handle amenities - let Laravel's casting handle the array conversion
        $table->amenities = $request->get('amenities', []);

        $table->save();

        return redirect()->route('admin.table.index')->with('message', 'Table record has been saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        return view('admin.table.show', compact('table'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return view('admin.table.form', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        // Define a list of valid amenities for validation
        $amenitiesList = ['wifi_available', 'power_outlet', 'window_view', 'air_conditioning', 'heating', 'wheelchair_accessible', 'high_chair_available', 'privacy_screen', 'soundproof', 'tv_screen'];

        $this->validate($request, [
            'table_number' => 'required|string|max:10|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'qr_code' => 'nullable|string|max:255|unique:tables,qr_code,' . $table->id,
            'nfc_tag_id' => 'nullable|string|max:100|unique:tables,nfc_tag_id,' . $table->id,
            'location_description' => 'nullable|string|max:255',
            'coordinates' => 'nullable|array',
            'coordinates.lat' => 'nullable|numeric|between:-90,90',
            'coordinates.lng' => 'nullable|numeric|between:-180,180',
            'table_type' => 'required|in:indoor,outdoor,vip',
            'amenities' => 'nullable|array',
            'amenities.*' => ['string', Rule::in($amenitiesList)],
            'is_active' => 'nullable|boolean',
        ], [
            'table_number.required' => 'Table number is required.',
            'table_number.unique' => 'Table number already exists.',
            'capacity.required' => 'Table capacity is required.',
            'capacity.min' => 'Table capacity must be at least 1.',
            'capacity.max' => 'Table capacity cannot exceed 50.',
            'qr_code.required' => 'QR code is required.',
            'qr_code.unique' => 'QR code already exists.',
            'nfc_tag_id.unique' => 'NFC tag ID already exists.',
            'status.in' => 'Invalid status selected.',
            'table_type.in' => 'Invalid table type selected.',
            'coordinates.array' => 'The coordinates must be an array.',
            
        ]);

        $table->fill($request->all());
        
        // Correctly handle the 'is_active' checkbox
        $table->is_active = $request->has('is_active');

        // Handle amenities - let Laravel's casting handle the array conversion
        $table->amenities = $request->get('amenities', []);
        
        $table->save();

        return redirect()->route('admin.table.index')->with('message', 'Table record has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.table.index')->with('message', 'Table record has been deleted!');
    }
}