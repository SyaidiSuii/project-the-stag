@extends('layouts.admin')

@section('title', 'Promotion Statistics - ' . $promotion->name)
@section('page-title', 'Promotion Statistics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
/* Stats Dashboard Styles */
.stats-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 32px;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.stats-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 8px;
}

.stats-header-meta {
    display: flex;
    gap: 24px;
    margin-top: 16px;
    flex-wrap: wrap;
}

.stats-header-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    opacity: 0.95;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card.primary { border-left-color: #667eea; }
.stat-card.success { border-left-color: #10b981; }
.stat-card.warning { border-left-color: #f59e0b; }
.stat-card.danger { border-left-color: #ef4444; }
.stat-card.info { border-left-color: #3b82f6; }

.stat-label {
    font-size: 0.85rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 900;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 8px;
}

.stat-description {
    font-size: 0.85rem;
    color: #9ca3af;
}

.chart-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 32px;
}

.chart-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.progress-bar-container {
    margin-bottom: 20px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.progress-bar {
    width: 100%;
    height: 12px;
    background: #f3f4f6;
    border-radius: 6px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 6px;
    transition: width 0.6s ease;
}

.usage-logs-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.usage-logs-table thead {
    background: #f9fafb;
}

.usage-logs-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.usage-logs-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
    color: #1f2937;
}

.usage-logs-table tbody tr:hover {
    background: #f9fafb;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: #dcfce7;
    color: #16a34a;
}

.badge-warning {
    background: #fef3c7;
    color: #d97706;
}

.badge-danger {
    background: #fee2e2;
    color: #dc2626;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 16px;
    opacity: 0.4;
}

.empty-state-text {
    font-size: 1.1rem;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.btn-back {
    padding: 10px 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-back:hover {
    border-color: #667eea;
    color: #667eea;
}

.btn-export {
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */

/* Large Desktop (≥1600px) */
@media (min-width: 1600px) {
    .stats-header {
        padding: 40px;
    }
    .stats-header h1 {
        font-size: 2.4rem;
    }
    .stats-grid {
        gap: 28px;
    }
    .stat-card {
        padding: 32px;
    }
    .stat-value {
        font-size: 3.2rem;
    }
    .chart-card {
        padding: 36px;
    }
    .chart-title {
        font-size: 1.6rem;
    }
}

/* Tablet (769px-1199px) */
@media (max-width: 1199px) and (min-width: 769px) {
    .action-buttons {
        gap: 10px;
    }
    .btn-back,
    .btn-export {
        padding: 8px 16px;
        font-size: 13px;
    }
    .stats-header {
        padding: 24px;
        margin-bottom: 24px;
    }
    .stats-header h1 {
        font-size: 1.5rem;
    }
    .stats-header p {
        font-size: 0.9rem !important;
    }
    .stats-header-meta {
        gap: 16px;
    }
    .stats-header-item {
        font-size: 0.85rem;
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .stat-card {
        padding: 18px;
    }
    .stat-label {
        font-size: 0.75rem;
    }
    .stat-value {
        font-size: 2rem;
    }
    .stat-description {
        font-size: 0.75rem;
    }
    .chart-card {
        padding: 20px;
        margin-bottom: 24px;
    }
    .chart-title {
        font-size: 1.1rem;
        margin-bottom: 16px;
    }
    .progress-label {
        font-size: 0.85rem;
    }
    .usage-logs-table th,
    .usage-logs-table td {
        padding: 10px 14px;
        font-size: 0.85rem;
    }
}

/* Mobile (≤768px) */
@media (max-width: 768px) {
    .admin-section {
        padding: 16px;
    }
    .action-buttons {
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }
    .btn-back,
    .btn-export {
        width: 100%;
        justify-content: center;
        padding: 10px 16px;
        font-size: 13px;
    }
    .stats-header {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 12px;
    }
    .stats-header h1 {
        font-size: 1.3rem;
        margin-bottom: 6px;
    }
    .stats-header p {
        font-size: 0.85rem !important;
        margin-top: 6px !important;
    }
    .stats-header-meta {
        gap: 10px;
        flex-direction: column;
        align-items: flex-start;
        margin-top: 12px;
    }
    .stats-header-item {
        font-size: 0.8rem;
    }
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-card {
        padding: 16px;
    }
    .stat-label {
        font-size: 0.7rem;
        margin-bottom: 6px;
    }
    .stat-value {
        font-size: 1.8rem;
        margin-bottom: 6px;
    }
    .stat-description {
        font-size: 0.75rem;
    }
    .chart-card {
        padding: 16px;
        margin-bottom: 20px;
    }
    .chart-title {
        font-size: 1rem;
        margin-bottom: 14px;
        padding-bottom: 10px;
    }
    .progress-bar-container {
        margin-bottom: 16px;
    }
    .progress-label {
        font-size: 0.8rem;
        margin-bottom: 6px;
    }
    .progress-bar {
        height: 10px;
    }
    .usage-logs-table {
        display: block;
        overflow-x: auto;
    }
    .usage-logs-table th,
    .usage-logs-table td {
        padding: 10px 12px;
        font-size: 0.8rem;
    }
    .usage-logs-table th {
        font-size: 0.75rem;
    }
    .badge {
        padding: 3px 8px;
        font-size: 0.7rem;
    }
    .empty-state {
        padding: 40px 16px;
    }
    .empty-state-icon {
        font-size: 3rem;
    }
    .empty-state-text {
        font-size: 0.95rem;
    }
}

/* Small Mobile (≤480px) */
@media (max-width: 480px) {
    .admin-section {
        padding: 12px;
    }
    .action-buttons {
        gap: 8px;
    }
    .btn-back,
    .btn-export {
        padding: 8px 14px;
        font-size: 12px;
    }
    .stats-header {
        padding: 16px;
        margin-bottom: 16px;
    }
    .stats-header h1 {
        font-size: 1.1rem;
    }
    .stats-header p {
        font-size: 0.8rem !important;
    }
    .stats-header-item {
        font-size: 0.75rem;
    }
    .stats-grid {
        gap: 10px;
    }
    .stat-card {
        padding: 12px;
        border-left-width: 3px;
    }
    .stat-label {
        font-size: 0.65rem;
        margin-bottom: 5px;
    }
    .stat-value {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    .stat-description {
        font-size: 0.7rem;
    }
    .chart-card {
        padding: 12px;
    }
    .chart-title {
        font-size: 0.9rem;
        margin-bottom: 12px;
        padding-bottom: 8px;
    }
    .progress-label {
        font-size: 0.75rem;
    }
    .progress-bar {
        height: 8px;
    }
    .usage-logs-table th,
    .usage-logs-table td {
        padding: 8px 10px;
        font-size: 0.75rem;
    }
    .usage-logs-table th {
        font-size: 0.7rem;
    }
    .badge {
        padding: 2px 6px;
        font-size: 0.65rem;
    }
}
</style>
@endsection

@section('content')
<div class="admin-section">
    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.promotions.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Promotions
        </a>
        <button class="btn-export" onclick="window.print()">
            <i class="fas fa-download"></i> Export Report
        </button>
    </div>

    <!-- Promotion Header -->
    <div class="stats-header">
        <h1>{{ $promotion->name }}</h1>
        <p style="font-size: 1.05rem; margin-top: 8px;">{{ $promotion->description ?? 'Promotion Statistics & Analytics' }}</p>
        <div class="stats-header-meta">
            <div class="stats-header-item">
                <i class="fas fa-tag"></i>
                <span>Type: {{ ucwords(str_replace('_', ' ', $promotion->type)) }}</span>
            </div>
            @if($promotion->promo_code)
            <div class="stats-header-item">
                <i class="fas fa-ticket-alt"></i>
                <span>Code: <strong>{{ $promotion->promo_code }}</strong></span>
            </div>
            @endif
            <div class="stats-header-item">
                <i class="fas fa-calendar"></i>
                <span>{{ $promotion->start_date->format('M d') }} - {{ $promotion->end_date->format('M d, Y') }}</span>
            </div>
            <div class="stats-header-item">
                <i class="fas fa-{{ $promotion->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                <span>{{ $promotion->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total Usage</div>
            <div class="stat-value">{{ $stats['total_usage'] }}</div>
            <div class="stat-description">
                @if($promotion->usage_limit)
                    out of {{ $promotion->usage_limit }} limit
                @else
                    No limit set
                @endif
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-label">Total Discount Given</div>
            <div class="stat-value">RM {{ number_format($stats['total_discount_given'], 2) }}</div>
            <div class="stat-description">Total savings provided</div>
        </div>

        <div class="stat-card info">
            <div class="stat-label">Unique Users</div>
            <div class="stat-value">{{ $stats['unique_users'] }}</div>
            <div class="stat-description">Different customers</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-label">Avg Discount</div>
            <div class="stat-value">RM {{ number_format($stats['average_discount'], 2) }}</div>
            <div class="stat-description">Per transaction</div>
        </div>

        @if($promotion->usage_limit)
        <div class="stat-card {{ $stats['remaining_uses'] <= 10 ? 'danger' : 'success' }}">
            <div class="stat-label">Remaining Uses</div>
            <div class="stat-value">{{ $stats['remaining_uses'] }}</div>
            <div class="stat-description">
                {{ number_format($stats['usage_percentage'], 1) }}% used
            </div>
        </div>
        @endif

        @if($promotion->usage_limit_per_user)
        <div class="stat-card info">
            <div class="stat-label">Per User Limit</div>
            <div class="stat-value">{{ $promotion->usage_limit_per_user }}</div>
            <div class="stat-description">Times per customer</div>
        </div>
        @endif
    </div>

    <!-- Usage Progress -->
    @if($promotion->usage_limit)
    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-bar"></i> Usage Progress
        </div>
        <div class="progress-bar-container">
            <div class="progress-label">
                <span>Used: {{ $stats['total_usage'] }} / {{ $promotion->usage_limit }}</span>
                <span><strong>{{ number_format($stats['usage_percentage'], 1) }}%</strong></span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ min($stats['usage_percentage'], 100) }}%;"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Metrics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Days Active</div>
            <div class="stat-value">{{ $stats['days_active'] }}</div>
            <div class="stat-description">
                {{ $stats['days_remaining'] }} days remaining
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-label">Daily Average Usage</div>
            <div class="stat-value">{{ number_format($stats['daily_avg_usage'], 1) }}</div>
            <div class="stat-description">Uses per day</div>
        </div>

        @if($stats['total_usage'] > 0)
        <div class="stat-card info">
            <div class="stat-label">Conversion Rate</div>
            <div class="stat-value">{{ number_format($stats['conversion_rate'] ?? 0, 1) }}%</div>
            <div class="stat-description">Views to usage ratio</div>
        </div>
        @endif
    </div>

    <!-- Recent Usage Logs -->
    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-history"></i> Recent Usage History (Last 20)
        </div>

        @if($stats['recent_logs']->count() > 0)
        <div style="overflow-x: auto;">
            <table class="usage-logs-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Order ID</th>
                        <th>Discount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['recent_logs'] as $log)
                    <tr>
                        <td>
                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: #9ca3af;">{{ $log->created_at->format('g:i A') }}</div>
                        </td>
                        <td>
                            @if($log->user)
                                <div style="font-weight: 600;">{{ $log->user->name }}</div>
                                <div style="font-size: 0.8rem; color: #9ca3af;">{{ $log->user->email }}</div>
                            @else
                                <span style="color: #9ca3af;">Guest</span>
                            @endif
                        </td>
                        <td>
                            @if($log->order_id)
                                <a href="{{ route('admin.orders.show', $log->order_id) }}" style="color: #667eea; font-weight: 600;">
                                    #{{ $log->order_id }}
                                </a>
                            @else
                                <span style="color: #9ca3af;">—</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color: #10b981;">RM {{ number_format($log->discount_amount, 2) }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-success">Applied</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <div class="empty-state-text">No usage data yet</div>
            <p style="margin-top: 8px; font-size: 0.9rem;">This promotion hasn't been used by any customers yet.</p>
        </div>
        @endif
    </div>

    <!-- Top Users (if applicable) -->
    @if(isset($stats['top_users']) && $stats['top_users']->count() > 0)
    <div class="chart-card">
        <div class="chart-title">
            <i class="fas fa-users"></i> Top Users
        </div>
        <div style="overflow-x: auto;">
            <table class="usage-logs-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Times Used</th>
                        <th>Total Savings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['top_users'] as $userStat)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $userStat->user->name }}</div>
                            <div style="font-size: 0.8rem; color: #9ca3af;">{{ $userStat->user->email }}</div>
                        </td>
                        <td><strong>{{ $userStat->usage_count }} times</strong></td>
                        <td><strong style="color: #10b981;">RM {{ number_format($userStat->total_discount, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Animate progress bar on load
document.addEventListener('DOMContentLoaded', function() {
    const progressFill = document.querySelector('.progress-fill');
    if (progressFill) {
        const width = progressFill.style.width;
        progressFill.style.width = '0%';
        setTimeout(() => {
            progressFill.style.width = width;
        }, 100);
    }
});
</script>
@endsection
