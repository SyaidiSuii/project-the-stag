<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Permission') }}
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
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Permission Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                   placeholder="e.g., manage-products, view-reports"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Use lowercase letters, numbers, and hyphens only. Format: action-resource (e.g., view-users, create-posts)
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description (Optional)
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                      placeholder="Brief description of what this permission allows">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Permission Naming Guidelines:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Use format: <strong>action-resource</strong></li>
                                <li>• Common actions: view, create, edit, delete, manage</li>
                                <li>• Examples: view-users, create-posts, manage-settings</li>
                                <li>• Use lowercase letters and hyphens only</li>
                            </ul>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.permissions.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Create Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
