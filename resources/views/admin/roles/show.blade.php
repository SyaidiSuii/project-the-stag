@extends('layouts.admin')

@section('title', 'Role Details')
@section('page-title', 'Role Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Role Details: {{ ucfirst($role->name) }}</h2>
        <div class="header-actions">
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="admin-btn btn-primary">
                <i class="fas fa-edit"></i> Edit Role
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
        </div>
    </div>

    <!-- Role Information -->
    <div class="form-section">
        <h3 class="section-subtitle">Role Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Role ID:</span>
                <span class="info-value">#{{ $role->id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Role Name:</span>
                <span class="info-value">{{ ucfirst($role->name) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Guard Name:</span>
                <span class="info-value">{{ $role->guard_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Created:</span>
                <span class="info-value">{{ $role->created_at->format('M d, Y h:i A') }}</span>
            </div>
            @if($role->updated_at != $role->created_at)
            <div class="info-item">
                <span class="info-label">Last Updated:</span>
                <span class="info-value">{{ $role->updated_at->format('M d, Y h:i A') }}</span>
            </div>
            @endif
            <div class="info-item">
                <span class="info-label">Total Permissions:</span>
                <span class="info-value">{{ $permissions->count() }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Users:</span>
                <span class="info-value">{{ $users->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Assigned Permissions -->
    <div class="form-section">
        <h3 class="section-subtitle">Assigned Permissions ({{ $permissions->count() }})</h3>

        @if($permissions->count() > 0)
        @php
        $groupedPermissions = $permissions->groupBy(function($permission) {
        $parts = explode('-', $permission->name);
        return count($parts) > 1 ? $parts[1] : 'general';
        });
        @endphp

        <div class="permissions-display">
            @foreach($groupedPermissions as $group => $perms)
            <div class="permission-group">
                <h4 class="permission-group-title">
                    {{ ucfirst(str_replace('-', ' ', $group)) }} ({{ $perms->count() }})
                </h4>
                <div class="permission-items">
                    @foreach($perms as $permission)
                    <span class="permission-badge">
                        <i class="fas fa-shield-alt"></i>
                        {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-permissions">
            <i class="fas fa-exclamation-triangle"></i>
            <p>No permissions assigned to this role.</p>
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-primary">
                <i class="fas fa-plus"></i> Assign Permissions
            </a>
        </div>
        @endif
    </div>

    <!-- Users with this Role -->
    <div class="form-section">
        <h3 class="section-subtitle">Users with this Role ({{ $users->count() }})</h3>

        @if($users->count() > 0)
        <div class="users-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Assigned Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">{{ $user->name }}</span>
                                @if($user->hasVerifiedEmail())
                                <span class="verified-badge">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-badge {{ $user->email_verified_at ? 'active' : 'inactive' }}">
                                {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.user.show', $user->id) }}" class="btn-view" title="View User">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn-edit" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="no-users">
            <i class="fas fa-users"></i>
            <p>No users currently have this role.</p>
            <a href="{{ route('admin.roles.assign.form') }}" class="btn-primary">
                <i class="fas fa-user-plus"></i> Assign Users
            </a>
        </div>
        @endif
    </div>

    <!-- Role Statistics -->
    <div class="form-section">
        <h3 class="section-subtitle">Role Statistics</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $permissions->count() }}</div>
                    <div class="stat-label">Permissions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $users->count() }}</div>
                    <div class="stat-label">Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $role->created_at->diffForHumans() }}</div>
                    <div class="stat-label">Created</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $role->updated_at->diffForHumans() }}</div>
                    <div class="stat-label">Last Updated</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning for Core Roles -->
    @if(in_array($role->name, ['admin', 'manager', 'customer']))
    <div class="form-section">
        <div class="warning-panel">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="warning-content">
                <h4 class="warning-title">Core System Role</h4>
                <p class="warning-text">This is a core system role. Be careful when modifying its permissions as it may affect system functionality.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-primary">
            <i class="fas fa-edit"></i> Edit Role
        </a>
        @unless(in_array($role->name, ['admin', 'manager', 'customer']))
        <button type="button" class="btn-danger" onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')">
            <i class="fas fa-trash"></i> Delete Role
        </button>
        @endunless
        <a href="{{ route('admin.roles.assign.form') }}" class="btn-secondary">
            <i class="fas fa-user-plus"></i> Assign to Users
        </a>
        <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Deletion</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the role "<span id="roleName"></span>"?</p>
            <p class="warning-text">This action cannot be undone and will affect all users with this role.</p>
        </div>
        <div class="modal-footer">
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete</button>
            </form>
            <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success/error messages
        @if(session('success'))
        showNotification('{{ session('
            success ') }}', 'success');
        @endif

        @if(session('error'))
        showNotification('{{ session('
            error ') }}', 'error');
        @endif
    });

    // Delete confirmation
    function confirmDelete(roleId, roleName) {
        document.getElementById('roleName').textContent = roleName;
        document.getElementById('deleteForm').action = '{{ route('
        admin.roles.destroy ', '
        ') }}/' + roleId;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

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
</script>
@endsection