@extends('layouts.admin')
@section('title', 'Special Events')
@section('page-title', 'Special Events')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Special Events</h2>
        <a href="{{ route('admin.rewards.special-events.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> New Event
        </a>
    </div>
    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead><tr><th>Event Name</th><th>Points Multiplier</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td><div style="font-weight: 600;">{{ $event->name }}</div></td>
                            <td><span class="badge badge-primary">{{ $event->points_multiplier }}x</span></td>
                            <td>{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if($event->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.special-events.edit', $event->id) }}" class="admin-btn btn-icon"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.rewards.special-events.destroy', $event->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-btn btn-icon btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; padding: 40px;"><div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;"><i class="fas fa-calendar" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i><p>No events found.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.rewards._table_scroll_script')
@endsection
