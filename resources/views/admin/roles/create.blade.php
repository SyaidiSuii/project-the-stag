@extends('layouts.admin')

@section('title', 'Create New Role')
@section('page-title', 'Create New Role')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New Role</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="role-form">
        @csrf

        <!-- Basic Role Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Basic Role Information</h3>
            <div class="form-group">
                <label for="name" class="form-label">Role Name <span class="required">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}"
                    placeholder="Enter role name (e.g., editor, moderator)"
                    required>
                @if($errors->get('name'))
                <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <div class="form-hint">Role name should be lowercase and descriptive (e.g., content-manager, project-lead)</div>
            </div>
        </div>

        <!-- Permissions Assignment -->
        <div class="form-section">
            <h3 class="section-subtitle">Assign Permissions</h3>

            <div class="permission-controls">
                <button type="button"
                    onclick="selectAllPermissions()"
                    class="btn-select-all">
                    <i class="fas fa-check-double"></i> Select All
                </button>
                <button type="button"
                    onclick="deselectAllPermissions()"
                    class="btn-deselect-all">
                    <i class="fas fa-times"></i> Deselect All
                </button>
            </div>

            @if($permissions->count() > 0)
            <div class="permissions-container {{ $errors->get('permissions') ? 'is-invalid' : '' }}">
                @php
                $groupedPermissions = $permissions->groupBy(function($permission) {
                $parts = explode('-', $permission->name);
                return count($parts) > 1 ? $parts[1] : 'general';
                });
                @endphp

                @foreach($groupedPermissions as $group => $perms)
                <div class="permission-group">
                    <h4 class="permission-group-title">
                        {{ ucfirst(str_replace('-', ' ', $group)) }}
                    </h4>
                    <div class="permission-items">
                        @foreach($perms as $permission)
                        <label class="permission-item">
                            <input type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                class="permission-checkbox"
                                {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                            <span class="permission-label">
                                {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="no-permissions">
                <i class="fas fa-exclamation-triangle"></i>
                <p>No permissions available. Please create permissions first.</p>
            </div>
            @endif

            @if($errors->get('permissions'))
            <div class="form-error">{{ implode(', ', $errors->get('permissions')) }}</div>
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
                        <li>Choose permissions carefully based on what this role should be able to do</li>
                        <li>You can modify permissions later by editing the role</li>
                        <li>Users assigned to this role will inherit all selected permissions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Create Role
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
    function selectAllPermissions() {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAllPermissions() {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-format role name input
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                // Convert to lowercase and replace spaces with hyphens
                e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            });
        }

        // Handle form submission loading state
        const roleForm = document.querySelector('.role-form');
        if (roleForm) {
            roleForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-save');

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Role...';
                submitBtn.disabled = true;
            });
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
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 24px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        ${type === 'success' ? 'background-color: #10b981;' : 'background-color: #ef4444;'}
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
</script>
@endsection