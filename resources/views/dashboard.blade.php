<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600">
                Welcome, <strong>{{ auth()->user()->name }}</strong>
                @if(auth()->user()->getRoleNames()->count() > 0)
                    - {{ auth()->user()->getRoleNames()->map(function($role) { return ucfirst($role); })->implode(', ') }}
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Role & Permission Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Your Access Level</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Assigned Roles:</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse(auth()->user()->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($role) }}
                                    </span>
                                @empty
                                    <span class="text-gray-500 text-sm">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Total Permissions:</h4>
                            <div class="text-2xl font-bold text-green-600">
                                {{ auth()->user()->getAllPermissions()->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions based on permissions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @can('create-permissions')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 bg-blue-500 rounded-md">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">Permission Management</h3>
                                    <p class="text-sm text-gray-500">Create and manage permissions</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage Permissions →</a>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('assign-roles')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 bg-green-500 rounded-md">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">Role Management</h3>
                                    <p class="text-sm text-gray-500">Assign roles to users</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.roles.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Manage Roles →</a>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('view-users')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 bg-purple-500 rounded-md">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">User Management</h3>
                                    <p class="text-sm text-gray-500">View and manage users</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.user.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Manage Users →</a>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('view-projects')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 bg-yellow-500 rounded-md">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">Projects</h3>
                                    <p class="text-sm text-gray-500">View project data</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-yellow-600 text-sm font-medium">Projects Module →</span>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('view-reports')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 bg-red-500 rounded-md">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">Reports</h3>
                                    <p class="text-sm text-gray-500">View system reports</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.sale-analytics.index') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">View Analytics →</a>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            <!-- System Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">System Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <strong>Last Login:</strong><br>
                            <span class="text-gray-600">{{ auth()->user()->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div>
                            <strong>Account Status:</strong><br>
                            <span class="text-green-600 font-medium">{{ auth()->user()->is_active ?? true ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div>
                            <strong>Email Verified:</strong><br>
                            <span class="{{ auth()->user()->email_verified_at ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ auth()->user()->email_verified_at ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
