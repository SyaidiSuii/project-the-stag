@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Edit Role</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="role-form">
        @csrf
        @method('PUT')

        <!-- Basic Role Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Role Information</h3>
            
            <div class="form-group">
                <label for="name" class="form-label">Role Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $role->name) }}"
                    placeholder="Enter role name (e.g., manager, editor, viewer)"
                    required>
                @if($errors->get('name'))
                    <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <div class="form-hint">Role name should be descriptive and lowercase (e.g., admin, manager, customer)</div>
            </div>
        </div>

        <!-- Current Role Info -->
        <div class="form-section">
            <h3 class="section-subtitle">Current Role Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Role ID:</span>
                    <span class="info-value">#{{ $role->id }}</span>
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
            </div>
        </div>

        <!-- Permissions Assignment -->
        <div class="form-section">
            <h3 class="section-subtitle">Assign Permissions</h3>
            <div class="permissions-grid">
                @foreach($permissions as $permission)
                    <div class="permission-item">
                        <label class="permission-label">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $permission->id }}"
                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                class="permission-checkbox">
                            <span class="permission-name">{{ $permission->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="form-hint">Select all permissions that should be assigned to this role</div>
        </div>

        <!-- Currently Assigned Users -->
        @if($role->users->count() > 0)
        <div class="form-section">
            <h3 class="section-subtitle">Users with this Role</h3>
            <div class="assigned-users">
                @foreach($role->users as $user)
                    <span class="assigned-user-badge">{{ $user->name }}</span>
                @endforeach
            </div>
            <div class="form-hint">This role is currently assigned to {{ $role->users->count() }} user(s)</div>
        </div>
        @endif

        <!-- Warning for Core Roles -->
        @if(in_array($role->name, ['admin', 'manager', 'customer']))
        <div class="form-section">
            <div class="warning-panel">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="warning-content">
                    <h4 class="warning-title">Core System Role</h4>
                    <p class="warning-text">This is a core system role. Changing its name may affect system functionality. Please proceed with caution.</p>
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
                        <li>Role names must be unique across the entire system</li>
                        <li>Changing the role name will affect all users that have this role</li>
                        <li>Use descriptive, lowercase names (e.g., admin, manager, customer)</li>
                        <li>Permission changes will be cached and may take a moment to take effect</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Role
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
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format role name input
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            // Convert to lowercase and remove special characters
            e.target.value = e.target.value.toLowerCase().replace(/[^a-z0-9]/g, '');
        });
    }

    // Handle form submission loading state
    const roleForm = document.querySelector('.role-form');
    if (roleForm) {
        roleForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-save');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Role...';
            submitBtn.disabled = true;
        });
    }

    // Select all permissions functionality
    const selectAllBtn = document.getElementById('select-all-permissions');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            
            this.textContent = allChecked ? 'Select All' : 'Deselect All';
        });
    }

    // Show success/error messages
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
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