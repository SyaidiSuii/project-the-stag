@extends('layouts.admin')

@section('title', 'Promotion Analytics - ' . $promotion->name)
@section('page-title', 'Promotion Analytics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
.analytics-header {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.analytics-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 8px;
}

.analytics-header .subtitle {
    color: var(--text-3);
    font-size: 14px;
}

.date-filter {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 24px;
    background: white;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.date-filter label {
    font-weight: 600;
    color: var(--text-2);
}

.date-filter input {
    padding: 8px 12px;
    border: 1px solid var(--muted);
    border-radius: 8px;
    font-size: 14px;
}

.date-filter button {
    padding: 8px 16px;
    background: var(--brand);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.date-filter button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.stat-card-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-2);
}

.stat-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.stat-card-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}

.stat-card-desc {
    font-size: 12px;
    color: var(--text-3);
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
}

.chart-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 16px;
}

.table-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
}

.table-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 16px;
}

.peak-hours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 12px;
    margin-top: 16px;
}

.hour-badge {
    padding: 12px 8px;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
}

.hour-badge.high {
    background: #fee2e2;
    color: #dc2626;
}

.hour-badge.medium {
    background: #fef3c7;
    color: #d97706;
}

.hour-badge.low {
    background: #e5e7eb;
    color: #6b7280;
}

.trend-chart {
    width: 100%;
    height: 300px;
    border: 1px solid var(--muted);
    border-radius: 8px;
    padding: 16px;
    display: flex;
    align-items: flex-end;
    gap: 4px;
}

.trend-bar {
    flex: 1;
    background: var(--brand);
    border-radius: 4px 4px 0 0;
    min-height: 10px;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}

.trend-bar:hover {
    opacity: 0.8;
}

.trend-bar-label {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    color: var(--text-3);
    white-space: nowrap;
}

/* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */

/* Large Desktop (≥1600px) */
@media (min-width: 1600px) {
    .analytics-header {
        padding: 32px;
    }
    .analytics-header h1 {
        font-size: 32px;
    }
    .stats-grid {
        gap: 28px;
    }
    .stat-card {
        padding: 28px;
    }
    .stat-card-value {
        font-size: 36px;
    }
    .chart-card,
    .table-card {
        padding: 32px;
    }
}

