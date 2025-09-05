<x-guest-layout>
    @section('title', 'Login')
    @section('header-subtitle', 'Customer Portal')

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <h2 class="form-title">Welcome Back</h2>
    <p class="form-subtitle">Sign in to your account to continue</p>
    
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

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
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <input id="password" 
                       class="form-input @error('password') error @enderror"
                       type="password"
                       name="password"
                       placeholder="Enter your password"
                       required 
                       autocomplete="current-password">
                <i class="fas fa-lock input-icon"></i>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="remember-forgot">
            <div class="remember">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('Remember me') }}</label>
            </div>
            
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary" id="loginBtn">
            <i class="fas fa-sign-in-alt"></i> 
            <span class="btn-text">{{ __('Sign In') }}</span>
        </button>
    </form>
    
    <div class="switch-form">
        Don't have an account? 
        <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            });
        });
    </script>
    @endpush
</x-guest-layout>