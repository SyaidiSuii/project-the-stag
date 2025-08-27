<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assign Roles to Users') }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Roles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.roles.assign') }}" method="POST" id="assignRoleForm">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select User <span class="text-red-500">*</span>
                            </label>
                            <select name="user_id" id="user_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror"
                                    required onchange="loadUserRoles()">
                                <option value="">-- Select a User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6" id="currentRoles" style="display: none;">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Current Roles:</h4>
                            <div id="currentRolesList" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Assign Roles
                            </label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($roles as $role)
                                    <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($role->name) }}
                                                @if(in_array($role->name, ['admin', 'manager', 'user']))
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                        Core
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $role->permissions()->count() }} permissions | {{ $role->users()->count() }} users
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            @error('roles')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Role Assignment Info</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-4 space-y-1">
                                            <li><strong>Admin:</strong> Full system access and can manage all permissions</li>
                                            <li><strong>Manager:</strong> Can manage users, projects, and view reports</li>
                                            <li><strong>User:</strong> Basic access to dashboard and project viewing</li>
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
                                Assign Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const users = @json($users);
        
        function loadUserRoles() {
            const userSelect = document.getElementById('user_id');
            const selectedUserId = userSelect.value;
            const currentRolesDiv = document.getElementById('currentRoles');
            const currentRolesList = document.getElementById('currentRolesList');
            
            if (selectedUserId) {
                const user = users.find(u => u.id == selectedUserId);
                if (user && user.roles.length > 0) {
                    currentRolesDiv.style.display = 'block';
                    currentRolesList.innerHTML = user.roles.map(role => 
                        `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">${role.name}</span>`
                    ).join('');
                    
                    // Check checkboxes for current roles
                    const checkboxes = document.querySelectorAll('input[name="roles[]"]');
                    checkboxes.forEach(checkbox => {
                        const roleId = parseInt(checkbox.value);
                        const hasRole = user.roles.some(role => role.id === roleId);
                        checkbox.checked = hasRole;
                    });
                } else {
                    currentRolesDiv.style.display = 'block';
                    currentRolesList.innerHTML = '<span class="text-gray-500">No roles assigned</span>';
                    
                    // Uncheck all checkboxes
                    const checkboxes = document.querySelectorAll('input[name="roles[]"]');
                    checkboxes.forEach(checkbox => checkbox.checked = false);
                }
            } else {
                currentRolesDiv.style.display = 'none';
                
                // Uncheck all checkboxes
                const checkboxes = document.querySelectorAll('input[name="roles[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = false);
            }
        }
    </script>
</x-app-layout>
