<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Roles Management') }}
            </h2>
            <div class="flex space-x-2">
                @can('assign-roles')
                    <a href="{{ route('admin.roles.assign.form') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Assign Roles to Users
                    </a>
                @endcan
                @can('create-roles')
                    <a href="{{ route('admin.roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Role
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($role->name) }}
                                                @if(in_array($role->name, ['admin', 'manager', 'user']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                        Core Role
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $role->users_count }} users
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ $role->permissions_count }} permissions
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $role->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @can('view-roles')
                                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                @endcan
                                                
                                                @can('edit-roles')
                                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                @endcan
                                                
                                                @can('delete-roles')
                                                    @if(!in_array($role->name, ['admin', 'manager', 'user']))
                                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">Cannot Delete</span>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No roles found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($roles->hasPages())
                        <div class="mt-6">
                            {{ $roles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
