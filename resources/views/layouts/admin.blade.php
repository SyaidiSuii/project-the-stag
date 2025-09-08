<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin Dashboard - The Stag')</title>
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">🦌</div>
            <div class="admin-logo-text">The Stag</div>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item active">
                <div class="admin-nav-icon"><i class="fas fa-chart-line"></i></div>
                <div class="admin-nav-text">Dashboard</div>
            </a>
            <a href="#" class="admin-nav-item">
                <div class="admin-nav-icon"><i class="fas fa-bell"></i></div>
                <div class="admin-nav-text">Notifications</div>
            </a>
            <a href="#" class="admin-nav-item">
                <div class="admin-nav-icon"><i class="fas fa-users"></i></div>
                <div class="admin-nav-text">Customers</div>
            </a>
            <a href="#" class="admin-nav-item">
                <div class="admin-nav-icon"><i class="fas fa-cog"></i></div>
                <div class="admin-nav-text">Settings</div>
            </a>
        </nav>
        <div class="admin-account">
            <div class="admin-user">
                <div class="admin-avatar"><i class="fas fa-user"></i></div>
                <div class="admin-user-info">
                    <div class="admin-user-name">Admin User</div>
                    <div class="admin-user-role">Restaurant Manager</div>
                </div>
            </div>
            <button type="button" class="admin-logout" id="logoutBtn">
                <div class="admin-nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div>Logout</div>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="admin-main" id="main-content">
        <header class="admin-header">
            <button class="admin-hamburger" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
            <h1 class="admin-title">@yield('page-title', 'Dashboard')</h1>
            <div class="admin-actions">
                <button class="admin-btn btn-secondary">
                    <div class="admin-nav-icon"><i class="fas fa-calendar"></i></div>
                    Today: <span id="currentDate"></span>
                </button>
                <button class="admin-btn btn-primary" id="viewSiteBtn">
                    <div class="admin-nav-icon"><i class="fas fa-globe"></i></div>
                    View Site
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        @yield('content')
    </main>

    <script src="{{ asset('js/admin/admin.js') }}"></script>
</body>
</html>
