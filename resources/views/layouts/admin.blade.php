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
    @stack('styles')
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
            @can('view-dashboard')
            <a href="{{ route('admin.dashboard') }}"
                class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-chart-line"></i></div>
                <div class="admin-nav-text">Dashboard</div>
            </a>
            @endcan

            <!-- Reports Menu -->
            @can('view-reports')
            <a href="{{ route('admin.reports.enhanced-monthly') }}"
                class="admin-nav-item {{ request()->routeIs('admin.reports.enhanced-monthly') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="admin-nav-text">Reports</div>
            </a>
            {{-- <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" id="reportsMenu">
                <div class="admin-nav-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="admin-nav-text">Reports</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="reportsSubmenu">
                <a href="{{ route('admin.reports.enhanced-monthly') }}" class="admin-nav-subitem {{ request()->routeIs('admin.reports.enhanced-monthly') ? 'active' : '' }}">
                    <div class="admin-nav-text">
                        <i class="fas fa-chart-line text-success"></i> Enhanced Analytics
                        <span class="badge badge-success badge-sm ml-1">New</span>
                    </div>
                </a>
                {{-- <a href="{{ route('admin.reports.monthly') }}" class="admin-nav-subitem {{ request()->routeIs('admin.reports.monthly') || request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                    <div class="admin-nav-text">Monthly Report</div>
                </a>
                <a href="{{ route('admin.reports.all-time') }}" class="admin-nav-subitem {{ request()->routeIs('admin.reports.all-time') ? 'active' : '' }}">
                    <div class="admin-nav-text">All-Time Report</div>
                </a>
            </div> --}}
            @endcan

            @can('view-users')
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.user.*') || request()->routeIs('admin.employees.*') ? 'active' : '' }}" id="userManagementMenu">
                <div class="admin-nav-icon"><i class="fas fa-users-cog"></i></div>
                <div class="admin-nav-text">User Management</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="userManagementSubmenu">
                <a href="{{ route('admin.user.index') }}" class="admin-nav-subitem {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Users</div>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="admin-nav-subitem {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Employees</div>
                </a>
            </div>
            @endcan

            @can('view-homepage-content')
            <a href="{{ route('admin.homepage.index') }}"
                class="admin-nav-item {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-home"></i></div>
                <div class="admin-nav-text">Homepage Content</div>
            </a>
            @endcan
            <!-- Kitchen Management Menu -->
            @can('view-kitchen-dashboard')
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.kitchen.*') ? 'active' : '' }}" id="kitchenMenu">
                <div class="admin-nav-icon"><i class="fas fa-fire"></i></div>
                <div class="admin-nav-text">Kitchen Management</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="kitchenSubmenu">
                <a href="{{ route('admin.kitchen.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.kitchen.index') ? 'active' : '' }}">
                    <div class="admin-nav-text">Dashboard</div>
                </a>
                <a href="{{ route('admin.kitchen.kds') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.kitchen.kds') ? 'active' : '' }}"
                    target="_blank">
                    <div class="admin-nav-text">Kitchen Display</div>
                </a>
                <a href="{{ route('admin.kitchen.stations.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.kitchen.stations.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Stations</div>
                </a>
                <a href="{{ route('admin.kitchen.orders') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.kitchen.orders') ? 'active' : '' }}">
                    <div class="admin-nav-text">Active Orders</div>
                </a>
                {{-- <a href="{{ route('admin.kitchen.analytics') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.kitchen.analytics') ? 'active' : '' }}">
                    <div class="admin-nav-text">Analytics</div>
                </a> --}}
            </div>
            @endcan
            <!-- Menu Managements Menu -->
            @can('view-menu-items')
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
            @endcan
            <!-- Order Managements Menu -->
            @can('view-orders')
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.order.*') || request()->routeIs('admin.menu-customizations.*') || request()->routeIs('admin.reports.activity-logs') ? 'active' : '' }}" id="orderMenu">
                <div class="admin-nav-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="admin-nav-text">Order Managements</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="orderSubmenu">
                <a href="{{ route('admin.order.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.order.index') || request()->routeIs('admin.order.show') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Orders</div>
                </a>
                {{-- <a href="{{ route('admin.reports.activity-logs') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.reports.activity-logs') ? 'active' : '' }}">
                    <div class="admin-nav-text">Order Activity Logs</div>
                </a> --}}
                @can('view-menu-customizations')
                <a href="{{ route('admin.menu-customizations.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.menu-customizations.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Order Customizations</div>
                </a>
                @endcan
            </div>
            @endcan

            <!-- Bookings Menu -->
            @canany(['view-tables', 'view-table-reservations'])
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.table-reservation.*') || request()->routeIs('admin.table.*') || request()->routeIs('admin.table-layout-config.*') || request()->routeIs('admin.table-qrcodes.*') ? 'active' : '' }}" id="bookingsMenu">
                <div class="admin-nav-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="admin-nav-text">Bookings</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="bookingsSubmenu">
                @can('view-table-reservations')
                <a href="{{ route('admin.table-reservation.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-reservation.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Bookings</div>
                </a>
                @endcan
                @can('view-table-qrcodes')
                <a href="{{ route('admin.table-qrcodes.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-qrcodes.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">QR Codes Generate</div>
                </a>
                @endcan
                @can('view-tables')
                <a href="{{ route('admin.table.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">All Table</div>
                </a>
                @endcan
                @can('view-table-layout-config')
                <a href="{{ route('admin.table-layout-config.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.table-layout-config.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Table Layout</div>
                </a>
                @endcan
            </div>
            @endcanany

            <!-- Rewards Menu -->
            @canany(['view-rewards-dashboard', 'view-redemptions', 'manage-redemptions', 'view-loyalty-members', 'view-promotions'])
            <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.rewards.*') || request()->routeIs('admin.promotions.*') ? 'active' : '' }}" id="rewardsMenu">
                <div class="admin-nav-icon"><i class="fas fa-gift"></i></div>
                <div class="admin-nav-text">Rewards & Promotions</div>
                <div class="admin-nav-arrow"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="admin-nav-submenu" id="rewardsSubmenu">
                @can('view-promotions')
                <a href="{{ route('admin.promotions.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Promotions</div>
                </a>
                @endcan
                @can('view-rewards-dashboard')
                <a href="{{ route('admin.rewards.index') }}"
                    class="admin-nav-subitem {{ request()->routeIs('admin.rewards.*') ? 'active' : '' }}">
                    <div class="admin-nav-text">Rewards</div>
                </a>
                @endcan
            </div>
            @endcanany

            <!-- Stock Management Menu -->
            @can('view-stock-dashboard')
            {{-- <div class="admin-nav-item admin-nav-parent {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" id="stockMenu">
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
            </div> --}}
            @endcan

            <!-- Role & Permission -->
            @canany(['view-roles', 'view-permissions'])
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
            @endcanany


            @can('view-settings')
            <a href="{{ route('admin.settings.index') }}"
                class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <div class="admin-nav-icon"><i class="fas fa-cog"></i></div>
                <div class="admin-nav-text">Settings</div>
            </a>
            @endcan
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
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
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

    <!-- Firebase Cloud Messaging (FCM) for Push Notifications -->
    @auth
        <script type="module">
            import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
            import { getMessaging, getToken, onMessage, deleteToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";
            import { getAuth, signInAnonymously, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

            const firebaseConfig = {
                apiKey: "{{ config('services.fcm.api_key') }}",
                authDomain: "{{ config('services.fcm.project_id') }}.firebaseapp.com",
                projectId: "{{ config('services.fcm.project_id') }}",
                storageBucket: "{{ config('services.fcm.storage_bucket') }}",
                messagingSenderId: "{{ config('services.fcm.messaging_sender_id') }}",
                appId: "{{ config('services.fcm.app_id') }}"
            };

            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);
            const messaging = getMessaging(app);

            // Global FCM object for admin
            window.AdminFCMNotifications = {
                currentToken: null,
                messaging: messaging,

                async initialize() {
                    try {
                        console.log('Admin FCM: Initializing...');

                        if (!('Notification' in window)) {
                            console.warn('Admin FCM: Browser does not support notifications');
                            return false;
                        }

                        const permission = await Notification.requestPermission();
                        console.log('Admin FCM: Permission status:', permission);

                        if (permission === 'granted') {
                            await this.registerDevice();
                            return true;
                        }
                        return false;
                    } catch (error) {
                        console.error('Admin FCM: Initialization error:', error);
                        return false;
                    }
                },

                async registerDevice() {
                    try {
                        console.log('Admin FCM: Requesting token with VAPID key...');

                        // Check if service worker is registered
                        if (!('serviceWorker' in navigator)) {
                            console.warn('Admin FCM: Service workers not supported');
                            return;
                        }

                        // Step 1: Sign in anonymously with Firebase Auth (required for FCM)
                        console.log('Admin FCM: Checking Firebase Auth...');
                        await new Promise((resolve, reject) => {
                            const unsubscribe = onAuthStateChanged(auth, async (user) => {
                                unsubscribe();
                                if (!user) {
                                    console.log('Admin FCM: Signing in anonymously...');
                                    try {
                                        await signInAnonymously(auth);
                                        console.log('Admin FCM: Anonymous sign-in successful');
                                        resolve();
                                    } catch (error) {
                                        console.error('Admin FCM: Anonymous sign-in failed:', error);
                                        reject(error);
                                    }
                                } else {
                                    console.log('Admin FCM: Already authenticated:', user.uid);
                                    resolve();
                                }
                            });
                        });

                        // Small delay to ensure auth is fully propagated
                        await new Promise(resolve => setTimeout(resolve, 500));

                        // Step 2: Unregister ALL existing service workers to start fresh
                        console.log('Admin FCM: Checking for existing service workers...');
                        const existingRegs = await navigator.serviceWorker.getRegistrations();
                        console.log(`Admin FCM: Found ${existingRegs.length} existing service worker(s)`);

                        for (const reg of existingRegs) {
                            const sub = await reg.pushManager.getSubscription();
                            if (sub) {
                                console.log('Admin FCM: Unsubscribing existing push subscription...');
                                await sub.unsubscribe();
                            }
                            console.log('Admin FCM: Unregistering service worker:', reg.scope);
                            await reg.unregister();
                        }

                        // Small delay after cleanup
                        await new Promise(resolve => setTimeout(resolve, 300));

                        // Step 3: Register service worker fresh
                        console.log('Admin FCM: Registering fresh service worker...');
                        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                            scope: '/'
                        });
                        console.log('Admin FCM: Service Worker registered:', registration);

                        // Wait for service worker to be active
                        await navigator.serviceWorker.ready;
                        console.log('Admin FCM: Service Worker is ready');

                        // Small delay before getting token
                        await new Promise(resolve => setTimeout(resolve, 500));

                        // Step 4: Delete any existing FCM token
                        console.log('Admin FCM: Deleting any existing FCM token...');
                        try {
                            const deleted = await deleteToken(messaging);
                            console.log('Admin FCM: Existing token deleted:', deleted);
                        } catch (deleteError) {
                            console.warn('Admin FCM: Could not delete existing token (may not exist):', deleteError.message);
                        }

                        // Small delay after deletion
                        await new Promise(resolve => setTimeout(resolve, 300));

                        // Step 5: Get FCM token
                        console.log('Admin FCM: Attempting to get FCM token...');
                        const token = await getToken(messaging, {
                            vapidKey: "{{ config('services.fcm.vapid_key') }}",
                            serviceWorkerRegistration: registration
                        });

                        if (token) {
                            console.log('Admin FCM: Token obtained:', token.substring(0, 20) + '...');
                            this.currentToken = token;

                            // Send token to backend
                            const response = await fetch('/api/fcm/register', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    device_token: token,
                                    device_type: 'web',
                                    platform: navigator.platform,
                                    browser: navigator.userAgent.match(/(firefox|msie|chrome|safari)[\/\s](\d+)/i)?.[1] || 'unknown',
                                    version: navigator.userAgent.match(/(firefox|msie|chrome|safari)[\/\s](\d+)/i)?.[2] || 'unknown'
                                })
                            });

                            const data = await response.json();
                            console.log('Admin FCM: Registration response:', data);

                            if (data.success) {
                                console.log('Admin FCM: Device registered successfully');
                                window.dispatchEvent(new Event('admin-fcm-registered'));
                            }
                        } else {
                            console.warn('Admin FCM: No token received');
                        }
                    } catch (error) {
                        console.error('Admin FCM: Token registration error:', error);
                        console.error('Admin FCM: Error details:', {
                            name: error.name,
                            message: error.message,
                            code: error.code
                        });

                        // Provide user-friendly message
                        if (error.code === 'messaging/permission-blocked') {
                            console.warn('Admin FCM: Notification permission blocked. Please enable in browser settings.');
                        } else if (error.message.includes('push service')) {
                            console.warn('Admin FCM: Push service error. This is normal if service worker is not set up. Admin will need to manually enable notifications.');
                        }
                    }
                },

                setupForegroundListener() {
                    onMessage(messaging, (payload) => {
                        console.log('Admin FCM: Message received in foreground', payload);

                        const notificationTitle = payload.notification?.title || payload.data?.title || 'New Notification';
                        const notificationBody = payload.notification?.body || payload.data?.body || '';

                        // Show toast notification
                        if (typeof showToast === 'function') {
                            showToast(notificationBody, 'info', 5000);
                        }

                        // Show browser notification
                        if (Notification.permission === 'granted') {
                            const notification = new Notification(notificationTitle, {
                                body: notificationBody,
                                icon: '/images/logo.png',
                                badge: '/images/logo.png',
                                tag: payload.data?.order_id || 'admin-notification',
                                requireInteraction: true,
                                data: payload.data
                            });

                            notification.onclick = function(event) {
                                event.preventDefault();
                                const clickAction = payload.data?.click_action;
                                if (clickAction) {
                                    window.focus();
                                    window.location.href = clickAction;
                                }
                                notification.close();
                            };
                        }

                        // Play notification sound
                        const audio = new Audio('/sounds/notification.mp3');
                        audio.play().catch(e => console.log('Could not play sound:', e));
                    });
                }
            };

            // Auto-initialize FCM for admin on page load
            document.addEventListener('DOMContentLoaded', async () => {
                console.log('Admin FCM: DOM ready, checking permission...');

                // Setup foreground listener immediately
                window.AdminFCMNotifications.setupForegroundListener();

                // Auto-register if permission already granted
                if (Notification.permission === 'granted') {
                    console.log('Admin FCM: Permission already granted, registering device...');
                    await window.AdminFCMNotifications.registerDevice();
                } else {
                    console.log('Admin FCM: Permission not granted. Waiting for manual enable.');
                }
            });
        </script>
    @endauth

    <script src="{{ asset('js/admin/layout-admin.js') }}?v={{ time() }}"></script>

    {{-- Auto-show toast for Laravel flash messages --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to ensure everything is rendered
            setTimeout(function() {
                @if(session('success'))
                    Toast.success('Success', '{{ session('success') }}');
                @endif

                @if(session('error'))
                    Toast.error('Error', '{{ session('error') }}');
                @endif

                @if(session('warning'))
                    Toast.warning('Warning', '{{ session('warning') }}');
                @endif

                @if(session('info'))
                    Toast.info('Info', '{{ session('info') }}');
                @endif
            }, 100);
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>