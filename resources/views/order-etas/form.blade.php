<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($orderEta->id)
                {{ __('Edit Order ETA') }} - #{{ $orderEta->id }}
            @else
                {{ __('Create New Order ETA') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($orderEta->id)
                            {{ __('Edit ETA Information') }}
                        @else
                            {{ __('ETA Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Set estimated completion time and track order progress.") }}
                    </p>
                </header>

                @if($orderEta->id)
                    <form method="post" action="{{ route('order-etas.update', $orderEta->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('order-etas.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Order Selection -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                        
                        <div>
                            <x-input-label for="order_id" :value="__('Select Order')" />
                            <select id="order_id" name="order_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required {{ $orderEta->id ? 'disabled' : '' }}>
                                <option value="">Select Order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" 
                                        @if(old('order_id', $orderEta->order_id) == $order->id) selected @endif
                                        data-customer="{{ $order->user->name ?? 'Unknown' }}"
                                        data-table="{{ $order->table ? $order->table->table_number : ($order->table_number ?? 'No table') }}"
                                        data-amount="{{ $order->total_amount }}"
                                        data-status="{{ $order->order_status }}"
                                        data-rush="{{ $order->is_rush_order ? 'true' : 'false' }}">
                                        #{{ $order->id }} - {{ $order->user->name ?? 'Unknown' }} 
                                        ({{ $order->order_status }} - RM {{ number_format($order->total_amount, 2) }})
                                        @if($order->table)
                                            - Table {{ $order->table->table_number }}
                                        @elseif($order->table_number)
                                            - {{ $order->table_number }}
                                        @endif
                                        @if($order->is_rush_order)
                                            - RUSH ORDER
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($orderEta->id)
                                <input type="hidden" name="order_id" value="{{ $orderEta->order_id }}">
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('order_id')" />
                        </div>

                        <!-- Selected Order Details (shown after selection) -->
                        <div id="order-details" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                            <h4 class="font-medium text-gray-900 mb-2">Selected Order Details</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Customer:</span>
                                    <div id="detail-customer" class="font-medium"></div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Table:</span>
                                    <div id="detail-table" class="font-medium"></div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Amount:</span>
                                    <div id="detail-amount" class="font-medium"></div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Status:</span>
                                    <div id="detail-status" class="font-medium capitalize"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Estimates -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Time Estimates</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="initial_estimate" :value="__('Initial Estimate (minutes)')" />
                                <x-text-input id="initial_estimate" name="initial_estimate" type="number" min="1" max="480" 
                                    class="mt-1 block w-full" :value="old('initial_estimate', $orderEta->initial_estimate)" 
                                    placeholder="30" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('initial_estimate')" />
                                <p class="mt-1 text-sm text-gray-500">Estimated time to complete the order (1-480 minutes)</p>
                            </div>

                            <div>
                                <x-input-label for="current_estimate" :value="__('Current Estimate (minutes)')" />
                                <x-text-input id="current_estimate" name="current_estimate" type="number" min="1" max="480" 
                                    class="mt-1 block w-full" :value="old('current_estimate', $orderEta->current_estimate)" 
                                    placeholder="30" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('current_estimate')" />
                                <p class="mt-1 text-sm text-gray-500">Current estimated completion time</p>
                            </div>
                        </div>

                        @if($orderEta->id && $orderEta->actual_completion_time)
                        <div class="mt-6">
                            <x-input-label for="actual_completion_time" :value="__('Actual Completion Time (minutes)')" />
                            <x-text-input id="actual_completion_time" name="actual_completion_time" type="number" min="1" max="600" 
                                class="mt-1 block w-full" :value="old('actual_completion_time', $orderEta->actual_completion_time)" 
                                placeholder="35"/>
                            <x-input-error class="mt-2" :messages="$errors->get('actual_completion_time')" />
                            <p class="mt-1 text-sm text-gray-500">How long it actually took to complete (1-600 minutes)</p>
                        </div>
                        @endif

                        <!-- Auto-calculation display -->
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg" id="estimate-info">
                            <div class="text-sm text-blue-800">
                                <strong>Estimate Information:</strong>
                                <div id="estimate-calculation" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Delay Information -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Delay Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="delay_reason" :value="__('Delay Reason (if applicable)')" />
                                <x-text-input id="delay_reason" name="delay_reason" type="text" maxlength="255"
                                    class="mt-1 block w-full" :value="old('delay_reason', $orderEta->delay_reason)" 
                                    placeholder="Kitchen busy, ingredient shortage, etc."/>
                                <x-input-error class="mt-2" :messages="$errors->get('delay_reason')" />
                            </div>

                            <div>
                                <x-input-label for="delay_duration" :value="__('Delay Duration (minutes)')" />
                                <x-text-input id="delay_duration" name="delay_duration" type="number" min="0" max="240"
                                    class="mt-1 block w-full" :value="old('delay_duration', $orderEta->delay_duration)" 
                                    placeholder="0" readonly/>
                                <x-input-error class="mt-2" :messages="$errors->get('delay_duration')" />
                                <p class="mt-1 text-sm text-gray-500">Auto-calculated based on estimate difference</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <label for="is_delayed" class="flex items-center">
                                <input type="checkbox" id="is_delayed" name="is_delayed" value="1" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       @if(old('is_delayed', $orderEta->is_delayed)) checked @endif>
                                <span class="ml-2 text-sm text-gray-600">{{ __('This order is delayed') }}</span>
                            </label>

                            <label for="customer_notified" class="flex items-center">
                                <input type="checkbox" id="customer_notified" name="customer_notified" value="1" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       @if(old('customer_notified', $orderEta->customer_notified)) checked @endif>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Customer has been notified about delay') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quick Actions (for editing existing ETA) -->
                    @if($orderEta->id)
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                        
                        <div class="flex flex-wrap gap-2">
                            @if($orderEta->is_delayed && !$orderEta->customer_notified)
                                <button type="button" onclick="notifyCustomer()" 
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                    Notify Customer
                                </button>
                            @endif
                            
                            @if(!$orderEta->actual_completion_time && $orderEta->order->order_status !== 'completed')
                                <button type="button" onclick="markCompleted()" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Mark as Completed
                                </button>
                            @endif

                            <button type="button" onclick="addTimeToEstimate(15)" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                +15 min
                            </button>
                            
                            <button type="button" onclick="addTimeToEstimate(30)" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                +30 min
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Current ETA Information (for edit) -->
                    @if($orderEta->id)
                        <div class="border-b pb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current ETA Information</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">ETA ID:</span>
                                        <span class="font-medium">#{{ $orderEta->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Order ID:</span>
                                        <span class="font-medium">#{{ $orderEta->order_id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Status:</span>
                                        <span class="font-medium {{ $orderEta->is_delayed ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $orderEta->is_delayed ? 'Delayed' : 'On Time' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Last Updated:</span>
                                        <span class="font-medium">{{ $orderEta->last_updated->format('M d, h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save ETA') }}</x-primary-button>

                        <a href="{{ route('order-etas.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>

                        @if($orderEta->id)
                            <a href="{{ route('order-etas.show', $orderEta->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                View Details
                            </a>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Show order details when order is selected
        document.getElementById('order_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const detailsDiv = document.getElementById('order-details');
            
            if (selectedOption.value) {
                document.getElementById('detail-customer').textContent = selectedOption.dataset.customer;
                document.getElementById('detail-table').textContent = selectedOption.dataset.table;
                document.getElementById('detail-amount').textContent = 'RM ' + parseFloat(selectedOption.dataset.amount).toFixed(2);
                document.getElementById('detail-status').textContent = selectedOption.dataset.status.replace('_', ' ');
                
                detailsDiv.classList.remove('hidden');
                
                // Auto-set initial estimate based on order type
                const isRush = selectedOption.dataset.rush === 'true';
                const status = selectedOption.dataset.status;
                
                if (!document.getElementById('initial_estimate').value) {
                    let suggestedTime = 30; // default
                    if (isRush) suggestedTime = 20;
                    if (status === 'preparing') suggestedTime = 15;
                    
                    document.getElementById('initial_estimate').value = suggestedTime;
                    document.getElementById('current_estimate').value = suggestedTime;
                }
            } else {
                detailsDiv.classList.add('hidden');
            }
            
            calculateDelay();
        });

        // Auto-calculate delay when estimates change
        function calculateDelay() {
            const initial = parseInt(document.getElementById('initial_estimate').value) || 0;
            const current = parseInt(document.getElementById('current_estimate').value) || 0;
            const delayDurationField = document.getElementById('delay_duration');
            const isDelayedCheckbox = document.getElementById('is_delayed');
            const calculationDiv = document.getElementById('estimate-calculation');
            
            if (initial && current) {
                const delay = current - initial;
                delayDurationField.value = Math.max(0, delay);
                
                if (delay > 0) {
                    isDelayedCheckbox.checked = true;
                    calculationDiv.innerHTML = `
                        <div class="text-red-600">
                            ⚠ Order is delayed by <strong>${delay} minutes</strong>
                        </div>
                        <div>Initial: ${initial} min → Current: ${current} min</div>
                    `;
                } else {
                    isDelayedCheckbox.checked = false;
                    calculationDiv.innerHTML = `
                        <div class="text-green-600">
                            ✓ Order is on time
                        </div>
                        <div>Estimate: ${current} minutes</div>
                    `;
                }
            }
        }

        // Add event listeners for estimate calculation
        document.getElementById('initial_estimate').addEventListener('input', calculateDelay);
        document.getElementById('current_estimate').addEventListener('input', calculateDelay);

        // Quick action functions (for edit mode)
        @if($orderEta->id)
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
                    alert('Customer has been notified!');
                    document.getElementById('customer_notified').checked = true;
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error notifying customer');
            });
        }

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
                    alert('Order marked as completed!');
                    if (data.orderEta.actual_completion_time) {
                        // Add actual completion time field if it doesn't exist
                        if (!document.getElementById('actual_completion_time')) {
                            const timeEstimatesSection = document.querySelector('.border-b').parentElement;
                            const actualTimeDiv = document.createElement('div');
                            actualTimeDiv.className = 'mt-6';
                            actualTimeDiv.innerHTML = `
                                <label class="block text-sm font-medium text-gray-700">Actual Completion Time (minutes)</label>
                                <input type="number" id="actual_completion_time" name="actual_completion_time" min="1" max="600"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="${data.orderEta.actual_completion_time}" readonly>
                                <p class="mt-1 text-sm text-gray-500">How long it actually took to complete</p>
                            `;
                            timeEstimatesSection.appendChild(actualTimeDiv);
                        }
                    }
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as completed');
            });
        }
        @endif

        function addTimeToEstimate(minutes) {
            const currentField = document.getElementById('current_estimate');
            const currentValue = parseInt(currentField.value) || 0;
            currentField.value = currentValue + minutes;
            calculateDelay();
        }

        // Auto-sync current estimate with initial estimate when initial changes (for new ETAs)
        @if(!$orderEta->id)
        document.getElementById('initial_estimate').addEventListener('input', function() {
            const currentField = document.getElementById('current_estimate');
            if (!currentField.value || currentField.value == this.defaultValue) {
                currentField.value = this.value;
            }
        });
        @endif

        // Initialize calculation on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateDelay();
            
            // Show order details if order is already selected (edit mode)
            const orderSelect = document.getElementById('order_id');
            if (orderSelect.value) {
                orderSelect.dispatchEvent(new Event('change'));
            }
        });

        // Validate estimates before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const initial = parseInt(document.getElementById('initial_estimate').value);
            const current = parseInt(document.getElementById('current_estimate').value);
            
            if (initial < 1 || initial > 480) {
                e.preventDefault();
                alert('Initial estimate must be between 1 and 480 minutes');
                return;
            }
            
            if (current < 1 || current > 480) {
                e.preventDefault();
                alert('Current estimate must be between 1 and 480 minutes');
                return;
            }
        });
    </script>
</x-app-layout>