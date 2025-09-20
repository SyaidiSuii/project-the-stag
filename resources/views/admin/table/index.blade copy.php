<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tables') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="pb-3">
               <a href="{{ route('admin.table.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white">Add New Table</a> 
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
                                <th class="text-left py-2">Table Number</th>
                                <th class="text-left py-2">Capacity</th>
                                <th class="text-left py-2">Type</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Location</th>
                                <th class="text-left py-2">Active</th>
                                <th class="text-left py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tables as $table)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ ($tables->currentPage() - 1) * $tables->perPage() + $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium">{{ $table->table_number }}</td>
                                <td class="px-6 py-4">{{ $table->capacity }} pax</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded capitalize 
                                        @if($table->table_type == 'vip') bg-purple-100 text-purple-800
                                        @elseif($table->table_type == 'private') bg-blue-100 text-blue-800
                                        @elseif($table->table_type == 'outdoor') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $table->table_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded capitalize
                                        @if($table->status == 'available') bg-green-100 text-green-800
                                        @elseif($table->status == 'occupied') bg-red-100 text-red-800
                                        @elseif($table->status == 'reserved') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $table->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ Str::limit($table->location_description, 30) ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded {{ $table->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $table->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.table.destroy', $table->id) }}" onsubmit="return confirm('Are you sure to delete this table?');" class="inline">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <a href="{{ route('admin.table.show', $table->id) }}" class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-black shadow">
                                            View
                                        </a>
                                        <a href="{{ route('admin.table.edit', $table->id) }}" class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 mr-2">
                                            Edit
                                        </a> 
                                        <x-danger-button class="text-xs">
                                            Delete
                                        </x-danger-button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No tables found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $tables->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>