<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - The Stag')</title>
    @stack('head')
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}?v={{ time() }}">

    <!-- Toast & Confirm Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm-modal.css') }}">

    @yield('styles')
</head>

<body>
    <!-- Updated Sidebar Section -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-logo">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="logo" class="admin-logo-icon">
            </div>
            <div class="admin-logo-text">The Stag</div>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}"
                class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-chart-line"></i></div>
                <div class="admin-nav-text">Dashboard</div>
            </a>

            <!-- Reports Menu -->
            <a href="{{ route('admin.reports.index') }}" class="admin-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="admin-nav-text">Reports</div>
            </a>

            <a href="{{ route('admin.user.index') }}"
                class="admin-nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-users"></i></div>
                <div class="admin-nav-text">Users Management</div>
            </a>

            <a href="{{ route('admin.homepage.index') }}" class="admin-nav-item">
                <div class="admin-nav-icon"><i class="fas fa-home"></i></div>
                <div class="admin-nav-text">Homepage Content</div>
            </a>
            <!-- Menu Managements Menu -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.menu-items.*') || request()->routeIs('admin.categories.*') ? 'active' : '' }}" id="menuMenu">
                <div class="admin-nav-icon"><i class="fas fa-utensils"></i></div>
                <div class="admin-nav-text">Menu Management</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="menuSubmenu">
                <a href="{{ route('admin.menu-items.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Menu Items</div>
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Categories</div>
                </a>
            </div>
            <!-- Order Managements Menu -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.order.*') || request()->routeIs('admin.menu-customizations.*') ? 'active' : '' }}" id="orderMenu">
                <div class="admin-nav-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="admin-nav-text">Order Managements</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="orderSubmenu">
                <a href="{{ route('admin.order.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.order.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Orders</div>
                </a>
                <a href="{{ route('admin.menu-customizations.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.menu-customizations.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Order Customizations</div>
                </a>
            </div>

            <!-- Bookings Menu -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.table-reservation.*') || request()->routeIs('admin.table.*') || request()->routeIs('admin.table-layout-config.*') || request()->routeIs('admin.table-qrcodes.*') ? 'active' : '' }}" id="bookingsMenu">
                <div class="admin-nav-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="admin-nav-text">Bookings</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="bookingsSubmenu">
                <a href="{{ route('admin.table-reservation.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-reservation.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Bookings</div>
                </a>
                <a href="{{ route('admin.table-qrcodes.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-qrcodes.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">QR Codes Generate</div>
                </a>
                <a href="{{ route('admin.table.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Table</div>
                </a>
                <a href="{{ route('admin.table-layout-config.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-layout-config.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Table Layout</div>
                </a>
            </div>

            <!-- Rewards Menu -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.rewards.*') || request()->routeIs('admin.promotions.*') ? 'active' : '' }}" id="rewardsMenu">
                <div class="admin-nav-icon"><i class="fas fa-gift"></i></div>
                <div class="admin-nav-text">Rewards & Promotions</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="rewardsSubmenu">
                <a href="{{ route('admin.promotions.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Promotions</div>
                </a>
                <a href="{{ route('admin.rewards.rewards.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.rewards.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Rewards</div>
                </a>
                <a href="{{ route('admin.rewards.voucher-templates.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.voucher-templates.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Voucher Templates</div>
                </a>
                <a href="{{ route('admin.rewards.checkin.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.checkin.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Check-in Settings</div>
                </a>
                <a href="{{ route('admin.rewards.special-events.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.special-events.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Special Events</div>
                </a>
                <a href="{{ route('admin.rewards.loyalty-tiers.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.loyalty-tiers.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Tiers & Levels</div>
                </a>
                <a href="{{ route('admin.rewards.redemptions.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.redemptions.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Redemptions</div>
                </a>
                <a href="{{ route('admin.rewards.members.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.members.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Members</div>
                </a>
                <a href="{{ route('admin.rewards.achievements.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.achievements.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Achievements</div>
                </a>
                <a href="{{ route('admin.rewards.voucher-collections.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.voucher-collections.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Voucher Collections</div>
                </a>
                <a href="{{ route('admin.rewards.bonus-challenges.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.bonus-challenges.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Bonus Challenges</div>
                </a>
            </div>

            <!-- Stock Management Menu -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" id="stockMenu">
                <div class="admin-nav-icon"><i class="fas fa-boxes"></i></div>
                <div class="admin-nav-text">Stock Management</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="stockSubmenu">
                <a href="{{ route('admin.stock.dashboard') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.stock.dashboard') ? 'active' : '' }}">
                    <div class="admin-nav-text">Dashboard</div>
                </a>
                <a href="{{ route('admin.stock.items.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.stock.items.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Stock Items</div>
                </a>
                <a href="{{ route('admin.stock.suppliers.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.stock.suppliers.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Suppliers</div>
                </a>
                <a href="{{ route('admin.stock.purchase-orders.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.stock.purchase-orders.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Purchase Orders</div>
                </a>
            </div>

            <!-- Role & Permission -->
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}" id="roleMenu">
                <div class="admin-nav-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="admin-nav-text">Role & Permission</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="roleSubmenu">
                <a href="{{ route('admin.permissions.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Permissions</div>
                </a>
                <a href="{{ route('admin.roles.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Roles</div>
                </a>
            </div>


            <a href="{{ route('admin.settings.index') }}"
                class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
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

    <!-- Toast & Confirm Modal JavaScript -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/confirm-modal.js') }}"></script>
    <script src="{{ asset('js/form-confirm-handler.js') }}"></script>

    <!-- Password Toggle Functionality -->
    <script src="{{ asset('js/password-toggle.js') }}"></script>

    <script src="{{ asset('js/admin/layout-admin.js') }}?v={{ time() }}"></script>
    @yield('scripts')
</body>

</html>