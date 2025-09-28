@extends('layouts.admin')

@section('title', 'Rewards Management')
@section('page-title', 'Rewards Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active Rewards</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalActiveRewards ?? 0 }}</div>
        <div class="admin-card-desc">Total configurable rewards</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Rewards Redeemed</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-ticket-alt"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalRedeemed ?? 0 }}</div>
        <div class="admin-card-desc">Across all rewards</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Points in System</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-users"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalPointsInSystem ?? 0 }}</div>
        <div class="admin-card-desc">Customer points balance</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active Special Events</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value">{{ $activeSpecialEvents ?? 0 }}</div>
        <div class="admin-card-desc">Currently running</div>
    </div>
</div>

<!-- Tabs -->
    <div class="admin-tabs">
      <div class="admin-tab active" data-tab="rewards">Rewards</div>
      <div class="admin-tab" data-tab="checkin">Check-in Settings</div>
      <div class="admin-tab" data-tab="events">Special Events</div>
      <div class="admin-tab" data-tab="content">Page Content</div>
      <div class="admin-tab" data-tab="tiers">Tiers & Levels</div>
      <div class="admin-tab" data-tab="redemptions">Redemptions</div>
      <div class="admin-tab" data-tab="members">Members</div>
    </div>

<!-- Search and Filter Section -->
<div class="admin-section">
    
    <div class="admin-section" id="rewards-section" style="display: block;">
      <div class="section-header">
        <h2 class="section-title">View Rewards</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
          <select class="admin-select" id="filterSelect">
            <option value="all">All Rewards</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="popular">Most Popular</option>
          </select>
          <a href="{{ route('admin.rewards.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            New Reward
          </a>
        </div>
      </div>

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
        <tbody id="rewards-table-body">
            @forelse($rewards as $reward)
            <tr>
              <td>
                <div>
                  <strong>{{ $reward->title }}</strong>
                  @if($reward->description)
                    <br><small class="text-muted">{{ Str::limit($reward->description, 50) }}</small>
                  @endif
                  <br><span class="badge badge-secondary">{{ ucfirst($reward->reward_type) }}</span>
                </div>
              </td>
              <td>{{ $reward->points_required ? $reward->points_required . ' points' : 'N/A' }}</td>
              <td>{{ $reward->customerRewards()->count() }} times</td>
              <td>
                <span class="status {{ $reward->is_active ? 'status-active' : 'status-inactive' }}">
                  {{ $reward->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="table-actions">
                <a href="{{ route('admin.rewards.edit', $reward->id) }}" class="action-btn edit-btn">Edit</a>
                <form style="display: inline" method="POST" action="{{ route('admin.rewards.toggle', $reward->id) }}" onsubmit="return confirm('Are you sure you want to {{ $reward->is_active ? 'disable' : 'enable' }} this reward?')">
                  @csrf
                  <button type="submit" class="action-btn delete-btn">
                    {{ $reward->is_active ? 'Disable' : 'Enable' }}
                  </button>
                </form>
                <form style="display: inline" method="POST" action="{{ route('admin.rewards.destroy', $reward->id) }}" onsubmit="return confirm('Are you sure you want to delete this reward? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="action-btn delete-btn">Delete</button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">No rewards found.</td>
            </tr>
            @endforelse
          </tbody>
      </table>
    </div>

    @if($rewards->hasPages())
    <!-- Pagination -->
    <div class="pagination">
        <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
            <span style="font-size: 14px; color: var(--text-2);">
                Showing {{ $rewards->firstItem() }} to {{ $rewards->lastItem() }} of {{ $rewards->total() }} results
            </span>
        </div>
        
        @if($rewards->onFirstPage())
            <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $rewards->previousPageUrl() }}" class="pagination-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        @foreach($rewards->getUrlRange(1, $rewards->lastPage()) as $page => $url)
            @if($page == $rewards->currentPage())
                <span class="pagination-btn active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
            @endif
        @endforeach

        @if($rewards->hasMorePages())
            <a href="{{ $rewards->nextPageUrl() }}" class="pagination-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Check for success/error messages from session
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});
</script>
@endsection