/* Tablet (769px-1199px) */
@media (max-width: 1199px) and (min-width: 769px) {
    .analytics-header {
        padding: 20px;
    }
    .analytics-header h1 {
        font-size: 20px;
    }
    .analytics-header .subtitle {
        font-size: 13px;
    }
    .date-filter {
        padding: 14px;
        flex-wrap: wrap;
    }
    .date-filter input,
    .date-filter button {
        font-size: 13px;
        padding: 7px 12px;
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .stat-card {
        padding: 18px;
    }
    .stat-card-title {
        font-size: 13px;
    }
    .stat-card-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    .stat-card-value {
        font-size: 22px;
    }
    .stat-card-desc {
        font-size: 11px;
    }
    .chart-card,
    .table-card {
        padding: 20px;
    }
    .chart-card-title,
    .table-card-title {
        font-size: 16px;
    }
}

/* Mobile (≤768px) */
@media (max-width: 768px) {
    .analytics-header {
        padding: 16px;
        margin-bottom: 16px;
    }
    .analytics-header h1 {
        font-size: 18px;
        margin-bottom: 6px;
    }
    .analytics-header .subtitle {
        font-size: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .date-filter {
        flex-direction: column;
        padding: 12px;
        gap: 10px;
    }
    .date-filter label {
        font-size: 13px;
    }
    .date-filter input,
    .date-filter button,
    .date-filter .admin-btn {
        width: 100%;
        font-size: 13px;
        padding: 10px 12px;
    }
    .date-filter .admin-btn {
        margin-left: 0 !important;
    }
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-card {
        padding: 16px;
    }
    .stat-card-title {
        font-size: 12px;
    }
    .stat-card-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    .stat-card-value {
        font-size: 20px;
    }
    .stat-card-desc {
        font-size: 11px;
    }
    .chart-card,
    .table-card {
        padding: 16px;
        margin-bottom: 20px;
    }
    .chart-card-title,
    .table-card-title {
        font-size: 15px;
        margin-bottom: 12px;
    }
    .trend-chart {
        height: 200px;
        padding: 12px;
    }
    .peak-hours-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .hour-badge {
        padding: 8px 6px;
        font-size: 12px;
    }
    .hour-badge > div:first-child {
        font-size: 14px !important;
    }
    .hour-badge > div:last-child {
        font-size: 10px !important;
    }
    .table-container {
        overflow-x: auto;
    }
    .admin-table {
        min-width: 600px;
    }
    .admin-table th,
    .admin-table td {
        padding: 8px;
        font-size: 12px;
    }
}

/* Small Mobile (≤480px) */
@media (max-width: 480px) {
    .analytics-header {
        padding: 12px;
    }
    .analytics-header h1 {
        font-size: 16px;
    }
    .analytics-header .subtitle {
        font-size: 11px;
    }
    .date-filter {
        padding: 10px;
    }
    .stats-grid {
        gap: 10px;
    }
    .stat-card {
        padding: 12px;
    }
    .stat-card-header {
        margin-bottom: 10px;
    }
    .stat-card-title {
        font-size: 11px;
    }
    .stat-card-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    .stat-card-value {
        font-size: 18px;
    }
    .stat-card-desc {
        font-size: 10px;
    }
    .chart-card,
    .table-card {
        padding: 12px;
    }
    .chart-card-title,
    .table-card-title {
        font-size: 14px;
    }
    .trend-chart {
        height: 150px;
        padding: 8px;
    }
    .peak-hours-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }
    .hour-badge {
        padding: 6px 4px;
        font-size: 11px;
    }
    .hour-badge > div:first-child {
        font-size: 12px !important;
    }
    .hour-badge > div:last-child {
        font-size: 9px !important;
    }
    .admin-table th,
    .admin-table td {
        padding: 6px;
        font-size: 11px;
    }
}
</style>
@endsection

@section('content')
<!-- Back Button -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.promotions.index') }}" class="admin-btn" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-arrow-left"></i> Back to Promotions
    </a>
</div>

<!-- Promotion Header -->
<div class="analytics-header">
    <h1>{{ $promotion->name }}</h1>
    <div class="subtitle">
        <i class="fas fa-calendar"></i> {{ $promotion->start_date->format('M d, Y') }} - {{ $promotion->end_date->format('M d, Y') }}
        <span style="margin-left: 16px;">
            <i class="fas fa-tag"></i> {{ ucwords(str_replace('_', ' ', $promotion->promotion_type)) }}
        </span>
    </div>
</div>

<!-- Date Range Filter -->
<form method="GET" action="{{ route('admin.promotions.analytics', $promotion->id) }}" class="date-filter">
    <label for="date_from">From:</label>
    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" required>

    <label for="date_to">To:</label>
    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" required>

    <button type="submit"><i class="fas fa-search"></i> Apply Filter</button>

    <a href="{{ route('admin.promotions.analytics', $promotion->id) }}" class="admin-btn" style="margin-left: auto;">
        <i class="fas fa-redo"></i> Reset (Last 30 Days)
    </a>
</form>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Revenue</div>
            <div class="stat-card-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-card-value">RM {{ number_format($analytics['total_revenue'], 2) }}</div>
        <div class="stat-card-desc">From this promotion</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Discount Given</div>
            <div class="stat-card-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fas fa-tags"></i>
            </div>
        </div>
        <div class="stat-card-value">RM {{ number_format($analytics['total_discount'], 2) }}</div>
        <div class="stat-card-desc">Total savings</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Usage</div>
            <div class="stat-card-icon" style="background: #dbeafe; color: #2563eb;">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ number_format($analytics['total_usage']) }}</div>
        <div class="stat-card-desc">Times used</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Unique Users</div>
            <div class="stat-card-icon" style="background: #e0e7ff; color: #4f46e5;">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ number_format($analytics['unique_users']) }}</div>
        <div class="stat-card-desc">Different customers</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Avg Order Value</div>
            <div class="stat-card-icon" style="background: #f3e8ff; color: #7c3aed;">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
        <div class="stat-card-value">RM {{ number_format($analytics['avg_order_value'], 2) }}</div>
        <div class="stat-card-desc">Per transaction</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Avg Discount</div>
            <div class="stat-card-icon" style="background: #fce7f3; color: #ec4899;">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
        <div class="stat-card-value">RM {{ number_format($analytics['avg_discount'], 2) }}</div>
        <div class="stat-card-desc">Per transaction</div>
    </div>
</div>

