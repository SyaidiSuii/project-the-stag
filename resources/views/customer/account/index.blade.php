@extends('layouts.customer')

@section('title', 'Account - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/account.css') }}">
@endsection

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="header-section" style="display: flex; justify-content: space-between; align-items: center;">
      <h1 class="category-title">My Account</h1>
      @if(!$isGuest && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="margin-left: auto;">
          <i class="fas fa-shield-alt"></i> Admin Panel
        </a>
      @endif
    </div>

    <div style="margin-top: 1rem;">
      @if(session('success'))
        <div style="background: #10b981; color: white; padding: 12px; border-radius: 8px; margin: 10px 0; text-align: center;">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="background: #ef4444; color: white; padding: 12px; border-radius: 8px; margin: 10px 0; text-align: center;">
          {{ session('error') }}
        </div>
      @endif
      @if($errors->any())
        <div style="background: #ef4444; color: white; padding: 12px; border-radius: 8px; margin: 10px 0;">
          <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
      <div class="profile-content">
        <div class="profile-avatar"><i class="fas fa-user"></i></div>
        <div class="profile-info">
          @if($isGuest)
            <h1>Welcome, Guest!</h1>
            <div class="email">Please login to access your account</div>
            <div class="member-since">{{ $memberSince }}</div>
          @else
            <h1>{{ $user->name }}</h1>
            <div class="email">{{ $user->email }}</div>
            <div class="member-since">Member since {{ $memberSince }}</div>
          @endif
        </div>
      </div>
      <div class="profile-stats">
        <div class="stat-item">
          <div class="stat-number">{{ $orderCount }}</div>
          <div class="stat-label">Orders</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ $loyaltyPoints }}</div>
          <div class="stat-label">Points</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">
            @if($isGuest)
              <i class="fas fa-user-plus"></i>
            @else
              <i class="fas fa-star"></i>
            @endif
          </div>
          <div class="stat-label">{{ $membershipLevel }}</div>
        </div>
      </div>
    </div>

    @if($isGuest)
      <!-- Guest User Content -->
      <div class="guest-content">
        <div class="account-card">
          <div class="card-header">
            <div class="card-icon"><i class="fas fa-user-plus"></i></div>
            <h2 class="card-title">Join The Stag SmartDine</h2>
          </div>
          <p style="color: var(--text-2); margin-bottom: 2rem; text-align: center;">
            Please login or create an account to access your profile, track orders, and manage your dining preferences.
          </p>
          <div class="btn-group" style="justify-content: center;">
            <a href="{{ route('login') }}" class="btn btn-primary">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary">
              <i class="fas fa-user-plus"></i> Create Account
            </a>
          </div>
        </div>
      </div>
    @else
      <!-- Content Grid -->
      <div class="content-grid">
      <!-- Personal Information -->
      <div class="account-card">
        <div class="card-header">
          <div class="card-icon"><i class="fas fa-user"></i></div>
          <h2 class="card-title">Personal Information</h2>
        </div>
        <form action="{{ route('customer.account.update') }}" method="POST">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="first_name" value="{{ explode(' ', $user->name)[0] }}" />
            </div>
            <div class="form-group">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" name="last_name" value="{{ count(explode(' ', $user->name)) > 1 ? explode(' ', $user->name)[1] : '' }}" />
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone_number" value="{{ $user->phone_number ?? '' }}" />
            </div>
            <div class="form-group">
              <label for="birthdate">Date of Birth</label>
              <input type="date" id="birthdate" name="date_of_birth" value="{{ $profile && $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '' }}" />
            </div>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Security Settings -->
      <div class="account-card">
        <div class="card-header">
          <div class="card-icon"><i class="fas fa-lock"></i></div>
          <h2 class="card-title">Security</h2>
        </div>
        <!-- Change Password Form -->
        <form id="passwordForm" action="{{ route('customer.account.change-password') }}" method="POST" style="display: none;">
          @csrf
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required />
          </div>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required />
          </div>
          <div class="form-group">
            <label for="new_password_confirmation">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required />
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Password</button>
            <button type="button" class="btn btn-secondary" onclick="togglePasswordForm()"><i class="fas fa-times"></i> Cancel</button>
          </div>
        </form>

        <!-- Forgot Password Form -->
        <form id="forgotPasswordForm" action="{{ route('customer.account.forgot-password') }}" method="POST" style="display: none;">
          @csrf
          <div class="form-group">
            <label for="forgot_email">Email Address</label>
            <input type="email" id="forgot_email" name="email" value="{{ $user->email }}" required readonly />
            <small style="color: var(--text-2); font-size: 0.8rem;">A password reset link will be sent to this email</small>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
            <button type="button" class="btn btn-secondary" onclick="toggleForgotPasswordForm()"><i class="fas fa-times"></i> Cancel</button>
          </div>
        </form>

        <!-- Security Actions -->
        <div id="securityActions">
          <ul class="info-list">
            <li class="info-item">
              <span class="info-label">Password</span>
              <span class="info-value">••••••••</span>
            </li>
            <li class="info-item">
              <span class="info-label">Email Status</span>
              <span class="info-value">
                @if($user->hasVerifiedEmail())
                  <span style="color: var(--success);">✓ Verified</span>
                @else
                  <span style="color: var(--warning);">⚠ Not Verified</span>
                @endif
              </span>
            </li>
          </ul>
          <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="togglePasswordForm()"><i class="fas fa-key"></i> Change Password</button>
            <button type="button" class="btn btn-warning" onclick="toggleForgotPasswordForm()"><i class="fas fa-question-circle"></i> Forgot Password</button>
            @if(!$user->hasVerifiedEmail())
              <a href="{{ route('verification.notice') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Verify Email</a>
            @else
              <button type="button" class="btn btn-success" disabled><i class="fas fa-check"></i> Email Verified</button>
            @endif
          </div>
          <div class="btn-group" style="margin-top: 1rem;">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="account-card" style="border: 2px solid var(--danger);">
      <div class="card-header">
        <div class="card-icon" style="background: var(--danger);"><i class="fas fa-exclamation-triangle"></i></div>
        <h2 class="card-title" style="color: var(--danger);">Danger Zone</h2>
      </div>
      <p style="color: var(--text-2); margin-bottom: 1rem;">
        These actions are permanent and cannot be undone. Please proceed with caution.
      </p>
      <div class="btn-group">
        <button class="btn btn-danger" onclick="showDeleteModal()"><i class="fas fa-trash"></i> Delete Account</button>
        <button class="btn btn-secondary"><i class="fas fa-upload"></i> Export Data</button>
      </div>
    </div>
  </div>

  <!-- Delete Account Modal -->
  <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; margin: 2rem;">
      <h3 style="color: var(--danger); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-exclamation-triangle"></i>
        Delete Account Permanently
      </h3>
      
      <p style="margin-bottom: 1.5rem; color: var(--text-2);">
        This action will permanently delete your account and all associated data including orders, profile, and rewards. This action cannot be undone.
      </p>

      <form id="deleteAccountForm" action="{{ route('customer.account.delete') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="form-group" style="margin-bottom: 1rem;">
          <label for="delete_password" style="font-weight: 700; margin-bottom: 0.5rem; display: block;">Enter your password to confirm:</label>
          <input type="password" id="delete_password" name="password" required 
                 style="width: 100%; padding: 12px; border: 2px solid var(--muted); border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label for="confirm_delete" style="font-weight: 700; margin-bottom: 0.5rem; display: block;">Type "DELETE" to confirm:</label>
          <input type="text" id="confirm_delete" name="confirm_delete" required 
                 style="width: 100%; padding: 12px; border: 2px solid var(--muted); border-radius: 8px;"
                 placeholder="Type DELETE in capital letters">
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">
            <i class="fas fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="fas fa-trash"></i> Delete My Account
          </button>
        </div>
      </form>
    </div>
  </div>
  @endif
