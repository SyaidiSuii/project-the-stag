@extends('layouts.admin')

@section('title', 'Kitchen Stations Management')
@section('page-title', 'Kitchen Stations Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Action Buttons --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 class="section-title" style="margin: 0;">Manage Kitchen Stations</h2>
                <div class="section-controls" style="display: flex; gap: 12px;">
                    <a href="{{ route('admin.kitchen.station-types.index') }}" class="admin-btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-layer-group"></i> Station Types
                    </a>
                    <a href="{{ route('admin.kitchen.stations.form') }}" class="admin-btn btn-primary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-plus-circle"></i> Add New Station
                    </a>
                    <a href="{{ route('admin.kitchen.index') }}" class="admin-btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stations Table --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Station Name</th>
                    <th>Type</th>
                    <th>Current Load</th>
                    <th>Max Capacity</th>
                    <th>Load %</th>
                    <th>Active Orders</th>
                    <th>Avg Time</th>
                    <th>Operating Hours</th>
                    <th>Actions</th>
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
                        <strong>{{ $station->name }}</strong>
                    </td>
                    <td>
                        <span style="font-size: 20px; margin-right: 8px;">
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
                        {{ ucfirst(str_replace('_', ' ', $station->station_type)) }}
                    </td>
                    <td>{{ $station->current_load }}</td>
                    <td>{{ $station->max_capacity }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $station->load_percentage }}%; background: {{ $station->isOverloaded() ? '#ef4444' : ($station->isApproachingCapacity() ? '#f59e0b' : '#10b981') }}; transition: width 0.3s;"></div>
                            </div>
                            <span style="font-weight: 600; min-width: 45px; color: {{ $station->isOverloaded() ? '#ef4444' : '#64748b' }};">
                                {{ number_format($station->load_percentage, 0) }}%
                            </span>
                        </div>
                    </td>
                    <td>{{ $station->active_loads_count ?? 0 }}</td>
                    <td>{{ $station->getAverageCompletionTime() }} min</td>
                    <td>
                        @if($station->operating_hours)
                            <small>{{ $station->operating_hours['start'] ?? 'N/A' }} - {{ $station->operating_hours['end'] ?? 'N/A' }}</small>
                        @else
                            <small class="text-muted">Not set</small>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('admin.kitchen.stations.edit', $station->id) }}" class="admin-btn btn-sm btn-primary"
                               title="Edit"
                               style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-edit" style="margin: 0;"></i>
                            </a>
                            <form action="{{ route('admin.kitchen.stations.toggleStatus', $station->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-btn btn-sm btn-warning"
                                        title="{{ $station->is_active ? 'Deactivate' : 'Activate' }}"
                                        style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-power-off" style="margin: 0;"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.kitchen.stations.destroy', $station->id) }}" method="POST" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn btn-sm btn-danger"
                                        title="Delete"
                                        data-station-name="{{ $station->name }}"
                                        style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-trash" style="margin: 0;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-store-slash" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                            <p>No kitchen stations found. Add your first station to get started!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
    </div>
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
