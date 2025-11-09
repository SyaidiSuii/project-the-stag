@extends('layouts.admin')

@section('title', 'Order Activity Logs')
@section('page-title', 'Order Activity Logs')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-label {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .logs-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .log-row {
        border-bottom: 1px solid #e5e7eb;
        padding: 16px;
        display: grid;
        grid-template-columns: auto 1fr 120px 150px;
        gap: 16px;
        align-items: start;
    }

    .log-row:hover {
        background: #f9fafb;
    }

    .log-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .log-content {
        flex: 1;
    }

    .log-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .log-message {
        font-size: 13px;
        color: #6b7280;
    }

    .log-metadata {
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
        font-size: 12px;
        color: #6b7280;
        margin-top: 8px;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-critical { background: #fecaca; color: #991b1b; }
    .badge-error { background: #fed7aa; color: #9a3412; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-info { background: #dbeafe; color: #1e40af; }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div style="padding: 24px;">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Logs</div>
            <div class="stat-value">{{ number_format($totalLogs) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Critical Issues</div>
            <div class="stat-value" style="color: #dc2626;">{{ number_format($criticalCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Errors</div>
            <div class="stat-value" style="color: #ea580c;">{{ number_format($errorCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Warnings</div>
            <div class="stat-value" style="color: #f59e0b;">{{ number_format($warningCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Info Logs</div>
            <div class="stat-value" style="color: #3b82f6;">{{ number_format($infoCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Recent Problems (24h)</div>
            <div class="stat-value" style="color: #ef4444;">{{ number_format($recentProblems) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reports.activity-logs') }}">
            <div class="filter-grid">
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 14px;">Activity Type</label>
                    <select name="activity_type" class="form-control">
                        <option value="all">All Types</option>
                        <option value="critical" {{ request('activity_type') == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="error" {{ request('activity_type') == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="warning" {{ request('activity_type') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="info" {{ request('activity_type') == 'info' ? 'selected' : '' }}>Info</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 14px;">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 14px;">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 14px;">Search Order</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order number..." class="form-control">
                </div>
            </div>
            <div style="margin-top: 16px; display: flex; gap: 8px;">
                <button type="submit" class="admin-btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="{{ route('admin.reports.activity-logs') }}" class="admin-btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="logs-table">
        <div style="padding: 20px; border-bottom: 2px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Activity Logs ({{ $activityLogs->total() }})</h3>
        </div>

        @forelse($activityLogs as $log)
        <div class="log-row">
            <div class="log-icon" style="background:
                {{ $log->activity_type == 'critical' ? '#fecaca' : ($log->activity_type == 'error' ? '#fed7aa' : ($log->activity_type == 'warning' ? '#fef3c7' : '#dbeafe')) }};
                color: {{ $log->activity_type == 'critical' ? '#dc2626' : ($log->activity_type == 'error' ? '#ea580c' : ($log->activity_type == 'warning' ? '#f59e0b' : '#3b82f6')) }};">
                <i class="fas fa-{{ $log->activity_type == 'critical' ? 'times-circle' : ($log->activity_type == 'error' ? 'exclamation-triangle' : ($log->activity_type == 'warning' ? 'exclamation-circle' : 'info-circle')) }}"></i>
            </div>

            <div class="log-content">
                <div class="log-title">
                    <a href="{{ route('admin.order.show', $log->order_id) }}" style="color: #1f2937; text-decoration: none;">
                        #{{ $log->order->confirmation_code ?? 'N/A' }}
                    </a>
                    - {{ $log->title }}
                </div>
                <div class="log-message">{{ $log->message }}</div>

                @if($log->metadata && !empty($log->metadata))
                <div class="log-metadata">
                    @foreach($log->metadata as $key => $value)
                        @if($key === 'delay_minutes')
                            <div><i class="fas fa-hourglass-half"></i> Delay: <strong>{{ $value }} minutes</strong></div>
                        @elseif($key === 'reason')
                            <div><i class="fas fa-info-circle"></i> {{ $value }}</div>
                        @elseif($key === 'failure_reason')
                            <div><i class="fas fa-times"></i> {{ $value }}</div>
                        @endif
                    @endforeach
                </div>
                @endif

                <div style="font-size: 12px; color: #9ca3af; margin-top: 8px;">
                    <i class="fas fa-user"></i> {{ $log->triggeredBy ? $log->triggeredBy->name : 'System' }}
                </div>
            </div>

            <div>
                <span class="badge badge-{{ $log->activity_type }}">{{ $log->activity_type }}</span>
            </div>

            <div style="text-align: right; font-size: 13px; color: #6b7280;">
                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                <div>{{ $log->created_at->format('h:i A') }}</div>
                <div style="font-size: 11px; color: #9ca3af;">{{ $log->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 60px; color: #9ca3af;">
            <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
            <div style="font-size: 18px; font-weight: 600;">No Activity Logs Found</div>
            <div style="font-size: 14px; margin-top: 8px;">Try adjusting your filters</div>
        </div>
        @endforelse

        @if($activityLogs->hasPages())
        <div class="pagination">
            {{ $activityLogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