@endsection

@section('scripts')
<script src="{{ asset('js/customer/account.js') }}"></script>
<script>
function togglePasswordForm() {
    const passwordForm = document.getElementById('passwordForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const securityActions = document.getElementById('securityActions');
    
    if (passwordForm.style.display === 'block') {
        // Currently showing password form, hide it
        passwordForm.style.display = 'none';
        securityActions.style.display = 'block';
        passwordForm.reset();
    } else {
        // Show password form, hide others
        passwordForm.style.display = 'block';
        forgotPasswordForm.style.display = 'none';
        securityActions.style.display = 'none';
    }
}

function toggleForgotPasswordForm() {
    const passwordForm = document.getElementById('passwordForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const securityActions = document.getElementById('securityActions');
    
    if (forgotPasswordForm.style.display === 'block') {
        // Currently showing forgot password form, hide it
        forgotPasswordForm.style.display = 'none';
        securityActions.style.display = 'block';
        forgotPasswordForm.reset();
    } else {
        // Show forgot password form, hide others
        forgotPasswordForm.style.display = 'block';
        passwordForm.style.display = 'none';
        securityActions.style.display = 'none';
    }
}

// Delete Account Modal Functions
function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.getElementById('deleteAccountForm').reset();
}

// Close modal when clicking outside
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('click', function(e) {
        if (e.target === this) {
            hideDeleteModal();
        }
    });
}

// Form validation for delete confirmation
const deleteAccountForm = document.getElementById('deleteAccountForm');
if (deleteAccountForm) {
    deleteAccountForm.addEventListener('submit', function(e) {
        const confirmInput = document.getElementById('confirm_delete').value;
        const passwordInput = document.getElementById('delete_password').value;

        if (confirmInput !== 'DELETE') {
            e.preventDefault();
            alert('Please type "DELETE" in capital letters to confirm account deletion.');
            return false;
        }

        if (!passwordInput) {
            e.preventDefault();
            alert('Please enter your password to confirm account deletion.');
            return false;
        }

        if (!confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('confirmDeleteBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting Account...';
    });
}
</script>
@endsection