<x-guest-layout>
    @section('title', 'Confirm Password')
    @section('header-subtitle', 'Security Verification')

    <h2 class="form-title">Confirm Password</h2>
    <p class="form-subtitle">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <div style="margin-bottom: 25px; padding: 16px; background: rgba(245, 158, 11, 0.1); border-radius: var(--radius); border: 1px solid rgba(245, 158, 11, 0.2);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-shield-alt" style="color: var(--warning);"></i>
            <p style="margin: 0; color: var(--warning); font-size: 14px; font-weight: 600;">
                Security checkpoint - Please verify your identity
            </p>
        </div>
    </div>
    
    <form method="POST" action="{{ route('password.confirm') }}" id="confirmPasswordForm">
        @csrf

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">{{ __('Current Password') }}</label>
            <div class="input-group">
                <input id="password" 
                       class="form-input @error('password') error @enderror"
                       type="password"
                       name="password"
                       placeholder="Enter your current password"
                       required 
                       autocomplete="current-password"
                       autofocus>
                <i class="fas fa-lock input-icon"></i>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" id="confirmBtn">
            <i class="fas fa-check-circle"></i>
            <span class="btn-text">{{ __('Confirm Password') }}</span>
        </button>
    </form>
    
    <div class="switch-form">
        <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('confirmPasswordForm');
            const btn = document.getElementById('confirmBtn');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            });
        });
    </script>
    @endpush
</x-guest-layout>