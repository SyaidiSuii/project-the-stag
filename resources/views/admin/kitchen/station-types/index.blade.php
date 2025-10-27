@extends('layouts.admin')

@section('title', 'Station Types Management')
@section('page-title', 'Station Types Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Action Buttons --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 class="section-title" style="margin: 0;">Manage Station Types</h2>
                <div class="section-controls" style="display: flex; gap: 12px;">
                    <a href="{{ route('admin.kitchen.station-types.create') }}" class="admin-btn btn-primary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-plus-circle"></i> Add New Station Type
                    </a>
                    <a href="{{ route('admin.kitchen.index') }}" class="admin-btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Station Types Table --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Station Type</th>
                            <th>Icon</th>
                            <th>Usage Count</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stationTypes as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td><strong>{{ $type->station_type }}</strong></td>
                            <td>
                                @if($type->icon)
                                    <span style="font-size: 24px;">{!! $type->icon !!}</span>
                                @else
                                    <span style="color: #94a3b8;">No icon</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $type->kitchen_stations_count > 0 ? 'success' : 'secondary' }}">
                                    {{ $type->kitchen_stations_count }} station(s)
                                </span>
                            </td>
                            <td>{{ $type->created_at->format('M d, Y') }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('admin.kitchen.station-types.edit', $type->id) }}"
                                       class="admin-btn btn-sm btn-primary"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.kitchen.station-types.destroy', $type->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="admin-btn btn-sm btn-danger delete-btn"
                                                title="Delete"
                                                data-station-type="{{ $type->station_type }}"
                                                {{ $type->kitchen_stations_count > 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <i class="fas fa-layer-group" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No station types found. Create your first station type!</p>
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

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const stationType = this.getAttribute('data-station-type');
            const form = this.closest('.delete-form');

            const confirmed = await Confirm.delete(
                'Delete Station Type?',
                `Are you sure you want to delete "${stationType}"? This action cannot be undone.`
            );

            if (confirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
