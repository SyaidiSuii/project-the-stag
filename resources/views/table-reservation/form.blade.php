<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($reservation->id)
                {{ __('Edit Reservation') }} - {{ $reservation->confirmation_code }}
            @else
                {{ __('Create New Reservation') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($reservation->id)
                            {{ __('Edit Reservation Information') }}
                        @else
                            {{ __('Reservation Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Complete the reservation details below.") }}
                    </p>
                </header>

                @if($reservation->id)
                    <form method="post" action="{{ route('table-reservation.update', $reservation->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('table-reservation.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Basic Reservation Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="reservation_date" :value="__('Reservation Date')" />
                            <x-text-input id="reservation_date" name="reservation_date" type="date" class="mt-1 block w-full" 
                                :value="old('reservation_date', $reservation->reservation_date ? $reservation->reservation_date->format('Y-m-d') : '')" 
                                min="{{ date('Y-m-d') }}" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('reservation_date')" />
                        </div>

                        <div>
                            <x-input-label for="reservation_time" :value="__('Reservation Time')" />
                            <x-text-input id="reservation_time" name="reservation_time" type="time" class="mt-1 block w-full" 
                                :value="old('reservation_time', $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') : '')" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('reservation_time')" />
                        </div>
                    </div>

                    <!-- Table and Party Size -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="table_id" :value="__('Table (Optional)')" />
                            <select id="table_id" name="table_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Table (Optional)</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" 
                                        @if(old('table_id', $reservation->table_id) == $table->id) selected @endif>
                                        {{ $table->table_number }} - {{ $table->table_type }} ({{ $table->capacity }} capacity)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('table_id')" />
                            <p class="mt-1 text-sm text-gray-500">Leave blank for walk-in assignment</p>
                        </div>

                        <div>
                            <x-input-label for="number_of_guests" :value="__('Number of Guests')" />
                            <x-text-input id="number_of_guests" name="number_of_guests" type="number" min="1" max="50" class="mt-1 block w-full" 
                                :value="old('number_of_guests', $reservation->number_of_guests)" placeholder="e.g. 4" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('number_of_guests')" />
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Guest Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="guest_name" :value="__('Guest Name')" />
                                <x-text-input id="guest_name" name="guest_name" type="text" class="mt-1 block w-full" 
                                    :value="old('guest_name', $reservation->guest_name)" placeholder="e.g. John Doe" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('guest_name')" />
                            </div>

                            <div>
                                <x-input-label for="guest_phone" :value="__('Guest Phone')" />
                                <x-text-input id="guest_phone" name="guest_phone" type="tel" class="mt-1 block w-full" 
                                    :value="old('guest_phone', $reservation->guest_phone)" placeholder="e.g. +60123456789" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('guest_phone')" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="guest_email" :value="__('Guest Email (Optional)')" />
                            <x-text-input id="guest_email" name="guest_email" type="email" class="mt-1 block w-full" 
                                :value="old('guest_email', $reservation->guest_email)" placeholder="e.g. john@example.com"/>
                            <x-input-error class="mt-2" :messages="$errors->get('guest_email')" />
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="user_id" :value="__('Created By')" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            @if(old('user_id', $reservation->user_id ?: auth()->id()) == $user->id) selected @endif>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="pending" @if(old('status', $reservation->status) == 'pending') selected @endif>Pending</option>
                                    <option value="confirmed" @if(old('status', $reservation->status) == 'confirmed') selected @endif>Confirmed</option>
                                    <option value="seated" @if(old('status', $reservation->status) == 'seated') selected @endif>Seated</option>
                                    <option value="completed" @if(old('status', $reservation->status) == 'completed') selected @endif>Completed</option>
                                    <option value="cancelled" @if(old('status', $reservation->status) == 'cancelled') selected @endif>Cancelled</option>
                                    <option value="no_show" @if(old('status', $reservation->status) == 'no_show') selected @endif>No Show</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        
                        <div>
                            <x-input-label for="special_requests" :value="__('Special Requests')" />
                            <textarea id="special_requests" name="special_requests" rows="3" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                      placeholder="Any special requests or dietary requirements...">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('special_requests')" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="notes" :value="__('Internal Notes')" />
                            <textarea id="notes" name="notes" rows="3" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                      placeholder="Internal notes for staff...">{{ old('notes', $reservation->notes) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="auto_release_time" :value="__('Auto Release Time (Optional)')" />
                            <input type="datetime-local" id="auto_release_time" name="auto_release_time" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   value="{{ old('auto_release_time', $reservation->auto_release_time ? $reservation->auto_release_time->format('Y-m-d\TH:i') : '') }}"/>
                            <x-input-error class="mt-2" :messages="$errors->get('auto_release_time')" />
                            <p class="mt-1 text-sm text-gray-500">Automatically release table if guest doesn't show up by this time</p>
                        </div>
                    </div>

                    <!-- Current Confirmation Code (for edit) -->
                    @if($reservation->id)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900">Current Confirmation Code</h4>
                                <p class="mt-1 text-lg font-mono text-gray-700">{{ $reservation->confirmation_code }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Reservation') }}</x-primary-button>

                        <a href="{{ route('table-reservation.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Auto-suggest table based on number of guests
        document.getElementById('number_of_guests').addEventListener('change', function() {
            const guestCount = parseInt(this.value);
            const tableSelect = document.getElementById('table_id');
            const tables = Array.from(tableSelect.options);
            
            // Sort tables by how well they match the guest count
            tables.slice(1).forEach(option => {
                const tableInfo = option.textContent;
                const capacityMatch = tableInfo.match(/\((\d+) capacity\)/);
                if (capacityMatch) {
                    const capacity = parseInt(capacityMatch[1]);
                    const efficiency = capacity >= guestCount ? (guestCount / capacity) : 0;
                    option.style.backgroundColor = efficiency > 0.7 ? '#f0f9ff' : '';
                }
            });
        });

        // Validate reservation time is not in the past
        document.getElementById('reservation_date').addEventListener('change', validateDateTime);
        document.getElementById('reservation_time').addEventListener('change', validateDateTime);

        function validateDateTime() {
            const dateInput = document.getElementById('reservation_date');
            const timeInput = document.getElementById('reservation_time');
            
            if (dateInput.value && timeInput.value) {
                const reservationDateTime = new Date(dateInput.value + 'T' + timeInput.value);
                const now = new Date();
                
                if (reservationDateTime < now) {
                    alert('Reservation time cannot be in the past');
                    if (dateInput.value === new Date().toISOString().split('T')[0]) {
                        timeInput.value = '';
                    }
                }
            }
        }
    </script>
</x-app-layout>