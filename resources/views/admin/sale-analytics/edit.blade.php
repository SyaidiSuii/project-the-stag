<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sale Analytics') }} - {{ $saleAnalytics->date->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Edit Sale Analytics Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Update the sales analytics data for") }} {{ $saleAnalytics->date->format('l, F d, Y') }}.
                    </p>
                </header>

                <form method="post" action="{{ route('sale-analytics.update', $saleAnalytics->id) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Analytics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <input type="date" id="date" name="date" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ old('date', $saleAnalytics->date->format('Y-m-d')) }}" required>
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>

                        <div>
                            <x-input-label for="total_sales" :value="__('Total Sales (RM)')" />
                            <x-text-input id="total_sales" name="total_sales" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('total_sales', $saleAnalytics->total_sales)" placeholder="0.00" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('total_sales')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="total_orders" :value="__('Total Orders')" />
                            <x-text-input id="total_orders" name="total_orders" type="number" min="0" class="mt-1 block w-full" 
                                :value="old('total_orders', $saleAnalytics->total_orders)" placeholder="0" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('total_orders')" />
                        </div>

                        <div>
                            <x-input-label for="average_order_value" :value="__('Average Order Value (RM)')" />
                            <x-text-input id="average_order_value" name="average_order_value" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('average_order_value', $saleAnalytics->average_order_value)" placeholder="0.00" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('average_order_value')" />
                        </div>
                    </div>

                    <!-- Customer Analytics -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Analytics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="unique_customers" :value="__('Unique Customers')" />
                                <x-text-input id="unique_customers" name="unique_customers" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('unique_customers', $saleAnalytics->unique_customers)" placeholder="0" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('unique_customers')" />
                            </div>

                            <div>
                                <x-input-label for="new_customers" :value="__('New Customers')" />
                                <x-text-input id="new_customers" name="new_customers" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('new_customers', $saleAnalytics->new_customers)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('new_customers')" />
                            </div>

                            <div>
                                <x-input-label for="returning_customers" :value="__('Returning Customers')" />
                                <x-text-input id="returning_customers" name="returning_customers" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('returning_customers', $saleAnalytics->returning_customers)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('returning_customers')" />
                            </div>
                        </div>
                    </div>

                    <!-- Order Type Analytics -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Type Analytics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="dine_in_orders" :value="__('Dine-in Orders')" />
                                <x-text-input id="dine_in_orders" name="dine_in_orders" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('dine_in_orders', $saleAnalytics->dine_in_orders)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('dine_in_orders')" />
                            </div>

                            <div>
                                <x-input-label for="takeaway_orders" :value="__('Takeaway Orders')" />
                                <x-text-input id="takeaway_orders" name="takeaway_orders" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('takeaway_orders', $saleAnalytics->takeaway_orders)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('takeaway_orders')" />
                            </div>

                            <div>
                                <x-input-label for="delivery_orders" :value="__('Delivery Orders')" />
                                <x-text-input id="delivery_orders" name="delivery_orders" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('delivery_orders', $saleAnalytics->delivery_orders)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('delivery_orders')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="mobile_orders" :value="__('Mobile Orders')" />
                                <x-text-input id="mobile_orders" name="mobile_orders" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('mobile_orders', $saleAnalytics->mobile_orders)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('mobile_orders')" />
                            </div>

                            <div>
                                <x-input-label for="qr_orders" :value="__('QR Orders')" />
                                <x-text-input id="qr_orders" name="qr_orders" type="number" min="0" class="mt-1 block w-full" 
                                    :value="old('qr_orders', $saleAnalytics->qr_orders)" placeholder="0"/>
                                <x-input-error class="mt-2" :messages="$errors->get('qr_orders')" />
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Analytics -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Order Type</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="total_revenue_dine_in" :value="__('Dine-in Revenue (RM)')" />
                                <x-text-input id="total_revenue_dine_in" name="total_revenue_dine_in" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                    :value="old('total_revenue_dine_in', $saleAnalytics->total_revenue_dine_in)" placeholder="0.00"/>
                                <x-input-error class="mt-2" :messages="$errors->get('total_revenue_dine_in')" />
                            </div>

                            <div>
                                <x-input-label for="total_revenue_takeaway" :value="__('Takeaway Revenue (RM)')" />
                                <x-text-input id="total_revenue_takeaway" name="total_revenue_takeaway" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                    :value="old('total_revenue_takeaway', $saleAnalytics->total_revenue_takeaway)" placeholder="0.00"/>
                                <x-input-error class="mt-2" :messages="$errors->get('total_revenue_takeaway')" />
                            </div>

                            <div>
                                <x-input-label for="total_revenue_delivery" :value="__('Delivery Revenue (RM)')" />
                                <x-text-input id="total_revenue_delivery" name="total_revenue_delivery" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                    :value="old('total_revenue_delivery', $saleAnalytics->total_revenue_delivery)" placeholder="0.00"/>
                                <x-input-error class="mt-2" :messages="$errors->get('total_revenue_delivery')" />
                            </div>
                        </div>
                    </div>

                    <!-- Peak Hours -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Peak Hours</h3>
                        <p class="text-sm text-gray-600 mb-4">Enter the hour (0-23) when each period was busiest.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="peak_breakfast" :value="__('Breakfast Peak Hour')" />
                                <select id="peak_breakfast" name="peak_hours[breakfast]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Hour</option>
                                    @for($i = 6; $i <= 11; $i++)
                                        <option value="{{ $i }}" @if(old('peak_hours.breakfast', $saleAnalytics->peak_hours['breakfast'] ?? '') == $i) selected @endif>
                                            {{ sprintf('%02d:00', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('peak_hours.breakfast')" />
                            </div>

                            <div>
                                <x-input-label for="peak_lunch" :value="__('Lunch Peak Hour')" />
                                <select id="peak_lunch" name="peak_hours[lunch]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Hour</option>
                                    @for($i = 11; $i <= 15; $i++)
                                        <option value="{{ $i }}" @if(old('peak_hours.lunch', $saleAnalytics->peak_hours['lunch'] ?? '') == $i) selected @endif>
                                            {{ sprintf('%02d:00', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('peak_hours.lunch')" />
                            </div>

                            <div>
                                <x-input-label for="peak_dinner" :value="__('Dinner Peak Hour')" />
                                <select id="peak_dinner" name="peak_hours[dinner]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Hour</option>
                                    @for($i = 17; $i <= 22; $i++)
                                        <option value="{{ $i }}" @if(old('peak_hours.dinner', $saleAnalytics->peak_hours['dinner'] ?? '') == $i) selected @endif>
                                            {{ sprintf('%02d:00', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('peak_hours.dinner')" />
                            </div>
                        </div>
                    </div>

                    <!-- Popular Items -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Items</h3>
                        <p class="text-sm text-gray-600 mb-4">Update the most popular items for this date (optional).</p>
                        
                        <div id="popular-items-container">
                            @php
                                $popularItems = old('popular_items', $saleAnalytics->popular_items ?? []);
                            @endphp
                            
                            @if(!empty($popularItems))
                                @foreach($popularItems as $index => $item)
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" name="popular_items[{{ $index }}][name]" 
                                               class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                               value="{{ $item['name'] ?? '' }}" placeholder="Item name...">
                                        <input type="number" name="popular_items[{{ $index }}][quantity]" 
                                               class="w-24 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                               value="{{ $item['quantity'] ?? '' }}" placeholder="Qty" min="1">
                                        <button type="button" onclick="removePopularItem(this)" 
                                                class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                            Remove
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" name="popular_items[0][name]" 
                                           class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           placeholder="Item name...">
                                    <input type="number" name="popular_items[0][quantity]" 
                                           class="w-24 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           placeholder="Qty" min="1">
                                    <button type="button" onclick="removePopularItem(this)" 
                                            class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                        Remove
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" onclick="addPopularItem()" 
                                class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Add Item
                        </button>
                        <x-input-error class="mt-2" :messages="$errors->get('popular_items')" />
                    </div>

                    <!-- Performance Metrics -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="average_preparation_time" :value="__('Average Preparation Time (minutes)')" />
                                <x-text-input id="average_preparation_time" name="average_preparation_time" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                    :value="old('average_preparation_time', $saleAnalytics->average_preparation_time)" placeholder="0.00"/>
                                <x-input-error class="mt-2" :messages="$errors->get('average_preparation_time')" />
                            </div>

                            <div>
                                <x-input-label for="customer_satisfaction_avg" :value="__('Customer Satisfaction Average (1-5)')" />
                                <x-text-input id="customer_satisfaction_avg" name="customer_satisfaction_avg" type="number" step="0.01" min="0" max="5" class="mt-1 block w-full" 
                                    :value="old('customer_satisfaction_avg', $saleAnalytics->customer_satisfaction_avg)" placeholder="0.00"/>
                                <x-input-error class="mt-2" :messages="$errors->get('customer_satisfaction_avg')" />
                            </div>
                        </div>
                    </div>

                    <!-- Current Record Information -->
                    <div class="border-t pt-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Current Record Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Record ID:</span>
                                    <span class="font-medium">#{{ $saleAnalytics->id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Original Date:</span>
                                    <span class="font-medium">{{ $saleAnalytics->date->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">{{ $saleAnalytics->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                @if($saleAnalytics->updated_at != $saleAnalytics->created_at)
                                <div>
                                    <span class="text-gray-600">Last Updated:</span>
                                    <span class="font-medium">{{ $saleAnalytics->updated_at->format('M d, Y h:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Re-generate Helper -->
                    <div class="border-t pt-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Re-generate Data</h4>
                            <p class="text-sm text-blue-700 mb-3">
                                You can re-generate analytics from existing orders for the selected date. This will overwrite current values.
                            </p>
                            <button type="button" onclick="regenerateFromOrders()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Re-generate from Orders
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Update Analytics') }}</x-primary-button>

                        <a href="{{ route('sale-analytics.show', $saleAnalytics->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            View Details
                        </a>

                        <a href="{{ route('sale-analytics.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Back to List
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        let popularItemIndex = {{ !empty($popularItems) ? count($popularItems) : 1 }};

        function addPopularItem() {
            const container = document.getElementById('popular-items-container');
            const newItem = document.createElement('div');
            newItem.className = 'flex items-center gap-2 mb-2';
            newItem.innerHTML = `
                <input type="text" name="popular_items[${popularItemIndex}][name]" 
                       class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                       placeholder="Item name...">
                <input type="number" name="popular_items[${popularItemIndex}][quantity]" 
                       class="w-24 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                       placeholder="Qty" min="1">
                <button type="button" onclick="removePopularItem(this)" 
                        class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(newItem);
            popularItemIndex++;
        }

        function removePopularItem(button) {
            const container = document.getElementById('popular-items-container');
            if (container.children.length > 1) {
                button.parentElement.remove();
            } else {
                // Clear the inputs instead of removing if it's the last one
                const inputs = button.parentElement.querySelectorAll('input');
                inputs.forEach(input => input.value = '');
            }
        }

        function regenerateFromOrders() {
            const dateInput = document.getElementById('date');
            if (!dateInput.value) {
                alert('Please select a date first');
                return;
            }

            if (!confirm('This will replace current form data with data from existing orders for the selected date. Continue?')) {
                return;
            }

            const date = dateInput.value;
            
            fetch(`{{ route('sale-analytics.generate', '') }}/${date}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.data) {
                    const analytics = data.data;
                    
                    // Fill form fields
                    if (analytics.total_sales) document.getElementById('total_sales').value = analytics.total_sales;
                    if (analytics.total_orders) document.getElementById('total_orders').value = analytics.total_orders;
                    if (analytics.average_order_value) document.getElementById('average_order_value').value = analytics.average_order_value;
                    if (analytics.unique_customers) document.getElementById('unique_customers').value = analytics.unique_customers;
                    if (analytics.new_customers) document.getElementById('new_customers').value = analytics.new_customers;
                    if (analytics.returning_customers) document.getElementById('returning_customers').value = analytics.returning_customers;
                    if (analytics.dine_in_orders) document.getElementById('dine_in_orders').value = analytics.dine_in_orders;
                    if (analytics.takeaway_orders) document.getElementById('takeaway_orders').value = analytics.takeaway_orders;
                    if (analytics.delivery_orders) document.getElementById('delivery_orders').value = analytics.delivery_orders;
                    if (analytics.mobile_orders) document.getElementById('mobile_orders').value = analytics.mobile_orders;
                    if (analytics.qr_orders) document.getElementById('qr_orders').value = analytics.qr_orders;
                    if (analytics.total_revenue_dine_in) document.getElementById('total_revenue_dine_in').value = analytics.total_revenue_dine_in;
                    if (analytics.total_revenue_takeaway) document.getElementById('total_revenue_takeaway').value = analytics.total_revenue_takeaway;
                    if (analytics.total_revenue_delivery) document.getElementById('total_revenue_delivery').value = analytics.total_revenue_delivery;
                    
                    // Fill peak hours
                    if (analytics.peak_hours) {
                        if (analytics.peak_hours.breakfast) document.getElementById('peak_breakfast').value = analytics.peak_hours.breakfast;
                        if (analytics.peak_hours.lunch) document.getElementById('peak_lunch').value = analytics.peak_hours.lunch;
                        if (analytics.peak_hours.dinner) document.getElementById('peak_dinner').value = analytics.peak_hours.dinner;
                    }
                    
                    // Fill popular items
                    if (analytics.popular_items && analytics.popular_items.length > 0) {
                        const container = document.getElementById('popular-items-container');
                        container.innerHTML = '';
                        
                        analytics.popular_items.forEach((item, index) => {
                            const newItem = document.createElement('div');
                            newItem.className = 'flex items-center gap-2 mb-2';
                            newItem.innerHTML = `
                                <input type="text" name="popular_items[${index}][name]" 
                                       class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                       value="${item.name || ''}" placeholder="Item name...">
                                <input type="number" name="popular_items[${index}][quantity]" 
                                       class="w-24 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                       value="${item.quantity || ''}" placeholder="Qty" min="1">
                                <button type="button" onclick="removePopularItem(this)" 
                                        class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Remove
                                </button>
                            `;
                            container.appendChild(newItem);
                        });
                        
                        popularItemIndex = analytics.popular_items.length;
                    }
                    
                    alert('Form updated with data from orders! You can now review and save.');
                } else {
                    alert(data.message || 'No order data found for this date');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error regenerating analytics from orders');
            });
        }

        // Auto-calculate average order value when total sales or orders change
        function calculateAverageOrderValue() {
            const totalSales = parseFloat(document.getElementById('total_sales').value) || 0;
            const totalOrders = parseInt(document.getElementById('total_orders').value) || 0;
            
            if (totalOrders > 0) {
                const avgOrderValue = totalSales / totalOrders;
                document.getElementById('average_order_value').value = avgOrderValue.toFixed(2);
            }
        }

        document.getElementById('total_sales').addEventListener('input', calculateAverageOrderValue);
        document.getElementById('total_orders').addEventListener('input', calculateAverageOrderValue);

        // Remove empty popular items before form submission
        document.querySelector('form').addEventListener('submit', function() {
            const items = document.querySelectorAll('#popular-items-container > div');
            items.forEach(item => {
                const nameInput = item.querySelector('input[name*="[name]"]');
                const quantityInput = item.querySelector('input[name*="[quantity]"]');
                if (!nameInput.value.trim() && !quantityInput.value.trim()) {
                    item.remove();
                }
            });
        });
    </script>
</x-app-layout>