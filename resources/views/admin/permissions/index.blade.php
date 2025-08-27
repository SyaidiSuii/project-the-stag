<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permissions Management') }}
            </h2>
            @can('create-permissions')
                <a href="{{ route('admin.permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Permission
                </a>
            @endcan
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($permission->roles->count() > 0)
                                                    @foreach($permission->roles as $role)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                            {{ $role->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-500">Not assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $permission->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @can('view-permissions')
                                                    <a href="{{ route('admin.permissions.show', $permission) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                @endcan
                                                
                                                @can('edit-permissions')
                                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                @endcan

                                                @can('assign-permissions')
                                                    <a href="{{ route('admin.permissions.assign.form', $permission) }}" class="text-green-600 hover:text-green-900">Assign</a>
                                                @endcan
                                                
                                                @can('delete-permissions')
                                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No permissions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        <div class="mt-6">
                            {{ $permissions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
