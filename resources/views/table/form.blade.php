<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($table->id)
                {{ __('Edit Table') }}
            @else
                {{ __('Create Table') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($table->id)
                            {{ __('Edit Table Information') }}
                        @else
                            {{ __('Table Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Table configuration and details.") }}
                    </p>
                </header>

                @if($table->id)
                    <form method="post" action="{{ route('table.update', $table->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('table.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="table_number" :value="__('Table Number')" />
                            <x-text-input id="table_number" name="table_number" type="text" class="mt-1 block w-full" :value="old('table_number', $table->table_number)" placeholder="e.g. T001, A1, VIP-1"/>
                            <x-input-error class="mt-2" :messages="$errors->get('table_number')" />
                        </div>

                        <div>
                            <x-input-label for="capacity" :value="__('Capacity (People)')" />
                            <x-text-input id="capacity" name="capacity" type="number" min="1" max="50" class="mt-1 block w-full" :value="old('capacity', $table->capacity)" placeholder="e.g. 4"/>
                            <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="available" @if(old('status', $table->status) == 'available') selected @endif>Available</option>
                                <option value="occupied" @if(old('status', $table->status) == 'occupied') selected @endif>Occupied</option>
                                <option value="reserved" @if(old('status', $table->status) == 'reserved') selected @endif>Reserved</option>
                                <option value="maintenance" @if(old('status', $table->status) == 'maintenance') selected @endif>Maintenance</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="table_type" :value="__('Table Type')" />
                            <select id="table_type" name="table_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="indoor" @if(old('table_type', $table->table_type) == 'indoor') selected @endif>Indoor</option>
                                <option value="outdoor" @if(old('table_type', $table->table_type) == 'outdoor') selected @endif>Outdoor</option>
                                <option value="private" @if(old('table_type', $table->table_type) == 'private') selected @endif>Private</option>
                                <option value="vip" @if(old('table_type', $table->table_type) == 'vip') selected @endif>VIP</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('table_type')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="qr_code" :value="__('QR Code')" />
                            <x-text-input id="qr_code" name="qr_code" type="text" class="mt-1 block w-full" :value="old('qr_code', $table->qr_code)" placeholder="e.g. QR_T001_2025"/>
                            <x-input-error class="mt-2" :messages="$errors->get('qr_code')" />
                        </div>

                        <div>
                            <x-input-label for="nfc_tag_id" :value="__('NFC Tag ID (Optional)')" />
                            <x-text-input id="nfc_tag_id" name="nfc_tag_id" type="text" class="mt-1 block w-full" :value="old('nfc_tag_id', $table->nfc_tag_id)" placeholder="e.g. NFC_001"/>
                            <x-input-error class="mt-2" :messages="$errors->get('nfc_tag_id')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="location_description" :value="__('Location Description')" />
                        <x-text-input id="location_description" name="location_description" type="text" class="mt-1 block w-full" :value="old('location_description', $table->location_description)" placeholder="e.g. Near window, Corner table, Main dining area"/>
                        <x-input-error class="mt-2" :messages="$errors->get('location_description')" />
                    </div>

                    <div>
                        <x-input-label for="amenities" :value="__('Amenities')" />
                        <div class="mt-2 space-y-2">
                            @php
                                $availableAmenities = [
                                    'wifi' => 'WiFi Available',
                                    'power_outlet' => 'Power Outlet',
                                    'window_view' => 'Window View',
                                    'air_conditioning' => 'Air Conditioning',
                                    'heating' => 'Heating',
                                    'wheelchair_accessible' => 'Wheelchair Accessible',
                                    'high_chair_available' => 'High Chair Available',
                                    'privacy_screen' => 'Privacy Screen',
                                    'soundproof' => 'Soundproof',
                                    'tv_screen' => 'TV Screen',
                                ];
                                
                                $tableAmenities = [];
                                if (old('amenities')) {
                                    $tableAmenities = old('amenities');
                                } elseif ($table->amenities) {
                                    if (is_array($table->amenities)) {
                                        $tableAmenities = $table->amenities;
                                    } else {
                                        $tableAmenities = json_decode($table->amenities, true) ?: [];
                                    }
                                }
                            @endphp
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($availableAmenities as $amenity => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @if(in_array($amenity, $tableAmenities)) checked @endif>
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('amenities')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="coordinates_lat" :value="__('Latitude (Optional)')" />
                            <x-text-input 
                                id="coordinates_lat" 
                                name="coordinates[lat]" 
                                type="text" 
                                class="mt-1 block w-full"
                                :value="old('coordinates.lat', data_get($table->coordinates, 'lat', ''))" 
                                placeholder="e.g. 1.4927"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('coordinates')" />
                        </div>

                        <div>
                            <x-input-label for="coordinates_lng" :value="__('Longitude (Optional)')" />
                            <x-text-input 
                                id="coordinates_lng" 
                                name="coordinates[lng]" 
                                type="text" 
                                class="mt-1 block w-full"
                                :value="old('coordinates.lng', data_get($table->coordinates, 'lng', ''))" 
                                placeholder="e.g. 103.7414"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('coordinates')" />
                        </div>

                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                               @if(old('is_active', $table->is_active ?? true)) checked @endif>
                        <label for="is_active" class="ml-2 text-sm text-gray-700">{{ __('Active Table') }}</label>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        <a href="{{ route('table.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>