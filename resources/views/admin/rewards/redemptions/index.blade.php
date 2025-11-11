@extends('layouts.admin')

@section('title', 'Redemption Management')
@section('page-title', 'Redemption Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Redemptions</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
        </div>
        <div class="admin-card-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="admin-card-desc">All time redemptions</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $stats['active'] ?? 0 }}</div>
        <div class="admin-card-desc">Ready to redeem</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Redeemed</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-check-double"></i></div>
        </div>
        <div class="admin-card-value">{{ $stats['redeemed'] ?? 0 }}</div>
        <div class="admin-card-desc">Used by customers</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Expired</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-exclamation-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $stats['expired'] ?? 0 }}</div>
        <div class="admin-card-desc">Past expiry date</div>
    </div>
</div>

<!-- Main Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Customer Redemptions</h2>
        <div class="section-controls">
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            <a href="{{ route('admin.rewards.redemptions.export') }}?{{ request()->getQueryString() }}" class="admin-btn btn-secondary">
                <i class="fas fa-download"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <form method="GET" action="{{ route('admin.rewards.redemptions.index') }}">
        <div class="search-filter">
            <div class="filter-group">
                <select class="filter-select" name="status" onchange="this.form.submit()">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <input type="date" name="date_from" class="filter-select" placeholder="From Date" value="{{ request('date_from') }}" onchange="this.form.submit()">

                <input type="date" name="date_to" class="filter-select" placeholder="To Date" value="{{ request('date_to') }}" onchange="this.form.submit()">

                @if(request('status') || request('date_from') || request('date_to'))
                    <a href="{{ route('admin.rewards.redemptions.index') }}" class="admin-btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Redemptions Table -->
    @if($redemptions->count() > 0)
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Reward</th>
                        <th class="cell-center">Points Spent</th>
                        <th class="cell-center">Status</th>
                        <th>Claimed Date</th>
                        <th>Expires At</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($redemptions as $redemption)
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: 600; color: var(--brand);">
                                #{{ $redemption->id }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">{{ $redemption->customerProfile->user->name ?? 'N/A' }}</span>
                                <span style="font-size: 12px; color: var(--text-3);">{{ $redemption->customerProfile->user->email ?? '' }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 500;">{{ $redemption->reward->title ?? 'N/A' }}</span>
                                <span style="font-size: 12px; color: var(--text-3);">{{ $redemption->reward->reward_type ?? '' }}</span>
                            </div>
                        </td>
                        <td class="cell-center">
                            <span style="font-weight: 600; color: var(--brand);">{{ $redemption->points_spent }}</span>
                        </td>
                        <td class="cell-center">
                            @php
                                $statusClass = 'status-active';
                                if ($redemption->status == 'pending') $statusClass = 'status-pending';
                                elseif ($redemption->status == 'redeemed') $statusClass = 'status-active';
                                elseif ($redemption->status == 'expired' || $redemption->status == 'cancelled') $statusClass = 'status-inactive';
                            @endphp
                            <span class="status {{ $statusClass }}">{{ ucfirst($redemption->status) }}</span>
                        </td>
                        <td>
                            {{ $redemption->claimed_at ? $redemption->claimed_at->format('d M Y') : 'N/A' }}
                            @if($redemption->claimed_at)
                                <br><span style="font-size: 11px; color: var(--text-3);">{{ $redemption->claimed_at->format('h:i A') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($redemption->expires_at)
                                {{ $redemption->expires_at->format('d M Y') }}
                                <br>
                                @if($redemption->expires_at->isPast() && $redemption->status !== 'redeemed')
                                    <span style="font-size: 11px; color: var(--danger); font-weight: 600;">Expired</span>
                                @else
                                    <span style="font-size: 11px; color: var(--text-3);">{{ $redemption->expires_at->diffForHumans() }}</span>
                                @endif
                            @else
                                <span style="color: var(--text-3);">No expiry</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.rewards.redemptions.show', $redemption->id) }}"
                                   class="action-btn view-btn"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($redemption->status == 'active')
                                    <form action="{{ route('admin.rewards.redemptions.mark-redeemed', $redemption->id) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Mark this redemption as used?');">
                                        @csrf
                                        <button type="submit" class="action-btn edit-btn" title="Mark as Redeemed">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($redemption->status, ['pending', 'active']))
                                    <form action="{{ route('admin.rewards.redemptions.cancel', $redemption->id) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Cancel this redemption and refund points?');">
                                        @csrf
                                        <input type="hidden" name="refund_points" value="1">
                                        <button type="submit" class="action-btn delete-btn" title="Cancel & Refund">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($redemptions->hasPages())
            <div class="pagination">
                {{ $redemptions->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="empty-state-title">No Redemptions Found</div>
            <p class="empty-state-text">
                @if(request('status') || request('date_from') || request('date_to'))
                    No redemptions match your current filters. Try adjusting your search criteria.
                @else
                    No customer redemptions yet. Redemptions will appear here when customers claim rewards.
                @endif
            </p>
        </div>
    @endif
</div>

@if(session('success'))
    <script>
        setTimeout(() => {
            alert('{{ session('success') }}');
        }, 100);
    </script>
@endif

@if(session('error'))
    <script>
        setTimeout(() => {
            alert('{{ session('error') }}');
        }, 100);
    </script>
@endif
@endsection
