<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Orders Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('order.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Create New Order
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('order.today') }}" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Today's Orders
                    </a>
                    <button onclick="filterByStatus('pending')" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                        Pending Orders
                    </button>
                    <button onclick="filterByStatus('preparing')" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                        Preparing
                    </button>
                    <button onclick="filterByStatus('ready')" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                        Ready
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('order.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order ID, customer, confirmation...">
                        </div>

                        <div>
                            <label for="order_status" class="block text-sm font-medium text-gray-700">Order Status</label>
                            <select name="order_status" id="order_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" @if(request('order_status') == 'pending') selected @endif>Pending</option>
                                <option value="confirmed" @if(request('order_status') == 'confirmed') selected @endif>Confirmed</option>
                                <option value="preparing" @if(request('order_status') == 'preparing') selected @endif>Preparing</option>
                                <option value="ready" @if(request('order_status') == 'ready') selected @endif>Ready</option>
                                <option value="served" @if(request('order_status') == 'served') selected @endif>Served</option>
                                <option value="completed" @if(request('order_status') == 'completed') selected @endif>Completed</option>
                                <option value="cancelled" @if(request('order_status') == 'cancelled') selected @endif>Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label for="order_type" class="block text-sm font-medium text-gray-700">Order Type</label>
                            <select name="order_type" id="order_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Types</option>
                                <option value="dine_in" @if(request('order_type') == 'dine_in') selected @endif>Dine In</option>
                                <option value="takeaway" @if(request('order_type') == 'takeaway') selected @endif>Takeaway</option>
                                <option value="delivery" @if(request('order_type') == 'delivery') selected @endif>Delivery</option>
                                <option value="event" @if(request('order_type') == 'event') selected @endif>Event</option>
                            </select>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Payment Status</option>
                                <option value="unpaid" @if(request('payment_status') == 'unpaid') selected @endif>Unpaid</option>
                                <option value="partial" @if(request('payment_status') == 'partial') selected @endif>Partial</option>
                                <option value="paid" @if(request('payment_status') == 'paid') selected @endif>Paid</option>
                                <option value="refunded" @if(request('payment_status') == 'refunded') selected @endif>Refunded</option>
                            </select>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Order Date</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}" 
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

                    @if(request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                        <div class="mt-3">
                            <a href="{{ route('order.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Orders Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Order ID</th>
                                    <th class="text-left py-2">Customer</th>
                                    <th class="text-left py-2">Type/Source</th>
                                    <th class="text-left py-2">Table</th>
                                    <th class="text-left py-2">Amount</th>
                                    <th class="text-left py-2">Order Status</th>
                                    <th class="text-left py-2">Payment</th>
                                    <th class="text-left py-2">Order Time</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">#{{ $order->id }}</div>
                                            @if($order->confirmation_code)
                                                <div class="text-sm font-mono text-gray-600">{{ $order->confirmation_code }}</div>
                                            @endif
                                            @if($order->is_rush_order)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    RUSH
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $order->user->name ?? 'Unknown' }}</div>
                                        @if($order->user->email)
                                            <div class="text-sm text-gray-600">{{ $order->user->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="px-2 py-1 text-xs rounded capitalize bg-blue-100 text-blue-800">
                                                {{ str_replace('_', ' ', $order->order_type) }}
                                            </span>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ ucfirst(str_replace('_', ' ', $order->order_source)) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->table)
                                            <div class="font-medium">{{ $order->table->table_number }}</div>
                                            <div class="text-sm text-gray-600">{{ $order->table->table_type }}</div>
                                        @elseif($order->table_number)
                                            <div class="font-medium">{{ $order->table_number }}</div>
                                        @else
                                            <span class="text-gray-500">No table</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">RM {{ number_format($order->total_amount, 2) }}</div>
                                        @if($order->items_count)
                                            <div class="text-sm text-gray-600">{{ $order->items_count }} items</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize
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
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize
                                            @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                            @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                            @elseif($order->payment_status == 'unpaid') bg-red-100 text-red-800
                                            @elseif($order->payment_status == 'refunded') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $order->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $order->order_time->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $order->order_time->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('order.show', $order->id) }}" 
                                               class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-white shadow">
                                                View
                                            </a>
                                            <a href="{{ route('order.edit', $order->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            @if(!in_array($order->order_status, ['completed', 'cancelled']))
                                                <form method="POST" action="{{ route('order.destroy', $order->id) }}" 
                                                      onsubmit="return confirm('Are you sure to delete this order?');" class="inline">
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
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No orders found</p>
                                            <p class="text-sm">Try adjusting your search criteria or create a new order</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function filterByStatus(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('order_status', status);
            window.location.href = url.toString();
        }

        // Auto-refresh for kitchen display every 30 seconds
        if (window.location.search.includes('order_status=pending') || 
            window.location.search.includes('order_status=preparing')) {
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        }
    </script>
</x-app-layout>