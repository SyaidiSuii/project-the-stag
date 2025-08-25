<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kitchen Dashboard') }} - {{ now()->format('M d, Y h:i A') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalActiveOrders = collect($activeTrackings)->flatten()->count();
                    $preparingCount = $activeTrackings->get('preparing', collect())->count() + $activeTrackings->get('confirmed', collect())->count();
                    $cookingCount = $activeTrackings->get('cooking', collect())->count();
                    $platingCount = $activeTrackings->get('plating', collect())->count();
                @endphp

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $totalActiveOrders }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalActiveOrders }}</dd>
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
                                    <span class="text-white font-bold text-sm">{{ $preparingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Preparing</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $preparingCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $cookingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Cooking</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $cookingCount }}</dd>
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
                                    <span class="text-white font-bold text-sm">{{ $platingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Plating</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $platingCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Active Orders by Station</h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.order-trackings.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        New Tracking
                    </a>
                    <a href="{{ route('admin.order-trackings.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        All Trackings
                    </a>
                    <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Orders by Station -->
            <div class="space-y-6">
                
                @forelse($activeTrackings as $stationName => $trackings)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">
                            {{ $stationName ?: 'No Station Assigned' }}
                            <span class="text-sm font-normal text-gray-600">({{ $trackings->count() }} orders)</span>
                        </h4>
                        <div class="text-sm text-gray-500">
                            Last updated: {{ now()->format('h:i A') }}
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($trackings->sortBy('started_at') as $tracking)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow
                                @if($tracking->status == 'confirmed') border-green-300 bg-green-50
                                @elseif($tracking->status == 'preparing') border-yellow-300 bg-yellow-50
                                @elseif($tracking->status == 'cooking') border-orange-300 bg-orange-50
                                @elseif($tracking->status == 'plating') border-blue-300 bg-blue-50
                                @else border-gray-300 @endif">
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h5 class="font-bold text-lg">Order #{{ $tracking->order->id }}</h5>
                                        @if($tracking->order->table)
                                            <p class="text-sm text-gray-600">Table {{ $tracking->order->table->table_number }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full capitalize font-medium
                                        @if($tracking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($tracking->status == 'preparing') bg-yellow-100 text-yellow-800
                                        @elseif($tracking->status == 'cooking') bg-orange-100 text-orange-800
                                        @elseif($tracking->status == 'plating') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $tracking->status) }}
                                    </span>
                                </div>

                                <!-- Order Items -->
                                @if($tracking->order->items->count() > 0)
                                <div class="mb-3">
                                    <p class="text-xs font-medium text-gray-700 mb-2">ITEMS:</p>
                                    <div class="space-y-1">
                                        @foreach($tracking->order->items->take(3) as $item)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Unknown' }}</span>
                                            <span class="text-gray-500">{{ ucfirst($item->status) }}</span>
                                        </div>
                                        @endforeach
                                        @if($tracking->order->items->count() > 3)
                                        <p class="text-xs text-gray-500">+{{ $tracking->order->items->count() - 3 }} more items</p>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Time Information -->
                                <div class="mb-3 text-sm text-gray-600">
                                    @if($tracking->started_at)
                                        <div class="flex items-center mb-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Started: {{ $tracking->started_at->format('h:i A') }}
                                            @php
                                                $elapsedMinutes = $tracking->started_at->diffInMinutes(now());
                                            @endphp
                                            <span class="ml-2 px-1 py-0.5 text-xs rounded {{ $elapsedMinutes > 30 ? 'bg-red-100 text-red-800' : ($elapsedMinutes > 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $elapsedMinutes }}m ago
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if($tracking->estimated_time)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Estimated: {{ $tracking->estimated_time }}m
                                        </div>
                                    @endif
                                </div>

                                <!-- Staff Assignment -->
                                @if($tracking->staff)
                                <div class="mb-3 text-sm">
                                    <span class="text-gray-600">Assigned to:</span>
                                    <span class="font-medium">{{ $tracking->staff->name }}</span>
                                </div>
                                @endif

                                <!-- Notes -->
                                @if($tracking->notes)
                                <div class="mb-3">
                                    <p class="text-xs text-amber-700 bg-amber-50 p-2 rounded border border-amber-200">
                                        <strong>Note:</strong> {{ Str::limit($tracking->notes, 50) }}
                                    </p>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-1">
                                        @if($tracking->status == 'confirmed')
                                            <form method="POST" action="{{ route('admin.order-trackings.update-status', $tracking->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit" class="px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                                    Start Prep
                                                </button>
                                            </form>
                                        @elseif($tracking->status == 'preparing')
                                            <form method="POST" action="{{ route('admin.order-trackings.update-status', $tracking->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cooking">
                                                <button type="submit" class="px-2 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700">
                                                    Start Cook
                                                </button>
                                            </form>
                                        @elseif($tracking->status == 'cooking')
                                            <form method="POST" action="{{ route('admin.order-trackings.update-status', $tracking->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="plating">
                                                <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                    Start Plate
                                                </button>
                                            </form>
                                        @elseif($tracking->status == 'plating')
                                            <form method="POST" action="{{ route('admin.order-trackings.update-status', $tracking->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="ready">
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                    Mark Ready
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-1">
                                        <a href="{{ route('admin.order-trackings.show', $tracking->id) }}" 
                                           class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                            View
                                        </a>
                                        <a href="{{ route('admin.order-trackings.edit', $tracking->id) }}" 
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
                @empty
                <!-- No Active Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No active orders</h3>
                        <p class="mt-1 text-sm text-gray-500">All orders have been completed. Great job!</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.order-trackings.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                View All Trackings
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse

            </div>

            <!-- Quick Actions Panel -->
            @if($totalActiveOrders > 0)
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Actions</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <form method="GET" action="{{ route('admin.order-trackings.stations.active-orders') }}" class="inline">
                            <input type="hidden" name="station_name" value="Kitchen">
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Kitchen Only
                            </button>
                        </form>
                        
                        <form method="GET" action="{{ route('admin.order-trackings.stations.active-orders') }}" class="inline">
                            <input type="hidden" name="station_name" value="Bar">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Bar Only
                            </button>
                        </form>
                        
                        <form method="GET" action="{{ route('admin.order-trackings.stations.active-orders') }}" class="inline">
                            <input type="hidden" name="station_name" value="Grill">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Grill Only
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.order-trackings.stats.performance') }}" 
                           class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-center">
                            Performance Stats
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-refresh page every 30 seconds for real-time updates
        setTimeout(function() {
            window.location.reload();
        }, 30000); // 30 seconds

        // Show confirmation for status updates
        document.querySelectorAll('form[action*="update-status"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const status = this.querySelector('input[name="status"]').value;
                const orderNumber = this.closest('.border').querySelector('h5').textContent;
                if (!confirm(`Update ${orderNumber} status to '${status}'?`)) {
                    e.preventDefault();
                }
            });
        });

        // Add visual indicator for orders that have been waiting too long
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.border.rounded-lg');
            cards.forEach(card => {
                const timeSpan = card.querySelector('.bg-red-100');
                if (timeSpan && timeSpan.textContent.includes('30')) {
                    card.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    card.style.animation = 'pulse 2s infinite';
                }
            });
        });
    </script>

    <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
    </style>
</x-app-layout>