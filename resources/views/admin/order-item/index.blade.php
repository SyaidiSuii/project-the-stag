<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Items Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('admin.order-item.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Order Item
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.order-item.kitchen') }}" class="px-4 py-2 bg-orange-600 text-white rounded font-semibold hover:bg-orange-700">
                        Kitchen View
                    </a>
                    <button onclick="filterByStatus('pending')" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                        Pending Items
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
                    <form method="GET" action="{{ route('admin.order-item.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order ID, menu item...">
                        </div>

                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700">Order</label>
                            <input type="text" name="order_id" id="order_id" value="{{ request('order_id') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order ID">
                        </div>

                        <div>
                            <label for="item_status" class="block text-sm font-medium text-gray-700">Item Status</label>
                            <select name="item_status" id="item_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" @if(request('item_status') == 'pending') selected @endif>Pending</option>
                                <option value="preparing" @if(request('item_status') == 'preparing') selected @endif>Preparing</option>
                                <option value="ready" @if(request('item_status') == 'ready') selected @endif>Ready</option>
                                <option value="served" @if(request('item_status') == 'served') selected @endif>Served</option>
                            </select>
                        </div>

                        <div>
                            <label for="menu_item" class="block text-sm font-medium text-gray-700">Menu Item</label>
                            <input type="text" name="menu_item" id="menu_item" value="{{ request('menu_item') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Menu item name">
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
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

                    @if(request()->hasAny(['search', 'order_id', 'item_status', 'menu_item', 'date']))
                        <div class="mt-3">
                            <a href="{{ route('admin.order-item.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Order Items Table -->
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
                                    <th class="text-left py-2">Order</th>
                                    <th class="text-left py-2">Menu Item</th>
                                    <th class="text-left py-2">Quantity</th>
                                    <th class="text-left py-2">Unit Price</th>
                                    <th class="text-left py-2">Total Price</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Special Note</th>
                                    <th class="text-left py-2">Created</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderItems as $item)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($orderItems->currentPage() - 1) * $orderItems->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">
                                                <a href="{{ route('admin.order.show', $item->order_id) }}" class="text-blue-600 hover:text-blue-800">
                                                    #{{ $item->order_id }}
                                                </a>
                                            </div>
                                            @if($item->order->confirmation_code)
                                                <div class="text-sm font-mono text-gray-600">{{ $item->order->confirmation_code }}</div>
                                            @endif
                                            @if($item->order->user)
                                                <div class="text-sm text-gray-600">{{ $item->order->user->name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $item->menuItem->name ?? 'Unknown Item' }}</div>
                                        @if($item->menuItem->category)
                                            <div class="text-sm text-gray-600">{{ $item->menuItem->category }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-lg">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">RM {{ number_format($item->unit_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-green-600">RM {{ number_format($item->total_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize
                                            @if($item->item_status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->item_status == 'preparing') bg-blue-100 text-blue-800
                                            @elseif($item->item_status == 'ready') bg-purple-100 text-purple-800
                                            @elseif($item->item_status == 'served') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ str_replace('_', ' ', $item->item_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->special_note)
                                            <div class="text-sm text-gray-700 max-w-xs truncate" title="{{ $item->special_note }}">
                                                {{ $item->special_note }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">No notes</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $item->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $item->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.order-item.show', $item->id) }}" 
                                               class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-white shadow">
                                                View
                                            </a>
                                            <a href="{{ route('admin.order-item.edit', $item->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            @if($item->item_status != 'served')
                                                <form method="POST" action="{{ route('admin.order-item.destroy', $item->id) }}" 
                                                      onsubmit="return confirm('Are you sure to delete this order item?');" class="inline">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    @csrf
                                                   <x-danger-button class="text-xs">
                                                        Delete
                                                    </x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        <!-- Quick Status Update -->
                                        @if($item->item_status != 'served')
                                        <div class="mt-2 flex space-x-1">
                                            @if($item->item_status == 'pending')
                                                <button onclick="updateItemStatus({{ $item->id }}, 'preparing')" 
                                                        class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                    Start
                                                </button>
                                            @elseif($item->item_status == 'preparing')
                                                <button onclick="updateItemStatus({{ $item->id }}, 'ready')" 
                                                        class="px-2 py-1 bg-purple-500 text-white text-xs rounded hover:bg-purple-600">
                                                    Ready
                                                </button>
                                            @elseif($item->item_status == 'ready')
                                                <button onclick="updateItemStatus({{ $item->id }}, 'served')" 
                                                        class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                                    Served
                                                </button>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No order items found</p>
                                            <p class="text-sm">Try adjusting your search criteria or add a new order item</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orderItems->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    <div id="bulkActionsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Bulk Actions</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="selectedCount">0 items selected</p>
                </div>
                <div class="flex justify-center space-x-2">
                    <button onclick="bulkUpdateStatus('preparing')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Mark Preparing
                    </button>
                    <button onclick="bulkUpdateStatus('ready')" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                        Mark Ready
                    </button>
                    <button onclick="bulkUpdateStatus('served')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Mark Served
                    </button>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeBulkModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterByStatus(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('item_status', status);
            window.location.href = url.toString();
        }

        function updateItemStatus(itemId, status) {
            if (!confirm(`Are you sure you want to mark this item as '${status}'?`)) {
                return;
            }

            fetch(`/order-item/${itemId}/update-status`, {
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

        // Bulk actions functionality
        let selectedItems = [];

        function toggleItemSelection(itemId) {
            const index = selectedItems.indexOf(itemId);
            if (index > -1) {
                selectedItems.splice(index, 1);
            } else {
                selectedItems.push(itemId);
            }
            updateBulkActions();
        }

        function selectAllItems() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            selectedItems = [];
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                if (selectAllCheckbox.checked) {
                    selectedItems.push(parseInt(checkbox.value));
                }
            });
            updateBulkActions();
        }

        function updateBulkActions() {
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedItems.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = `${selectedItems.length} items selected`;
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function showBulkModal() {
            document.getElementById('bulkActionsModal').classList.remove('hidden');
        }

        function closeBulkModal() {
            document.getElementById('bulkActionsModal').classList.add('hidden');
        }

        function bulkUpdateStatus(status) {
            if (selectedItems.length === 0) {
                alert('No items selected');
                return;
            }

            fetch('/order-item/bulk-update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_ids: selectedItems,
                    item_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating items: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating items');
            });
        }

        // Auto-refresh for kitchen display
        if (window.location.search.includes('item_status=pending') || 
            window.location.search.includes('item_status=preparing')) {
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        }
    </script>
</x-app-layout>