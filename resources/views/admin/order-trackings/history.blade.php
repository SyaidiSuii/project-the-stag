<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Tracking History') }} - Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Order #{{ $order->id }} Timeline</h3>
                    <p class="text-sm text-gray-600">
                        @if($order->table)
                            Table {{ $order->table->table_number }} - 
                        @endif
                        {{ $order->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.order-trackings.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Add New Tracking
                    </a>
                    <a href="{{ route('admin.order-trackings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        All Trackings
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Order Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Order ID:</span>
                            <p class="font-bold text-lg">#{{ $order->id }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Status:</span>
                            <p>
                                <span class="px-3 py-1 text-sm rounded-full capitalize
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </p>
                        </div>

                        @if($order->table)
                        <div>
                            <span class="text-sm text-gray-600">Table:</span>
                            <p class="font-medium">{{ $order->table->table_number }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($order->table->table_type) }} ({{ $order->table->capacity }} capacity)</p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Total Amount:</span>
                            <p class="font-medium text-lg">RM {{ number_format($order->total_amount, 2) }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Payment Status:</span>
                            <p class="font-medium">{{ ucfirst($order->payment_status) }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Order Date:</span>
                            <p class="font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tracking Timeline -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Tracking Timeline</h4>
                    </div>
                    <div class="p-6">
                        @if($trackings->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($trackings as $tracking)
                                <li>
                                    <div class="relative pb-8 {{ $loop->last ? '' : 'border-l-2 border-gray-200' }}">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                    @if($tracking->status == 'completed') bg-green-500
                                                    @elseif($tracking->status == 'served') bg-indigo-500
                                                    @elseif($tracking->status == 'ready') bg-blue-500
                                                    @elseif($tracking->status == 'cooking') bg-orange-500
                                                    @elseif($tracking->status == 'preparing') bg-yellow-500
                                                    @elseif($tracking->status == 'confirmed') bg-green-400
                                                    @elseif($tracking->status == 'received') bg-gray-400
                                                    @else bg-gray-400 @endif">
                                                    @if($tracking->status == 'completed')
                                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @elseif($tracking->status == 'cooking')
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                                        </svg>
                                                    @elseif($tracking->status == 'preparing')
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 capitalize">
                                                            {{ str_replace('_', ' ', $tracking->status) }}
                                                            @if($tracking->station_name)
                                                                <span class="text-gray-500">- {{ $tracking->station_name }}</span>
                                                            @endif
                                                        </p>
                                                        @if($tracking->staff)
                                                            <p class="text-sm text-gray-500">by {{ $tracking->staff->name }}</p>
                                                        @endif
                                                        @if($tracking->notes)
                                                            <p class="text-sm text-gray-600 mt-1 bg-yellow-50 p-2 rounded border border-yellow-200">{{ $tracking->notes }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right whitespace-nowrap">
                                                        @if($tracking->started_at)
                                                            <p class="text-sm text-gray-500">{{ $tracking->started_at->format('h:i A') }}</p>
                                                            <p class="text-xs text-gray-400">{{ $tracking->started_at->format('M d') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($tracking->completed_at || $tracking->estimated_time || $tracking->actual_time)
                                                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                    @if($tracking->completed_at)
                                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded">
                                                            Completed: {{ $tracking->completed_at->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                    @if($tracking->estimated_time)
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                                            Est: {{ $tracking->estimated_time }}m
                                                        </span>
                                                    @endif
                                                    @if($tracking->actual_time)
                                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded">
                                                            Actual: {{ $tracking->actual_time }}m
                                                        </span>
                                                    @endif
                                                </div>
                                                @endif

                                                <div class="mt-2 flex gap-2">
                                                    <a href="{{ route('admin.order-trackings.show', $tracking->id) }}" 
                                                       class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                                        View Details
                                                    </a>
                                                    <a href="{{ route('admin.order-trackings.edit', $tracking->id) }}" 
                                                       class="text-xs bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700">
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tracking history</h3>
                            <p class="mt-1 text-sm text-gray-500">This order has no tracking records yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.order-trackings.create', ['order_id' => $order->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Create First Tracking
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                @if($order->items->count() > 0)
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Items</h4>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Item</th>
                                        <th class="text-left py-2">Qty</th>
                                        <th class="text-left py-2">Unit Price</th>
                                        <th class="text-left py-2">Total</th>
                                        <th class="text-left py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr class="border-b">
                                        <td class="py-2">
                                            <div>
                                                <p class="font-medium">{{ $item->menuItem->name ?? 'Unknown Item' }}</p>
                                                @if($item->special_instructions)
                                                    <p class="text-xs text-gray-500">{{ $item->special_instructions }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-2">{{ $item->quantity }}</td>
                                        <td class="py-2">RM {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="py-2 font-medium">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-1 text-xs rounded capitalize
                                                @if($item->status == 'completed') bg-green-100 text-green-800
                                                @elseif($item->status == 'preparing') bg-yellow-100 text-yellow-800
                                                @elseif($item->status == 'pending') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ str_replace('_', ' ', $item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-300">
                                        <td colspan="3" class="py-2 text-right font-semibold">Total:</td>
                                        <td class="py-2 font-bold text-lg">RM {{ number_format($order->total_amount, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Performance Summary -->
                @if($trackings->where('actual_time')->count() > 0)
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Performance Summary</h4>
                    </div>
                    <div class="p-6">
                        @php
                            $totalEstimated = $trackings->sum('estimated_time');
                            $totalActual = $trackings->sum('actual_time');
                            $avgEfficiency = $totalEstimated > 0 ? ($totalEstimated / $totalActual) * 100 : 0;
                        @endphp
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Total Estimated</p>
                                <p class="text-2xl font-bold">{{ $totalEstimated }}m</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Total Actual</p>
                                <p class="text-2xl font-bold">{{ $totalActual }}m</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Variance</p>
                                <p class="text-2xl font-bold {{ $totalActual - $totalEstimated <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $totalActual - $totalEstimated > 0 ? '+' : '' }}{{ $totalActual - $totalEstimated }}m
                                </p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Efficiency</p>
                                <p class="text-2xl font-bold {{ $avgEfficiency >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ number_format($avgEfficiency, 1) }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>