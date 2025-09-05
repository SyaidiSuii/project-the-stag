<x-guest-layout>
    @section('title', 'Forgot Password')
    @section('header-subtitle', 'Password Recovery')

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <h2 class="form-title">Forgot Password?</h2>
    <p class="form-subtitle">
        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>
    
    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
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
                       placeholder="Enter your email address" 
                       required 
                       autofocus>
                <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" id="forgotBtn">
            <i class="fas fa-paper-plane"></i>
            <span class="btn-text">{{ __('Email Password Reset Link') }}</span>
        </button>
    </form>
    
    <div class="switch-form">
        Remember your password? 
        <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotPasswordForm');
            const btn = document.getElementById('forgotBtn');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending email...';
            });
        });
    </script>
    @endpush
</x-guest-layout>