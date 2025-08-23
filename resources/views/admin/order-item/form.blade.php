<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($orderItem->id)
                {{ __('Edit Order Item') }} - #{{ $orderItem->id }}
            @else
                {{ __('Add New Order Item') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($orderItem->id)
                            {{ __('Edit Order Item Information') }}
                        @else
                            {{ __('Order Item Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Complete the order item details below.") }}
                    </p>
                </header>

                @if($orderItem->id)
                    <form method="post" action="{{ route('order-item.update', $orderItem->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('order-item.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Order Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="order_id" :value="__('Order')" />
                            <select id="order_id" name="order_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" 
                                        @if(old('order_id', $orderItem->order_id) == $order->id) selected @endif
                                        data-customer="{{ $order->user->name ?? 'Unknown' }}"
                                        data-total="{{ $order->total_amount }}">
                                        #{{ $order->id }}
                                        @if($order->confirmation_code) - {{ $order->confirmation_code }} @endif
                                        - {{ $order->user->name ?? 'Unknown Customer' }}
                                        (RM {{ number_format($order->total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('order_id')" />
                        </div>

                        <div>
                            <x-input-label for="menu_item_id" :value="__('Menu Item')" />
                            <select id="menu_item_id" name="menu_item_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Menu Item</option>
                                @foreach($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}" 
                                        @if(old('menu_item_id', $orderItem->menu_item_id) == $menuItem->id) selected @endif
                                        data-price="{{ $menuItem->price }}">
                                        {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('menu_item_id')" />
                        </div>
                    </div>

                    <!-- Quantity and Pricing -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity')" />
                            <x-text-input id="quantity" name="quantity" type="number" min="1" max="999" class="mt-1 block w-full" 
                                :value="old('quantity', $orderItem->quantity ?? 1)" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>

                        <div>
                            <x-input-label for="unit_price" :value="__('Unit Price (RM)')" />
                            <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('unit_price', $orderItem->unit_price)" placeholder="0.00" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('unit_price')" />
                        </div>

                        <div>
                            <x-input-label for="total_price" :value="__('Total Price (RM)')" />
                            <x-text-input id="total_price" name="total_price" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('total_price', $orderItem->total_price)" placeholder="0.00" readonly required/>
                            <x-input-error class="mt-2" :messages="$errors->get('total_price')" />
                            <p class="mt-1 text-sm text-gray-500">Auto-calculated based on quantity and unit price</p>
                        </div>
                    </div>

                    <!-- Status and Notes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="item_status" :value="__('Item Status')" />
                            <select id="item_status" name="item_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="pending" @if(old('item_status', $orderItem->item_status) == 'pending') selected @endif>Pending</option>
                                <option value="preparing" @if(old('item_status', $orderItem->item_status) == 'preparing') selected @endif>Preparing</option>
                                <option value="ready" @if(old('item_status', $orderItem->item_status) == 'ready') selected @endif>Ready</option>
                                <option value="served" @if(old('item_status', $orderItem->item_status) == 'served') selected @endif>Served</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('item_status')" />
                        </div>

                        <div>
                            <x-input-label for="special_note" :value="__('Special Note (Optional)')" />
                            <textarea id="special_note" name="special_note" rows="3" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                      placeholder="Any special instructions or notes...">{{ old('special_note', $orderItem->special_note) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('special_note')" />
                            <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                        </div>
                    </div>

                    <!-- Order Summary (for context) -->
                    @if($orderItem->order_id)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Context</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Order:</span>
                                    <span class="font-medium">#{{ $orderItem->order->id ?? $orderItem->order_id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Customer:</span>
                                    <span class="font-medium">{{ $orderItem->order->user->name ?? 'Unknown' }}</span>
                                </div>
                                @if($orderItem->order->confirmation_code ?? false)
                                <div>
                                    <span class="text-gray-600">Confirmation:</span>
                                    <span class="font-mono font-medium">{{ $orderItem->order->confirmation_code }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600">Order Total:</span>
                                    <span class="font-medium">RM {{ number_format($orderItem->order->total_amount ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Current Item Information (for edit) -->
                    @if($orderItem->id)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Item Information</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Item ID:</span>
                                        <span class="font-medium">#{{ $orderItem->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Menu Item:</span>
                                        <span class="font-medium">{{ $orderItem->menuItem->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Current Status:</span>
                                        <span class="font-medium capitalize">{{ str_replace('_', ' ', $orderItem->item_status) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-medium">{{ $orderItem->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Order Item') }}</x-primary-button>

                        <a href="{{ route('order-item.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>

                        @if($orderItem->id)
                            <a href="{{ route('order-item.duplicate', $orderItem->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Duplicate Item
                            </a>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Auto-populate unit price when menu item is selected
        document.getElementById('menu_item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                document.getElementById('unit_price').value = parseFloat(price).toFixed(2);
                calculateTotal();
            } else {
                document.getElementById('unit_price').value = '';
                document.getElementById('total_price').value = '';
            }
        });

        // Calculate total price when quantity or unit price changes
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
            const total = (quantity * unitPrice).toFixed(2);
            document.getElementById('total_price').value = total;
        }

        // Add event listeners for auto-calculation
        document.getElementById('quantity').addEventListener('input', calculateTotal);
        document.getElementById('unit_price').addEventListener('input', calculateTotal);

        // Validate quantity
        document.getElementById('quantity').addEventListener('change', function() {
            const quantity = parseInt(this.value);
            if (quantity < 1) {
                this.value = 1;
                calculateTotal();
            } else if (quantity > 999) {
                this.value = 999;
                calculateTotal();
            }
        });

        // Validate unit price
        document.getElementById('unit_price').addEventListener('change', function() {
            const price = parseFloat(this.value);
            if (price < 0) {
                this.value = '0.00';
                calculateTotal();
            }
        });

        // Real-time price calculation using AJAX
        function fetchMenuItemPrice(menuItemId) {
            if (!menuItemId) return;

            fetch(`/order-item/get-menu-item-price?menu_item_id=${menuItemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('unit_price').value = parseFloat(data.price).toFixed(2);
                        calculateTotal();
                    }
                })
                .catch(error => {
                    console.error('Error fetching menu item price:', error);
                });
        }

        // Enhanced menu item selection with AJAX
        document.getElementById('menu_item_id').addEventListener('change', function() {
            const menuItemId = this.value;
            if (menuItemId) {
                fetchMenuItemPrice(menuItemId);
            }
        });

        // Auto-calculate on page load if editing
        @if($orderItem->id)
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
        @endif

        // Form validation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const quantity = parseInt(document.getElementById('quantity').value);
            const unitPrice = parseFloat(document.getElementById('unit_price').value);
            const totalPrice = parseFloat(document.getElementById('total_price').value);

            if (quantity < 1) {
                alert('Quantity must be at least 1');
                e.preventDefault();
                return false;
            }

            if (unitPrice < 0) {
                alert('Unit price cannot be negative');
                e.preventDefault();
                return false;
            }

            if (totalPrice < 0) {
                alert('Total price cannot be negative');
                e.preventDefault();
                return false;
            }

            // Check if calculated total matches displayed total
            const calculatedTotal = (quantity * unitPrice).toFixed(2);
            if (Math.abs(calculatedTotal - totalPrice.toFixed(2)) > 0.01) {
                alert('Price calculation mismatch. Please check your values.');
                e.preventDefault();
                return false;
            }
        });

        // Character counter for special note
        const specialNoteTextarea = document.getElementById('special_note');
        const maxLength = 1000;

        // Create character counter
        const charCounter = document.createElement('div');
        charCounter.className = 'text-sm text-gray-500 mt-1';
        charCounter.id = 'char-counter';
        specialNoteTextarea.parentNode.appendChild(charCounter);

        function updateCharCounter() {
            const remaining = maxLength - specialNoteTextarea.value.length;
            charCounter.textContent = `${specialNoteTextarea.value.length}/${maxLength} characters`;
            
            if (remaining < 50) {
                charCounter.className = 'text-sm text-red-500 mt-1';
            } else if (remaining < 100) {
                charCounter.className = 'text-sm text-yellow-500 mt-1';
            } else {
                charCounter.className = 'text-sm text-gray-500 mt-1';
            }
        }

        specialNoteTextarea.addEventListener('input', updateCharCounter);
        updateCharCounter(); // Initial call

        // Prevent exceeding max length
        specialNoteTextarea.addEventListener('input', function() {
            if (this.value.length > maxLength) {
                this.value = this.value.substring(0, maxLength);
                updateCharCounter();
            }
        });

        // Order selection enhancement
        document.getElementById('order_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const customer = selectedOption.getAttribute('data-customer');
                const total = selectedOption.getAttribute('data-total');
                
                // Could add visual feedback here about the selected order
                console.log(`Selected order for ${customer} with total RM ${total}`);
            }
        });
    </script>
</x-app-layout>