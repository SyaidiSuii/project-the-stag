<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }} - #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Order #{{ $order->id }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ $order->user->name ?? 'Unknown Customer' }} - {{ $order->order_time->format('M d, Y h:i A') }}
                        @if($order->confirmation_code)
                            - {{ $order->confirmation_code }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('order.edit', $order->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Order
                    </a>
                    <a href="{{ route('order.duplicate', $order->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Duplicate
                    </a>
                    <a href="{{ route('order.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Quick Status Update -->
            @if(!in_array($order->order_status, ['completed', 'cancelled']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Status Update</h4>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex gap-2">
                            @if($order->order_status == 'pending')
                                <button onclick="updateOrderStatus('confirmed')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Confirm Order
                                </button>
                            @elseif($order->order_status == 'confirmed')
                                <button onclick="updateOrderStatus('preparing')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Start Preparing
                                </button>
                            @elseif($order->order_status == 'preparing')
                                <button onclick="updateOrderStatus('ready')" 
                                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                    Mark Ready
                                </button>
                            @elseif($order->order_status == 'ready')
                                <button onclick="updateOrderStatus('served')" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Mark Served
                                </button>
                            @elseif($order->order_status == 'served')
                                <button onclick="updateOrderStatus('completed')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Complete Order
                                </button>
                            @endif
                            
                            @if(!in_array($order->order_status, ['completed', 'cancelled']))
                                <a href="{{ route('order.cancel', $order->id) }}" 
                                   onclick="return confirm('Are you sure you want to cancel this order?')"
                                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Cancel Order
                                </a>
                            @endif
                        </div>

                        @if($order->payment_status != 'paid')
                        <div class="border-l pl-4">
                            <label class="text-sm text-gray-600 mr-2">Payment:</label>
                            <select onchange="updatePaymentStatus(this.value)" class="rounded border-gray-300">
                                <option value="unpaid" @if($order->payment_status == 'unpaid') selected @endif>Unpaid</option>
                                <option value="partial" @if($order->payment_status == 'partial') selected @endif>Partial</option>
                                <option value="paid" @if($order->payment_status == 'paid') selected @endif>Paid</option>
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Order Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Summary</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order ID:</span>
                                <p class="font-bold text-lg">#{{ $order->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Confirmation Code:</span>
                                @if($order->confirmation_code)
                                    <p class="font-mono text-lg font-bold">{{ $order->confirmation_code }}</p>
                                @else
                                    <p class="text-gray-500">Not assigned</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($order->order_status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->order_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($order->order_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($order->order_status == 'served') bg-indigo-100 text-indigo-800
                                        @elseif($order->order_status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $order->order_status) }}
                                    </span>
                                    @if($order->is_rush_order)
                                        <span class="ml-2 px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-bold">
                                            RUSH
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Payment Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status == 'unpaid') bg-red-100 text-red-800
                                        @elseif($order->payment_status == 'refunded') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->payment_status }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Type:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $order->order_type) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order Source:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $order->order_source) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <p class="font-bold text-xl text-green-600">RM {{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order Time:</span>
                                <p class="font-medium">{{ $order->order_time->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($order->table || $order->table_number)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Table Information:</span>
                            @if($order->table)
                                <p class="font-medium">Table {{ $order->table->table_number }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($order->table->table_type) }} ({{ $order->table->capacity }} capacity)</p>
                            @elseif($order->table_number)
                                <p class="font-medium">{{ $order->table_number }}</p>
                            @endif
                        </div>
                        @endif

                        @if($order->reservation)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Related Reservation:</span>
                            <p class="font-medium">{{ $order->reservation->confirmation_code }}</p>
                            <p class="text-sm text-gray-500">{{ $order->reservation->guest_name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customer Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($order->user)
                            <div>
                                <span class="text-sm text-gray-600">Name:</span>
                                <p class="font-medium text-lg">{{ $order->user->name }}</p>
                            </div>

                            <div>
                                <span class="text-sm text-gray-600">Email:</span>
                                <p class="font-medium">
                                    <a href="mailto:{{ $order->user->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $order->user->email }}
                                    </a>
                                </p>
                            </div>

                            @if($order->user->phone)
                            <div>
                                <span class="text-sm text-gray-600">Phone:</span>
                                <p class="font-medium">
                                    <a href="tel:{{ $order->user->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $order->user->phone }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        @else
                            <p class="text-gray-500">Customer information not available</p>
                        @endif

                        @if($order->special_instructions && count($order->special_instructions) > 0)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Special Instructions:</span>
                            <div class="mt-2 space-y-2">
                                @foreach($order->special_instructions as $instruction)
                                    @if($instruction)
                                        <p class="bg-yellow-50 p-3 rounded-md border border-yellow-200">{{ $instruction }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Timing Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Timing Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Order Placed:</span>
                            <p class="font-medium">{{ $order->order_time->format('M d, Y h:i A') }}</p>
                        </div>

                        @if($order->estimated_completion_time)
                        <div>
                            <span class="text-sm text-gray-600">Estimated Completion:</span>
                            <p class="font-medium">{{ $order->estimated_completion_time->format('M d, Y h:i A') }}</p>
                            @php
                                $now = now();
                                $isOverdue = $order->estimated_completion_time < $now && !$order->actual_completion_time;
                            @endphp
                            @if($isOverdue)
                                <span class="text-sm text-red-600 font-bold">OVERDUE</span>
                            @endif
                        </div>
                        @endif

                        @if($order->actual_completion_time)
                        <div>
                            <span class="text-sm text-gray-600">Actual Completion:</span>
                            <p class="font-medium">{{ $order->actual_completion_time->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Created:</span>
                            <p class="font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        @if($order->updated_at != $order->created_at)
                        <div>
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <p class="font-medium">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Items</h4>
                    </div>
                    <div class="p-6">
                        @if($order->items && $order->items->count() > 0)
                            <div class="space-y-3">
                                @foreach($order->items as $item)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $item->name ?? 'Item #' . $item->id }}</p>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity ?? 1 }}</p>
                                        @if($item->notes)
                                            <p class="text-sm text-yellow-700">Notes: {{ $item->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">RM {{ number_format($item->price ?? 0, 2) }}</p>
                                        <p class="text-sm text-gray-600">Total: RM {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="border-t mt-4 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold">Total Amount:</span>
                                    <span class="text-xl font-bold text-green-600">RM {{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No items found for this order</p>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function updateOrderStatus(status) {
            if (!confirm(`Are you sure you want to change the order status to '${status}'?`)) {
                return;
            }

            fetch(`{{ route('order.updateStatus', $order->id) }}`, {
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
                    location.reload();
                } else {
                    alert('Error updating order status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status');
            });
        }

        function updatePaymentStatus(status) {
            fetch(`{{ route('order.updatePaymentStatus', $order->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating payment status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating payment status');
            });
        }
    </script>
</x-app-layout>