<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                {{ __('Roles') }}
            </h2>
            <a href="{{ route('admin.role.create') }}"
               class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ __('Create Role') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="p-6">
                    @if(session('success'))
                        <div class="p-4 mb-4 rounded-lg bg-green-100 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($roles->isEmpty())
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                No roles found
                            </h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">
                                Get started by creating a new role.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.role.create') }}"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('Create Role') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="p-4 text-left">Name</th>
                                        <th class="p-4 text-left">Description</th>
                                        <th class="p-4 text-left">Created</th>
                                        <th class="p-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($roles as $role)
                                    <tr>
                                        <td class="p-4">{{ $role->name }}</td>
                                        <td class="p-4">{{ $role->description ?? '-' }}</td>
                                        <td class="p-4">{{ $role->created_at->diffForHumans() }}</td>
                                        <td class="p-4 text-right">
                                            <a href="{{ route('admin.role.edit', $role) }}"
                                               class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <form action="{{ route('admin.role.destroy', $role) }}"
                                                  method="POST"
                                                  class="inline ml-3"
                                                  onsubmit="return confirm('Delete this role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $roles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
