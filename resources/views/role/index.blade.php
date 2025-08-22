<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="pb-3">
               <a href="{{ route('role.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white">Add New Role</a> 
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-3">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">#</th>
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Description</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Staff Count</th>
                                <th class="text-left py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium">{{ $role->name }}</td>
                                <td class="px-6 py-4">{{ Str::limit($role->description, 50) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $role->staffProfiles->count() }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('role.destroy', $role->id) }}" onsubmit="return confirm('Are you sure to delete this role?');" class="inline">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <a href="{{ route('role.show', $role->id) }}" 
                                        class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-black shadow">
                                        View
                                        </a>
        
                                        <a href="{{ route('role.edit', $role->id) }}" class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 mr-2">
                                            Edit
                                        </a> 
                                        @if($role->staffProfiles->count() == 0)
                                        <x-danger-button class="text-xs">
                                            Delete
                                        </x-danger-button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No roles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $roles->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>