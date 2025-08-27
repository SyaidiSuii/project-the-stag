<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Role') }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Roles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                   placeholder="Enter role name (e.g., editor, moderator)"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Role name should be lowercase and descriptive (e.g., content-manager, project-lead)</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Assign Permissions
                            </label>
                            
                            <div class="mb-4 flex items-center space-x-4">
                                <button type="button" 
                                        onclick="selectAllPermissions()" 
                                        class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                    Select All
                                </button>
                                <button type="button" 
                                        onclick="deselectAllPermissions()" 
                                        class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded">
                                    Deselect All
                                </button>
                            </div>
                            
                            @if($permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                                    @php
                                        $groupedPermissions = $permissions->groupBy(function($permission) {
                                            $parts = explode('-', $permission->name);
                                            return count($parts) > 1 ? $parts[1] : 'general';
                                        });
                                    @endphp
                                    
                                    @foreach($groupedPermissions as $group => $perms)
                                        <div class="border border-gray-100 rounded-lg p-3">
                                            <h4 class="font-medium text-gray-800 mb-2 capitalize border-b border-gray-200 pb-1">
                                                {{ str_replace('-', ' ', $group) }}
                                            </h4>
                                            @foreach($perms as $permission)
                                                <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700">
                                                        {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>No permissions available. Please create permissions first.</p>
                                </div>
                            @endif
                            
                            @error('permissions')
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
                                    <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-4 space-y-1">
                                            <li>Choose permissions carefully based on what this role should be able to do</li>
                                            <li>You can modify permissions later by editing the role</li>
                                            <li>Users assigned to this role will inherit all selected permissions</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.roles.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectAllPermissions() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllPermissions() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Auto-format role name input
        document.getElementById('name').addEventListener('input', function(e) {
            // Convert to lowercase and replace spaces with hyphens
            e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        });
    </script>
</x-app-layout>