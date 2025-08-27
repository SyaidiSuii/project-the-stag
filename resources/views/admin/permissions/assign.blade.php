<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assign Permission: ') . $permission->name }}
            </h2>
            <a href="{{ route('admin.permissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Permissions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Permission Details</h3>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm"><strong>Name:</strong> {{ $permission->name }}</p>
                            <p class="text-sm mt-1"><strong>Created:</strong> {{ $permission->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.permissions.assign', $permission) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Assign to Roles
                            </label>
                            
                            <div class="space-y-3">
                                @forelse($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               {{ in_array($role->id, $assignedRoles) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-900">
                                            {{ ucfirst($role->name) }}
                                            <span class="text-gray-500">
                                                ({{ $role->users()->count() }} users)
                                            </span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">No roles available.</p>
                                @endforelse
                            </div>
                            
                            @error('roles')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Assigning this permission to roles will immediately grant access to all users with those roles. Make sure you understand the implications before proceeding.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.permissions.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Update Assignments
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
