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

    <!-- Modern Toast Notifications Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 400px;"></div>

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
      @if($isGuest)
      <!-- Guest Join Section -->
      <div class="profile-stats" style="flex-direction: column; align-items: stretch; gap: 1rem; position: relative; z-index: 10;">
        <div style="text-align: center; padding: 1rem; position: relative; z-index: 10;">
          <h2 style="color: white; font-size: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">
            <i class="fas fa-users"></i> JOIN THE STAG SMARTDINE
          </h2>
          <p style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.5;">
            Please login or create an account to access your profile, track orders, and manage your dining preferences.
          </p>
          <div class="btn-group" style="justify-content: center; gap: 1rem;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="background: white; color: #6366f1; border: 2px solid white; font-weight: 600; position: relative; z-index: 100; cursor: pointer;">
              <i class="fas fa-sign-in-alt"></i> LOGIN
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary" style="background: transparent; color: white; border: 2px solid white; font-weight: 600; position: relative; z-index: 100; cursor: pointer;">
              <i class="fas fa-user-plus"></i> CREATE ACCOUNT
            </a>
          </div>
        </div>
      </div>
      @else
      <!-- User Stats -->
      <div class="profile-stats">
        <div class="stat-item">
          <div class="stat-number">{{ $orderCount }}</div>
          <div class="stat-label">Orders</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ number_format($user->points_balance ?? 0) }}</div>
          <div class="stat-label">Points</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><i class="fas fa-star"></i></div>
          <div class="stat-label">{{ $membershipLevel }}</div>
        </div>
      </div>
      @endif
    </div>

    @if(!$isGuest)

      <!-- Quick Actions -->
    <div class="quick-actions">
      <div class="card-header">
        <div class="card-icon"><i class="fas fa-bolt"></i></div>
        <h2 class="card-title">Quick Actions</h2>
      </div>
      <div class="quick-actions-grid">
        <a href="{{ route('customer.orders.index') }}" class="quick-action">
          <div class="quick-action-icon"><i class="fas fa-shopping-bag"></i></div>
          <div class="quick-action-text">View Orders</div>
        </a>
        <a href="{{ route('customer.booking.index') }}" class="quick-action">
          <div class="quick-action-icon"><i class="fas fa-calendar-alt"></i></div>
          <div class="quick-action-text">Book Table</div>
        </a>
        <a href="{{ route('customer.rewards.index') }}" class="quick-action">
          <div class="quick-action-icon"><i class="fas fa-gift"></i></div>
          <div class="quick-action-text">My Rewards</div>
        </a>
        @if(false)
        {{-- Rating feature disabled --}}
        <a href="#" data-disabled-route="customer.reviews.my-reviews" class="quick-action" style="display: none;">
          <div class="quick-action-icon"><i class="fas fa-star"></i></div>
          <div class="quick-action-text">My Reviews</div>
        </a>
        @endif
      </div>
    </div>

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
            <button type="submit" class="btn btn-primary" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Saving...'; this.disabled=true;"><i class="fas fa-save"></i> Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Account Settings -->
      <div class="account-card">
        <div class="card-header">
          <div class="card-icon"><i class="fas fa-cog"></i></div>
          <h2 class="card-title">Preferences</h2>
        </div>
        {{-- <div class="toggle-group">
          <div class="toggle-info">
            <div class="toggle-label">Email Notifications</div>
            <div class="toggle-description">Receive order updates and promotions</div>
          </div>
          <div class="toggle-switch active" data-toggle="email">
            <div class="toggle-slider"></div>
          </div>
        </div>
        <div class="toggle-group">
          <div class="toggle-info">
            <div class="toggle-label">SMS Notifications</div>
            <div class="toggle-description">Get text updates for orders</div>
          </div>
          <div class="toggle-switch" data-toggle="sms">
            <div class="toggle-slider"></div>
          </div>
        </div>
        <div class="toggle-group">
          <div class="toggle-info">
            <div class="toggle-label">Marketing Communications</div>
            <div class="toggle-description">Special offers and restaurant news</div>
          </div>
          <div class="toggle-switch active" data-toggle="marketing">
            <div class="toggle-slider"></div>
          </div>
        </div> --}}
        <div class="toggle-group">
          <div class="toggle-info">
            <div class="toggle-label">Push Notifications</div>
            <div class="toggle-description">Get instant order updates (preparing, ready, completed)</div>
          </div>
          <div class="toggle-switch" data-toggle="push-notifications" id="push-notification-toggle">
            <div class="toggle-slider"></div>
          </div>
        </div>
        {{-- <div class="toggle-group">
          <div class="toggle-info">
            <div class="toggle-label">Dark Mode</div>
            <div class="toggle-description">Switch to dark theme</div>
          </div>
          <div class="toggle-switch" data-toggle="darkmode">
            <div class="toggle-slider"></div>
          </div>
        </div> --}}
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
            <div class="input-group" style="position: relative;">
              <input type="password" id="current_password" name="current_password" required />
            </div>
          </div>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <div class="input-group" style="position: relative;">
              <input type="password" id="new_password" name="new_password" required />
            </div>
          </div>
          <div class="form-group">
            <label for="new_password_confirmation">Confirm New Password</label>
            <div class="input-group" style="position: relative;">
              <input type="password" id="new_password_confirmation" name="new_password_confirmation" required />
            </div>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Updating...'; this.disabled=true;"><i class="fas fa-save"></i> Update Password</button>
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
            <form action="{{ route('logout') }}" method="POST" style="display: inline;" id="logoutForm">
              @csrf
              <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Account Statistics -->
      <div class="account-card">
        <div class="card-header">
          <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
          <h2 class="card-title">Account Overview</h2>
        </div>
        <ul class="info-list">
          <li class="info-item">
            <span class="info-label">Total Orders</span>
            <span class="info-value">{{ $orderCount }}</span>
          </li>
          <li class="info-item">
            <span class="info-label">Total Spent</span>
            <span class="info-value">RM {{ number_format($totalSpent, 2) }}</span>
          </li>
          <li class="info-item">
            <span class="info-label">Favorite Dish</span>
            <span class="info-value">{{ $favoriteDish }}</span>
          </li>
          <li class="info-item">
            <span class="info-label">Member Level</span>
            <span class="info-value">{{ $membershipLevel }}</span>
          </li>
          <li class="info-item">
            <span class="info-label">Loyalty Points</span>
            <span class="info-value">{{ $loyaltyPoints }} pts</span>
          </li>
        </ul>

        @php
            // FIXED: Same tier calculation logic as rewards page
            $tierService = app(\App\Services\Loyalty\TierService::class);
            $currentTier = $tierService->calculateEligibleTier($user);
            $currentPoints = $user->points_balance ?? 0;

            // Initialize variables
            $progress = 0;
            $nextTier = null;
            $amountNeeded = 0;
            $statusMessage = '';

            // Find next tier in hierarchy
            if ($currentTier) {
                $nextTier = \App\Models\LoyaltyTier::where('order', '>', $currentTier->order)
                    ->where('is_active', true)
                    ->orderBy('order', 'asc')
                    ->first();

                if ($nextTier) {
                    // Calculate progress from current tier to next tier
                    $currentThreshold = $currentTier->points_threshold ?? 0;
                    $nextThreshold = $nextTier->points_threshold ?? 0;
                    $rangeSize = $nextThreshold - $currentThreshold;

                    if ($rangeSize > 0) {
                        $pointsInRange = $currentPoints - $currentThreshold;
                        $progress = min(100, ($pointsInRange / $rangeSize) * 100);
                    } else {
                        $progress = 100;
                    }

                    $amountNeeded = max(0, $nextThreshold - $currentPoints);
                    $statusMessage = "Earn {$amountNeeded} more points to reach {$nextTier->name}";
                } else {
                    // Max tier reached
                    $progress = 100;
                    $statusMessage = 'Maximum level achieved!';
                }
            } else {
                // No tier yet - find first tier
                $firstTier = \App\Models\LoyaltyTier::where('is_active', true)
                    ->orderBy('order', 'asc')
                    ->first();

                if ($firstTier) {
                    $nextTier = $firstTier;
                    $progress = min(100, ($currentPoints / ($firstTier->points_threshold ?: 1)) * 100);
                    $amountNeeded = max(0, $firstTier->points_threshold - $currentPoints);
                    $statusMessage = "Earn {$amountNeeded} more points to reach {$firstTier->name}";
                } else {
                    $statusMessage = 'No tiers available';
                }
            }
        @endphp

        @if($currentTier || $nextTier)
        <!-- Tier Progress Section -->
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.875rem; color: #64748b; font-weight: 600;">Tier Progress</span>
            <span style="font-size: 0.875rem; color: #475569; font-weight: 700;">{{ number_format($progress, 1) }}%</span>
          </div>

          <!-- Progress Bar -->
          <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 9999px; overflow: hidden; margin-bottom: 0.75rem;">
            <div style="height: 100%; background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%); border-radius: 9999px; transition: width 0.5s ease; width: {{ $progress }}%;"></div>
          </div>

          <p style="font-size: 0.8125rem; color: #64748b; margin: 0;">
            {{ $statusMessage }}
          </p>
        </div>
        @endif

        <div class="btn-group" style="margin-top: 1rem;">
          <a href="{{ route('customer.orders.index') }}" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> View All Orders</a>
        </div>
      </div>
    </div>

      <!-- Danger Zone -->
      <div class="account-card" style="border: 2px solid var(--danger); grid-column: 1 / -1; margin-top: 2rem;">
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
          <div class="input-group" style="position: relative;">
            <input type="password" id="delete_password" name="password" required
                   style="width: 100%; padding: 12px; border: 2px solid var(--muted); border-radius: 8px;">
          </div>
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
// Modern Toast Notification System
function showToast(message, type = 'success', duration = 5000) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    // Icon based on type
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    // Colors based on type
    const colors = {
        success: { bg: '#10b981', icon: '#059669' },
        error: { bg: '#ef4444', icon: '#dc2626' },
        warning: { bg: '#f59e0b', icon: '#d97706' },
        info: { bg: '#3b82f6', icon: '#2563eb' }
    };

    const color = colors[type] || colors.info;
    const icon = icons[type] || icons.info;

    toast.style.cssText = `
        background: white;
        border-left: 4px solid ${color.bg};
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out, fadeOut 0.3s ease-out ${duration - 300}ms forwards;
        position: relative;
        overflow: hidden;
    `;

    toast.innerHTML = `
        <div style="
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: ${color.bg}15;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        ">
            <i class="fas ${icon}" style="color: ${color.icon}; font-size: 18px;"></i>
        </div>
        <div style="flex: 1; color: #1e293b; font-size: 14px; font-weight: 500; line-height: 1.5;">
            ${message}
        </div>
        <button onclick="this.parentElement.remove()" style="
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
            flex-shrink: 0;
        " onmouseover="this.style.background='#f1f5f9'; this.style.color='#475569';" onmouseout="this.style.background='none'; this.style.color='#94a3b8';">
            ×
        </button>
        <div style="
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: ${color.bg};
            width: 100%;
            animation: shrink ${duration}ms linear forwards;
        "></div>
    `;

    container.appendChild(toast);

    // Auto remove after duration
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, duration);
}

