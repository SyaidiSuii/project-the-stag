@extends('layouts.admin')

@section('title', 'Loyalty Members')
@section('page-title', 'Loyalty Members')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Members</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="admin-card-value">{{ $stats['total_members'] ?? 0 }}</div>
        <div class="admin-card-desc">Loyalty program members</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Points</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-coins"></i></div>
        </div>
        <div class="admin-card-value">{{ number_format($stats['total_points'] ?? 0) }}</div>
        <div class="admin-card-desc">Points in circulation</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Avg Points</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="admin-card-value">{{ number_format($stats['avg_points'] ?? 0, 0) }}</div>
        <div class="admin-card-desc">Per member</div>
    </div>
    {{-- <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Spending</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($stats['total_spending'] ?? 0, 2) }}</div>
        <div class="admin-card-desc">All time revenue</div>
    </div> --}}
</div>

<!-- Main Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Loyalty Program Members</h2>
        <div class="section-controls">
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            <a href="{{ route('admin.rewards.members.export') }}" class="admin-btn btn-secondary">
                <i class="fas fa-download"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <form method="GET" action="{{ route('admin.rewards.members.index') }}">
        <div class="search-filter">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <select class="filter-select" name="tier_id" onchange="this.form.submit()">
                    <option value="all" {{ request('tier_id', 'all') == 'all' ? 'selected' : '' }}>All Tiers</option>
                    @php
                        $tiers = \App\Models\LoyaltyTier::orderBy('points_threshold')->get();
                    @endphp
                    @foreach($tiers as $tier)
                        <option value="{{ $tier->id }}" {{ request('tier_id') == $tier->id ? 'selected' : '' }}>
                            {{ $tier->name }}
                        </option>
                    @endforeach
                </select>

                <select class="filter-select" name="sort" onchange="this.form.submit()">
                    <option value="points_balance" {{ request('sort', 'points_balance') == 'points_balance' ? 'selected' : '' }}>Sort by Points</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Sort by Join Date</option>
                </select>

                <select class="filter-select" name="direction" onchange="this.form.submit()">
                    <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                @if(request('search') || request('tier_id') != 'all' || request('sort') != 'points_balance')
                    <a href="{{ route('admin.rewards.members.index') }}" class="admin-btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>

            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-search"></i>
                Search
            </button>
        </div>
    </form>

    <!-- Members Table -->
    @if($members->count() > 0)
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th class="cell-center">Loyalty Tier</th>
                        <th class="cell-center">Points Balance</th>
                        {{-- <th class="cell-center">Total Spent</th> --}}
                        {{-- <th class="cell-center">Visit Count</th> --}}
                        <th>Join Date</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;">{{ $member->name }}</span>
                                <span style="font-size: 12px; color: var(--text-3);">{{ $member->email }}</span>
                                @if($member->phone_number)
                                    <span style="font-size: 11px; color: var(--text-3);">{{ $member->phone_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="cell-center">
                            {{-- FIXED: Use calculatedTier (real-time) instead of loyaltyTier (static) --}}
                            @if(isset($member->calculatedTier) && $member->calculatedTier)
                                <span class="status status-active" style="background: {{ $member->calculatedTier->color ?? '#dbeafe' }}; color: white;">
                                    {{ $member->calculatedTier->name }}
                                </span>
                            @else
                                <span class="status status-inactive">No Tier</span>
                                {{-- DEBUG: Show why no tier --}}
                                @if($member->email === 'admin@example.com')
                                    <br><small style="color: #999;">
                                        Points: {{ $member->points_balance ?? 0 }} |
                                        Spent: RM {{ $member->customerProfile ? number_format($member->customerProfile->total_spent, 2) : 'NO PROFILE' }}
                                    </small>
                                @endif
                            @endif
                        </td>
                        <td class="cell-center">
                            <span style="font-weight: 700; font-size: 16px; color: var(--brand);">
                                {{ number_format($member->points_balance ?? 0) }}
                            </span>
                        </td>
                        {{-- <td class="cell-center">
                            <span style="font-weight: 600;">
                                RM {{ number_format($member->customerProfile->total_spent ?? 0, 2) }}
                            </span>
                        </td>
                        <td class="cell-center">
                            {{ $member->customerProfile->visit_count ?? 0 }}
                        </td> --}}
                        <td>
                            {{ $member->created_at->format('d M Y') }}
                            <br><span style="font-size: 11px; color: var(--text-3);">{{ $member->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.rewards.members.show', $member->id) }}"
                                   class="action-btn view-btn"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="pagination">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-users-slash"></i>
            </div>
            <div class="empty-state-title">No Members Found</div>
            <p class="empty-state-text">
                @if(request('search') || request('tier_id') != 'all')
                    No members match your current filters. Try adjusting your search criteria.
                @else
                    No loyalty program members yet. Members will appear here when customers earn points.
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
