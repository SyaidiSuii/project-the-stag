@extends('layouts.admin')

@section('title', 'Kitchen Analytics')
@section('page-title', 'Kitchen Performance Analytics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* Analytics Grid Layouts */
.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.analytics-two-column {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

@media (max-width: 968px) {
    .analytics-two-column {
        grid-template-columns: 1fr;
    }
}

/* Metric Cards */
.metric-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.metric-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.metric-label {
    font-size: 13px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 8px;
}

.metric-value {
    font-size: 36px;
    font-weight: 800;
    color: #1e293b;
    margin: 8px 0;
    line-height: 1;
}

.metric-unit {
    font-size: 16px;
    color: #64748b;
    font-weight: 500;
}

.metric-change {
    font-size: 13px;
    margin-top: 8px;
    font-weight: 500;
}

.metric-change.positive {
    color: #10b981;
}

.metric-change.negative {
    color: #ef4444;
}

/* Date Filter */
.date-filter {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.date-filter input[type="date"] {
    padding: 10px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
}

.date-filter input[type="date"]:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Leaderboard */
.leaderboard-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 16px;
    transition: all 0.2s;
}

.leaderboard-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateX(4px);
}

.leaderboard-rank {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 20px;
    margin-right: 20px;
    flex-shrink: 0;
}

.leaderboard-rank.gold {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
}

.leaderboard-rank.silver {
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    box-shadow: 0 4px 12px rgba(148, 163, 184, 0.4);
}

.leaderboard-rank.bronze {
    background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
    box-shadow: 0 4px 12px rgba(251, 146, 60, 0.4);
}

.leaderboard-content {
    flex: 1;
}

.leaderboard-stats {
    display: flex;
    gap: 20px;
    margin-top: 8px;
    font-size: 13px;
    color: #64748b;
    flex-wrap: wrap;
}

.leaderboard-stats span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.leaderboard-progress {
    text-align: right;
    min-width: 140px;
}

.progress-bar-wrapper {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 6px;
}

.progress-bar-fill {
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 4px;
}

/* Top Performer Card */
.top-performer-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    padding: 32px 24px;
}

.top-performer-icon {
    font-size: 56px;
    margin-bottom: 16px;
}

.top-performer-title {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
}

.top-performer-score {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.top-performer-score-value {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 4px;
}

.top-performer-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 20px;
}

.top-performer-stat {
    background: rgba(255, 255, 255, 0.15);
    padding: 12px;
    border-radius: 8px;
}

.top-performer-stat-value {
    font-size: 24px;
    font-weight: 700;
}

.top-performer-stat-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-top: 4px;
}

/* Chart Container */
.chart-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    position: relative;
    min-height: 350px;
}

.chart-canvas {
    height: 300px;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 20px;
    background: #f8fafc;
    border-radius: 8px;
    border: 2px dashed #e2e8f0;
    min-height: 280px;
}

.empty-state i {
    font-size: 56px;
    color: #cbd5e1;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state p {
    color: #94a3b8;
    font-size: 15px;
    margin: 0;
}

/* ===== RESPONSIVE DESIGN - Analytics Page ===== */

