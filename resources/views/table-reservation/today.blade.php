<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Today's Reservations") }} - {{ now()->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                @php
                    $totalReservations = collect($reservations)->flatten()->count();
                    $pendingCount = $reservations->get('pending', collect())->count();
                    $confirmedCount = $reservations->get('confirmed', collect())->count();
                    $seatedCount = $reservations->get('seated', collect())->count();
                    $completedCount = $reservations->get('completed', collect())->count();
                @endphp

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $totalReservations }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalReservations }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $pendingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pendingCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $confirmedCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Confirmed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $confirmedCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $seatedCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Seated</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $seatedCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $completedCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $completedCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Reservations by Status</h3>
                <div class="flex gap-2">
                    <a href="{{ route('table-reservation.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        New Reservation
                    </a>
                    <a href="{{ route('table-reservation.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        All Reservations
                    </a>
                </div>
            </div>

            <!-- Reservations by Status -->
            <div class="space-y-6">
                
                @foreach(['pending', 'confirmed', 'seated', 'completed'] as $status)
                    @if($reservations->has($status) && $reservations->get($status)->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                            <h4 class="font-semibold text-gray-800 capitalize">
                                {{ str_replace('_', ' ', $status) }} Reservations 
                                <span class="text-sm font-normal text-gray-600">({{ $reservations->get($status)->count() }})</span>
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($reservations->get($status)->sortBy('reservation_time') as $reservation)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-lg">{{ $reservation->guest_name }}</h5>
                                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $reservation->confirmation_code }}</span>
                                    </div>
                                    
                                    <div class="space-y-1 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ $reservation->number_of_guests }} guests
                                        </div>
                                        @if($reservation->table)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Table {{ $reservation->table->table_number }}
                                        </div>
                                        @endif
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $reservation->guest_phone }}
                                        </div>
                                    </div>

                                    @if($reservation->special_requests)
                                    <div class="mb-3">
                                        <p class="text-xs text-yellow-700 bg-yellow-50 p-2 rounded border border-yellow-200">
                                            <strong>Special:</strong> {{ Str::limit($reservation->special_requests, 50) }}
                                        </p>
                                    </div>
                                    @endif

                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-1">
                                            @if($status == 'pending')
                                                <form method="POST" action="{{ route('table-reservation.update-status', $reservation->id) }}" class="inline" data-guest-name="{{ $reservation->guest_name }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        Confirm
                                                    </button>
                                                </form>
                                            @elseif($status == 'confirmed')
                                                <form method="POST" action="{{ route('table-reservation.update-status', $reservation->id) }}" class="inline" data-guest-name="{{ $reservation->guest_name }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="seated">
                                                    <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                        Seat
                                                    </button>
                                                </form>
                                            @elseif($status == 'seated')
                                                <form method="POST" action="{{ route('table-reservation.update-status', $reservation->id) }}" class="inline" data-guest-name="{{ $reservation->guest_name }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                                        Complete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-1">
                                            <a href="{{ route('table-reservation.show', $reservation->id) }}" 
                                               class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                View
                                            </a>
                                            <a href="{{ route('table-reservation.edit', $reservation->id) }}" 
                                               class="px-2 py-1 bg-gray-800 text-white text-xs rounded hover:bg-gray-900">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach

                <!-- No Show and Cancelled Reservations -->
                @php
                    $problemReservations = collect();
                    if ($reservations->has('cancelled')) {
                        $problemReservations = $problemReservations->merge($reservations->get('cancelled'));
                    }
                    if ($reservations->has('no_show')) {
                        $problemReservations = $problemReservations->merge($reservations->get('no_show'));
                    }
                @endphp

                @if($problemReservations->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-red-50 border-b">
                        <h4 class="font-semibold text-red-800">
                            Cancelled & No Show Reservations
                            <span class="text-sm font-normal text-red-600">({{ $problemReservations->count() }})</span>
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($problemReservations->sortBy('reservation_time') as $reservation)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $reservation->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                    </span>
                                    <div>
                                        <p class="font-medium">{{ $reservation->guest_name }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }} - 
                                            {{ $reservation->number_of_guests }} guests
                                            @if($reservation->table)
                                                - Table {{ $reservation->table->table_number }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('table-reservation.show', $reservation->id) }}" 
                                       class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                        View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Empty State -->
                @if($totalReservations == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4l6 6m0-6l-6 6"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No reservations today</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new reservation.</p>
                        <div class="mt-6">
                            <a href="{{ route('table-reservation.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                New Reservation
                            </a>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page every 5 minutes to keep data current
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes

        // Show confirmation for status updates
        document.querySelectorAll('form[action*="update-status"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const guestName = this.dataset.guestName;
                const status = this.querySelector('input[name="status"]').value;
                if (!confirm(`Are you sure you want to mark ${guestName}'s reservation as '${status}'?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-app-layout>