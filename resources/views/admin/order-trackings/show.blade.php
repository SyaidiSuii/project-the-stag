<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Tracking Details') }} - Order #{{ $orderTracking->order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Order #{{ $orderTracking->order->id }} Tracking</h3>
                    <p class="text-sm text-gray-600">
                        {{ $orderTracking->station_name ? $orderTracking->station_name . ' - ' : '' }}
                        {{ $orderTracking->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('order-trackings.edit', $orderTracking->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Tracking
                    </a>
                    <a href="{{ route('orders.tracking-history', $orderTracking->order->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View History
                    </a>
                    <a href="{{ route('order-trackings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Quick Status Update -->
            @if(!in_array($orderTracking->status, ['completed', 'served']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Status Update</h4>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('order-trackings.update-status', $orderTracking->id) }}" class="flex items-end gap-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">New Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="received" @if($orderTracking->status == 'received') selected @endif>Received</option>
                                <option value="confirmed" @if($orderTracking->status == 'confirmed') selected @endif>Confirmed</option>
                                <option value="preparing" @if($orderTracking->status == 'preparing') selected @endif>Preparing</option>
                                <option value="cooking" @if($orderTracking->status == 'cooking') selected @endif>Cooking</option>
                                <option value="plating" @if($orderTracking->status == 'plating') selected @endif>Plating</option>
                                <option value="ready" @if($orderTracking->status == 'ready') selected @endif>Ready</option>
                                <option value="served" @if($orderTracking->status == 'served') selected @endif>Served</option>
                                <option value="completed" @if($orderTracking->status == 'completed') selected @endif>Completed</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <input type="text" name="notes" id="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add a note about this status change...">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Tracking Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Tracking Details</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order ID:</span>
                                <p class="font-bold text-lg">#{{ $orderTracking->order->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($orderTracking->status == 'completed') bg-green-100 text-green-800
                                        @elseif($orderTracking->status == 'served') bg-indigo-100 text-indigo-800
                                        @elseif($orderTracking->status == 'ready') bg-blue-100 text-blue-800
                                        @elseif($orderTracking->status == 'cooking') bg-orange-100 text-orange-800
                                        @elseif($orderTracking->status == 'preparing') bg-yellow-100 text-yellow-800
                                        @elseif($orderTracking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($orderTracking->status == 'received') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $orderTracking->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Station:</span>
                                <p class="font-medium">{{ $orderTracking->station_name ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Assigned Staff:</span>
                                <p class="font-medium">{{ $orderTracking->staff->name ?? 'Unassigned' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Started At:</span>
                                @if($orderTracking->started_at)
                                    <p class="font-medium">{{ $orderTracking->started_at->format('M d, Y h:i A') }}</p>
                                @else
                                    <p class="text-gray-500">Not started</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Completed At:</span>
                                @if($orderTracking->completed_at)
                                    <p class="font-medium">{{ $orderTracking->completed_at->format('M d, Y h:i A') }}</p>
                                @else
                                    <p class="text-gray-500">In progress</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Estimated Time:</span>
                                <p class="font-medium">{{ $orderTracking->estimated_time ? $orderTracking->estimated_time . ' minutes' : 'Not set' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Actual Time:</span>
                                @if($orderTracking->actual_time)
                                    <p class="font-medium">{{ $orderTracking->actual_time }} minutes</p>
                                    @if($orderTracking->estimated_time)
                                        @php
                                            $variance = $orderTracking->actual_time - $orderTracking->estimated_time;
                                        @endphp
                                        <p class="text-xs {{ $variance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $variance > 0 ? '+' : '' }}{{ $variance }} min {{ $variance > 0 ? 'over' : 'under' }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-gray-500">Not completed</p>
                                @endif
                            </div>
                        </div>

                        @if($orderTracking->notes)
                        <div>
                            <span class="text-sm text-gray-600">Notes:</span>
                            <p class="font-medium bg-yellow-50 p-3 rounded-md border border-yellow-200">{{ $orderTracking->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Status:</span>
                                <p class="font-medium">{{ ucfirst($orderTracking->order->status) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Payment Status:</span>
                                <p class="font-medium">{{ ucfirst($orderTracking->order->payment_status) }}</p>
                            </div>
                        </div>

                        @if($orderTracking->order->table)
                        <div>
                            <span class="text-sm text-gray-600">Table:</span>
                            <p class="font-medium">{{ $orderTracking->order->table->table_number }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($orderTracking->order->table->table_type) }} ({{ $orderTracking->order->table->capacity }} capacity)</p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Order Date:</span>
                            <p class="font-medium">{{ $orderTracking->order->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Total Amount:</span>
                            <p class="font-medium text-lg">RM {{ number_format($orderTracking->order->total_amount, 2) }}</p>
                        </div>

                        @if($orderTracking->order->items->count() > 0)
                        <div>
                            <span class="text-sm text-gray-600">Order Items:</span>
                            <div class="mt-2 space-y-2">
                                @foreach($orderTracking->order->items as $item)
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <div>
                                        <p class="font-medium">{{ $item->menuItem->name ?? 'Unknown Item' }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($item->status) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">System Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Created At:</span>
                            <p class="font-medium">{{ $orderTracking->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        @if($orderTracking->updated_at != $orderTracking->created_at)
                        <div>
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <p class="font-medium">{{ $orderTracking->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Tracking ID:</span>
                            <p class="font-mono text-sm">{{ $orderTracking->id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                @if($orderTracking->actual_time && $orderTracking->estimated_time)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Performance Metrics</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @php
                            $efficiency = ($orderTracking->estimated_time / $orderTracking->actual_time) * 100;
                            $variance = $orderTracking->actual_time - $orderTracking->estimated_time;
                            $variancePercentage = ($variance / $orderTracking->estimated_time) * 100;
                        @endphp
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Efficiency:</span>
                                <p class="font-medium text-lg {{ $efficiency >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ number_format($efficiency, 1) }}%
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Time Variance:</span>
                                <p class="font-medium text-lg {{ $variance <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $variance > 0 ? '+' : '' }}{{ $variance }} min
                                    ({{ $variancePercentage > 0 ? '+' : '' }}{{ number_format($variancePercentage, 1) }}%)
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $efficiency >= 100 ? 'green' : 'orange' }}-600 h-2 rounded-full" 
                                     style="width: {{ min($efficiency, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Efficiency meter (100% = on time or faster)</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>