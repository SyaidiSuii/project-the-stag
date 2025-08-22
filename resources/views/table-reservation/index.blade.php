<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Table Reservations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('table-reservation.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Reservation
                </a>
                <a href="{{ route('table-reservation.today') }}" class="items-center px-4 py-2 bg-blue-600 rounded font-semibold text-white hover:bg-blue-700">
                    Today's Reservations
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('table-reservation.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Name, phone, email, code...">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @if(request('status') == $status) selected @endif>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="table_id" class="block text-sm font-medium text-gray-700">Table</label>
                            <select name="table_id" id="table_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Tables</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" @if(request('table_id') == $table->id) selected @endif>
                                        {{ $table->table_number }} ({{ $table->capacity }} pax)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 !text-white font-semibold 
                                    rounded-md hover:bg-indigo-700 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Filter
                            </button>
                        </div>
                    </form>

                    @if(request()->hasAny(['search', 'status', 'date', 'table_id']))
                        <div class="mt-3">
                            <a href="{{ route('table-reservation.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Reservations Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Confirmation</th>
                                    <th class="text-left py-2">Guest</th>
                                    <th class="text-left py-2">Date & Time</th>
                                    <th class="text-left py-2">Table</th>
                                    <th class="text-left py-2">Guests</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Created By</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $reservation)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($reservations->currentPage() - 1) * $reservations->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm font-medium">{{ $reservation->confirmation_code }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">{{ $reservation->guest_name }}</div>
                                            <div class="text-sm text-gray-600">{{ $reservation->guest_phone }}</div>
                                            @if($reservation->guest_email)
                                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">{{ $reservation->reservation_date->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($reservation->table)
                                            <span class="font-medium">{{ $reservation->table->table_number }}</span>
                                            <div class="text-sm text-gray-600">{{ $reservation->table->table_type }}</div>
                                        @else
                                            <span class="text-gray-500">No table assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium">{{ $reservation->number_of_guests }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize
                                            @if($reservation->status == 'confirmed') bg-green-100 text-green-800
                                            @elseif($reservation->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($reservation->status == 'seated') bg-blue-100 text-blue-800
                                            @elseif($reservation->status == 'completed') bg-indigo-100 text-indigo-800
                                            @elseif($reservation->status == 'cancelled') bg-red-100 text-red-800
                                            @elseif($reservation->status == 'no_show') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ str_replace('_', ' ', $reservation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $reservation->user->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('table-reservation.show', $reservation->id) }}" 
                                               class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-black shadow">
                                                View
                                            </a>
                                            <a href="{{ route('table-reservation.edit', $reservation->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                                                <form method="POST" action="{{ route('table-reservation.destroy', $reservation->id) }}" 
                                                      onsubmit="return confirm('Are you sure to delete this reservation?');" class="inline">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    @csrf
                                                   <x-danger-button class="text-xs">
                                                        Delete
                                                    </x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No reservations found</p>
                                            <p class="text-sm">Try adjusting your search criteria or create a new reservation</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reservations->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>