<!-- Usage Trend Chart -->
@if(!empty($analytics['usage_trend']))
<div class="chart-card">
    <div class="chart-card-title"><i class="fas fa-chart-area"></i> Usage Trend (Daily)</div>
    <div class="trend-chart">
        @php
            $maxUsage = max(array_column($analytics['usage_trend'], 'count'));
            $maxUsage = $maxUsage > 0 ? $maxUsage : 1;
        @endphp
        @foreach($analytics['usage_trend'] as $day)
            <div class="trend-bar"
                 style="height: {{ ($day['count'] / $maxUsage) * 100 }}%;"
                 title="{{ $day['date'] }}: {{ $day['count'] }} uses">
                <div class="trend-bar-label">{{ \Carbon\Carbon::parse($day['date'])->format('M d') }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Peak Hours -->
@if(!empty($analytics['peak_hours']))
<div class="chart-card">
    <div class="chart-card-title"><i class="fas fa-clock"></i> Peak Usage Hours</div>
    <div class="peak-hours-grid">
        @php
            $maxHourUsage = max(array_column($analytics['peak_hours'], 'count'));
        @endphp
        @foreach($analytics['peak_hours'] as $hour)
            @php
                $percentage = $maxHourUsage > 0 ? ($hour['count'] / $maxHourUsage) * 100 : 0;
                $badgeClass = $percentage >= 70 ? 'high' : ($percentage >= 40 ? 'medium' : 'low');
            @endphp
            <div class="hour-badge {{ $badgeClass }}" title="{{ $hour['count'] }} uses">
                <div style="font-size: 18px;">{{ str_pad($hour['hour'], 2, '0', STR_PAD_LEFT) }}:00</div>
                <div style="font-size: 12px; margin-top: 4px;">{{ $hour['count'] }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Top Users Table -->
@if(!empty($analytics['top_users']))
<div class="table-card">
    <div class="table-card-title"><i class="fas fa-star"></i> Top Users</div>
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th class="cell-center">Times Used</th>
                    <th class="cell-center">Total Spent</th>
                    <th class="cell-center">Total Discount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['top_users'] as $index => $user)
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 18px; color: {{ $index === 0 ? '#f59e0b' : ($index === 1 ? '#9ca3af' : ($index === 2 ? '#cd7f32' : 'var(--text-3)')) }};">
                            #{{ $index + 1 }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $user['user_name'] }}</div>
                        <div style="font-size: 12px; color: var(--text-3);">{{ $user['user_email'] }}</div>
                    </td>
                    <td class="cell-center">
                        <span style="font-weight: 600; color: var(--brand);">{{ $user['usage_count'] }}x</span>
                    </td>
                    <td class="cell-center">
                        <span style="font-weight: 600;">RM {{ number_format($user['total_revenue'], 2) }}</span>
                    </td>
                    <td class="cell-center">
                        <span style="font-weight: 600; color: #10b981;">RM {{ number_format($user['total_discount'], 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Conversion Stats -->
@if(isset($analytics['conversion_rate']))
<div class="chart-card">
    <div class="chart-card-title"><i class="fas fa-bullseye"></i> Conversion Statistics</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-title">Views</div>
            <div class="stat-card-value">{{ number_format($analytics['total_views'] ?? 0) }}</div>
            <div class="stat-card-desc">Total promotion views</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-title">Conversion Rate</div>
            <div class="stat-card-value">{{ number_format($analytics['conversion_rate'], 1) }}%</div>
            <div class="stat-card-desc">Views to usage</div>
        </div>
    </div>
</div>
@endif

<!-- No Data Message -->
@if($analytics['total_usage'] == 0)
<div class="chart-card" style="text-align: center; padding: 60px 20px;">
    <i class="fas fa-chart-line" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;"></i>
    <h3 style="color: var(--text-2); margin-bottom: 8px;">No Analytics Data Yet</h3>
    <p style="color: var(--text-3);">This promotion hasn't been used yet. Analytics will appear once customers start using it.</p>
</div>
@endif

@endsection

@section('scripts')
<script>
// Optional: Add interactivity for charts
document.addEventListener('DOMContentLoaded', function() {
    // Animate bars on load
    const bars = document.querySelectorAll('.trend-bar');
    bars.forEach((bar, index) => {
        setTimeout(() => {
            bar.style.opacity = '0';
            bar.style.opacity = '1';
        }, index * 50);
    });
});
</script>
@endsection
