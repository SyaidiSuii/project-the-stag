<x-guest-layout>
    @section('title', 'Reset Password')
    @section('header-subtitle', 'Create New Password')

    <h2 class="form-title">Reset Password</h2>
    <p class="form-subtitle">Enter your new password below</p>
    
    <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email Address') }}</label>
            <div class="input-group">
                <input id="email" 
                       class="form-input @error('email') error @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email', $request->email) }}" 
                       placeholder="Enter your email address" 
                       required 
                       autofocus 
                       autocomplete="username">
                <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">{{ __('New Password') }}</label>
            <div class="input-group">
                <input id="password" 
                       class="form-input @error('password') error @enderror" 
                       type="password" 
                       name="password" 
                       placeholder="Enter your new password" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
            <div class="input-group">
                <input id="password_confirmation" 
                       class="form-input @error('password_confirmation') error @enderror" 
                       type="password" 
                       name="password_confirmation" 
                       placeholder="Confirm your new password" 
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
            </div>
            @error('password_confirmation')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" id="resetBtn">
            <i class="fas fa-key"></i>
            <span class="btn-text">{{ __('Reset Password') }}</span>
        </button>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('resetPasswordForm');
            const btn = document.getElementById('resetBtn');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting password...';
            });
        });
    </script>
    @endpush
</x-guest-layout>