// Add animations to page
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }

        @keyframes shrink {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
    `;
    document.head.appendChild(style);
}

// Show notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast("{{ $error }}", 'error');
        @endforeach
    @endif
});

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

// Logout confirmation with toast
function confirmLogout() {
    const logoutForm = document.getElementById('logoutForm');
    const button = event.target.closest('button');

    // Create custom confirmation toast
    const container = document.getElementById('toast-container');
    const confirmToast = document.createElement('div');

    confirmToast.style.cssText = `
        background: white;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        min-width: 350px;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out;
    `;

    confirmToast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #f59e0b15;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            ">
                <i class="fas fa-sign-out-alt" style="color: #d97706; font-size: 18px;"></i>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Confirm Logout</div>
                <div style="font-size: 14px; color: #64748b;">Are you sure you want to logout?</div>
            </div>
        </div>
        <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <button onclick="this.closest('div').parentElement.remove()" style="
                padding: 8px 16px;
                background: #e2e8f0;
                border: none;
                border-radius: 8px;
                color: #475569;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            " onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">
                Cancel
            </button>
            <button onclick="document.getElementById('logoutForm').submit()" style="
                padding: 8px 16px;
                background: #ef4444;
                border: none;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            " onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                Logout
            </button>
        </div>
    `;

    container.appendChild(confirmToast);

    // Auto remove after 10 seconds if no action
    setTimeout(() => {
        if (confirmToast.parentElement) {
            confirmToast.remove();
        }
    }, 10000);
}

