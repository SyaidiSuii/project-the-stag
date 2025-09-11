<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The Stag - SmartDine')</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }}">
    
    <!-- Page Specific Styles -->
    @yield('styles')
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar" aria-label="Primary">
        <div class="logo" aria-hidden="true">🦌</div>

        <a class="nav-item {{ Request::routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-text">HOME</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.food*') ? 'active' : '' }}" href="{{ route('customer.food.index') }}">
            <div class="nav-icon"><i class="fas fa-utensils"></i></div>
            <div class="nav-text">FOOD</div>
        </a>
        <a class="nav-item {{ Request::routeIs('customer.drinks*') ? 'active' : '' }}" href="{{ route('customer.drinks.index') }}">
            <div class="nav-icon"><i class="fas fa-cocktail"></i></div>
            <div class="nav-text">DRINKS</div>
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
        <a class="nav-item account-spacer {{ Request::routeIs('customer.account*') ? 'active' : '' }}" href="{{ route('customer.account.index') }}">
            <div class="nav-icon"><i class="fas fa-user"></i></div>
            <div class="nav-text">ACCOUNT</div>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Customer Design System JavaScript -->
    <script src="{{ asset('js/customer/layout.js') }}"></script>
    <!-- Page Specific Scripts -->
    @yield('scripts')
</body>
</html>