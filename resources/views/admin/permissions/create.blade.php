@extends('layouts.admin')

@section('title', 'Create New Permission')
@section('page-title', 'Create New Permission')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/role_permission.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New Permission</h2>
        <a href="{{ route('admin.permissions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Permissions
        </a>
    </div>

    <form method="POST" action="{{ route('admin.permissions.store') }}" class="permission-form">
        @csrf

        <!-- Basic Permission Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Basic Permission Information</h3>
            
            <div class="form-group">
                <label for="name" class="form-label">Permission Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control {{ $errors->get('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}"
                    placeholder="Enter permission name (e.g., view-users, create-posts)"
                    required>
                @if($errors->get('name'))
                    <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <div class="form-hint">Permission name should be lowercase with hyphens (e.g., view-users, edit-posts, delete-comments)</div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control {{ $errors->get('description') ? 'is-invalid' : '' }}"
                    rows="3"
                    placeholder="Describe what this permission allows (optional)">{{ old('description') }}</textarea>
                @if($errors->get('description'))
                    <div class="form-error">{{ implode(', ', $errors->get('description')) }}</div>
                @endif
                <div class="form-hint">Optional description to explain what this permission grants access to</div>
            </div>
        </div>

        <!-- Permission Examples -->
        <div class="form-section">
            <h3 class="section-subtitle">Common Permission Examples</h3>
            <div class="examples-grid">
                <div class="example-category">
                    <h4 class="example-title">User Management</h4>
                    <ul class="example-list">
                        <li><code>view-users</code> - View user listings</li>
                        <li><code>create-users</code> - Create new users</li>
                        <li><code>edit-users</code> - Edit user information</li>
                        <li><code>delete-users</code> - Delete users</li>
                    </ul>
                </div>
                <div class="example-category">
                    <h4 class="example-title">Content Management</h4>
                    <ul class="example-list">
                        <li><code>view-posts</code> - View all posts</li>
                        <li><code>create-posts</code> - Create new posts</li>
                        <li><code>edit-posts</code> - Edit existing posts</li>
                        <li><code>publish-posts</code> - Publish/unpublish posts</li>
                    </ul>
                </div>
                <div class="example-category">
                    <h4 class="example-title">System Administration</h4>
                    <ul class="example-list">
                        <li><code>view-settings</code> - View system settings</li>
                        <li><code>edit-settings</code> - Modify system settings</li>
                        <li><code>view-logs</code> - Access system logs</li>
                        <li><code>manage-backups</code> - Handle system backups</li>
                    </ul>
                </div>
            </div>
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
                        <li>Permission names must be unique across the entire system</li>
                        <li>Use descriptive, lowercase names with hyphens (kebab-case)</li>
                        <li>Follow a consistent naming pattern: action-resource (e.g., view-users)</li>
                        <li>Once created, permissions can be assigned to roles and then to users</li>
                        <li>Consider the security implications of each permission carefully</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Create Permission
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Permission...';
            submitBtn.disabled = true;
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