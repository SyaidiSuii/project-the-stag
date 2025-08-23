<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Item Details') }} - #{{ $orderItem->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Order Item #{{ $orderItem->id }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ $orderItem->menuItem->name ?? 'Unknown Item' }} - 
                        Order #{{ $orderItem->order_id }} - 
                        {{ $orderItem->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('order-item.edit', $orderItem->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Item
                    </a>
                    <a href="{{ route('order-item.duplicate', $orderItem->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Duplicate
                    </a>
                    <a href="{{ route('order-item.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Quick Status Update -->
            @if($orderItem->item_status != 'served')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Status Update</h4>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex gap-2">
                            @if($orderItem->item_status == 'pending')
                                <button onclick="updateItemStatus('preparing')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Start Preparing
                                </button>
                            @elseif($orderItem->item_status == 'preparing')
                                <button onclick="updateItemStatus('ready')" 
                                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                    Mark Ready
                                </button>
                            @elseif($orderItem->item_status == 'ready')
                                <button onclick="updateItemStatus('served')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Mark Served
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Item Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Item Details</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Item ID:</span>
                                <p class="font-bold text-lg">#{{ $orderItem->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($orderItem->item_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($orderItem->item_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($orderItem->item_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($orderItem->item_status == 'served') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $orderItem->item_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Menu Item:</span>
                                <p class="font-medium text-lg">{{ $orderItem->menuItem->name ?? 'Unknown Item' }}</p>
                                @if($orderItem->menuItem->category ?? false)
                                    <p class="text-sm text-gray-500">{{ $orderItem->menuItem->category }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Quantity:</span>
                                <p class="font-bold text-2xl text-blue-600">{{ $orderItem->quantity }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Unit Price:</span>
                                <p class="font-medium text-lg">RM {{ number_format($orderItem->unit_price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total Price:</span>
                                <p class="font-bold text-xl text-green-600">RM {{ number_format($orderItem->total_price, 2) }}</p>
                            </div>
                        </div>

                        @if($orderItem->special_note)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Special Note:</span>
                            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <p class="text-gray-700">{{ $orderItem->special_note }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $orderItem->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($orderItem->updated_at != $orderItem->created_at)
                                <div>
                                    <span class="text-gray-600">Last Updated:</span>
                                    <p class="font-medium">{{ $orderItem->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Order Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Related Order Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order ID:</span>
                                <p class="font-bold text-lg">
                                    <a href="{{ route('order.show', $orderItem->order_id) }}" class="text-blue-600 hover:text-blue-800">
                                        #{{ $orderItem->order_id }}
                                    </a>
                                </p>
                            </div>
                            @if($orderItem->order->confirmation_code ?? false)
                            <div>
                                <span class="text-sm text-gray-600">Confirmation Code:</span>
                                <p class="font-mono text-lg font-bold">{{ $orderItem->order->confirmation_code }}</p>
                            </div>
                            @endif
                        </div>

                        @if($orderItem->order->user ?? false)
                        <div>
                            <span class="text-sm text-gray-600">Customer:</span>
                            <p class="font-medium text-lg">{{ $orderItem->order->user->name }}</p>
                            @if($orderItem->order->user->email)
                                <p class="text-sm text-gray-500">
                                    <a href="mailto:{{ $orderItem->order->user->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $orderItem->order->user->email }}
                                    </a>
                                </p>
                            @endif
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($orderItem->order->order_status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($orderItem->order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($orderItem->order->order_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($orderItem->order->order_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($orderItem->order->order_status == 'served') bg-indigo-100 text-indigo-800
                                        @elseif($orderItem->order->order_status == 'completed') bg-green-100 text-green-800
                                        @elseif($orderItem->order->order_status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $orderItem->order->order_status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Payment Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($orderItem->order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($orderItem->order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                        @elseif($orderItem->order->payment_status == 'unpaid') bg-red-100 text-red-800
                                        @elseif($orderItem->order->payment_status == 'refunded') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $orderItem->order->payment_status }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Type:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $orderItem->order->order_type) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order Total:</span>
                                <p class="font-bold text-xl text-green-600">RM {{ number_format($orderItem->order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        @if($orderItem->order->table || $orderItem->order->table_number)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Table Information:</span>
                            @if($orderItem->order->table)
                                <p class="font-medium">Table {{ $orderItem->order->table->table_number }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($orderItem->order->table->table_type) }} ({{ $orderItem->order->table->capacity }} capacity)</p>
                            @elseif($orderItem->order->table_number)
                                <p class="font-medium">{{ $orderItem->order->table_number }}</p>
                            @endif
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Order Time:</span>
                            <p class="font-medium">{{ $orderItem->order->order_time->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item Information -->
                @if($orderItem->menuItem)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Menu Item Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Name:</span>
                            <p class="font-medium text-lg">{{ $orderItem->menuItem->name }}</p>
                        </div>

                        @if($orderItem->menuItem->description)
                        <div>
                            <span class="text-sm text-gray-600">Description:</span>
                            <p class="text-gray-700">{{ $orderItem->menuItem->description }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if($orderItem->menuItem->category)
                            <div>
                                <span class="text-sm text-gray-600">Category:</span>
                                <p class="font-medium">{{ $orderItem->menuItem->category }}</p>
                            </div>
                            @endif
                            <div>
                                <span class="text-sm text-gray-600">Current Price:</span>
                                <p class="font-medium">RM {{ number_format($orderItem->menuItem->price, 2) }}</p>
                                @if($orderItem->unit_price != $orderItem->menuItem->price)
                                    <p class="text-sm text-orange-600">(Ordered at RM {{ number_format($orderItem->unit_price, 2) }})</p>
                                @endif
                            </div>
                        </div>

                        @if($orderItem->menuItem->is_available !== null)
                        <div>
                            <span class="text-sm text-gray-600">Availability:</span>
                            <p>
                                <span class="px-2 py-1 text-xs rounded
                                    {{ $orderItem->menuItem->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $orderItem->menuItem->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Other Items in This Order</h4>
                    </div>
                    <div class="p-6">
                        @if($orderItem->order->items && $orderItem->order->items->count() > 1)
                            <div class="space-y-3">
                                @foreach($orderItem->order->items->where('id', '!=', $orderItem->id) as $otherItem)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $otherItem->menuItem->name ?? 'Unknown Item' }}</p>
                                        <p class="text-sm text-gray-600">
                                            Quantity: {{ $otherItem->quantity }} | 
                                            Status: <span class="capitalize">{{ str_replace('_', ' ', $otherItem->item_status) }}</span>
                                        </p>
                                        @if($otherItem->special_note)
                                            <p class="text-sm text-yellow-700">Note: {{ Str::limit($otherItem->special_note, 50) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">RM {{ number_format($otherItem->total_price, 2) }}</p>
                                        <a href="{{ route('order-item.show', $otherItem->id) }}" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="border-t mt-4 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold">Order Total:</span>
                                    <span class="text-xl font-bold text-green-600">RM {{ number_format($orderItem->order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">This is the only item in the order</p>
                        @endif
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>

    <script>
        function updateItemStatus(status) {
            if (!confirm(`Are you sure you want to change the item status to '${status}'?`)) {
                return;
            }

            fetch(`{{ route('order-item.updateStatus', $orderItem->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating item status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating item status');
            });
        }
    </script>
</x-app-layout>