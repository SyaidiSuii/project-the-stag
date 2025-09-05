<x-guest-layout>
    @section('title', 'Register')
    @section('header-subtitle', 'Join Us Today')

    <h2 class="form-title">Create Account</h2>
    <p class="form-subtitle">Join us for the best dining experience</p>
    
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label class="form-label" for="name">{{ __('Full Name') }}</label>
            <div class="input-group">
                <input id="name" 
                       class="form-input @error('name') error @enderror" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Enter your full name" 
                       required 
                       autofocus 
                       autocomplete="name">
                <i class="fas fa-user input-icon"></i>
            </div>
            @error('name')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email Address') }}</label>
            <div class="input-group">
                <input id="email" 
                       class="form-input @error('email') error @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="Enter your email" 
                       required 
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
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <input id="password" 
                       class="form-input @error('password') error @enderror"
                       type="password"
                       name="password"
                       placeholder="Create a password"
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
                       placeholder="Confirm your password"
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

        <!-- Terms Agreement -->
        <div class="remember" style="margin-bottom: 20px;">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">
                I agree to the 
                <a href="#" class="forgot-link">Terms & Conditions</a> 
                and 
                <a href="#" class="forgot-link">Privacy Policy</a>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" id="registerBtn">
            <i class="fas fa-user-plus"></i>
            <span class="btn-text">{{ __('Create Account') }}</span>
        </button>
    </form>
    
    <div class="switch-form">
        Already have an account? 
        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const btn = document.getElementById('registerBtn');
            const btnText = btn.querySelector('.btn-text');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
            });
        });
    </script>
    @endpush
</x-guest-layout>