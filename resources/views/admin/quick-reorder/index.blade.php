<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quick Reorders Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('admin.quick-reorder.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Create New Quick Reorder
                </a>
                <div class="flex space-x-2">
                    <button onclick="showPopularQuickReorders()" class="px-4 py-2 bg-green-600 text-white rounded font-semibold hover:bg-green-700">
                        Popular Items
                    </button>
                    <button onclick="showRecentQuickReorders()" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Recently Ordered
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('admin.quick-reorder.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order name, customer...">
                        </div>

                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                            <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Customers</option>
                                @foreach(\App\Models\CustomerProfile::with('user')->get() as $customer)
                                    <option value="{{ $customer->id }}" @if(request('customer_id') == $customer->id) selected @endif>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700">Sort By</label>
                            <select name="sort_by" id="sort_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="created_at" @if(request('sort_by') == 'created_at') selected @endif>Newest First</option>
                                <option value="order_frequency" @if(request('sort_by') == 'order_frequency') selected @endif>Most Popular</option>
                                <option value="last_ordered_at" @if(request('sort_by') == 'last_ordered_at') selected @endif>Recently Ordered</option>
                                <option value="order_name" @if(request('sort_by') == 'order_name') selected @endif>Order Name</option>
                            </select>
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

                    @if(request()->hasAny(['search', 'customer_id', 'sort_by']))
                        <div class="mt-3">
                            <a href="{{ route('admin.quick-reorder.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Reorders Table -->
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
                                    <th class="text-left py-2">Order Name</th>
                                    <th class="text-left py-2">Customer</th>
                                    <th class="text-left py-2">Items</th>
                                    <th class="text-left py-2">Total Amount</th>
                                    <th class="text-left py-2">Order Frequency</th>
                                    <th class="text-left py-2">Last Ordered</th>
                                    <th class="text-left py-2">Created</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quickReorders as $quickReorder)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($quickReorders->currentPage() - 1) * $quickReorders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $quickReorder->order_name }}</div>
                                        @if($quickReorder->order_frequency > 5)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Popular
                                            </span>
                                        @elseif($quickReorder->last_ordered_at && $quickReorder->last_ordered_at->isAfter(now()->subDays(7)))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Recent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $quickReorder->customerProfile->name ?? 'Unknown' }}</div>
                                        @if($quickReorder->customerProfile && $quickReorder->customerProfile->email)
                                            <div class="text-sm text-gray-600">{{ $quickReorder->customerProfile->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($quickReorder->order_items && count($quickReorder->order_items) > 0)
                                            <div class="text-sm">
                                                <div class="font-medium">{{ count($quickReorder->order_items) }} items</div>
                                                <div class="text-gray-600">
                                                    @foreach(array_slice($quickReorder->order_items, 0, 2) as $item)
                                                        <div>{{ $item['quantity'] ?? 1 }}x {{ $item['name'] ?? 'Item' }}</div>
                                                    @endforeach
                                                    @if(count($quickReorder->order_items) > 2)
                                                        <div class="text-gray-500">... and {{ count($quickReorder->order_items) - 2 }} more</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">No items</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-green-600">RM {{ number_format($quickReorder->total_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-sm rounded bg-gray-100 text-gray-800">
                                                {{ $quickReorder->order_frequency }}x
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($quickReorder->last_ordered_at)
                                            <div class="text-sm">{{ $quickReorder->last_ordered_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-600">{{ $quickReorder->last_ordered_at->format('h:i A') }}</div>
                                        @else
                                            <span class="text-gray-500">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $quickReorder->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $quickReorder->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="convertToOrder({{ $quickReorder->id }})" 
                                               class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 
                                                border border-transparent rounded-lg font-medium text-sm text-white shadow">
                                                Order Now
                                            </button>
                                            <a href="{{ route('admin.quick-reorder.show', $quickReorder->id) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-white shadow">
                                                View
                                            </a>
                                            <a href="{{ route('admin.quick-reorder.edit', $quickReorder->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.quick-reorder.destroy', $quickReorder->id) }}" 
                                                  onsubmit="return confirm('Are you sure to delete this quick reorder?');" class="inline">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                               <x-danger-button class="text-xs">
                                                    Delete
                                                </x-danger-button>
                                            </form>
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
                                            <p class="text-lg font-medium">No quick reorders found</p>
                                            <p class="text-sm">Try adjusting your search criteria or create a new quick reorder</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $quickReorders->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Convert to Order Modal -->
    <div id="convertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 text-center">Convert to Order</h3>
                <form id="convertForm" class="mt-4 space-y-4">
                    <div>
                        <label for="order_type" class="block text-sm font-medium text-gray-700">Order Type</label>
                        <select name="order_type" id="order_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="delivery">Delivery</option>
                            <option value="event">Event</option>
                        </select>
                    </div>

                    <div>
                        <label for="order_source" class="block text-sm font-medium text-gray-700">Order Source</label>
                        <select name="order_source" id="order_source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="counter">Counter</option>
                            <option value="web">Web</option>
                            <option value="mobile">Mobile</option>
                            <option value="waiter">Waiter</option>
                            <option value="qr_scan">QR Scan</option>
                        </select>
                    </div>

                    <div>
                        <label for="table_id" class="block text-sm font-medium text-gray-700">Table (Optional)</label>
                        <select name="table_id" id="table_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select Table (Optional)</option>
                            @foreach(\App\Models\Table::where('is_active', true)->get() as $table)
                                <option value="{{ $table->id }}">{{ $table->table_number }} - {{ $table->status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="closeConvertModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Convert to Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentQuickReorderId = null;

        function convertToOrder(quickReorderId) {
            currentQuickReorderId = quickReorderId;
            document.getElementById('convertModal').style.display = 'block';
        }

        function closeConvertModal() {
            document.getElementById('convertModal').style.display = 'none';
            currentQuickReorderId = null;
        }

        document.getElementById('convertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentQuickReorderId) return;

            const formData = new FormData(this);
            const data = {
                order_type: formData.get('order_type'),
                order_source: formData.get('order_source'),
                table_id: formData.get('table_id') || null,
                special_instructions: []
            };

            fetch(`/admin/quick-reorder/${currentQuickReorderId}/convert-to-order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Quick reorder converted to order successfully!');
                    closeConvertModal();
                    window.location.reload();
                } else {
                    alert('Error converting to order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error converting to order');
            });
        });

        function showPopularQuickReorders() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', 'order_frequency');
            window.location.href = url.toString();
        }

        function showRecentQuickReorders() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', 'last_ordered_at');
            window.location.href = url.toString();
        }

        // Close modal when clicking outside
        document.getElementById('convertModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConvertModal();
            }
        });
    </script>
</x-app-layout>