<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($quickReorder->id)
                {{ __('Edit Quick Reorder') }} - {{ $quickReorder->order_name }}
            @else
                {{ __('Create New Quick Reorder') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($quickReorder->id)
                            {{ __('Edit Quick Reorder Information') }}
                        @else
                            {{ __('Quick Reorder Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Create a reusable order template that customers can quickly reorder.") }}
                    </p>
                </header>

                @if($quickReorder->id)
                    <form method="post" action="{{ route('admin.quick-reorder.update', $quickReorder->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('admin.quick-reorder.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Basic Quick Reorder Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="customer_profile_id" :value="__('Customer Profile')" />
                            <select id="customer_profile_id" name="customer_profile_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Customer</option>
                                @foreach($customerProfiles as $customer)
                                    <option value="{{ $customer->id }}" 
                                        @if(old('customer_profile_id', $quickReorder->customer_profile_id) == $customer->id) selected @endif>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('customer_profile_id')" />
                        </div>

                        <div>
                            <x-input-label for="order_name" :value="__('Order Name')" />
                            <x-text-input id="order_name" name="order_name" type="text" class="mt-1 block w-full" 
                                :value="old('order_name', $quickReorder->order_name)" placeholder="e.g. My Usual Order, Friday Special" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('order_name')" />
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
                        
                        <div id="order-items-container">
                            @if(old('order_items', $quickReorder->order_items))
                                @foreach(old('order_items', $quickReorder->order_items ?? []) as $index => $item)
                                    <div class="order-item-row border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                            <div class="md:col-span-2">
                                                <x-input-label for="order_items_{{ $index }}_name" :value="__('Item Name')" />
                                                <x-text-input id="order_items_{{ $index }}_name" name="order_items[{{ $index }}][name]" type="text" class="mt-1 block w-full" 
                                                    value="{{ $item['name'] ?? '' }}" placeholder="Enter item name" required/>
                                            </div>

                                            <div>
                                                <x-input-label for="order_items_{{ $index }}_quantity" :value="__('Quantity')" />
                                                <x-text-input id="order_items_{{ $index }}_quantity" name="order_items[{{ $index }}][quantity]" type="number" min="1" class="mt-1 block w-full quantity-input" 
                                                    value="{{ $item['quantity'] ?? 1 }}" placeholder="1" required/>
                                            </div>

                                            <div>
                                                <x-input-label for="order_items_{{ $index }}_price" :value="__('Unit Price (RM)')" />
                                                <x-text-input id="order_items_{{ $index }}_price" name="order_items[{{ $index }}][price]" type="number" step="0.01" min="0" class="mt-1 block w-full price-input" 
                                                    value="{{ $item['price'] ?? '' }}" placeholder="0.00" required/>
                                            </div>

                                            <div>
                                                <x-input-label for="order_items_{{ $index }}_total" :value="__('Total (RM)')" />
                                                <x-text-input id="order_items_{{ $index }}_total" name="order_items[{{ $index }}][total]" type="number" step="0.01" min="0" class="mt-1 block w-full total-input bg-gray-100" 
                                                    value="{{ $item['total'] ?? '' }}" readonly/>
                                            </div>

                                            <div>
                                                <button type="button" onclick="removeOrderItem(this)" 
                                                        class="w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="order_items[{{ $index }}][id]" value="{{ $item['id'] ?? $index + 1 }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="order-item-row border border-gray-200 rounded-lg p-4 mb-4" data-index="0">
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                        <div class="md:col-span-2">
                                            <x-input-label for="order_items_0_name" :value="__('Item Name')" />
                                            <x-text-input id="order_items_0_name" name="order_items[0][name]" type="text" class="mt-1 block w-full" 
                                                placeholder="Enter item name" required/>
                                        </div>

                                        <div>
                                            <x-input-label for="order_items_0_quantity" :value="__('Quantity')" />
                                            <x-text-input id="order_items_0_quantity" name="order_items[0][quantity]" type="number" min="1" class="mt-1 block w-full quantity-input" 
                                                value="1" placeholder="1" required/>
                                        </div>

                                        <div>
                                            <x-input-label for="order_items_0_price" :value="__('Unit Price (RM)')" />
                                            <x-text-input id="order_items_0_price" name="order_items[0][price]" type="number" step="0.01" min="0" class="mt-1 block w-full price-input" 
                                                placeholder="0.00" required/>
                                        </div>

                                        <div>
                                            <x-input-label for="order_items_0_total" :value="__('Total (RM)')" />
                                            <x-text-input id="order_items_0_total" name="order_items[0][total]" type="number" step="0.01" min="0" class="mt-1 block w-full total-input bg-gray-100" 
                                                value="0.00" readonly/>
                                        </div>

                                        <div>
                                            <button type="button" onclick="removeOrderItem(this)" 
                                                    class="w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="order_items[0][id]" value="1">
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" onclick="addOrderItem()" 
                                class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Add Item
                        </button>
                        <x-input-error class="mt-2" :messages="$errors->get('order_items')" />
                    </div>

                    <!-- Order Summary -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="total_amount" :value="__('Total Amount (RM)')" />
                                <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0" class="mt-1 block w-full bg-gray-100" 
                                    :value="old('total_amount', $quickReorder->total_amount)" placeholder="0.00" readonly required/>
                                <x-input-error class="mt-2" :messages="$errors->get('total_amount')" />
                                <p class="mt-1 text-sm text-gray-500">Automatically calculated from order items</p>
                            </div>

                            <div>
                                <x-input-label for="order_frequency" :value="__('Order Frequency (Optional)')" />
                                <x-text-input id="order_frequency" name="order_frequency" type="number" min="1" class="mt-1 block w-full" 
                                    :value="old('order_frequency', $quickReorder->order_frequency)" placeholder="1"/>
                                <x-input-error class="mt-2" :messages="$errors->get('order_frequency')" />
                                <p class="mt-1 text-sm text-gray-500">How many times this order has been placed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Quick Reorder Information (for edit) -->
                    @if($quickReorder->id)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Quick Reorder Information</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Quick Reorder ID:</span>
                                        <span class="font-medium">#{{ $quickReorder->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Order Frequency:</span>
                                        <span class="font-medium">{{ $quickReorder->order_frequency }}x</span>
                                    </div>
                                    @if($quickReorder->last_ordered_at)
                                    <div>
                                        <span class="text-gray-600">Last Ordered:</span>
                                        <span class="font-medium">{{ $quickReorder->last_ordered_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-medium">{{ $quickReorder->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Quick Reorder') }}</x-primary-button>

                        <a href="{{ route('admin.quick-reorder.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>

                        @if($quickReorder->id)
                            <a href="{{ route('admin.quick-reorder.duplicate', $quickReorder->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Duplicate
                            </a>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        let itemIndex = {{ $quickReorder->order_items ? count($quickReorder->order_items) : 1 }};

        // Order Items Management
        function addOrderItem() {
            const container = document.getElementById('order-items-container');
            const newItem = document.createElement('div');
            newItem.className = 'order-item-row border border-gray-200 rounded-lg p-4 mb-4';
            newItem.setAttribute('data-index', itemIndex);
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label for="order_items_${itemIndex}_name" class="block font-medium text-sm text-gray-700">Item Name</label>
                        <input id="order_items_${itemIndex}_name" name="order_items[${itemIndex}][name]" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" 
                            placeholder="Enter item name" required>
                    </div>

                    <div>
                        <label for="order_items_${itemIndex}_quantity" class="block font-medium text-sm text-gray-700">Quantity</label>
                        <input id="order_items_${itemIndex}_quantity" name="order_items[${itemIndex}][quantity]" type="number" min="1" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full quantity-input" 
                            value="1" placeholder="1" required>
                    </div>

                    <div>
                        <label for="order_items_${itemIndex}_price" class="block font-medium text-sm text-gray-700">Unit Price (RM)</label>
                        <input id="order_items_${itemIndex}_price" name="order_items[${itemIndex}][price]" type="number" step="0.01" min="0" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full price-input" 
                            placeholder="0.00" required>
                    </div>

                    <div>
                        <label for="order_items_${itemIndex}_total" class="block font-medium text-sm text-gray-700">Total (RM)</label>
                        <input id="order_items_${itemIndex}_total" name="order_items[${itemIndex}][total]" type="number" step="0.01" min="0" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full total-input bg-gray-100" 
                            value="0.00" readonly>
                    </div>

                    <div>
                        <button type="button" onclick="removeOrderItem(this)" 
                                class="w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Remove
                        </button>
                    </div>
                </div>

                <input type="hidden" name="order_items[${itemIndex}][id]" value="${itemIndex + 1}">
            `;
            container.appendChild(newItem);
            
            // Add event listeners to new inputs
            attachCalculationEvents(newItem);
            itemIndex++;
        }

        function removeOrderItem(button) {
            const container = document.getElementById('order-items-container');
            if (container.children.length > 1) {
                button.closest('.order-item-row').remove();
                calculateGrandTotal();
            } else {
                // Clear the inputs instead of removing if it's the last one
                const row = button.closest('.order-item-row');
                row.querySelectorAll('input[type="text"], input[type="number"]:not(.total-input)').forEach(input => {
                    input.value = input.type === 'number' && input.classList.contains('quantity-input') ? '1' : '';
                });
                row.querySelector('.total-input').value = '0.00';
                calculateGrandTotal();
            }
        }

        function attachCalculationEvents(row) {
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            const totalInput = row.querySelector('.total-input');

            function calculateRowTotal() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const total = quantity * price;
                totalInput.value = total.toFixed(2);
                calculateGrandTotal();
            }

            quantityInput.addEventListener('input', calculateRowTotal);
            priceInput.addEventListener('input', calculateRowTotal);
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            document.getElementById('total_amount').value = grandTotal.toFixed(2);
        }

        // Initialize calculation events for existing items
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.order-item-row').forEach(row => {
                attachCalculationEvents(row);
            });
            calculateGrandTotal();
        });

        // Form validation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            // Check if at least one item has valid data
            const items = document.querySelectorAll('.order-item-row');
            let hasValidItem = false;

            items.forEach(item => {
                const name = item.querySelector('input[name*="[name]"]').value.trim();
                const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]').value) || 0;
                const price = parseFloat(item.querySelector('input[name*="[price]"]').value) || 0;

                if (name && quantity > 0 && price >= 0) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem) {
                e.preventDefault();
                alert('Please add at least one valid item with name, quantity, and price.');
                return false;
            }

            // Remove empty items before submission
            items.forEach(item => {
                const name = item.querySelector('input[name*="[name]"]').value.trim();
                if (!name) {
                    item.remove();
                }
            });
        });

        // Auto-save functionality (optional)
        let autoSaveTimer;
        function setupAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Could implement auto-save to draft here
                console.log('Auto-save triggered');
            }, 5000);
        }

        // Trigger auto-save on input changes
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', setupAutoSave);
        });
    </script>
</x-app-layout>