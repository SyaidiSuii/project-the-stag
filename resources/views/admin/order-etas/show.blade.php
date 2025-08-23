<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order ETA Details') }} - #{{ $orderEta->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Order ETA #{{ $orderEta->id }}</h3>
                    <p class="text-sm text-gray-600">
                        Order #{{ $orderEta->order->id }} - {{ $orderEta->order->user->name ?? 'Unknown Customer' }}
                        @if($orderEta->order->confirmation_code)
                            - {{ $orderEta->order->confirmation_code }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('order-etas.edit', $orderEta->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit ETA
                    </a>
                    <a href="{{ route('order.show', $orderEta->order->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Order
                    </a>
                    <a href="{{ route('order-etas.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Status Alert -->
            @if($orderEta->is_delayed)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <strong class="font-bold">Order Delayed!</strong>
                        <span class="block sm:inline ml-2">
                            This order is delayed by {{ $orderEta->delay_duration }} minutes.
                            @if(!$orderEta->customer_notified)
                                Customer has not been notified yet.
                            @else
                                Customer has been notified.
                            @endif
                        </span>
                    </div>
                </div>
            @else
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <strong class="font-bold">On Time!</strong>
                        <span class="block sm:inline ml-2">This order is progressing as scheduled.</span>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @if(!$orderEta->actual_completion_time || ($orderEta->is_delayed && !$orderEta->customer_notified))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Actions</h4>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4 flex-wrap">
                        @if($orderEta->is_delayed && !$orderEta->customer_notified)
                            <button onclick="notifyCustomer()" 
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Notify Customer
                            </button>
                        @endif
                        
                        @if(!$orderEta->actual_completion_time && $orderEta->order->order_status !== 'completed')
                            <button onclick="markCompleted()" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Mark as Completed
                            </button>
                        @endif

                        <div class="border-l pl-4 flex gap-2">
                            <button onclick="updateEstimate(15)" 
                                    class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                +15 min
                            </button>
                            <button onclick="updateEstimate(30)" 
                                    class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                +30 min
                            </button>
                            <button onclick="updateEstimate(-10)" 
                                    class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                -10 min
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- ETA Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">ETA Summary</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">ETA ID:</span>
                                <p class="font-bold text-lg">#{{ $orderEta->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order ID:</span>
                                <p class="font-bold text-lg">
                                    <a href="{{ route('order.show', $orderEta->order->id) }}" class="text-blue-600 hover:text-blue-800">
                                        #{{ $orderEta->order->id }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h5 class="font-medium text-gray-900 mb-3">Time Estimates (in minutes)</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <span class="text-sm text-gray-600">Initial Estimate</span>
                                        <p class="font-bold text-blue-600">{{ $orderEta->initial_estimate }} minutes</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">Original estimate</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center p-3 rounded-lg {{ $orderEta->current_estimate > $orderEta->initial_estimate ? 'bg-red-50' : 'bg-green-50' }}">
                                    <div>
                                        <span class="text-sm text-gray-600">Current Estimate</span>
                                        <p class="font-bold {{ $orderEta->current_estimate > $orderEta->initial_estimate ? 'text-red-600' : 'text-green-600' }}">{{ $orderEta->current_estimate }} minutes</p>
                                    </div>
                                    <div class="text-right">
                                        @if($orderEta->current_estimate > $orderEta->initial_estimate)
                                            <span class="text-xs text-red-600">+{{ $orderEta->current_estimate - $orderEta->initial_estimate }} min</span>
                                        @elseif($orderEta->current_estimate < $orderEta->initial_estimate)
                                            <span class="text-xs text-green-600">{{ $orderEta->current_estimate - $orderEta->initial_estimate }} min</span>
                                        @else
                                            <span class="text-xs text-green-600">On schedule</span>
                                        @endif
                                    </div>
                                </div>

                                @if($orderEta->actual_completion_time)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="text-sm text-gray-600">Actual Completion</span>
                                        <p class="font-bold text-gray-600">{{ $orderEta->actual_completion_time }} minutes</p>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $variance = $orderEta->actual_completion_time - $orderEta->initial_estimate;
                                        @endphp
                                        @if($variance > 0)
                                            <span class="text-xs text-red-600">+{{ $variance }} min vs initial</span>
                                        @elseif($variance < 0)
                                            <span class="text-xs text-green-600">{{ $variance }} min vs initial</span>
                                        @else
                                            <span class="text-xs text-green-600">Exact match!</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($orderEta->is_delayed)
                        <div class="border-t pt-4">
                            <h5 class="font-medium text-gray-900 mb-3">Delay Information</h5>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Delay Duration:</span>
                                    <span class="font-medium text-red-600">{{ $orderEta->delay_duration }} minutes</span>
                                </div>
                                @if($orderEta->delay_reason)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Reason:</span>
                                    <span class="font-medium">{{ $orderEta->delay_reason }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Customer Notified:</span>
                                    <span class="font-medium {{ $orderEta->customer_notified ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $orderEta->customer_notified ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Last Updated:</span>
                                <span class="font-medium">{{ $orderEta->last_updated->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Related Order Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Customer:</span>
                                <p class="font-medium text-lg">{{ $orderEta->order->user->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <p class="font-bold text-lg text-green-600">RM {{ number_format($orderEta->order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($orderEta->order->order_status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($orderEta->order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($orderEta->order->order_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($orderEta->order->order_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($orderEta->order->order_status == 'served') bg-indigo-100 text-indigo-800
                                        @elseif($orderEta->order->order_status == 'completed') bg-green-100 text-green-800
                                        @elseif($orderEta->order->order_status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $orderEta->order->order_status) }}
                                    </span>
                                    @if($orderEta->order->is_rush_order)
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
                                        @if($orderEta->order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($orderEta->order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                        @elseif($orderEta->order->payment_status == 'unpaid') bg-red-100 text-red-800
                                        @elseif($orderEta->order->payment_status == 'refunded') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $orderEta->order->payment_status }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Type:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $orderEta->order->order_type) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order Source:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $orderEta->order->order_source) }}</p>
                            </div>
                        </div>

                        @if($orderEta->order->table || $orderEta->order->table_number)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Table Information:</span>
                            @if($orderEta->order->table)
                                <p class="font-medium">Table {{ $orderEta->order->table->table_number }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($orderEta->order->table->table_type) }} ({{ $orderEta->order->table->capacity }} capacity)</p>
                            @elseif($orderEta->order->table_number)
                                <p class="font-medium">{{ $orderEta->order->table_number }}</p>
                            @endif
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Order Time:</span>
                                    <p class="font-medium">{{ $orderEta->order->order_time->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($orderEta->order->estimated_completion_time)
                                <div>
                                    <span class="text-sm text-gray-600">Original ETA:</span>
                                    <p class="font-medium">{{ $orderEta->order->estimated_completion_time->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                @if($orderEta->order->items && $orderEta->order->items->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Items ({{ $orderEta->order->items->count() }})</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($orderEta->order->items as $item)
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
                                <span class="text-xl font-bold text-green-600">RM {{ number_format($orderEta->order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Timeline/History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">ETA Timeline</h4>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">ETA created with initial estimate of <span class="font-medium text-gray-900">{{ $orderEta->initial_estimate }} minutes</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $orderEta->created_at->format('M d, h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($orderEta->is_delayed)
                                <li>
                                    <div class="relative pb-8">
                                        @if($orderEta->actual_completion_time)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Order delayed by <span class="font-medium text-red-600">{{ $orderEta->delay_duration }} minutes</span></p>
                                                    @if($orderEta->delay_reason)
                                                        <p class="text-sm text-gray-400">Reason: {{ $orderEta->delay_reason }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $orderEta->last_updated->format('M d, h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($orderEta->customer_notified && $orderEta->is_delayed)
                                <li>
                                    <div class="relative pb-8">
                                        @if($orderEta->actual_completion_time)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Customer notified about delay</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $orderEta->updated_at->format('M d, h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($orderEta->actual_completion_time)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Order completed in <span class="font-medium text-green-600">{{ $orderEta->actual_completion_time }} minutes</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $orderEta->order->actual_completion_time ? $orderEta->order->actual_completion_time->format('M d, h:i A') : 'Unknown' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // Notify customer function
        function notifyCustomer() {
            if (!confirm('Are you sure you want to notify the customer about the delay?')) {
                return;
            }

            fetch('{{ route("order-etas.notifyCustomer", $orderEta->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error notifying customer');
            });
        }

        // Mark as completed function
        function markCompleted() {
            if (!confirm('Are you sure you want to mark this order as completed?')) {
                return;
            }

            fetch('{{ route("order-etas.markCompleted", $orderEta->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as completed');
            });
        }

        // Update estimate function
        function updateEstimate(minutes) {
            const currentEstimate = {{ $orderEta->current_estimate }};
            const newEstimate = Math.max(1, currentEstimate + minutes);
            
            if (!confirm(`Update estimate to ${newEstimate} minutes?`)) {
                return;
            }

            fetch('{{ route("order-etas.updateEstimate", $orderEta->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_estimate: newEstimate,
                    delay_reason: minutes > 0 ? 'Time adjustment needed' : null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating estimate');
            });
        }
    </script>
</x-app-layout>