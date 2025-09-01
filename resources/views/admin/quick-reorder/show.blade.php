<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quick Reorder Details') }} - {{ $quickReorder->order_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $quickReorder->order_name }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ $quickReorder->customerProfile->name ?? 'Unknown Customer' }} - 
                        Created {{ $quickReorder->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button onclick="showConvertToOrderModal()" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Convert to Order
                    </button>
                    <a href="{{ route('admin.quick-reorder.edit', $quickReorder->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Quick Reorder
                    </a>
                    <a href="{{ route('admin.quick-reorder.duplicate', $quickReorder->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Duplicate
                    </a>
                    <a href="{{ route('admin.quick-reorder.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Quick Reorder Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Quick Reorder Summary</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Quick Reorder ID:</span>
                                <p class="font-bold text-lg">#{{ $quickReorder->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Order Name:</span>
                                <p class="font-bold text-lg">{{ $quickReorder->order_name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Frequency:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                        {{ $quickReorder->order_frequency }}x ordered
                                    </span>
                                    @if($quickReorder->order_frequency > 10)
                                        <span class="ml-2 px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-bold">
                                            POPULAR
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <p class="font-bold text-xl text-green-600">RM {{ number_format($quickReorder->total_amount, 2) }}</p>
                            </div>
                        </div>

                        @if($quickReorder->last_ordered_at)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Last Ordered:</span>
                            <p class="font-medium">{{ $quickReorder->last_ordered_at->format('M d, Y h:i A') }}</p>
                            <p class="text-sm text-gray-500">{{ $quickReorder->last_ordered_at->diffForHumans() }}</p>
                        </div>
                        @else
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Last Ordered:</span>
                            <p class="text-gray-500">Never ordered</p>
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $quickReorder->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($quickReorder->updated_at != $quickReorder->created_at)
                                <div>
                                    <span class="text-sm text-gray-600">Last Updated:</span>
                                    <p class="font-medium">{{ $quickReorder->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customer Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($quickReorder->customerProfile)
                            <div>
                                <span class="text-sm text-gray-600">Customer Name:</span>
                                <p class="font-medium text-lg">{{ $quickReorder->customerProfile->name }}</p>
                            </div>

                            @if($quickReorder->customerProfile->email)
                            <div>
                                <span class="text-sm text-gray-600">Email:</span>
                                <p class="font-medium">
                                    <a href="mailto:{{ $quickReorder->customerProfile->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $quickReorder->customerProfile->email }}
                                    </a>
                                </p>
                            </div>
                            @endif

                            @if($quickReorder->customerProfile->phone)
                            <div>
                                <span class="text-sm text-gray-600">Phone:</span>
                                <p class="font-medium">
                                    <a href="tel:{{ $quickReorder->customerProfile->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $quickReorder->customerProfile->phone }}
                                    </a>
                                </p>
                            </div>
                            @endif

                            @if($quickReorder->customerProfile->address)
                            <div>
                                <span class="text-sm text-gray-600">Address:</span>
                                <p class="font-medium">{{ $quickReorder->customerProfile->address }}</p>
                            </div>
                            @endif

                            @if($quickReorder->customerProfile->dietary_preferences)
                            <div class="border-t pt-4">
                                <span class="text-sm text-gray-600">Dietary Preferences:</span>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($quickReorder->customerProfile->dietary_preferences as $preference)
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">{{ $preference }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @else
                            <p class="text-gray-500">Customer information not available</p>
                        @endif

                        <!-- Customer's Other Quick Reorders -->
                        @if($quickReorder->customerProfile)
                            @php
                                $otherQuickReorders = $quickReorder->customerProfile->quickReorders()
                                    ->where('id', '!=', $quickReorder->id)
                                    ->orderBy('order_frequency', 'desc')
                                    ->limit(3)
                                    ->get();
                            @endphp

                            @if($otherQuickReorders->count() > 0)
                            <div class="border-t pt-4">
                                <span class="text-sm text-gray-600">Other Quick Reorders:</span>
                                <div class="mt-2 space-y-1">
                                    @foreach($otherQuickReorders as $other)
                                        <div class="text-sm">
                                            <a href="{{ route('admin.quick-reorder.show', $other->id) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $other->order_name }}
                                            </a>
                                            <span class="text-gray-500">({{ $other->order_frequency }}x)</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>

            <!-- Order Items -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Order Items</h4>
                </div>
                <div class="p-6">
                    @if($quickReorder->order_items && count($quickReorder->order_items) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">#</th>
                                        <th class="text-left py-2">Item Name</th>
                                        <th class="text-left py-2">Quantity</th>
                                        <th class="text-left py-2">Unit Price</th>
                                        <th class="text-left py-2">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quickReorder->order_items as $index => $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3">{{ $index + 1 }}</td>
                                        <td class="py-3">
                                            <div class="font-medium">{{ $item['name'] ?? 'Item' }}</div>
                                        </td>
                                        <td class="py-3">{{ $item['quantity'] ?? 1 }}</td>
                                        <td class="py-3">RM {{ number_format($item['price'] ?? 0, 2) }}</td>
                                        <td class="py-3 font-medium">RM {{ number_format($item['total'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 font-bold">
                                        <td colspan="4" class="py-3 text-right">Grand Total:</td>
                                        <td class="py-3 text-xl text-green-600">RM {{ number_format($quickReorder->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No items found for this quick reorder</p>
                    @endif
                </div>
            </div>

            <!-- Order History -->
            @if($quickReorder->order_frequency > 0)
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Order Statistics</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $quickReorder->order_frequency }}</div>
                            <div class="text-sm text-blue-800">Times Ordered</div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">RM {{ number_format($quickReorder->total_amount * $quickReorder->order_frequency, 2) }}</div>
                            <div class="text-sm text-green-800">Total Revenue</div>
                        </div>

                        @if($quickReorder->last_ordered_at)
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $quickReorder->last_ordered_at->diffInDays($quickReorder->created_at) }}</div>
                            <div class="text-sm text-purple-800">Days Since Created</div>
                        </div>
                        @endif

                        <div class="bg-orange-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ count($quickReorder->order_items ?? []) }}</div>
                            <div class="text-sm text-orange-800">Items in Order</div>
                        </div>
                    </div>

                    @if($quickReorder->order_frequency > 1)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-yellow-800">
                                <span class="font-medium">Popular Item:</span> This quick reorder has been used {{ $quickReorder->order_frequency }} times, making it a customer favorite!
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Convert to Order Modal -->
    <div id="convertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 text-center">Convert to Order</h3>
                <div class="mt-2 text-sm text-gray-600 text-center">
                    <p>{{ $quickReorder->order_name }}</p>
                    <p class="font-medium">Total: RM {{ number_format($quickReorder->total_amount, 2) }}</p>
                </div>
                
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

                    <div>
                        <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions (Optional)</label>
                        <textarea name="special_instructions" id="special_instructions" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                  placeholder="Any special requests or modifications..."></textarea>
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
        function showConvertToOrderModal() {
            document.getElementById('convertModal').style.display = 'block';
        }

        function closeConvertModal() {
            document.getElementById('convertModal').style.display = 'none';
        }

        document.getElementById('convertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const specialInstructions = formData.get('special_instructions').trim();
            
            const data = {
                order_type: formData.get('order_type'),
                order_source: formData.get('order_source'),
                table_id: formData.get('table_id') || null,
                special_instructions: specialInstructions ? [specialInstructions] : []
            };

            fetch(`{{ route('admin.quick-reorder.convertToOrder', $quickReorder->id) }}`, {
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
                    // Optionally redirect to the new order
                    if (data.order && data.order.id) {
                        window.location.href = `/admin/order/${data.order.id}`;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error converting to order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error converting to order');
            });
        });

        // Close modal when clicking outside
        document.getElementById('convertModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConvertModal();
            }
        });

        // Auto-hide table selection for non-dine-in orders
        document.getElementById('order_type').addEventListener('change', function() {
            const tableSelect = document.getElementById('table_id');
            const tableContainer = tableSelect.closest('div');
            
            if (this.value === 'dine_in') {
                tableContainer.style.display = 'block';
            } else {
                tableContainer.style.display = 'none';
                tableSelect.value = '';
            }
        });

        // Initialize table visibility
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('order_type').dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>