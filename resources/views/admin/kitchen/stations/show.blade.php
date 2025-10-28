@extends('layouts.admin')

@section('title', 'Station Details - ' . $station->name)
@section('page-title', 'Station Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
/* Hero Header */
.station-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 48px;
    margin-bottom: 32px;
    color: white;
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.station-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(80px);
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 24px;
}

.hero-left {
    display: flex;
    align-items: center;
    gap: 24px;
}

.station-icon-large {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 56px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.hero-info h1 {
    font-size: 36px;
    font-weight: 800;
    margin: 0 0 12px 0;
    color: white;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.hero-type {
    font-size: 18px;
    opacity: 0.95;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
}

.hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 15px;
}

.hero-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    color: white;
}

.hero-btn.primary {
    background: white;
    color: #667eea;
    border-color: white;
}

.hero-btn.primary:hover {
    background: #f8f9ff;
    color: #5568d3;
}

.status-badge-large {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 24px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.status-badge-large::before {
    content: "●";
    font-size: 18px;
}

.status-badge-large.active::before {
    color: #10b981;
    text-shadow: 0 0 10px #10b981;
}

.status-badge-large.inactive::before {
    color: #ef4444;
    text-shadow: 0 0 10px #ef4444;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-icon.purple { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 4px;
}

.stat-description {
    font-size: 13px;
    color: #94a3b8;
}

/* Content Sections */
.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.section-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.section-header {
    padding: 24px 28px;
    border-bottom: 2px solid #f1f5f9;
    background: linear-gradient(to bottom, #ffffff, #f8fafc);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #667eea;
    font-size: 20px;
}

.section-body {
    padding: 24px 28px;
    max-height: 500px;
    overflow-y: auto;
}

.section-body::-webkit-scrollbar {
    width: 6px;
}

.section-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.section-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.section-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Order Item Card */
.order-card {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.order-card:last-child {
    margin-bottom: 0;
}

.order-card:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.order-id {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

.order-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.order-badge.warning {
    background: #fef3c7;
    color: #92400e;
}

.order-badge.info {
    background: #dbeafe;
    color: #1e40af;
}

.order-badge.secondary {
    background: #f1f5f9;
    color: #475569;
}

.order-time {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.order-items {
    font-size: 14px;
    color: #64748b;
    line-height: 1.8;
}

.order-item-line {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
}

.order-item-line::before {
    content: "•";
    color: #667eea;
    font-weight: bold;
}

/* Activity Log */
.log-item {
    padding: 16px 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
}

.log-item:last-child {
    border-bottom: none;
}

.log-item:hover {
    background: #f8fafc;
    margin: 0 -28px;
    padding: 16px 28px;
}

.log-timestamp {
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.log-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.log-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.log-badge.danger {
    background: #fee2e2;
    color: #991b1b;
}

.log-badge.info {
    background: #dbeafe;
    color: #1e40af;
}

.log-message {
    font-size: 14px;
    color: #475569;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 56px;
    color: #e2e8f0;
    margin-bottom: 16px;
    display: inline-block;
}

.empty-text {
    font-size: 15px;
    color: #94a3b8;
    font-weight: 500;
}

/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}

.setting-item {
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.setting-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.setting-value {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
}

/* Full Width Section */
.full-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

/* Load Bar */
.load-bar-wrapper {
    margin-top: 12px;
}

.load-bar-container {
    height: 12px;
    background: #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.load-bar {
    height: 100%;
    border-radius: 12px;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.load-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.load-bar.success {
    background: linear-gradient(90deg, #10b981, #059669);
}

.load-bar.warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.load-bar.danger {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .hero-top {
        flex-direction: column;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')

<div class="kitchen-page" style="max-width: 1400px; margin: 0 auto;">
    {{-- Hero Header --}}
    <div class="station-hero">
        <div class="hero-content">
            <div class="hero-top">
                <div class="hero-left">
                    <div class="station-icon-large">
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
                    </div>
                    <div class="hero-info">
                        <h1>{{ $station->name }}</h1>
                        <div class="hero-type">
                            <span>{{ ucfirst(str_replace('_', ' ', $station->station_type)) }}</span>
                            <span class="status-badge-large {{ $station->is_active ? 'active' : 'inactive' }}">
                                {{ $station->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="hero-actions">
                    <a href="{{ route('admin.kitchen.index') }}" class="hero-btn">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.kitchen.stations.index') }}" class="hero-btn">
                        <i class="fas fa-list"></i> All Stations
                    </a>
                    <a href="{{ route('admin.kitchen.stations.edit', $station->id) }}" class="hero-btn primary">
                        <i class="fas fa-edit"></i> Edit Station
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Current Load</span>
                <div class="stat-icon purple">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
            <div class="stat-value">{{ $station->current_load }} / {{ $station->max_capacity }}</div>
            <div class="stat-description">Orders in queue</div>
            <div class="load-bar-wrapper">
                <div class="load-bar-container">
                    <div class="load-bar {{ $station->isOverloaded() ? 'danger' : ($station->isApproachingCapacity() ? 'warning' : 'success') }}"
                         style="width: {{ $station->load_percentage }}%;"></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Load Percentage</span>
                <div class="stat-icon {{ $station->isOverloaded() ? 'orange' : 'green' }}">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
            <div class="stat-value" style="color: {{ $station->isOverloaded() ? '#ef4444' : ($station->isApproachingCapacity() ? '#f59e0b' : '#10b981') }}">
                {{ number_format($station->load_percentage, 1) }}%
            </div>
            <div class="stat-description">
                @if($station->isOverloaded())
                    ⚠️ Station overloaded
                @elseif($station->isApproachingCapacity())
                    ⏰ Approaching capacity
                @else
                    ✅ Operating normally
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Pending Orders</span>
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value">{{ $station->pendingAssignments()->count() }}</div>
            <div class="stat-description">Awaiting preparation</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Avg Time</span>
                <div class="stat-icon blue">
                    <i class="fas fa-stopwatch"></i>
                </div>
            </div>
            <div class="stat-value">{{ $station->getAverageCompletionTime() }}<span style="font-size: 20px; color: #94a3b8;"> min</span></div>
            <div class="stat-description">Completion time</div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid">
        {{-- Active Orders --}}
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-fire"></i> Active Orders
                </h3>
            </div>
            <div class="section-body">
                @forelse($station->activeLoads as $load)
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <span class="order-id">Order #{{ $load->order->id }}</span>
                                <span class="order-badge {{ $load->status == 'in_progress' ? 'warning' : 'info' }}">
                                    {{ ucfirst($load->status) }}
                                </span>
                            </div>
                            <div class="order-time">
                                <i class="fas fa-clock"></i>
                                {{ $load->created_at->diffForHumans() }}
                            </div>
                        </div>
                        @if($load->order && $load->order->items)
                            <div class="order-items">
                                @foreach($load->order->items as $item)
                                    <div class="order-item-line">
                                        <strong>{{ $item->quantity }}x</strong> {{ $item->menuItem->name ?? 'Unknown Item' }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <div class="empty-text">No active orders at this station</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Assignments --}}
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-hourglass-half"></i> Pending Assignments
                </h3>
            </div>
            <div class="section-body">
                @forelse($station->pendingAssignments as $assignment)
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <span class="order-id">Order #{{ $assignment->order->id }}</span>
                                <span class="order-badge secondary">{{ ucfirst($assignment->status) }}</span>
                            </div>
                            <div class="order-time">
                                <i class="fas fa-clock"></i>
                                {{ $assignment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="empty-text">No pending assignments</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="full-section" style="margin-bottom: 24px;">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-history"></i> Recent Activity (Last 20)
            </h3>
        </div>
        <div class="section-body">
            @forelse($station->logs as $log)
                <div class="log-item">
                    <div class="log-timestamp">
                        <i class="fas fa-clock"></i>
                        {{ $log->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div class="log-content">
                        <span class="log-badge {{ $log->action_type == 'overload_alert' ? 'danger' : 'info' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                        </span>
                        <span class="log-message">{{ $log->reason ?? 'No details available' }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-history"></i></div>
                    <div class="empty-text">No recent activity logged</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Settings --}}
    <div class="full-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-cog"></i> Station Settings
            </h3>
        </div>
        <div class="section-body">
            <div class="settings-grid">
                <div class="setting-item">
                    <div class="setting-label">
                        <i class="fas fa-clock"></i> Operating Hours
                    </div>
                    <div class="setting-value">
                        @if($station->operating_hours)
                            {{ $station->operating_hours['start'] ?? 'N/A' }} - {{ $station->operating_hours['end'] ?? 'N/A' }}
                        @else
                            <span style="color: #94a3b8;">Not set</span>
                        @endif
                    </div>
                </div>
                <div class="setting-item">
                    <div class="setting-label">
                        <i class="fas fa-layer-group"></i> Max Capacity
                    </div>
                    <div class="setting-value">{{ $station->max_capacity }} orders</div>
                </div>
                <div class="setting-item">
                    <div class="setting-label">
                        <i class="fas fa-info-circle"></i> Status
                    </div>
                    <div class="setting-value" style="color: {{ $station->is_active ? '#10b981' : '#ef4444' }}">
                        {{ $station->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
