@extends('layouts.admin')

@section('title', 'Kitchen Stations Management')
@section('page-title', 'Kitchen Stations Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.stations-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 32px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stations-header h1 {
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 12px;
}

.stations-header h1 i {
    color: #6b7280;
    font-size: 24px;
}

.stations-header p {
    margin: 0;
    color: #6b7280;
    font-size: 15px;
    line-height: 1.6;
}

.header-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    flex-wrap: wrap;
}

.header-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    font-size: 14px;
}

.header-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #111827;
}

.header-btn.btn-primary {
    background: #111827;
    color: #ffffff;
    border-color: #111827;
}

.header-btn.btn-primary:hover {
    background: #1f2937;
    border-color: #1f2937;
    color: #ffffff;
}

.stations-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.stat-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.stat-icon.blue { background: #eff6ff; border-color: #dbeafe; }
.stat-icon.blue i { color: #3b82f6; }
.stat-icon.green { background: #f0fdf4; border-color: #dcfce7; }
.stat-icon.green i { color: #22c55e; }
.stat-icon.orange { background: #fff7ed; border-color: #fed7aa; }
.stat-icon.orange i { color: #f97316; }
.stat-icon.red { background: #fef2f2; border-color: #fecaca; }
.stat-icon.red i { color: #ef4444; }

.stat-value {
    font-size: 32px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stations-table-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.table-header h2 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-header h2 i {
    color: #6b7280;
    font-size: 14px;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead th {
    background: #f9fafb;
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e5e7eb;
}

.admin-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.15s ease;
}

.admin-table tbody tr:hover {
    background: #f9fafb;
}

.admin-table tbody tr:last-child {
    border-bottom: none;
}

.admin-table tbody td {
    padding: 16px;
    color: #374151;
    font-size: 14px;
    vertical-align: middle;
}

.station-name {
    font-weight: 600;
    color: #111827;
    font-size: 14px;
}

.station-type-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.station-type-icon {
    font-size: 20px;
    opacity: 0.9;
}

.load-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
}

.load-bar-container {
    flex: 1;
    height: 8px;
    background: #f3f4f6;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.load-bar {
    height: 100%;
    border-radius: 6px;
    transition: all 0.3s ease;
    background: #22c55e;
}

.load-bar.warning {
    background: #f59e0b;
}

.load-bar.danger {
    background: #ef4444;
}

.load-percentage {
    font-weight: 600;
    font-size: 13px;
    min-width: 45px;
    text-align: right;
}

.load-percentage.normal { color: #22c55e; }
.load-percentage.warning { color: #f59e0b; }
.load-percentage.danger { color: #ef4444; }

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    text-decoration: none;
    background: #ffffff;
}

.action-btn.edit {
    color: #3b82f6;
}

.action-btn.edit:hover {
    background: #eff6ff;
    border-color: #3b82f6;
}

.action-btn.toggle {
    color: #6b7280;
}

.action-btn.toggle:hover {
    background: #f3f4f6;
    border-color: #6b7280;
}

.action-btn.delete {
    color: #ef4444;
}

.action-btn.delete:hover {
    background: #fef2f2;
    border-color: #ef4444;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #dcfce7;
}

.badge-success::before {
    content: "●";
    font-size: 12px;
    color: #22c55e;
}

.badge-danger {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.badge-danger::before {
    content: "●";
    font-size: 12px;
    color: #ef4444;
}

.empty-state {
    text-align: center;
    padding: 64px 40px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    opacity: 0.4;
    margin-bottom: 20px;
    display: block;
    color: #d1d5db;
}

.empty-state h3 {
    font-size: 18px;
    color: #374151;
    margin: 0 0 8px 0;
    font-weight: 600;
}

.empty-state p {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 24px 0;
}

.empty-state-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: #111827;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    font-size: 14px;
}

.empty-state-btn:hover {
    background: #1f2937;
    color: white;
}

.operating-hours {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.operating-hours i {
    font-size: 12px;
    color: #9ca3af;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Header --}}
    <div class="stations-header">
        <h1><i class="fas fa-store"></i> Kitchen Stations Management</h1>
        <p>Configure and manage your kitchen stations for optimal order distribution</p>
        <div class="header-actions">
            <a href="{{ route('admin.kitchen.index') }}" class="header-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('admin.kitchen.station-types.index') }}" class="header-btn">
                <i class="fas fa-layer-group"></i> Manage Station Types
            </a>
            <a href="{{ route('admin.kitchen.stations.form') }}" class="header-btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add New Station
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stations-stats">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-value">{{ $stations->count() }}</div>
            <div class="stat-label">Total Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stations->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value">{{ $stations->filter(function($s) { return $s->isApproachingCapacity(); })->count() }}</div>
            <div class="stat-label">Busy Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-value">{{ $stations->filter(function($s) { return $s->isOverloaded(); })->count() }}</div>
            <div class="stat-label">Overloaded</div>
        </div>
    </div>

    {{-- Stations Table --}}
    <div class="stations-table-wrapper">
        <div class="table-header">
            <h2><i class="fas fa-list"></i> All Kitchen Stations</h2>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Station Name</th>
                    <th>Type</th>
                    <th style="text-align: center;">Current Load</th>
                    <th style="text-align: center;">Capacity</th>
                    <th>Load Percentage</th>
                    <th style="text-align: center;">Active Orders</th>
                    <th style="text-align: center;">Avg Time</th>
                    <th>Operating Hours</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stations as $station)
                <tr>
                    <td>
                        @if($station->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="station-name">{{ $station->name }}</div>
                    </td>
                    <td>
                        <div class="station-type-cell">
                            <span class="station-type-icon">
                                @if($station->station_type == 'hot_kitchen')
                                    &#x1F525;
                                @elseif($station->station_type == 'cold_kitchen')
                                    &#x1F957;
                                @elseif($station->station_type == 'drinks')
                                    &#x1F379;
                                @elseif($station->station_type == 'desserts')
                                    &#x1F370;
                                @elseif($station->station_type == 'grill')
                                    &#x1F969;
                                @elseif($station->station_type == 'bakery')
                                    &#x1F956;
                                @elseif($station->station_type == 'salad_bar')
                                    &#x1F96D;
                                @elseif($station->station_type == 'pastry')
                                    &#x1F9C1;
                                @else
                                    &#x1F3E0;
                                @endif
                            </span>
                            <span>{{ ucfirst(str_replace('_', ' ', $station->station_type)) }}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $station->current_load }}</strong>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $station->max_capacity }}</strong>
                    </td>
                    <td>
                        <div class="load-indicator">
                            <div class="load-bar-container">
                                <div class="load-bar {{ $station->isOverloaded() ? 'danger' : ($station->isApproachingCapacity() ? 'warning' : '') }}"
                                     style="width: {{ $station->load_percentage }}%;"></div>
                            </div>
                            <span class="load-percentage {{ $station->isOverloaded() ? 'danger' : ($station->isApproachingCapacity() ? 'warning' : 'normal') }}">
                                {{ number_format($station->load_percentage, 0) }}%
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $station->active_loads_count ?? 0 }}</strong>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $station->getAverageCompletionTime() }}</strong> min
                    </td>
                    <td>
                        <div class="operating-hours">
                            @if($station->operating_hours)
                                <i class="fas fa-clock"></i>
                                <span>{{ $station->operating_hours['start'] ?? 'N/A' }} - {{ $station->operating_hours['end'] ?? 'N/A' }}</span>
                            @else
                                <i class="fas fa-minus-circle"></i>
                                <span style="color: #cbd5e1;">Not set</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.kitchen.stations.edit', $station->id) }}"
                               class="action-btn edit"
                               title="Edit Station">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.kitchen.stations.toggleStatus', $station->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="action-btn toggle"
                                        title="{{ $station->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.kitchen.stations.destroy', $station->id) }}"
                                  method="POST"
                                  class="delete-form"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="action-btn delete"
                                        title="Delete Station"
                                        data-station-name="{{ $station->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="fas fa-store-slash"></i>
                            <h3>No Kitchen Stations Found</h3>
                            <p>Get started by adding your first kitchen station to enable smart order distribution</p>
                            <a href="{{ route('admin.kitchen.stations.form') }}" class="empty-state-btn">
                                <i class="fas fa-plus-circle"></i> Add Your First Station
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- End kitchen-page --}}

@endsection

@section('scripts')
<script>
// Delete confirmation with modern modal
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const stationName = this.querySelector('[data-station-name]')?.getAttribute('data-station-name') || 'this station';

        const confirmed = await Confirm.delete(
            'Delete Station?',
            `Are you sure you want to delete ${stationName}? This action cannot be undone.`
        );

        if (confirmed) {
            this.submit();
        }
    });
});
</script>
@endsection