/* Tablet View (769px - 1199px) */
@media (max-width: 1199px) and (min-width: 769px) {
    .analytics-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .metric-card {
        padding: 18px;
    }

    .metric-label {
        font-size: 12px;
    }

    .metric-value {
        font-size: 28px;
    }

    .date-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .date-filter input[type="date"],
    .date-filter button,
    .date-filter a {
        width: 100%;
    }

    .leaderboard-rank {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .chart-container {
        padding: 18px;
        min-height: 300px;
    }

    .chart-canvas {
        height: 250px;
    }
}

/* Mobile View (≤768px) */
@media (max-width: 768px) {
    /* Grid Layouts - Single Column */
    .analytics-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }

    .analytics-two-column {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    /* Metric Cards */
    .metric-card {
        padding: 16px;
    }

    .metric-label {
        font-size: 11px;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 24px;
        margin: 6px 0;
    }

    .metric-unit {
        font-size: 14px;
    }

    .metric-change {
        font-size: 12px;
        margin-top: 6px;
    }

    /* Date Filter */
    .date-filter {
        flex-direction: column;
        gap: 8px;
    }

    .date-filter input[type="date"] {
        width: 100%;
        padding: 8px 12px;
        font-size: 13px;
    }

    .date-filter button,
    .date-filter a {
        width: 100%;
        padding: 8px 16px !important;
        font-size: 13px !important;
        justify-content: center;
    }

    .date-filter form {
        width: 100%;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .date-filter span {
        display: none;
    }

    /* Leaderboard */
    .leaderboard-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 16px;
    }

    .leaderboard-rank {
        width: 36px;
        height: 36px;
        font-size: 16px;
        margin-right: 0;
        margin-bottom: 8px;
    }

    .leaderboard-stats {
        flex-direction: column;
        gap: 8px;
        margin-top: 6px;
        font-size: 12px;
    }

    .leaderboard-progress {
        width: 100%;
        min-width: auto;
        text-align: left;
        margin-top: 8px;
    }

    .leaderboard-item > div:last-child {
        width: 100%;
        text-align: left !important;
    }

    .leaderboard-item > div:last-child > div {
        width: 100% !important;
    }

    .leaderboard-item > div:last-child small {
        font-size: 11px;
    }

    /* Top Performer Card */
    .top-performer-card {
        padding: 24px 18px;
    }

    .top-performer-icon {
        font-size: 40px;
        margin-bottom: 12px;
    }

    .top-performer-title {
        font-size: 18px;
    }

    .top-performer-score-value {
        font-size: 36px;
    }

    .top-performer-stat-value {
        font-size: 20px;
    }

    .top-performer-stat-label {
        font-size: 10px;
    }

    /* Charts */
    .chart-container {
        padding: 16px;
        min-height: 250px;
    }

    .chart-canvas {
        height: 220px;
    }

    .chart-container h4 {
        font-size: 13px !important;
        margin-bottom: 16px !important;
    }

    .chart-container > div[style*="grid"] {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }

    /* Empty State */
    .empty-state {
        padding: 50px 16px;
        min-height: 200px;
    }

    .empty-state i {
        font-size: 40px;
    }

    .empty-state p {
        font-size: 13px;
    }

    /* Recommendation Cards */
    .metric-card h4 {
        font-size: 14px;
        margin-bottom: 10px !important;
    }

    .metric-card ul {
        font-size: 12px;
        line-height: 1.6 !important;
    }

    /* Responsive table for bottleneck events */
    .admin-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .admin-table {
        min-width: 700px;
    }

    /* Leaderboard responsive adjustments */
    .row {
        margin: 0;
    }

    .col-md-8,
    .col-md-4 {
        width: 100%;
        padding: 0;
        margin-bottom: 16px;
    }

    /* Top performer in mobile */
    .col-md-4 .metric-card {
        margin-top: 0;
    }

    .col-md-4 .metric-card > div {
        padding: 16px;
    }

    .col-md-4 .metric-card h3 {
        font-size: 18px !important;
    }

    .col-md-4 .metric-card > div > div:nth-child(3) {
        font-size: 28px !important;
    }

    .col-md-4 .metric-card > div > div:last-child {
        gap: 8px !important;
    }

    .col-md-4 .metric-card > div > div:last-child > div {
        padding: 10px !important;
    }

    .col-md-4 .metric-card > div > div:last-child > div > div:first-child {
        font-size: 18px !important;
    }
}

/* Small Mobile (≤480px) */
@media (max-width: 480px) {
    .metric-card {
        padding: 14px;
    }

    .metric-value {
        font-size: 22px;
    }

    .leaderboard-item {
        padding: 14px;
    }

    .chart-container {
        padding: 14px;
    }

    .chart-canvas {
        height: 200px;
    }
}

/* Large Desktop (≥1600px) */
@media (min-width: 1600px) {
    .analytics-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 28px;
    }

    .metric-card {
        padding: 32px;
    }

    .metric-label {
        font-size: 15px;
    }

    .metric-value {
        font-size: 44px;
    }

    .metric-unit {
        font-size: 18px;
    }

    .chart-container {
        padding: 32px;
        min-height: 400px;
    }

    .chart-canvas {
        height: 350px;
    }

    .leaderboard-rank {
        width: 56px;
        height: 56px;
        font-size: 24px;
    }

    .performance-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Header with Date Filter --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Kitchen Performance Analytics</h2>
                    <p class="text-muted">Comprehensive insights into kitchen operations</p>
                </div>
                <div class="section-controls">
                    <div class="date-filter">
                        <form method="GET" action="{{ route('admin.kitchen.analytics') }}" style="display: flex; gap: 12px; align-items: center;">
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ request('start_date', now()->subDays(7)->format('Y-m-d')) }}"
                                   style="width: auto;">
                            <span>to</span>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                   style="width: auto;">
                            <button type="submit" class="admin-btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </form>
                        <a href="{{ route('admin.kitchen.index') }}" class="admin-btn btn-secondary">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Metrics --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="analytics-grid">
                <div class="metric-card">
                    <div class="metric-label">
                        <i class="fas fa-receipt"></i> Total Orders
                    </div>
                    <div class="metric-value">{{ $analytics['summary']['total_orders'] ?? 0 }}</div>
                    <div class="metric-change positive">
                        <i class="fas fa-arrow-up"></i> +12% from last period
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-label">
                        <i class="fas fa-clock"></i> Avg Completion
                    </div>
                    <div class="metric-value">
                        {{ $analytics['summary']['avg_completion_time'] ?? 0 }}<span class="metric-unit">min</span>
                    </div>
                    <div class="metric-change positive">
                        <i class="fas fa-arrow-down"></i> 8% faster
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-label">
                        <i class="fas fa-check-circle"></i> On-Time Rate
                    </div>
                    <div class="metric-value">
                        {{ $analytics['summary']['on_time_percentage'] ?? 100 }}<span class="metric-unit">%</span>
                    </div>
                    <div class="metric-change {{ ($analytics['summary']['on_time_percentage'] ?? 100) >= 90 ? 'positive' : 'negative' }}">
                        <i class="fas fa-{{ ($analytics['summary']['on_time_percentage'] ?? 100) >= 90 ? 'check' : 'exclamation-triangle' }}"></i>
                        {{ ($analytics['summary']['on_time_percentage'] ?? 100) >= 90 ? 'Excellent' : 'Needs Improvement' }}
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-label">
                        <i class="fas fa-exclamation-triangle"></i> Overload Alerts
                    </div>
                    <div class="metric-value">{{ $analytics['summary']['overload_alerts'] ?? 0 }}</div>
                    <div class="metric-change {{ ($analytics['summary']['overload_alerts'] ?? 0) > 5 ? 'negative' : 'positive' }}">
                        <i class="fas fa-{{ ($analytics['summary']['overload_alerts'] ?? 0) > 5 ? 'exclamation-circle' : 'check-circle' }}"></i>
                        {{ ($analytics['summary']['overload_alerts'] ?? 0) > 5 ? 'High frequency' : 'Under control' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Station Performance Leaderboard --}}
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-trophy" style="color: #fbbf24;"></i> Station Performance Leaderboard
        </h2>
    </div>

    <div class="row">
        <div class="col-md-8">
            @php
                $stationPerformance = collect($analytics['station_performance'] ?? [])->sortByDesc('efficiency_score')->values()->all();
            @endphp

            @forelse($stationPerformance as $index => $performance)
            <div class="leaderboard-item">
                <div class="leaderboard-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                    {{ $index + 1 }}
                </div>
                <div style="flex: 1;">
                    <strong style="font-size: 16px;">{{ $performance['station_name'] }}</strong>
                    <div style="margin-top: 4px; display: flex; gap: 16px; font-size: 13px; color: #64748b;">
                        <span><i class="fas fa-receipt"></i> {{ $performance['orders_completed'] ?? 0 }} orders</span>
                        <span><i class="fas fa-clock"></i> {{ $performance['avg_completion_time'] ?? 0 }} min avg</span>
                        <span><i class="fas fa-chart-line"></i> {{ $performance['efficiency_score'] ?? 0 }}% efficiency</span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="width: 120px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; margin-bottom: 4px;">
                        <div style="height: 100%; width: {{ $performance['efficiency_score'] ?? 0 }}%; background: {{ $index == 0 ? '#fbbf24' : ($index == 1 ? '#94a3b8' : '#667eea') }};"></div>
                    </div>
                    <small style="color: #64748b;">{{ $performance['efficiency_score'] ?? 0 }}% efficiency</small>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px;">
                <i class="fas fa-chart-bar" style="font-size: 48px; color: #cbd5e1; margin-bottom: 12px;"></i>
                <p style="color: #94a3b8;">No performance data available for selected period</p>
            </div>
            @endforelse
        </div>

        <div class="col-md-4">
            <div class="metric-card">
                <h4 style="margin: 0 0 16px 0; font-size: 16px; color: #64748b;">
                    <i class="fas fa-star"></i> Top Performer
                </h4>
                @if(isset($stationPerformance[0]))
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 12px;">&#x1F3C6;</div>
                    <h3 style="margin: 0; font-size: 20px; color: #1e293b;">{{ $stationPerformance[0]['station_name'] ?? 'N/A' }}</h3>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div style="font-size: 32px; font-weight: 700; color: #fbbf24;">{{ $stationPerformance[0]['efficiency_score'] ?? 0 }}%</div>
                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">EFFICIENCY SCORE</div>
                    </div>
                    <div style="margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; text-align: center;">
                        <div>
                            <div style="font-size: 20px; font-weight: 600; color: #6366f1;">{{ $stationPerformance[0]['orders_completed'] ?? 0 }}</div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Orders</div>
                        </div>
                        <div>
                            <div style="font-size: 20px; font-weight: 600; color: #10b981;">{{ $stationPerformance[0]['avg_completion_time'] ?? 0 }}m</div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Avg Time</div>
                        </div>
                    </div>
                </div>
                @else
                <p style="text-align: center; color: #94a3b8; padding: 40px 0;">No data yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

    {{-- Hourly Distribution Chart --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header" style="margin-bottom: 24px;">
                <h2 class="section-title">
                    <i class="fas fa-chart-line"></i> Hourly Order Distribution
                </h2>
            </div>

            <div class="chart-container">
                <canvas id="hourlyDistributionChart" class="chart-canvas"></canvas>
                <div id="hourlyDistributionEmpty" class="empty-state" style="display: none;">
                    <i class="fas fa-chart-line"></i>
                    <p>No hourly data available for the selected period</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Station Performance Charts --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header" style="margin-bottom: 24px;">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i> Station Performance Comparison
                </h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px;">
                <div class="chart-container">
                    <h4 style="margin: 0 0 20px 0; font-size: 15px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-receipt"></i> Orders Completed by Station
                    </h4>
                    <canvas id="stationOrdersChart" style="height: 280px;"></canvas>
                    <div id="stationOrdersEmpty" class="empty-state" style="display: none;">
                        <i class="fas fa-receipt"></i>
                        <p>No station order data available</p>
                    </div>
                </div>
                <div class="chart-container">
                    <h4 style="margin: 0 0 20px 0; font-size: 15px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-clock"></i> Average Completion Time
                    </h4>
                    <canvas id="stationTimeChart" style="height: 280px;"></canvas>
                    <div id="stationTimeEmpty" class="empty-state" style="display: none;">
                        <i class="fas fa-clock"></i>
                        <p>No completion time data available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Bottleneck Events Log --}}
