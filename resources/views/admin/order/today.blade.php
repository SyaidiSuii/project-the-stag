<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Today's Orders") }} - {{ now()->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                @php
                    $totalOrders = collect($orders)->flatten()->count();
                    $pendingCount = $orders->get('pending', collect())->count();
                    $preparingCount = $orders->get('preparing', collect())->count();
                    $readyCount = $orders->get('ready', collect())->count();
                    $servedCount = $orders->get('served', collect())->count();
                    $completedCount = $orders->get('completed', collect())->count();
                    $totalRevenue = collect($orders)->flatten()->where('payment_status', 'paid')->sum('total_amount');
                @endphp

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $totalOrders }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalOrders }}</dd>
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
                                    <span class="text-white font-bold text-sm">{{ $pendingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
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
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $preparingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
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
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $readyCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ready</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $readyCount }}</dd>
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
                                    <span class="text-white font-bold text-sm">{{ $servedCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Served</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $servedCount }}</dd>
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
                                    <span class="text-white font-bold text-xs">RM</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalRevenue, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Orders by Status</h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.order.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        New Order
                    </a>
                    <a href="{{ route('admin.order.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        All Orders
                    </a>
                </div>
            </div>

            <!-- Kitchen Display - Active Orders -->
            <div class="space-y-6">
                
                @foreach(['pending', 'confirmed', 'preparing', 'ready', 'served'] as $status)
                    @if($orders->has($status) && $orders->get($status)->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b flex justify-between items-center
                            @if($status == 'pending') bg-yellow-50
                            @elseif($status == 'confirmed') bg-green-50
                            @elseif($status == 'preparing') bg-blue-50
                            @elseif($status == 'ready') bg-purple-50
                            @elseif($status == 'served') bg-indigo-50
                            @else bg-gray-50 @endif">
                            <h4 class="font-semibold text-gray-800 capitalize">
                                {{ str_replace('_', ' ', $status) }} Orders 
                                <span class="text-sm font-normal text-gray-600">({{ $orders->get($status)->count() }})</span>
                            </h4>
                            @if(in_array($status, ['preparing', 'ready']))
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Kitchen Priority</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($orders->get($status)->sortBy('order_time') as $order)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow
                                    @if($order->is_rush_order) border-red-500 bg-red-50 @endif">
                                    
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h5 class="font-bold text-lg">#{{ $order->id }}</h5>
                                            <p class="text-sm text-gray-600">{{ $order->user->name ?? 'Unknown' }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($order->confirmation_code)
                                                <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $order->confirmation_code }}</span>
                                            @endif
                                            @if($order->is_rush_order)
                                                <div class="mt-1">
                                                    <span class="text-xs font-bold bg-red-100 text-red-800 px-2 py-1 rounded">RUSH</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $order->order_time->format('h:i A') }}
                                            </div>
                                            <span class="font-bold text-green-600">RM {{ number_format($order->total_amount, 2) }}</span>
                                        </div>

                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            @if($order->table)
                                                Table {{ $order->table->table_number }}
                                            @elseif($order->table_number)
                                                {{ $order->table_number }}
                                            @else
                                                {{ ucfirst($order->order_type) }}
                                            @endif
                                        </div>

                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                        </div>

                                        @if($order->estimated_completion_time)
                                            @php
                                                $isOverdue = $order->estimated_completion_time < now() && !$order->actual_completion_time;
                                            @endphp
                                            <div class="flex items-center {{ $isOverdue ? 'text-red-600 font-bold' : '' }}">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                ETA: {{ $order->estimated_completion_time->format('h:i A') }}
                                                @if($isOverdue)
                                                    <span class="ml-2 text-xs bg-red-100 px-1 rounded">OVERDUE</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    @if($order->special_instructions && count($order->special_instructions) > 0)
                                    <div class="mb-3">
                                        <div class="text-xs text-yellow-700 bg-yellow-50 p-2 rounded border border-yellow-200">
                                            <strong>Special Instructions:</strong>
                                            @foreach($order->special_instructions as $instruction)
                                                @if($instruction)
                                                    <div>• {{ $instruction }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Order Items Preview -->
                                    @if($order->items && $order->items->count() > 0)
                                    <div class="mb-3 text-xs bg-gray-50 p-2 rounded">
                                        <strong>Items ({{ $order->items->count() }}):</strong>
                                        @foreach($order->items->take(3) as $item)
                                            <div>{{ $item->quantity ?? 1 }}x {{ $item->name ?? 'Item' }}</div>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <div class="text-gray-500">... and {{ $order->items->count() - 3 }} more items</div>
                                        @endif
                                    </div>
                                    @endif

                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-1">
                                            @if($status == 'pending')
                                                <button onclick="updateOrderStatus({{ $order->id }}, 'confirmed')" 
                                                        class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                    Confirm
                                                </button>
                                            @elseif($status == 'confirmed')
                                                <button onclick="updateOrderStatus({{ $order->id }}, 'preparing')" 
                                                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                    Start Preparing
                                                </button>
                                            @elseif($status == 'preparing')
                                                <button onclick="updateOrderStatus({{ $order->id }}, 'ready')" 
                                                        class="px-2 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700">
                                                    Ready
                                                </button>
                                            @elseif($status == 'ready')
                                                <button onclick="updateOrderStatus({{ $order->id }}, 'served')" 
                                                        class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                                    Served
                                                </button>
                                            @elseif($status == 'served')
                                                <button onclick="updateOrderStatus({{ $order->id }}, 'completed')" 
                                                        class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                    Complete
                                                </button>
                                            @endif

                                            <span class="px-2 py-1 text-xs rounded
                                                @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                                @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                                @elseif($order->payment_status == 'unpaid') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex space-x-1">
                                            <a href="{{ route('admin.order.show', $order->id) }}" 
                                               class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                View
                                            </a>
                                            <a href="{{ route('admin.order.edit', $order->id) }}" 
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

                <!-- Completed and Cancelled Orders -->
                @php
                    $finishedOrders = collect();
                    if ($orders->has('completed')) {
                        $finishedOrders = $finishedOrders->merge($orders->get('completed'));
                    }
                    if ($orders->has('cancelled')) {
                        $finishedOrders = $finishedOrders->merge($orders->get('cancelled'));
                    }
                @endphp

                @if($finishedOrders->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">
                            Completed & Cancelled Orders
                            <span class="text-sm font-normal text-gray-600">({{ $finishedOrders->count() }})</span>
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($finishedOrders->sortByDesc('order_time') as $order)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $order->order_status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                    <div>
                                        <p class="font-medium">#{{ $order->id }} - {{ $order->user->name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $order->order_time->format('h:i A') }} - 
                                            RM {{ number_format($order->total_amount, 2) }}
                                            @if($order->table)
                                                - Table {{ $order->table->table_number }}
                                            @elseif($order->table_number)
                                                - {{ $order->table_number }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.order.show', $order->id) }}" 
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
                @if($totalOrders == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders today</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.order.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                New Order
                            </a>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function updateOrderStatus(orderId, status) {
            if (!confirm(`Are you sure you want to mark this order as '${status}'?`)) {
                return;
            }

            fetch(`/order/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Error updating order status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status');
            });
        }

        // Auto-refresh page every 60 seconds for kitchen display
        setTimeout(function() {
            window.location.reload();
        }, 60000); // 1 minute

        // Visual timer for rush orders
        function updateRushOrderTimers() {
            const rushOrders = document.querySelectorAll('[data-rush-order]');
            rushOrders.forEach(order => {
                const orderTime = new Date(order.dataset.orderTime);
                const now = new Date();
                const diffMinutes = Math.floor((now - orderTime) / 60000);
                
                if (diffMinutes > 15) {
                    order.classList.add('border-red-600', 'bg-red-100');
                    order.querySelector('.rush-timer').textContent = `${diffMinutes} min ago`;
                }
            });
        }

        // Update timers every minute
        setInterval(updateRushOrderTimers, 60000);
        updateRushOrderTimers(); // Initial call
    </script>
</x-app-layout>