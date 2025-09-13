@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Users</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalUsers ?? 0 }}</div>
        <div class="admin-card-desc">System users</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active Customers</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-user-shield"></i></div>
        </div>
        <div class="admin-card-value">{{ $activeCustomers ?? 0 }}</div>
        <div class="admin-card-desc">Verified customers</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">New Registrations</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-user-plus"></i></div>
        </div>
        <div class="admin-card-value">{{ $newRegistrations ?? 0 }}</div>
        <div class="admin-card-desc">Last 30 days</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Avg. Orders per Customer</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="admin-card-value">{{ $avgOrdersPerCustomer ?? '0.0' }}</div>
        <div class="admin-card-desc">Order frequency</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">User Accounts</h2>
    </div>
    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search users..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="roleFilter">
                <option value="all">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="statusFilter">
                <option value="all">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select class="filter-select" id="sortBy">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>By Name</option>
                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>By Email</option>
            </select>
        </div>
        <a href="{{ route('admin.user.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add User
        </a>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-customer">User</th>
                    <th class="th-contact">Contact</th>
                    <th class="th-status">Role</th>
                    <th class="th-account-status">Account Status</th>
                    {{-- <th class="th-total-orders">Total Orders</th> --}}
                    <th class="th-last-activity">Created</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="customer-name">{{ $user->name }}</div>
                                <div class="customer-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $user->email }}</div>
                        @if($user->formatted_phone)
                            <div style="font-size: 13px; color: var(--text-3);">{{ $user->formatted_phone }}</div>
                        @else
                            <div style="font-size: 13px; color: var(--text-3); font-style: italic;">No phone</div>
                        @endif
                    </td>
                    <td class="cell-center">
                        @if($user->roles->isNotEmpty())
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                @foreach($user->roles as $role)
                                    <span class="status status-active">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="status" style="background: #fee2e2; color: var(--danger);">No Role</span>
                        @endif
                    </td>
                    <td class="cell-center">
                        @if($user->is_active)
                            <span class="status status-active">Active</span>
                        @else
                            <span class="status status-inactive">Inactive</span>
                        @endif
                    </td>
                    {{-- <td class="cell-center">
                        <span class="orders-count">{{ $user->orders_count ?? 0 }}</span>
                    </td> --}}
                    <td>
                        <div style="font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: var(--text-3);">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="action-btn edit-btn" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.user.destroy', $user->id) }}" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="_method" value="DELETE">
                                @csrf
                                <button type="submit" class="action-btn delete-btn" title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <!-- Pagination -->
    <div class="pagination">
        <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
            <span style="font-size: 14px; color: var(--text-2);">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
            </span>
        </div>
        
        @if($users->onFirstPage())
            <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="pagination-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if($page == $users->currentPage())
                <span class="pagination-btn active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
            @endif
        @endforeach

        @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="pagination-btn">
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
<script src="{{ asset('js/admin/user-management.js') }}"></script>
@endsection