<div class="kitchen-section">
    <div class="admin-section" style="margin-bottom: 0;">
        <div class="section-header" style="margin-bottom: 24px;">
            <h2 class="section-title">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Recent Bottleneck Events
            </h2>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Station</th>
                        <th>Event Type</th>
                        <th>Load %</th>
                        <th>Duration</th>
                        <th>Impact</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bottlenecks = $analytics['bottleneck_events'] ?? [];
                    @endphp
                    @forelse($bottlenecks as $event)
                    <tr>
                        <td>{{ $event['timestamp'] ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $event['station_name'] ?? 'Unknown' }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-danger">Overload Alert</span>
                        </td>
                        <td>
                            <span style="color: #ef4444; font-weight: 600;">{{ $event['load_percentage'] ?? 0 }}%</span>
                        </td>
                        <td>{{ $event['duration'] ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ ($event['load_percentage'] ?? 0) > 100 ? 'danger' : 'warning' }}">
                                {{ ($event['load_percentage'] ?? 0) > 100 ? 'Critical' : 'Moderate' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <i class="fas fa-check-circle" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px; color: #10b981;"></i>
                                <p>No bottleneck events in selected period. Kitchen running smoothly! &#x1F389;</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recommendations --}}
<div class="kitchen-section">
    <div class="admin-section" style="margin-bottom: 0;">
        <div class="section-header" style="margin-bottom: 24px;">
            <h2 class="section-title">
                <i class="fas fa-lightbulb" style="color: #fbbf24;"></i> Smart Recommendations
            </h2>
            <small style="color: #94a3b8; font-weight: normal;">Data-driven insights based on your kitchen performance</small>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div class="metric-card" style="border-left: 4px solid #10b981;">
                <h4 style="color: #10b981; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> Strengths
                </h4>
                <ul style="padding-left: 20px; margin: 0; color: #64748b; line-height: 1.8;">
                    @forelse($recommendations['strengths'] ?? [] as $strength)
                        <li>{{ $strength }}</li>
                    @empty
                        <li>No data available for analysis</li>
                    @endforelse
                </ul>
            </div>
            <div class="metric-card" style="border-left: 4px solid #f59e0b;">
                <h4 style="color: #f59e0b; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-exclamation-circle"></i> Areas to Improve
                </h4>
                <ul style="padding-left: 20px; margin: 0; color: #64748b; line-height: 1.8;">
                    @forelse($recommendations['improvements'] ?? [] as $improvement)
                        <li>{{ $improvement }}</li>
                    @empty
                        <li>Operations running smoothly</li>
                    @endforelse
                </ul>
            </div>
            <div class="metric-card" style="border-left: 4px solid #6366f1;">
                <h4 style="color: #6366f1; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-lightbulb"></i> Suggestions
                </h4>
                <ul style="padding-left: 20px; margin: 0; color: #64748b; line-height: 1.8;">
                    @forelse($recommendations['suggestions'] ?? [] as $suggestion)
                        <li>{{ $suggestion }}</li>
                    @empty
                        <li>Continue current operations</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get station performance data from blade
    const stationPerformance = Array.isArray(@json($analytics['station_performance'] ?? [])) ? @json($analytics['station_performance'] ?? []) : [];
    const hourlyData = Array.isArray(@json($analytics['hourly_distribution'] ?? [])) ? @json($analytics['hourly_distribution'] ?? []) : [];

    // Chart colors
    const brandColors = ['#667eea', '#764ba2', '#f59e0b', '#10b981', '#ef4444', '#3b82f6'];

    // 1. Hourly Distribution Line Chart
    if (document.getElementById('hourlyDistributionChart')) {
        const hours = Array.from({length: 24}, (_, i) => `${i}:00`);
        const hourlyValues = hours.map(hour => {
            const found = hourlyData.find(h => h.hour === hour);
            return found ? found.count : 0;
        });

        const hasData = hourlyValues.some(val => val > 0);

        if (!hasData) {
            document.getElementById('hourlyDistributionChart').style.display = 'none';
            document.getElementById('hourlyDistributionEmpty').style.display = 'flex';
        } else {
            new Chart(document.getElementById('hourlyDistributionChart'), {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Orders',
                    data: hourlyValues,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' orders';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
        }
    }

    // 2. Station Orders Bar Chart
    if (document.getElementById('stationOrdersChart')) {
        const hasOrderData = stationPerformance.length > 0 && stationPerformance.some(s => s.orders_completed > 0);

        if (!hasOrderData) {
            document.getElementById('stationOrdersChart').style.display = 'none';
            document.getElementById('stationOrdersEmpty').style.display = 'flex';
        } else {
            new Chart(document.getElementById('stationOrdersChart'), {
            type: 'bar',
            data: {
                labels: stationPerformance.map(s => s.station_name),
                datasets: [{
                    label: 'Orders Completed',
                    data: stationPerformance.map(s => s.orders_completed),
                    backgroundColor: brandColors,
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        }
    }

    // 3. Station Time Horizontal Bar Chart
    if (document.getElementById('stationTimeChart')) {
        const hasTimeData = stationPerformance.length > 0 && stationPerformance.some(s => s.avg_completion_time > 0);

        if (!hasTimeData) {
            document.getElementById('stationTimeChart').style.display = 'none';
            document.getElementById('stationTimeEmpty').style.display = 'flex';
        } else {
        new Chart(document.getElementById('stationTimeChart'), {
            type: 'bar',
            data: {
                labels: stationPerformance.map(s => s.station_name),
                datasets: [{
                    label: 'Avg Time (minutes)',
                    data: stationPerformance.map(s => s.avg_completion_time || 0),
                    backgroundColor: brandColors.slice().reverse(),
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' minutes';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        }
    }

    console.log('✅ Chart.js initialized with analytics data');
});
</script>

</div>
    </div>
    </div>
</div>
{{-- End kitchen-page --}}

@endsection