// Form validation for delete confirmation
const deleteAccountForm = document.getElementById('deleteAccountForm');
if (deleteAccountForm) {
    deleteAccountForm.addEventListener('submit', function(e) {
        const confirmInput = document.getElementById('confirm_delete').value;
        const passwordInput = document.getElementById('delete_password').value;

        if (confirmInput !== 'DELETE') {
            e.preventDefault();
            showToast('Please type "DELETE" in capital letters to confirm account deletion.', 'error');
            return false;
        }

        if (!passwordInput) {
            e.preventDefault();
            showToast('Please enter your password to confirm account deletion.', 'error');
            return false;
        }

        e.preventDefault();

        // Use modern confirmation modal
        if (typeof showConfirm === 'function') {
            showConfirm(
                'Delete Account?',
                'Are you absolutely sure you want to delete your account? This action cannot be undone.',
                'danger',
                'Delete Account',
                'Cancel'
            ).then(confirmed => {
                if (confirmed) {
                    // Show loading state
                    const submitBtn = document.getElementById('confirmDeleteBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                    }
                    // Submit form
                    e.target.submit();
                }
            });
        } else {
            // Fallback to native confirm
            if (confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
                const submitBtn = document.getElementById('confirmDeleteBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                }
                e.target.submit();
            }
        }
    });
}

// Custom password toggle for customer account page (eye icon only, no lock icon)
document.addEventListener('DOMContentLoaded', function() {
    const passwordFields = document.querySelectorAll('#current_password, #new_password, #new_password_confirmation, #delete_password');

    passwordFields.forEach(function(passwordInput) {
        const inputGroup = passwordInput.closest('.input-group');

        if (!inputGroup || inputGroup.querySelector('.password-toggle')) return;

        // Create toggle button (eye icon only)
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'password-toggle';
        toggleButton.setAttribute('aria-label', 'Toggle password visibility');
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';

        // Add styles - positioned at the end with proper spacing
        toggleButton.style.cssText = `
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            font-size: 18px;
            padding: 4px;
            transition: color 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // Hover effect - simple color change to blue
        toggleButton.addEventListener('mouseenter', function() {
            this.style.color = '#6366f1';
        });

        toggleButton.addEventListener('mouseleave', function() {
            this.style.color = '#64748b';
        });

        // Toggle functionality
        toggleButton.addEventListener('click', function() {
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.setAttribute('aria-label', 'Show password');
            }
        });

        // Add padding to input for the eye icon
        const currentPaddingRight = window.getComputedStyle(passwordInput).paddingRight;
        const currentPadding = parseInt(currentPaddingRight) || 12;
        passwordInput.style.paddingRight = (currentPadding + 40) + 'px';

        // Append toggle button to input group
        inputGroup.appendChild(toggleButton);
    });

    // ==========================================
    // Push Notification Toggle Handler
    // ==========================================
    const pushToggle = document.getElementById('push-notification-toggle');

    if (pushToggle && typeof window.FCMNotifications !== 'undefined') {
        // Check current notification permission status
        const updateToggleState = async () => {
            // Wait for FCM to be fully initialized
            if (!window.FCMNotifications) {
                setTimeout(updateToggleState, 100);
                return;
            }

            // Check permission - this is the most reliable indicator
            if (Notification.permission === 'granted') {
                pushToggle.classList.add('active');
            } else {
                pushToggle.classList.remove('active');
            }
        };

        // Initial state - wait a bit for FCM to initialize
        setTimeout(updateToggleState, 500);

        // Handle toggle click
        pushToggle.addEventListener('click', async function() {
            if (this.classList.contains('active')) {
                // Currently enabled - disable notifications
                if (confirm('Disable push notifications? You will no longer receive instant order updates.')) {
                    // Clear token from backend (optional - implement if needed)
                    this.classList.remove('active');
                    showToast('Push notifications disabled', 'info');
                }
            } else {
                // Currently disabled - enable notifications
                try {
                    showToast('Requesting notification permission...', 'info', 3000);

                    // Request permission and register device
                    await window.FCMNotifications.initialize();
                    await window.FCMNotifications.registerDevice();

                    // Update toggle state
                    updateToggleState();

                    if (Notification.permission === 'granted') {
                        showToast('Push notifications enabled! You will receive order updates.', 'success');
                    } else {
                        showToast('Notification permission denied. Please enable in browser settings.', 'warning');
                    }
                } catch (error) {
                    console.error('Failed to enable push notifications:', error);
                    showToast('Failed to enable push notifications. Please try again.', 'error');
                }
            }
        });

        // Listen for FCM registration events
        window.addEventListener('fcm-registered', () => {
            updateToggleState();
        });
    } else if (pushToggle) {
        // FCM not available - disable toggle
        pushToggle.style.opacity = '0.5';
        pushToggle.style.cursor = 'not-allowed';
        pushToggle.addEventListener('click', (e) => {
            e.preventDefault();
            showToast('Push notifications are not available on this browser', 'warning');
        });
    }
});
</script>
@endsection