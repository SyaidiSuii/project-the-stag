@extends('layouts.admin')

@section('title', 'Kitchen Management Dashboard')
@section('page-title', 'Kitchen Management Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Summary Stats Cards --}}
    <div class="kitchen-section">
        <div class="admin-cards" style="margin-bottom: 0;">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Active Orders</div>
                    <div class="admin-card-icon icon-blue"><i class="fas fa-utensils"></i></div>
                </div>
                <div class="admin-card-value">{{ $todayStats['active_orders'] ?? 0 }}</div>
                <div class="admin-card-desc">Currently being prepared</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Completed Today</div>
                    <div class="admin-card-icon icon-green"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="admin-card-value">{{ $todayStats['total_orders_completed'] ?? 0 }}</div>
                <div class="admin-card-desc">Orders finished today</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Avg Completion</div>
                    <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
                </div>
                <div class="admin-card-value">{{ $todayStats['avg_completion_time'] ?? 0 }} min</div>
                <div class="admin-card-desc">Average time per order</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Overload Alerts</div>
                    <div class="admin-card-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <div class="admin-card-value">{{ $todayStats['overload_alerts'] ?? 0 }}</div>
                <div class="admin-card-desc">Capacity warnings today</div>
            </div>
        </div>
    </div>

    {{-- Station Status Section --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 class="section-title" style="margin: 0;">Kitchen Stations Status</h2>
        <div class="section-controls">
            <a href="{{ route('admin.kitchen.stations.index') }}" class="admin-btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                <i class="fas fa-cog"></i> Manage Stations
            </a>
        </div>
    </div>

    <div class="stations-grid">
        @forelse($stations as $station)
        <div class="station-card {{ $station->isOverloaded() ? 'overloaded' : '' }} {{ $station->isApproachingCapacity() ? 'approaching' : '' }}"
             data-station-id="{{ $station->id }}">

            <div class="station-header">
                <div class="station-icon-wrapper">
                    <span class="station-icon">
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
                </div>
                <div class="station-info">
                    <h3 class="station-name">{{ $station->name }}</h3>
                    <p class="station-type">{{ ucfirst(str_replace('_', ' ', $station->station_type)) }}</p>
                </div>
                @if($station->isOverloaded())
                    <span class="station-badge badge-danger">Overloaded</span>
                @elseif($station->isApproachingCapacity())
                    <span class="station-badge badge-warning">Busy</span>
                @else
                    <span class="station-badge badge-success">Normal</span>
                @endif
            </div>

            <div class="station-progress">
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $station->load_percentage }}%"></div>
                </div>
                <div class="progress-label">
                    <span class="load-text">{{ $station->current_load }} / {{ $station->max_capacity }}</span>
                    <span class="load-percentage">{{ number_format($station->load_percentage, 0) }}%</span>
                </div>
            </div>

            <div class="station-stats">
                <div class="stat-item">
                    <i class="fas fa-list"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $station->pendingAssignments()->count() }}</span>
                        <span class="stat-label">Pending</span>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $station->getAverageCompletionTime() }} min</span>
                        <span class="stat-label">Avg Time</span>
                    </div>
                </div>
            </div>

            <div class="station-actions">
                <a href="{{ route('admin.kitchen.orders', ['station_id' => $station->id]) }}" class="btn-view">
                    <i class="fas fa-eye"></i> View Orders
                </a>
                <a href="{{ route('admin.kitchen.stations.detail', $station->id) }}" class="btn-detail">
                    <i class="fas fa-info-circle"></i> Details
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-store-slash"></i>
            <h3>No Kitchen Stations</h3>
            <p>Please create kitchen stations to start load balancing</p>
            <a href="{{ route('admin.kitchen.stations') }}" class="admin-btn btn-primary">Add Station</a>
        </div>
        @endforelse
    </div>
</div>

{{-- Bottlenecks Alert Section --}}
@if($bottlenecks->isNotEmpty())
<div class="admin-section alert-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-exclamation-triangle text-danger"></i> Active Bottlenecks
        </h2>
    </div>

    <div class="bottleneck-list">
        @foreach($bottlenecks as $bottleneck)
        <div class="bottleneck-card">
            <div class="bottleneck-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="bottleneck-content">
                <h4>{{ $bottleneck['station']->name }}</h4>
                <p>{{ $bottleneck['load_percentage'] }}% capacity ({{ $bottleneck['current_load'] }}/{{ $bottleneck['max_capacity'] }} orders)</p>
                <p class="suggestion">💡 {{ $bottleneck['suggested_action'] }}</p>
            </div>
            <div class="bottleneck-actions">
                <a href="{{ route('admin.kitchen.orders', ['station_id' => $bottleneck['station']->id]) }}" class="admin-btn btn-sm btn-secondary">
                    View Orders
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Alerts Section --}}
<div class="admin-section" style="margin-top: 32px;">
    <div class="section-header">
        <h2 class="section-title">Recent Alerts (Today)</h2>
    </div>

    <div class="alerts-timeline">
        @forelse($recentAlerts as $alert)
        <div class="alert-item">
            <div class="alert-time">
                <i class="fas fa-clock"></i>
                {{ $alert->created_at->format('h:i A') }}
            </div>
            <div class="alert-content">
                <span class="alert-station">{{ $alert->station->name ?? 'Unknown' }}</span>
                <p class="alert-message">{{ $alert->reason }}</p>
                @if($alert->metadata)
                <div class="alert-metadata">
                    Load: {{ $alert->metadata['current_load'] ?? 'N/A' }}/{{ $alert->metadata['max_capacity'] ?? 'N/A' }}
                    ({{ $alert->metadata['load_percentage'] ?? 0 }}%)
                </div>
                @endif
            </div>
            <div class="alert-badge">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
        @empty
        <div class="empty-state-small">
            <i class="fas fa-check-circle"></i>
            <p>No alerts today - kitchen running smoothly!</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Quick Stats Performance --}}
@if(isset($todayStats['stations_performance']) && count($todayStats['stations_performance']) > 0)
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Today's Performance by Station</h2>
        <a href="{{ route('admin.kitchen.analytics') }}" class="admin-btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Full Analytics
        </a>
    </div>

    <div class="performance-grid">
        @foreach($todayStats['stations_performance'] as $performance)
        <div class="performance-card">
            <h4>{{ $performance['station_name'] }}</h4>
            <div class="performance-stats">
                <div class="perf-stat">
                    <span class="perf-value">{{ $performance['orders_completed'] }}</span>
                    <span class="perf-label">Orders</span>
                </div>
                <div class="perf-stat">
                    <span class="perf-value">{{ $performance['avg_time'] }} min</span>
                    <span class="perf-label">Avg Time</span>
                </div>
                <div class="perf-stat">
                    <span class="perf-value">{{ $performance['efficiency'] }}%</span>
                    <span class="perf-label">Efficiency</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    </div>
@endif

</div>
{{-- End kitchen-page --}}

@endsection

@section('scripts')
<script src="{{ asset('js/admin/kitchen-dashboard.js') }}"></script>
@endsection
