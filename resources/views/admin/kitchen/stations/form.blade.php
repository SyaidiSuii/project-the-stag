@extends('layouts.admin')

@section('title', $station ? 'Edit Kitchen Station' : 'Add New Kitchen Station')
@section('page-title', $station ? 'Edit Kitchen Station' : 'Add New Kitchen Station')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.form-container {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #334155;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 13px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.text-danger {
    color: #ef4444;
}

.text-muted {
    color: #94a3b8;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="form-container">
                <form action="{{ $station ? route('admin.kitchen.stations.update', $station->id) : route('admin.kitchen.stations.store') }}" method="POST">
                    @csrf
                    @if($station)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="name">Station Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $station->name ?? '') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="station_type">Station Type <span class="text-danger">*</span></label>
                        <select id="station_type" name="station_type" required {{ $station ? 'disabled' : '' }}>
                            <option value="">-- Select Type --</option>
                            <option value="hot_kitchen" {{ old('station_type', $station->station_type ?? '') == 'hot_kitchen' ? 'selected' : '' }}>&#x1F525; Hot Kitchen</option>
                            <option value="cold_kitchen" {{ old('station_type', $station->station_type ?? '') == 'cold_kitchen' ? 'selected' : '' }}>&#x1F957; Cold Kitchen</option>
                            <option value="drinks" {{ old('station_type', $station->station_type ?? '') == 'drinks' ? 'selected' : '' }}>&#x1F379; Beverages & Drinks</option>
                            <option value="desserts" {{ old('station_type', $station->station_type ?? '') == 'desserts' ? 'selected' : '' }}>&#x1F370; Desserts</option>
                            <option value="grill" {{ old('station_type', $station->station_type ?? '') == 'grill' ? 'selected' : '' }}>&#x1F969; Grill Station</option>
                            <option value="bakery" {{ old('station_type', $station->station_type ?? '') == 'bakery' ? 'selected' : '' }}>&#x1F956; Bakery</option>
                            <option value="salad_bar" {{ old('station_type', $station->station_type ?? '') == 'salad_bar' ? 'selected' : '' }}>&#x1F96D; Salad Bar</option>
                            <option value="pastry" {{ old('station_type', $station->station_type ?? '') == 'pastry' ? 'selected' : '' }}>&#x1F9C1; Pastry</option>
                        </select>
                        @if($station)
                            <small class="text-muted">Station type cannot be changed</small>
                        @endif
                        @error('station_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="max_capacity">Max Capacity <span class="text-danger">*</span></label>
                        <input type="number" id="max_capacity" name="max_capacity" min="1" value="{{ old('max_capacity', $station->max_capacity ?? 10) }}" required>
                        <small>Maximum concurrent orders this station can handle</small>
                        @error('max_capacity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="operating_hours_start">Operating Hours - Start</label>
                            <input type="time" id="operating_hours_start" name="operating_hours[start]"
                                   value="{{ old('operating_hours.start', $station->operating_hours['start'] ?? '10:00') }}">
                            @error('operating_hours.start')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="operating_hours_end">Operating Hours - End</label>
                            <input type="time" id="operating_hours_end" name="operating_hours[end]"
                                   value="{{ old('operating_hours.end', $station->operating_hours['end'] ?? '22:00') }}">
                            @error('operating_hours.end')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $station->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" style="margin: 0;">Active</label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.kitchen.stations.index') }}" class="admin-btn btn-secondary" style="padding: 10px 24px;">Cancel</a>
                        <button type="submit" class="admin-btn btn-primary" style="padding: 10px 24px;">
                            {{ $station ? 'Save Changes' : 'Create Station' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
