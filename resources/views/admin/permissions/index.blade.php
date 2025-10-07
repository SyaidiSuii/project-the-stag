@extends('layouts.admin')

@section('title', 'Permissions Management')
@section('page-title', 'Permissions Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Permissions</h2>
    </div>

    <div class="search-filter">
        <a href="{{ route('admin.permissions.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Permission
        </a>
    </div>

    <!-- Permissions Table -->
    @if($permissions->count() > 0)
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Permission Name</th>
                    <th class="th-customer">Assigned Roles</th>
                    <th class="th-time">Created Date</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                <tr>
                    <td>
                        <div class="customer-info">
                            <div class="customer-name">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</div>
                            <div class="permission-code">{{ $permission->name }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="roles-list">
                            @if($permission->roles->count() > 0)
                            @foreach($permission->roles as $role)
                            <span class="role-badge">{{ ucfirst($role->name) }}</span>
                            @endforeach
                            @else
                            <span class="no-roles">No roles assigned</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 13px;">{{ $permission->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.permissions.show', $permission->id) }}"
                                class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                class="action-btn edit-btn" title="Edit Permission">
                                <i class="fas fa-edit"></i>
                            </a>

                            @if(!in_array($permission->name, ['view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles']))
                            <form method="POST"
                                action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                <input type="hidden" name="_method" value="DELETE">
                                @csrf
                                <button type="submit" class="action-btn delete-btn" title="Delete Permission">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted" style="font-size: 12px; color: #6c757d;">Core</span>
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
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="empty-state-title">No permissions found</div>
        <div class="empty-state-text">
            @if(request()->hasAny(['search', 'filter']))
            No permissions match your current filters. Try adjusting your search criteria.
            @else
            No permissions have been created yet.
            @endif
        </div>
        @if(!request()->hasAny(['search', 'filter']))
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.permissions.create') }}" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create Permission
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Pagination -->
    @if($permissions->hasPages())
    <div class="pagination">
        <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
            <span style="font-size: 14px; color: var(--text-2);">
                Showing {{ $permissions->firstItem() }} to {{ $permissions->lastItem() }} of {{ $permissions->total() }} results
            </span>
        </div>

        @if($permissions->onFirstPage())
        <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
            <i class="fas fa-chevron-left"></i>
        </span>
        @else
        <a href="{{ $permissions->previousPageUrl() }}" class="pagination-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
        @endif

        @foreach($permissions->getUrlRange(1, $permissions->lastPage()) as $page => $url)
        @if($page == $permissions->currentPage())
        <span class="pagination-btn active">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
        @endif
        @endforeach

        @if($permissions->hasMorePages())
        <a href="{{ $permissions->nextPageUrl() }}" class="pagination-btn">
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
        showNotification('{{ session('
            message ') }}', 'success');
        @endif

        @if(session('success'))
        showNotification('{{ session('
            success ') }}', 'success');
        @endif

        @if(session('error'))
        showNotification('{{ session('
            error ') }}', 'error');
        @endif
    });
</script>
@endsection