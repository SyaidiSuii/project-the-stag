<x-guest-layout>
    @section('title', 'Email Verification')
    @section('header-subtitle', 'Verify Your Account')

    <!-- Session Status -->
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <h2 class="form-title">Verify Your Email</h2>
    <p class="form-subtitle">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    <div style="margin-bottom: 25px; padding: 20px; background: rgba(99, 102, 241, 0.1); border-radius: var(--radius); border: 1px solid rgba(99, 102, 241, 0.2);">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <i class="fas fa-envelope-open-text" style="font-size: 24px; color: var(--primary);"></i>
            <div>
                <h3 style="margin: 0; color: var(--primary); font-size: 16px;">Check Your Email</h3>
                <p style="margin: 0; color: var(--gray); font-size: 14px;">We've sent a verification link to your email address</p>
            </div>
        </div>
        <p style="margin: 0; color: var(--dark); font-size: 14px; line-height: 1.5;">
            Click the verification link in the email to activate your account. If you don't see it, check your spam folder.
        </p>
    </div>
    
    <div style="display: flex; gap: 12px; margin-bottom: 25px;">
        <form method="POST" action="{{ route('verification.send') }}" style="flex: 1;" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-primary" id="resendBtn">
                <i class="fas fa-paper-plane"></i>
                <span class="btn-text">{{ __('Resend Verification Email') }}</span>
            </button>
        </form>
    </div>

    <div style="display: flex; gap: 12px;">
        <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('resendForm');
            const btn = document.getElementById('resendBtn');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending email...';
            });
        });
    </script>
    @endpush
</x-guest-layout>