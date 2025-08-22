<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Role Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">{{ $role->name }}</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('role.edit', $role->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Edit Role
                                </a>
                                <a href="{{ route('role.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Role Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                                    {{ $role->name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md min-h-[80px]">
                                    {{ $role->description ?: 'No description provided.' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs rounded {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Staff Count</label>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                                    {{ $role->staffProfiles->count() }} staff members assigned
                                </div>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                            <div class="bg-gray-50 p-4 rounded-md">
                                @php
                                    $availablePermissions = [
                                        'users.view' => 'View Users',
                                        'users.create' => 'Create Users',
                                        'users.edit' => 'Edit Users',
                                        'users.delete' => 'Delete Users',
                                        'orders.view' => 'View Orders',
                                        'orders.create' => 'Create Orders',
                                        'orders.edit' => 'Edit Orders',
                                        'orders.delete' => 'Delete Orders',
                                        'products.view' => 'View Products',
                                        'products.create' => 'Create Products',
                                        'products.edit' => 'Edit Products',
                                        'products.delete' => 'Delete Products',
                                        'reports.view' => 'View Reports',
                                        'settings.view' => 'View Settings',
                                        'settings.edit' => 'Edit Settings',
                                    ];
                                    
                                    $rolePermissions = [];
                                    if ($role->permissions) {
                                        if (is_array($role->permissions)) {
                                            $rolePermissions = $role->permissions;
                                        } else {
                                            $rolePermissions = json_decode($role->permissions, true) ?: [];
                                        }
                                    }
                                @endphp
                                
                                @if(empty($rolePermissions))
                                    <p class="text-sm text-gray-500 italic">No permissions assigned to this role.</p>
                                @else
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($availablePermissions as $permission => $label)
                                            @if(in_array($permission, $rolePermissions))
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Staff Members (if any) -->
                    @if($role->staffProfiles->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Assigned Staff Members</h4>
                            <div class="bg-gray-50 rounded-md">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">#</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Name</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Email</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($role->staffProfiles as $staff)
                                            <tr>
                                                <td class="py-3 px-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                                <td class="py-3 px-4 text-sm text-gray-900">{{ $staff->name ?? 'N/A' }}</td>
                                                <td class="py-3 px-4 text-sm text-gray-900">{{ $staff->email ?? 'N/A' }}</td>
                                                <td class="py-3 px-4 text-sm">
                                                    <span class="inline-flex px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                                        Active
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Created:</span>
                                <div class="mt-1">{{ $role->created_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                            <div>
                                <span class="font-medium">Last Updated:</span>
                                <div class="mt-1">{{ $role->updated_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                            @if($role->deleted_at)
                                <div>
                                    <span class="font-medium text-red-600">Deleted:</span>
                                    <div class="mt-1 text-red-600">{{ $role->deleted_at->format('M d, Y \a\t g:i A') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>