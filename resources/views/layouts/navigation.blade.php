<nav x-data="{ open: false }" class="bg-white border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links with Horizontal Scroll -->
                <div class="hidden sm:flex items-center sm:ms-10">
                    <!-- Always visible main links -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="mr-4">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                        <!-- Dropdown for Management -->
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-600 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 transition duration-150 ease-in-out">
                                {{ __('Management') }}
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('admin.user.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.user.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Users') }}
                                    </a>
                                    @can('view-roles')
                                        <a href="{{ route('admin.roles.index') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            {{ __('Role Management') }}
                                        </a>
                                    @endcan
                                    @can('view-permissions')
                                        <a href="{{ route('admin.permissions.index') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            {{ __('Permissions') }}
                                        </a>
                                    @endcan
                                    <a href="{{ route('admin.menu-items.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.menu-items.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Menu Items') }}
                                    </a>
                                    <a href="{{ route('admin.sale-analytics.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.sale-analytics.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Sale Analytics') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown for Tables & Reservation -->
                        <div class="relative ml-4" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 transition duration-150 ease-in-out">
                                {{ __('Tables') }}
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('admin.table.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.table.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Tables') }}
                                    </a>
                                    <a href="{{ route('admin.table-reservation.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.table-reservation.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Table Reservations') }}
                                    </a>
                                    <a href="{{ route('admin.table-layout-config.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.table-layout-config.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Table Layout Configs') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown for Orders & Tracking -->
                        <div class="relative ml-4" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 transition duration-150 ease-in-out">
                                {{ __('Orders') }}
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('admin.order.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.order.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Orders') }}
                                    </a>
                                    <a href="{{ route('admin.order-item.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.order-item.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Order Items') }}
                                    </a>
                                    <a href="{{ route('admin.order-etas.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.order-etas.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Order ETAS') }}
                                    </a>
                                    <a href="{{ route('admin.order-trackings.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('admin.order-trackings.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ __('Order Trackings') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                    <!-- Profile Link -->
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="ml-4">
                        {{ __('Profile') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white  hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

                <!-- Mobile Links -->
                <x-responsive-nav-link :href="route('admin.user.index')" :active="request()->routeIs('admin.user.index')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.table.index')" :active="request()->routeIs('admin.table.index')">
                    {{ __('Tables') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.table-reservation.index')" :active="request()->routeIs('admin.table-reservation.index')">
                    {{ __('Table Reservations') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.table-layout-config.index')" :active="request()->routeIs('admin.table-layout-config.index')">
                    {{ __('Table Layout Configs') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.menu-items.index')" :active="request()->routeIs('admin.menu-items.index')">
                    {{ __('Menu Items') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.order.index')" :active="request()->routeIs('admin.order.index')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.order-item.index')" :active="request()->routeIs('admin.order-item.index')">
                    {{ __('Order Items') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.order-etas.index')" :active="request()->routeIs('admin.order-etas.index')">
                    {{ __('Order ETAs') }}
                </x-responsive-nav-link>
                

            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>