@extends('layouts.admin')

@section('title', 'Kitchen Stations Management')
@section('page-title', 'Kitchen Stations Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.stations-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 32px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.stations-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
}

.stations-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
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
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 14px;
}

.header-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    color: white;
}

.header-btn.btn-primary {
    background: white;
    color: #667eea;
    border-color: white;
}

.header-btn.btn-primary:hover {
    background: #f8f9ff;
    color: #5568d3;
}

.stations-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
}

.stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.green { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
.stat-icon.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-icon.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}

.stations-table-wrapper {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.table-header {
    padding: 24px 32px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.table-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead th {
    background: #f8fafc;
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
}

.admin-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
}

.admin-table tbody tr:hover {
    background: #f8fafc;
}

.admin-table tbody td {
    padding: 20px;
    color: #334155;
    font-size: 14px;
    vertical-align: middle;
}

.station-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 15px;
}

.station-type-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.station-type-icon {
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.load-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
}

.load-bar-container {
    flex: 1;
    height: 10px;
    background: #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.load-bar {
    height: 100%;
    border-radius: 10px;
    transition: all 0.4s ease;
    background: linear-gradient(90deg, #10b981, #059669);
}

.load-bar.warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.load-bar.danger {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

.load-percentage {
    font-weight: 700;
    font-size: 14px;
    min-width: 50px;
    text-align: right;
}

.load-percentage.normal { color: #10b981; }
.load-percentage.warning { color: #f59e0b; }
.load-percentage.danger { color: #ef4444; }

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
    text-decoration: none;
}

.action-btn.edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-btn.edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
}

.action-btn.toggle {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.action-btn.toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.4);
}

.action-btn.delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.action-btn.delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.4);
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-success::before {
    content: "●";
    font-size: 16px;
    color: #10b981;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-danger::before {
    content: "●";
    font-size: 16px;
    color: #ef4444;
}

.empty-state {
    text-align: center;
    padding: 80px 40px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 24px;
    display: block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-state h3 {
    font-size: 20px;
    color: #64748b;
    margin: 0 0 12px 0;
}

.empty-state p {
    font-size: 15px;
    margin: 0 0 24px 0;
}

.empty-state-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.empty-state-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.operating-hours {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.operating-hours i {
    font-size: 12px;
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
                <i class="fas fa-store" style="color: white;"></i>
            </div>
            <div class="stat-value">{{ $stations->count() }}</div>
            <div class="stat-label">Total Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle" style="color: white;"></i>
            </div>
            <div class="stat-value">{{ $stations->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-triangle" style="color: white;"></i>
            </div>
            <div class="stat-value">{{ $stations->filter(function($s) { return $s->isApproachingCapacity(); })->count() }}</div>
            <div class="stat-label">Busy Stations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-fire" style="color: white;"></i>
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
