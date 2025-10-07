@extends('layouts.admin')

@section('title', 'Rewards Management')
@section('page-title', 'View Rewards')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div class="admin-card-title" style="margin-bottom: 16px;">Active Rewards</div>
                <div class="admin-card-value" style="margin-bottom: 8px;">{{ $rewards->where('is_active', true)->count() }}</div>
                <div class="admin-card-desc">Total configurable rewards</div>
            </div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
        </div>
    </div>

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div class="admin-card-title" style="margin-bottom: 16px;">Rewards Redeemed</div>
                <div class="admin-card-value" style="margin-bottom: 8px;">{{ $rewards->sum('customer_rewards_count') }}</div>
                <div class="admin-card-desc">Across all rewards</div>
            </div>
            <div class="admin-card-icon icon-green"><i class="fas fa-ticket-alt"></i></div>
        </div>
    </div>

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div class="admin-card-title" style="margin-bottom: 16px;">Total Points in System</div>
                <div class="admin-card-value" style="margin-bottom: 8px;">{{ \App\Models\User::sum('points_balance') ?? 0 }}</div>
                <div class="admin-card-desc">Customer points balance</div>
            </div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div class="admin-card-title" style="margin-bottom: 16px;">Active Special Events</div>
                <div class="admin-card-value" style="margin-bottom: 8px;">{{ \App\Models\SpecialEvent::count() }}</div>
                <div class="admin-card-desc">Currently running</div>
            </div>
            <div class="admin-card-icon icon-red"><i class="fas fa-calendar-check"></i></div>
        </div>
    </div>
</div>

<!-- Rewards Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Rewards</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <select class="admin-select" id="filterSelect">
                <option value="all">All Rewards</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="popular">Most Popular</option>
            </select>
            <a href="{{ route('admin.rewards.rewards.create') }}" class="admin-btn btn-primary">
                <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
                New Reward
            </a>
        </div>
    </div>

    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reward</th>
                        <th>Points Required</th>
                        <th>Redeemed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rewards as $reward)
                        <tr data-filter="{{ $reward->is_active ? 'active' : 'inactive' }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($reward->image_url)
                                        <img src="{{ asset('storage/' . $reward->image_url) }}" alt="{{ $reward->title }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-gift" style="color: #94a3b8;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 600;">{{ $reward->title }}</div>
                                        <div style="font-size: 13px; color: #64748b;">{{ Str::limit($reward->description, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-primary">{{ $reward->points_required }} pts</span></td>
                            <td>{{ $reward->customer_rewards_count }}</td>
                            <td>
                                @if($reward->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.rewards.edit', $reward->id) }}" class="admin-btn btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.rewards.rewards.destroy', $reward->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this reward?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn btn-icon btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-gift" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No rewards found. Create your first reward!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make table scrollable if more than 6 rows
    const tableContainer = document.querySelector('.admin-table-container');
    const tableRows = document.querySelectorAll('.admin-table tbody tr:not(.empty-state)');

    if (tableRows.length > 6) {
        tableContainer.classList.add('scrollable');
    }

    // Filter functionality
    const filterSelect = document.getElementById('filterSelect');
    const filterableRows = document.querySelectorAll('.admin-table tbody tr[data-filter]');

    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;

            filterableRows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else if (filterValue === 'popular') {
                    // Show all and sort by redeemed count
                    row.style.display = '';
                } else {
                    if (row.dataset.filter === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }
});
</script>
@endsection
