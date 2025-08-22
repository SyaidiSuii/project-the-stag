<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Table Details') }} - {{ $table->table_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Table {{ $table->table_number }}</h3>
                            <p class="text-sm text-gray-600">{{ $table->location_description ?: 'No location specified' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('table.edit', $table->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Edit Table
                            </a>
                            <a href="{{ route('table.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                Back to List
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Basic Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Basic Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Table Number:</span>
                                    <span class="font-medium">{{ $table->table_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Capacity:</span>
                                    <span class="font-medium">{{ $table->capacity }} people</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Type:</span>
                                    <span class="px-2 py-1 text-xs rounded capitalize 
                                        @if($table->table_type == 'vip') bg-purple-100 text-purple-800
                                        @elseif($table->table_type == 'private') bg-blue-100 text-blue-800
                                        @elseif($table->table_type == 'outdoor') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $table->table_type }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 text-xs rounded capitalize
                                        @if($table->status == 'available') bg-green-100 text-green-800
                                        @elseif($table->status == 'occupied') bg-red-100 text-red-800
                                        @elseif($table->status == 'reserved') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $table->status }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Active:</span>
                                    <span class="px-2 py-1 text-xs rounded {{ $table->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $table->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Technical Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">QR Code:</span>
                                    <span class="font-mono text-xs">{{ $table->qr_code }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NFC Tag:</span>
                                    <span class="font-mono text-xs">{{ $table->nfc_tag_id ?: 'Not set' }}</span>
                                </div>
                                @if($table->coordinates)
                                    @php
                                        $coords = is_array($table->coordinates) ? $table->coordinates : json_decode($table->coordinates, true);
                                    @endphp
                                    @if(isset($coords['lat']) && isset($coords['lng']))
                                    <div>
                                        <span class="text-gray-600">Coordinates:</span>
                                        <div class="font-mono text-xs mt-1">
                                            <div>Lat: {{ $coords['lat'] }}</div>
                                            <div>Lng: {{ $coords['lng'] }}</div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Amenities</h4>
                            @if($table->amenities)
                                @php
                                    $amenities = is_array($table->amenities) ? $table->amenities : json_decode($table->amenities, true);
                                    $amenityLabels = [
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
                                @endphp
                                <div class="space-y-1">
                                    @forelse($amenities as $amenity)
                                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1 mb-1">
                                            {{ $amenityLabels[$amenity] ?? ucfirst(str_replace('_', ' ', $amenity)) }}
                                        </span>
                                    @empty
                                        <p class="text-gray-500 text-sm">No amenities specified</p>
                                    @endforelse
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">No amenities specified</p>
                            @endif
                        </div>

                    </div>

                    <!-- Location Description -->
                    @if($table->location_description)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-3">Location Description</h4>
                        <p class="text-sm text-gray-700">{{ $table->location_description }}</p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>