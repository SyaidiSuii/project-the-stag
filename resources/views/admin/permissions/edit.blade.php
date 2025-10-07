@extends('layouts.admin')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Edit Permission</h2>
        <a href="{{ route('admin.permissions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Permissions
        </a>
    </div>

    <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}" class="permission-form">
        @csrf
        @method('PUT')

        <!-- Basic Permission Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Permission Information</h3>

            <div class="form-group">
                <label for="name" class="form-label">Permission Name <span class="required">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $permission->name) }}"
                    placeholder="Enter permission name (e.g., view-users, create-posts)"
                    required>
                @if($errors->get('name'))
                <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <div class="form-hint">Permission name should be lowercase with hyphens (e.g., view-users, edit-posts, delete-comments)</div>
            </div>
        </div>

        <!-- Current Permission Info -->
        <div class="form-section">
            <h3 class="section-subtitle">Current Permission Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Permission ID:</span>
                    <span class="info-value">#{{ $permission->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Guard Name:</span>
                    <span class="info-value">{{ $permission->guard_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created:</span>
                    <span class="info-value">{{ $permission->created_at->format('M d, Y h:i A') }}</span>
                </div>
                @if($permission->updated_at != $permission->created_at)
                <div class="info-item">
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value">{{ $permission->updated_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Assigned Roles Information -->
        @if($permission->roles->count() > 0)
        <div class="form-section">
            <h3 class="section-subtitle">Currently Assigned to Roles</h3>
            <div class="assigned-roles">
                @foreach($permission->roles as $role)
                <span class="assigned-role-badge">{{ ucfirst($role->name) }}</span>
                @endforeach
            </div>
            <div class="form-hint">This permission is currently assigned to {{ $permission->roles->count() }} role(s)</div>
        </div>
        @endif

        <!-- Warning for Core Permissions -->
        @if(in_array($permission->name, ['view-users', 'create-users', 'edit-users', 'delete-users', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles']))
        <div class="form-section">
            <div class="warning-panel">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="warning-content">
                    <h4 class="warning-title">Core System Permission</h4>
                    <p class="warning-text">This is a core system permission. Changing its name may affect system functionality. Please proceed with caution.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Important Notes -->
        <div class="form-section">
            <div class="info-panel">
                <div class="info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="info-content">
                    <h4 class="info-title">Important Notes</h4>
                    <ul class="info-list">
                        <li>Permission names must be unique across the entire system</li>
                        <li>Changing the permission name will affect all roles and users that have this permission</li>
                        <li>Use descriptive, lowercase names with hyphens (kebab-case)</li>
                        <li>Changes will be cached and may take a moment to take effect</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Permission
            </button>
            <a href="{{ route('admin.permissions.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-format permission name input
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                // Convert to lowercase and replace spaces with hyphens
                e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            });
        }

        // Handle form submission loading state
        const permissionForm = document.querySelector('.permission-form');
        if (permissionForm) {
            permissionForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-save');

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Permission...';
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