<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The Stag - SmartDine')</title>

    <!-- Firebase Configuration -->
    <script>
        window.FIREBASE_CONFIG = {
            apiKey: "AIzaSyBO-_-PSDlUZkY0dCI7lI8LeJzoRRBvSEQ",
            authDomain: "the-stag-notif-v2.firebaseapp.com",
            projectId: "the-stag-notif-v2",
            storageBucket: "the-stag-notif-v2.firebasestorage.app",
            messagingSenderId: "595478392275",
            appId: "1:595478392275:web:56b641955e431fe3ddd326"
        };
        window.FIREBASE_VAPID_KEY = "{{ config('services.fcm.vapid_key') }}";
    </script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }}">

    <!-- Toast & Confirm Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm-modal.css') }}">

    <!-- Page Specific Styles -->
    @yield('styles')
</head>

<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar" aria-label="Primary">
        <div aria-hidden="true">
            <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo">
        </div>

        <a class="nav-item {{ Request::routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-text">HOME</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.promotions*') ? 'active' : '' }}" href="{{ route('customer.promotions.index') }}">
            <div class="nav-icon"><i class="fas fa-tags"></i></div>
            <div class="nav-text">PROMOS</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.menu*') || Request::routeIs('customer.food*') || Request::routeIs('customer.drinks*') ? 'active' : '' }}" href="{{ route('customer.menu.index') }}">
            <div class="nav-icon"><i class="fas fa-utensils"></i></div>
            <div class="nav-text">MENU</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.orders*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
            <div class="nav-icon"><i class="fas fa-shopping-bag"></i></div>
            <div class="nav-text">ORDERS</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.rewards*') ? 'active' : '' }}" href="{{ route('customer.rewards.index') }}">
            <div class="nav-icon"><i class="fas fa-gift"></i></div>
            <div class="nav-text">REWARDS</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.booking*') ? 'active' : '' }}" href="{{ route('customer.booking.index') }}">
            <div class="nav-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="nav-text">BOOKING</div>
        </a>
        <a class="nav-item account-spacer {{ Request::routeIs('customer.account*') || Request::routeIs('customer.reviews*') ? 'active' : '' }}" href="{{ route('customer.account.index') }}">
            <div class="nav-icon"><i class="fas fa-user"></i></div>
            <div class="nav-text">ACCOUNT</div>
            {{-- <div class="nav-text">{{ auth()->check() ? strtoupper(explode(' ', auth()->user()->name)[0]) : 'ACCOUNT' }}</div> --}}
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Toast & Confirm Modal JavaScript -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/confirm-modal.js') }}"></script>

    <!-- Flash Messages Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for logout message cookie
            const logoutMessage = getCookie('logout_message');
            if (logoutMessage) {
                Toast.success('Success', logoutMessage);
                // Delete the cookie after showing
                document.cookie = 'logout_message=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';

                // Clear chatbot session from localStorage on logout
                localStorage.removeItem('chatbot_session');
                console.log('Chatbot session cleared after logout');
            }

            @if(session('success'))
            Toast.success('Success', '{{ session('
                success ') }}');
            @endif

            @if(session('error'))
            Toast.error('Error', '{{ session('
                error ') }}');
            @endif

            @if(session('warning'))
            Toast.warning('Warning', '{{ session('
                warning ') }}');
            @endif

            @if(session('info'))
            Toast.info('Info', '{{ session('
                info ') }}');
            @endif
        });

        // Helper function to get cookie value
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
            return null;
        }
    </script>

    <!-- Customer Design System JavaScript -->
    <script src="{{ asset('js/customer/layout.js') }}"></script>

    <!-- Password Toggle Functionality -->
    <script src="{{ asset('js/password-toggle.js') }}"></script>

    <!-- FCM Push Notifications -->
    @auth
    <script src="{{ asset('js/customer/notifications.js') }}"></script>

    <!-- Auto-prompt for new users -->
    @if(session('show_notification_prompt') && !auth()->user()->notification_prompted_at)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('New user detected - will prompt for notifications');
            if (window.FCMNotifications) {
                window.FCMNotifications.promptNewUser();
            }
        });
    </script>
    @endif
    @endauth

    {{-- 🧠 Chatbot (AI Groq) --}}
    @include('partials.chatbot')

    <!-- Page Specific Scripts -->
    @yield('scripts')
</body>

</html>