@extends('layouts.admin')

@section('title', 'Roles Management')
@section('page-title', 'Roles Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Roles</h2>
    </div>

    <div class="search-filter">
        <a href="{{ route('admin.roles.assign.form') }}" class="admin-btn btn-secondary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Assign Roles to Users
        </a>

        <a href="{{ route('admin.roles.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Roles
        </a>
    </div>

    <!-- Roles Table -->
    @if($roles->count() > 0)
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Role Name</th>
                    <th class="th-customer">Users Name</th>
                    <th class="th-time">Permission Count</th>
                    <th class="th-time">Created Date</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td data-label="Role Name">
                        <div class="customer-info">
                            <div class="customer-name">{{ ucfirst($role->name) }}</div>
                        </div>
                    </td>
                    <td data-label="Users">
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <span class="status status-active">{{ $role->users_count }} users</span>
                        </div>
                    </td>
                    <td data-label="Permissions">
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <span class="status status-active">{{ $role->permissions_count }} permissions</span>
                        </div>
                    </td>
                    <td data-label="Created Date">
                        <div style="font-size: 13px;">{{ $role->created_at->format('d M Y') }}</div>
                    </td>
                    <td data-label="Actions">
                        <div class="table-actions">
                            <a href="{{ route('admin.roles.show', $role->id) }}"
                                class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="action-btn edit-btn" title="Edit Role">
                                <i class="fas fa-edit"></i>
                            </a>

                            @if(!in_array($role->name, ['admin', 'manager', 'user']))
                                <form method="POST"
                                    action="{{ route('admin.roles.destroy', $role->id) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    <input type="hidden" name="_method" value="DELETE">
                                    @csrf
                                    <button type="submit" class="action-btn delete-btn" title="Delete Role">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted" style="font-size: 12px; color: #6c757d;">Protected</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State Outside Table -->
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="empty-state-title">No roles found</div>
        <div class="empty-state-text">
            @if(request()->hasAny(['search', 'filter']))
            No roles match your current filters. Try adjusting your search criteria.
            @else
            No roles have been created yet.
            @endif
        </div>
        @if(!request()->hasAny(['search', 'filter']))
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.roles.create') }}" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create Role
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Pagination -->
    @if($roles->hasPages())
    <div class="pagination">
        <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
            <span style="font-size: 14px; color: var(--text-2);">
                Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} results
            </span>
        </div>

        @if($roles->onFirstPage())
        <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
            <i class="fas fa-chevron-left"></i>
        </span>
        @else
        <a href="{{ $roles->previousPageUrl() }}" class="pagination-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
        @endif

        @foreach($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
        @if($page == $roles->currentPage())
        <span class="pagination-btn active">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
        @endif
        @endforeach

        @if($roles->hasMorePages())
        <a href="{{ $roles->nextPageUrl() }}" class="pagination-btn">
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
        // Create a simple notification
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

    // Check for session messages on page load
    document.addEventListener('DOMContentLoaded', function() {
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