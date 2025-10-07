@extends('layouts.admin')

@section('title', 'Settings - The Stag')

@section('page-title', 'Settings')

@section('styles')
<style>
    /* Settings specific styles */
    .settings-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
    }

    .settings-sidebar {
        background: white;
        border-radius: var(--radius, 12px);
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        height: fit-content;
    }

    .settings-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: var(--radius, 12px);
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #64748b;
        font-weight: 500;
    }

    .settings-nav-item:hover {
        background: #f8fafc;
        color: #6366f1;
    }

    .settings-nav-item.active {
        background: #f8fafc;
        color: #6366f1;
        font-weight: 600;
    }

    .settings-content {
        background: white;
        border-radius: var(--radius, 12px);
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .settings-section {
        margin-bottom: 32px;
    }

    .settings-section-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-bottom: 20px;
    }

    /* User/Admin list styles */
    .user-list {
        margin-top: 20px;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 16px;
        background: #f8fafc;
        border-radius: var(--radius, 12px);
        margin-bottom: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .user-email {
        font-size: 14px;
        color: #64748b;
    }

    .user-role {
        display: inline-block;
        padding: 4px 8px;
        background: #e2e8f0;
        border-radius: 20px;
        font-size: 12px;
        margin-left: 8px;
    }

    .user-actions {
        display: flex;
        gap: 8px;
    }

    /* Modal styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(30, 41, 59, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(4px);
    }

    .modal {
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: modal-fade-in 0.3s ease;
    }

    @keyframes modal-fade-in {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #94a3b8;
        line-height: 1;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s ease;
    }

    .modal-close:hover {
        background: #f8fafc;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 0 0 12px 12px;
    }

    /* Form styles */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input.error,
    .form-select.error {
        border-color: #ef4444;
    }

    .form-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .form-help {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid;
        font-size: 14px;
    }

    .alert-info {
        background-color: #dbeafe;
        border-color: #bfdbfe;
        color: #1e40af;
    }

    .input-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #64748b;
    }

    .password-toggle:hover {
        color: #6366f1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .settings-container {
            grid-template-columns: 1fr;
        }

        .settings-sidebar {
            display: flex;
            overflow-x: auto;
            gap: 12px;
            padding: 16px;
        }

        .settings-nav-item {
            flex-shrink: 0;
            margin-bottom: 0;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <!-- Settings Sidebar -->
    <div class="settings-sidebar">
        <div class="settings-nav-item active" data-section="users">
            <i class="fas fa-user-shield"></i>
            <span>Admin Management</span>
        </div>
        <!-- Add more settings sections here in the future -->
    </div>

    <!-- Settings Content -->
    <div class="settings-content">
        <!-- Admin Management Section -->
        <div class="settings-section" id="users-section" style="display: block;">
            <h2 class="settings-section-title">Admin Management</h2>

            @if($currentUser->is_super_admin)
                <div class="form-actions">
                    <button class="admin-btn btn-primary" id="addUserBtn">
                        <i class="fas fa-user-plus"></i>
                        Add New Admin
                    </button>
                </div>
            @endif

            <div class="user-list" id="adminList">
                @forelse($admins as $admin)
                    <div class="user-item" data-admin-id="{{ $admin->id }}">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name">
                                {{ $admin->name }}
                                <span class="user-role">{{ $admin->id === $currentUser->id ? 'Me' : ($admin->is_super_admin ? 'Restaurant Manager' : 'Admin') }}</span>
                            </div>
                            <div class="user-email">{{ $admin->email }}</div>
                            @if($admin->phone_number)
                                <div class="user-phone" style="font-size: 0.85rem; color: #6c757d;">{{ $admin->phone_number }}</div>
                            @endif
                        </div>
                        <div class="user-actions">
                            @if($currentUser->is_super_admin || $currentUser->id === $admin->id)
                                <button class="admin-btn btn-icon edit-admin" data-id="{{ $admin->id }}" title="Edit Admin">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endif

                            @if($currentUser->is_super_admin && $admin->id !== $currentUser->id)
                                <button class="admin-btn btn-icon delete-admin" data-id="{{ $admin->id }}" title="Delete Admin">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-users" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <p>No admins found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Admin Modal -->
<div class="modal-overlay" id="adminModal" role="dialog" aria-labelledby="adminModalTitle" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="adminModalTitle">Add New Admin</h3>
            <button class="modal-close" id="closeAdminModal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info" id="adminPermissionNotice" style="margin-bottom: 1.5rem; display: none;">
                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                The main admin will be able to edit this account after creation. Other admins can only edit their own accounts.
            </div>
            <form id="adminForm" novalidate>
                <input type="hidden" id="adminId">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="adminName" class="form-label">Full Name</label>
                        <input type="text" id="adminName" class="form-input" placeholder="John Doe" required>
                        <div class="form-error" id="adminName-error">Please enter a full name</div>
                    </div>

                    <div class="form-group">
                        <label for="adminEmail" class="form-label">Email</label>
                        <input type="email" id="adminEmail" class="form-input" placeholder="john@example.com" required>
                        <div class="form-error" id="adminEmail-error">Please enter a valid email</div>
                    </div>

                    <div class="form-group">
                        <label for="adminPhone" class="form-label">Phone Number <small>(Optional)</small></label>
                        <input type="tel" id="adminPhone" class="form-input" placeholder="+60 12 345 6789">
                        <div class="form-error" id="adminPhone-error">Please enter a valid phone number</div>
                    </div>

                    <div class="form-group">
                        <label for="adminDateOfBirth" class="form-label">Date of Birth <small>(Optional)</small></label>
                        <input type="date" id="adminDateOfBirth" class="form-input">
                        <div class="form-error" id="adminDateOfBirth-error">Please enter a valid date</div>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="adminPassword" class="form-input" placeholder="••••••••" required>
                            <i class="fas fa-eye password-toggle" data-target="adminPassword" title="Show/Hide Password"></i>
                        </div>
                        <div class="form-error" id="adminPassword-error">Password must be at least 8 characters</div>
                        <small class="form-help" id="passwordHelp">Minimum 8 characters. Admin will be required to change password on first login.</small>
                    </div>

                    <div class="form-group">
                        <label for="adminPasswordConfirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="adminPasswordConfirmation" class="form-input" placeholder="••••••••" required>
                            <i class="fas fa-eye password-toggle" data-target="adminPasswordConfirmation" title="Show/Hide Password"></i>
                        </div>
                        <div class="form-error" id="adminPasswordConfirmation-error">Passwords must match</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="admin-btn btn-secondary" id="cancelAdminBtn">Cancel</button>
            <button type="submit" class="admin-btn btn-primary" form="adminForm" id="saveAdminBtn">
                Save Admin
                <span class="spinner" style="display: none;"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentEditId = null;

    // Modal management
    const adminModal = document.getElementById('adminModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeAdminModal = document.getElementById('closeAdminModal');
    const cancelAdminBtn = document.getElementById('cancelAdminBtn');
    const saveAdminBtn = document.getElementById('saveAdminBtn');
    const adminForm = document.getElementById('adminForm');
    const passwordHelp = document.getElementById('passwordHelp');
    const permissionNotice = document.getElementById('adminPermissionNotice');

    // Password toggle functionality
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetInput = document.getElementById(targetId);

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });

    // Open modal for adding
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            showModal();
        });
    }

    // Show modal function
    function showModal(admin = null) {
        const title = document.getElementById('adminModalTitle');

        if (admin) {
            // Edit mode
            currentEditId = admin.id;
            title.textContent = 'Edit Admin';
            document.getElementById('adminName').value = admin.name;
            document.getElementById('adminEmail').value = admin.email;
            document.getElementById('adminPhone').value = admin.phone || '';
            document.getElementById('adminDateOfBirth').value = admin.date_of_birth || '';
            document.getElementById('adminPassword').value = '';
            document.getElementById('adminPasswordConfirmation').value = '';
            passwordHelp.textContent = 'Leave blank to keep current password';
            document.getElementById('adminPassword').required = false;
            document.getElementById('adminPasswordConfirmation').required = false;
            permissionNotice.style.display = 'none';
        } else {
            // Add mode
            currentEditId = null;
            title.textContent = 'Add New Admin';
            adminForm.reset();
            passwordHelp.textContent = 'Minimum 8 characters. Admin will be required to change password on first login.';
            document.getElementById('adminPassword').required = true;
            document.getElementById('adminPasswordConfirmation').required = true;
            permissionNotice.style.display = 'block';
        }

        adminModal.style.display = 'flex';
        adminModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Focus the first input field
        setTimeout(() => {
            document.getElementById('adminName').focus();
        }, 100);
    }

    // Hide modal function
    function hideModal() {
        adminModal.style.display = 'none';
        adminModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        adminForm.reset();
        clearFormErrors();
        currentEditId = null;
    }

    // Close modal handlers
    closeAdminModal.addEventListener('click', hideModal);
    cancelAdminBtn.addEventListener('click', hideModal);

    adminModal.addEventListener('click', function(e) {
        if (e.target === adminModal) {
            hideModal();
        }
    });

    // Edit admin
    document.querySelectorAll('.edit-admin').forEach(btn => {
        btn.addEventListener('click', function() {
            const adminId = this.dataset.id;
            const adminItem = this.closest('.user-item');
            const nameElement = adminItem.querySelector('.user-name');
            const name = nameElement.childNodes[0].textContent.trim();
            const email = adminItem.querySelector('.user-email').textContent;
            const phoneElement = adminItem.querySelector('.user-phone');
            const phone = phoneElement ? phoneElement.textContent : '';

            const admin = {
                id: adminId,
                name: name,
                email: email,
                phone: phone
            };

            showModal(admin);
        });
    });

    // Save admin
    saveAdminBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        clearFormErrors();

        const name = document.getElementById('adminName').value.trim();
        const email = document.getElementById('adminEmail').value.trim();
        const phone = document.getElementById('adminPhone').value.trim();
        const dateOfBirth = document.getElementById('adminDateOfBirth').value;
        const password = document.getElementById('adminPassword').value;
        const passwordConfirmation = document.getElementById('adminPasswordConfirmation').value;

        // Validation
        let hasError = false;

        if (!name) {
            showError('adminName', 'Please enter a full name');
            hasError = true;
        }

        if (!email || !isValidEmail(email)) {
            showError('adminEmail', 'Please enter a valid email');
            hasError = true;
        }

        if (!currentEditId && !password) {
            showError('adminPassword', 'Password is required');
            hasError = true;
        }

        if (password && password.length < 8) {
            showError('adminPassword', 'Password must be at least 8 characters');
            hasError = true;
        }

        if (password && password !== passwordConfirmation) {
            showError('adminPasswordConfirmation', 'Passwords must match');
            hasError = true;
        }

        if (hasError) return;

        // Disable button
        saveAdminBtn.disabled = true;
        const spinner = saveAdminBtn.querySelector('.spinner');
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const url = currentEditId
                ? `/admin/settings/admins/${currentEditId}`
                : '/admin/settings/admins';

            const method = currentEditId ? 'PUT' : 'POST';

            const data = {
                name: name,
                email: email,
                phone_number: phone,
                date_of_birth: dateOfBirth,
                role: 'admin'
            };

            if (password) {
                data.password = password;
                data.password_confirmation = passwordConfirmation;
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message || 'An error occurred');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving the admin');
        } finally {
            saveAdminBtn.disabled = false;
            if (spinner) spinner.style.display = 'none';
        }
    });

    // Delete admin
    document.querySelectorAll('.delete-admin').forEach(btn => {
        btn.addEventListener('click', async function() {
            const adminId = this.dataset.id;
            const adminItem = this.closest('.user-item');
            const nameElement = adminItem.querySelector('.user-name');
            const adminName = nameElement.childNodes[0].textContent.trim();

            if (!confirm(`Are you sure you want to delete ${adminName}? This action cannot be undone.`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/settings/admins/${adminId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message || 'An error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the admin');
            }
        });
    });

    // Helper functions
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const error = document.getElementById(`${fieldId}-error`);
        if (field && error) {
            field.classList.add('error');
            error.textContent = message;
            error.style.display = 'block';
        }
    }

    function clearFormErrors() {
        document.querySelectorAll('.form-error').forEach(error => {
            error.style.display = 'none';
        });
        document.querySelectorAll('.form-input, .form-select').forEach(input => {
            input.classList.remove('error');
        });
    }
});
</script>
@endsection
