<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  dark:text-gray-200 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="pb-3">
               <a href="{{ route('admin.user.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white">Add New User</a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-3">
                <div class="p-6 text-gray-900 ">
                    
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->phone_number }}</td>

                                <td class="p-4">
                                @if($user->roles->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">No roles assigned</span>
                                @endif
                                </td>
                                <td class="p-4">{{ $user->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.user.destroy', $user->id) }}" onsubmit="return confirm('Are your sure to delete this?');">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <a href="{{ route('admin.user.edit', $user->id) }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white">
                                            Edit
                                        </a> 
                                        <x-danger-button class="ms-3">
                                            Delete
                                        </x-danger-button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
