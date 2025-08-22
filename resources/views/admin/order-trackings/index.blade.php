<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Trackings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('order-trackings.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Tracking
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('order-trackings.stats.performance') }}" class="items-center px-4 py-2 bg-blue-600 rounded font-semibold text-white hover:bg-blue-700">
                        Performance Stats
                    </a>
                    <a href="{{ route('order-trackings.stations.active-orders', ['station_name' => 'Kitchen']) }}" class="items-center px-4 py-2 bg-green-600 rounded font-semibold text-white hover:bg-green-700">
                        Kitchen View
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('order-trackings.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700">Order ID</label>
                            <input type="number" name="order_id" id="order_id" value="{{ request('order_id') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order ID...">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="received" @if(request('status') == 'received') selected @endif>Received</option>
                                <option value="confirmed" @if(request('status') == 'confirmed') selected @endif>Confirmed</option>
                                <option value="preparing" @if(request('status') == 'preparing') selected @endif>Preparing</option>
                                <option value="cooking" @if(request('status') == 'cooking') selected @endif>Cooking</option>
                                <option value="plating" @if(request('status') == 'plating') selected @endif>Plating</option>
                                <option value="ready" @if(request('status') == 'ready') selected @endif>Ready</option>
                                <option value="served" @if(request('status') == 'served') selected @endif>Served</option>
                                <option value="completed" @if(request('status') == 'completed') selected @endif>Completed</option>
                            </select>
                        </div>

                        <div>
                            <label for="station_name" class="block text-sm font-medium text-gray-700">Station</label>
                            <select name="station_name" id="station_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Stations</option>
                                <option value="Kitchen" @if(request('station_name') == 'Kitchen') selected @endif>Kitchen</option>
                                <option value="Bar" @if(request('station_name') == 'Bar') selected @endif>Bar</option>
                                <option value="Grill" @if(request('station_name') == 'Grill') selected @endif>Grill</option>
                                <option value="Pastry" @if(request('station_name') == 'Pastry') selected @endif>Pastry</option>
                                <option value="Cold Station" @if(request('station_name') == 'Cold Station') selected @endif>Cold Station</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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

                    @if(request()->hasAny(['order_id', 'status', 'station_name', 'date_from', 'date_to']))
                        <div class="mt-3">
                            <a href="{{ route('order-trackings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Order Trackings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Order</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Station</th>
                                    <th class="text-left py-2">Started</th>
                                    <th class="text-left py-2">Completed</th>
                                    <th class="text-left py-2">Time</th>
                                    <th class="text-left py-2">Staff</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trackings as $tracking)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($trackings->currentPage() - 1) * $trackings->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">Order #{{ $tracking->order->id }}</div>
                                            @if($tracking->order->table)
                                                <div class="text-sm text-gray-600">Table {{ $tracking->order->table->table_number }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500">{{ $tracking->order->created_at->format('M d, h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize
                                            @if($tracking->status == 'completed') bg-green-100 text-green-800
                                            @elseif($tracking->status == 'served') bg-indigo-100 text-indigo-800
                                            @elseif($tracking->status == 'ready') bg-blue-100 text-blue-800
                                            @elseif($tracking->status == 'cooking') bg-orange-100 text-orange-800
                                            @elseif($tracking->status == 'preparing') bg-yellow-100 text-yellow-800
                                            @elseif($tracking->status == 'confirmed') bg-green-100 text-green-800
                                            @elseif($tracking->status == 'received') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ str_replace('_', ' ', $tracking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tracking->station_name)
                                            <span class="font-medium">{{ $tracking->station_name }}</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tracking->started_at)
                                            <div>
                                                <div class="font-medium">{{ $tracking->started_at->format('h:i A') }}</div>
                                                <div class="text-sm text-gray-600">{{ $tracking->started_at->format('M d') }}</div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tracking->completed_at)
                                            <div>
                                                <div class="font-medium">{{ $tracking->completed_at->format('h:i A') }}</div>
                                                <div class="text-sm text-gray-600">{{ $tracking->completed_at->format('M d') }}</div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">In Progress</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tracking->actual_time)
                                            <span class="font-medium">{{ $tracking->actual_time }}m</span>
                                        @elseif($tracking->estimated_time)
                                            <span class="text-gray-500">Est: {{ $tracking->estimated_time }}m</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $tracking->staff->name ?? 'Unassigned' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('order-trackings.show', $tracking->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded text-xs text-white uppercase tracking-widest">
                                                View
                                            </a>
                                            <a href="{{ route('order-trackings.edit', $tracking->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            @if(!in_array($tracking->status, ['completed', 'served']))
                                                <form method="POST" action="{{ route('order-trackings.destroy', $tracking->id) }}" 
                                                      onsubmit="return confirm('Are you sure to delete this tracking?');" class="inline">
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
                                            <p class="text-lg font-medium">No order trackings found</p>
                                            <p class="text-sm">Try adjusting your search criteria or create a new tracking</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $trackings->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page every 30 seconds for real-time updates
        setTimeout(function() {
            window.location.reload();
        }, 30000); // 30 seconds
    </script>
</x-app-layout>