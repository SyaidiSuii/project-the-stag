@extends('layouts.admin')

@section('title', 'Assign Roles to Users')
@section('page-title', 'Assign Roles to Users')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Assign Roles to Users</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <form method="POST" action="{{ route('admin.roles.assign') }}" class="role-assign-form">
        @csrf

        <!-- User Selection -->
        <div class="form-section">
            <h3 class="section-subtitle">Select User</h3>
            <div class="form-group">
                <label for="user_id" class="form-label">User <span class="required">*</span></label>
                <select
                    id="user_id"
                    name="user_id"
                    class="form-control {{ $errors->get('user_id') ? 'is-invalid' : '' }}"
                    required
                    onchange="loadUserRoles(this.value)">
                    <option value="">Select a user...</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                @if($errors->get('user_id'))
                <div class="form-error">{{ implode(', ', $errors->get('user_id')) }}</div>
                @endif
            </div>

            <!-- Current User Roles Display -->
            <div id="current-roles-display" class="current-roles-section" style="display: none;">
                <h4 class="current-roles-title">Current Roles</h4>
                <div id="current-roles-list" class="current-roles-list"></div>
            </div>
        </div>

        <!-- Role Assignment -->
        <div class="form-section">
            <h3 class="section-subtitle">Assign Roles</h3>

            <div class="role-controls">
                <button type="button"
                    onclick="selectAllRoles()"
                    class="btn-select-all">
                    <i class="fas fa-check-double"></i> Select All
                </button>
                <button type="button"
                    onclick="deselectAllRoles()"
                    class="btn-deselect-all">
                    <i class="fas fa-times"></i> Deselect All
                </button>
            </div>

            @if($roles->count() > 0)
            <div class="roles-container {{ $errors->get('roles') ? 'is-invalid' : '' }}">
                @foreach($roles as $role)
                <label class="role-item">
                    <input type="checkbox"
                        name="roles[]"
                        value="{{ $role->id }}"
                        class="role-checkbox"
                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                    <span class="role-label">
                        <strong>{{ ucfirst($role->name) }}</strong>
                        <small class="role-permissions-count">{{ $role->permissions_count ?? 0 }} permissions</small>
                    </span>
                </label>
                @endforeach
            </div>
            @else
            <div class="no-roles">
                <i class="fas fa-exclamation-triangle"></i>
                <p>No roles available. Please create roles first.</p>
            </div>
            @endif

            @if($errors->get('roles'))
            <div class="form-error">{{ implode(', ', $errors->get('roles')) }}</div>
            @endif
        </div>

        <!-- Important Notes -->
        <div class="form-section">
            <div class="info-panel">
                <div class="info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="info-content">
                    <h4 class="info-title">Important Notes</h4>
                    <ul class="info-list">
                        <li>Selecting new roles will replace all existing roles for the user</li>
                        <li>Users will inherit all permissions from assigned roles</li>
                        <li>Changes take effect immediately after saving</li>
                        <li>Users can have multiple roles assigned</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save" id="assign-btn" disabled>
                <i class="fas fa-user-check"></i> Assign Roles
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function selectAllRoles() {
        const checkboxes = document.querySelectorAll('input[name="roles[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAllRoles() {
        const checkboxes = document.querySelectorAll('input[name="roles[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function loadUserRoles(userId) {
        const assignBtn = document.getElementById('assign-btn');
        const currentRolesDisplay = document.getElementById('current-roles-display');
        const currentRolesList = document.getElementById('current-roles-list');

        if (!userId) {
            assignBtn.disabled = true;
            currentRolesDisplay.style.display = 'none';
            return;
        }

        assignBtn.disabled = false;

        // Find user data from the users array passed to the view
        const users = @json($users);
        const selectedUser = users.find(user => user.id == userId);

        if (selectedUser && selectedUser.roles) {
            currentRolesDisplay.style.display = 'block';

            if (selectedUser.roles.length > 0) {
                currentRolesList.innerHTML = selectedUser.roles.map(role =>
                    `<span class="current-role-badge">${role.name}</span>`
                ).join('');

                // Pre-select current roles
                const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
                roleCheckboxes.forEach(checkbox => {
                    const roleId = parseInt(checkbox.value);
                    checkbox.checked = selectedUser.roles.some(role => role.id === roleId);
                });
            } else {
                currentRolesList.innerHTML = '<span class="no-current-roles">No roles assigned</span>';
            }
        } else {
            currentRolesDisplay.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submission loading state
        const roleForm = document.querySelector('.role-assign-form');
        if (roleForm) {
            roleForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-save');

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning Roles...';
                submitBtn.disabled = true;
            });
        }

        // Load current user roles if user is pre-selected
        const userSelect = document.getElementById('user_id');
        if (userSelect.value) {
            loadUserRoles(userSelect.value);
        }

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
</script>
@endsection