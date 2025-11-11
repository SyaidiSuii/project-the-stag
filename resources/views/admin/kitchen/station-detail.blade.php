@extends('layouts.admin')

@section('title', 'Station Details - ' . $station->name)
@section('page-title', 'Station Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.detail-container {
    max-width: 1200px;
    margin: 0 auto;
}

.detail-header {
    background: white;
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.detail-header-top {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 24px;
}

.detail-icon {
    width: 80px;
    height: 80px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
}

.detail-info h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 8px 0;
}

.detail-info .station-type-label {
    color: #64748b;
    font-size: 16px;
}

.detail-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.detail-stat-card {
    background: #f8fafc;
    padding: 16px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.detail-stat-label {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 8px;
}

.detail-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.detail-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.detail-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.order-item {
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
}

.order-item:last-child {
    margin-bottom: 0;
}

.log-item {
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
}

.log-item:last-child {
    border-bottom: none;
}

.log-time {
    font-size: 13px;
    color: #64748b;
}

.log-message {
    font-size: 14px;
    color: #1e293b;
    margin-top: 4px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #334155;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.back-button:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.edit-station-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #6366f1;
    color: white;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.edit-station-button:hover {
    background: #5558e3;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    color: white;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    margin-left: 8px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
}

.status-badge.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 40px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 56px;
    opacity: 0.3;
    margin-bottom: 16px;
    line-height: 1;
}

.empty-state p {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    <div class="detail-container">
        {{-- Back Button --}}
        <div class="kitchen-section">
            <a href="{{ route('admin.kitchen.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        {{-- Station Header --}}
        <div class="detail-header">
            <div class="detail-header-top">
                <div class="detail-icon">
                    {!! $station->icon ?? '🍽️' !!}
                </div>
                <div class="detail-info">
                    <h1>{{ $station->name }}</h1>
                    <div class="station-type-label">
                        @if($station->is_active)
                            <span class="status-badge badge-success">Active</span>
                        @else
                            <span class="status-badge badge-danger">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-stats">
                <div class="detail-stat-card">
                    <div class="detail-stat-label">Current Load</div>
                    <div class="detail-stat-value">{{ $station->current_load }} / {{ $station->max_capacity }}</div>
                </div>
                <div class="detail-stat-card">
                    <div class="detail-stat-label">Load Percentage</div>
                    <div class="detail-stat-value" style="color: {{ $station->isOverloaded() ? '#ef4444' : ($station->isApproachingCapacity() ? '#f59e0b' : '#10b981') }}">
                        {{ number_format($station->load_percentage, 1) }}%
                    </div>
                </div>
                <div class="detail-stat-card">
                    <div class="detail-stat-label">Pending Orders</div>
                    <div class="detail-stat-value">{{ $station->pendingAssignments()->count() }}</div>
                </div>
                <div class="detail-stat-card">
                    <div class="detail-stat-label">Avg Completion Time</div>
                    <div class="detail-stat-value">{{ $station->getAverageCompletionTime() }} min</div>
                </div>
            </div>
        </div>

        {{-- Active Orders --}}
        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-utensils"></i> Active Orders
            </h3>
            @forelse($station->activeLoads as $load)
                <div class="order-item">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <div>
                            <strong>Order #{{ $load->order->id }}</strong>
                            <span class="badge badge-{{ $load->status == 'in_progress' ? 'warning' : 'info' }}">
                                {{ ucfirst($load->status) }}
                            </span>
                        </div>
                        <div style="text-align: right; font-size: 13px; color: #64748b;">
                            {{ $load->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($load->order && $load->order->items)
                        <div style="font-size: 14px; color: #64748b;">
                            @foreach($load->order->items as $item)
                                <div>{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Unknown Item' }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No active orders</p>
                </div>
            @endforelse
        </div>

        {{-- Pending Assignments --}}
        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-clock"></i> Pending Assignments
            </h3>
            @forelse($station->pendingAssignments as $assignment)
                <div class="order-item">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <strong>Order #{{ $assignment->order->id }}</strong>
                            <span class="badge badge-secondary">{{ ucfirst($assignment->status) }}</span>
                        </div>
                        <div style="text-align: right; font-size: 13px; color: #64748b;">
                            Assigned {{ $assignment->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No pending assignments</p>
                </div>
            @endforelse
        </div>

        {{-- Recent Activity Logs --}}
        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-history"></i> Recent Activity (Last 20)
            </h3>
            @forelse($station->logs as $log)
                <div class="log-item">
                    <div class="log-time">
                        <i class="fas fa-clock"></i> {{ $log->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div class="log-message">
                        <span class="badge badge-{{ $log->action_type == 'overload_alert' ? 'danger' : 'info' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                        </span>
                        {{ $log->reason ?? 'No details' }}
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No recent activity</p>
                </div>
            @endforelse
        </div>

        {{-- Operating Hours & Settings --}}
        <div class="detail-section">
            <h3 class="detail-section-title">
                <i class="fas fa-cog"></i> Settings
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">Operating Hours</div>
                    <div style="font-size: 16px; font-weight: 600;">
                        @if($station->operating_hours)
                            {{ $station->operating_hours['start'] ?? 'N/A' }} - {{ $station->operating_hours['end'] ?? 'N/A' }}
                        @else
                            Not set
                        @endif
                    </div>
                </div>
                <div>
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">Max Capacity</div>
                    <div style="font-size: 16px; font-weight: 600;">{{ $station->max_capacity }} orders</div>
                </div>
            </div>
            <div style="margin-top: 24px;">
                <a href="{{ route('admin.kitchen.stations.edit', $station->id) }}" class="edit-station-button">
                    <i class="fas fa-edit"></i> Edit Station
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
