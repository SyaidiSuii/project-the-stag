<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($order->id)
                {{ __('Edit Order') }} - #{{ $order->id }}
            @else
                {{ __('Create New Order') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($order->id)
                            {{ __('Edit Order Information') }}
                        @else
                            {{ __('Order Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Complete the order details below.") }}
                    </p>
                </header>

                @if($order->id)
                    <form method="post" action="{{ route('order.update', $order->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('order.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Basic Order Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="user_id" :value="__('Customer')" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Customer</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        @if(old('user_id', $order->user_id) == $user->id) selected @endif>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>

                        <div>
                            <x-input-label for="total_amount" :value="__('Total Amount (RM)')" />
                            <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('total_amount', $order->total_amount)" placeholder="0.00" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('total_amount')" />
                        </div>
                    </div>

                    <!-- Order Type and Source -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="order_type" :value="__('Order Type')" />
                            <select id="order_type" name="order_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Order Type</option>
                                <option value="dine_in" @if(old('order_type', $order->order_type) == 'dine_in') selected @endif>Dine In</option>
                                <option value="takeaway" @if(old('order_type', $order->order_type) == 'takeaway') selected @endif>Takeaway</option>
                                <option value="delivery" @if(old('order_type', $order->order_type) == 'delivery') selected @endif>Delivery</option>
                                <option value="event" @if(old('order_type', $order->order_type) == 'event') selected @endif>Event</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('order_type')" />
                        </div>

                        <div>
                            <x-input-label for="order_source" :value="__('Order Source')" />
                            <select id="order_source" name="order_source" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="counter" @if(old('order_source', $order->order_source) == 'counter') selected @endif>Counter</option>
                                <option value="web" @if(old('order_source', $order->order_source) == 'web') selected @endif>Web</option>
                                <option value="mobile" @if(old('order_source', $order->order_source) == 'mobile') selected @endif>Mobile</option>
                                <option value="waiter" @if(old('order_source', $order->order_source) == 'waiter') selected @endif>Waiter</option>
                                <option value="qr_scan" @if(old('order_source', $order->order_source) == 'qr_scan') selected @endif>QR Scan</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('order_source')" />
                        </div>
                    </div>

                    <!-- Table Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Table Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="table_id" :value="__('Table (Optional)')" />
                                <select id="table_id" name="table_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Table (Optional)</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" 
                                            @if(old('table_id', $order->table_id) == $table->id) selected @endif>
                                            {{ $table->table_number }} - {{ $table->status }} ({{ $table->capacity }} capacity)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('table_id')" />
                            </div>

                            <div>
                                <x-input-label for="table_number" :value="__('Table Number (Manual)')" />
                                <x-text-input id="table_number" name="table_number" type="text" class="mt-1 block w-full" 
                                    :value="old('table_number', $order->table_number)" placeholder="e.g. A1, B2"/>
                                <x-input-error class="mt-2" :messages="$errors->get('table_number')" />
                                <p class="mt-1 text-sm text-gray-500">Use this for custom table numbers or delivery orders</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="reservation_id" :value="__('Related Reservation (Optional)')" />
                            <select id="reservation_id" name="reservation_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Reservation (Optional)</option>
                                @foreach($reservations as $reservation)
                                    <option value="{{ $reservation->id }}" 
                                        @if(old('reservation_id', $order->reservation_id) == $reservation->id) selected @endif>
                                        {{ $reservation->confirmation_code }} - {{ $reservation->guest_name }} 
                                        ({{ $reservation->reservation_date->format('M d') }} {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('reservation_id')" />
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="order_status" :value="__('Order Status')" />
                                <select id="order_status" name="order_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="pending" @if(old('order_status', $order->order_status) == 'pending') selected @endif>Pending</option>
                                    <option value="confirmed" @if(old('order_status', $order->order_status) == 'confirmed') selected @endif>Confirmed</option>
                                    <option value="preparing" @if(old('order_status', $order->order_status) == 'preparing') selected @endif>Preparing</option>
                                    <option value="ready" @if(old('order_status', $order->order_status) == 'ready') selected @endif>Ready</option>
                                    <option value="served" @if(old('order_status', $order->order_status) == 'served') selected @endif>Served</option>
                                    <option value="completed" @if(old('order_status', $order->order_status) == 'completed') selected @endif>Completed</option>
                                    <option value="cancelled" @if(old('order_status', $order->order_status) == 'cancelled') selected @endif>Cancelled</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('order_status')" />
                            </div>

                            <div>
                                <x-input-label for="payment_status" :value="__('Payment Status')" />
                                <select id="payment_status" name="payment_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="unpaid" @if(old('payment_status', $order->payment_status) == 'unpaid') selected @endif>Unpaid</option>
                                    <option value="partial" @if(old('payment_status', $order->payment_status) == 'partial') selected @endif>Partial</option>
                                    <option value="paid" @if(old('payment_status', $order->payment_status) == 'paid') selected @endif>Paid</option>
                                    <option value="refunded" @if(old('payment_status', $order->payment_status) == 'refunded') selected @endif>Refunded</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
                            </div>
                        </div>
                    </div>

                    <!-- Timing Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Timing Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="estimated_completion_time" :value="__('Estimated Completion Time')" />
                                <input type="datetime-local" id="estimated_completion_time" name="estimated_completion_time" 
                                       class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       value="{{ old('estimated_completion_time', $order->estimated_completion_time ? $order->estimated_completion_time->format('Y-m-d\TH:i') : '') }}"/>
                                <x-input-error class="mt-2" :messages="$errors->get('estimated_completion_time')" />
                            </div>

                            <div>
                                <x-input-label for="actual_completion_time" :value="__('Actual Completion Time')" />
                                <input type="datetime-local" id="actual_completion_time" name="actual_completion_time" 
                                       class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       value="{{ old('actual_completion_time', $order->actual_completion_time ? $order->actual_completion_time->format('Y-m-d\TH:i') : '') }}"/>
                                <x-input-error class="mt-2" :messages="$errors->get('actual_completion_time')" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="is_rush_order" class="flex items-center">
                                <input type="checkbox" id="is_rush_order" name="is_rush_order" value="1" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       @if(old('is_rush_order', $order->is_rush_order)) checked @endif>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Rush Order (Priority Processing)') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Special Instructions</h3>
                        
                        <div id="special-instructions-container">
                            @if(old('special_instructions', $order->special_instructions))
                                @foreach(old('special_instructions', $order->special_instructions ?? []) as $index => $instruction)
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" name="special_instructions[]" 
                                               class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                               value="{{ $instruction }}" placeholder="Enter special instruction...">
                                        <button type="button" onclick="removeInstruction(this)" 
                                                class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                            Remove
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" name="special_instructions[]" 
                                           class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           placeholder="Enter special instruction...">
                                    <button type="button" onclick="removeInstruction(this)" 
                                            class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                        Remove
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" onclick="addInstruction()" 
                                class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Add Instruction
                        </button>
                        <x-input-error class="mt-2" :messages="$errors->get('special_instructions')" />
                    </div>

                    <!-- Confirmation Code -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmation</h3>
                        
                        <div>
                            <x-input-label for="confirmation_code" :value="__('Confirmation Code (Optional)')" />
                            <x-text-input id="confirmation_code" name="confirmation_code" type="text" class="mt-1 block w-full" 
                                :value="old('confirmation_code', $order->confirmation_code)" placeholder="Auto-generated if empty"/>
                            <x-input-error class="mt-2" :messages="$errors->get('confirmation_code')" />
                            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate a unique confirmation code</p>
                        </div>
                    </div>

                    <!-- Current Order Information (for edit) -->
                    @if($order->id)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Order Information</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Order ID:</span>
                                        <span class="font-medium">#{{ $order->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Order Time:</span>
                                        <span class="font-medium">{{ $order->order_time->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @if($order->confirmation_code)
                                    <div>
                                        <span class="text-gray-600">Confirmation Code:</span>
                                        <span class="font-mono font-medium">{{ $order->confirmation_code }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Order') }}</x-primary-button>

                        <a href="{{ route('order.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>

                        @if($order->id)
                            <a href="{{ route('order.duplicate', $order->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Duplicate Order
                            </a>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Special Instructions Management
        function addInstruction() {
            const container = document.getElementById('special-instructions-container');
            const newInstruction = document.createElement('div');
            newInstruction.className = 'flex items-center gap-2 mb-2';
            newInstruction.innerHTML = `
                <input type="text" name="special_instructions[]" 
                       class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                       placeholder="Enter special instruction...">
                <button type="button" onclick="removeInstruction(this)" 
                        class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(newInstruction);
        }

        function removeInstruction(button) {
            const container = document.getElementById('special-instructions-container');
            if (container.children.length > 1) {
                button.parentElement.remove();
            } else {
                // Clear the input instead of removing if it's the last one
                button.parentElement.querySelector('input').value = '';
            }
        }

        // Auto-populate table number when table is selected
        document.getElementById('table_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const tableNumber = selectedOption.textContent.split(' - ')[0];
                document.getElementById('table_number').value = tableNumber;
            } else {
                document.getElementById('table_number').value = '';
            }
        });

        // Order type specific field visibility
        document.getElementById('order_type').addEventListener('change', function() {
            const orderType = this.value;
            const tableSection = document.querySelector('[data-table-section]') || document.querySelector('h3').parentElement;
            
            if (orderType === 'delivery') {
                // Hide table selection for delivery orders
                document.getElementById('table_id').disabled = true;
                document.getElementById('table_number').placeholder = 'Delivery Address or ID';
            } else if (orderType === 'takeaway') {
                document.getElementById('table_id').disabled = true;
                document.getElementById('table_number').placeholder = 'Takeaway Order Number';
            } else {
                document.getElementById('table_id').disabled = false;
                document.getElementById('table_number').placeholder = 'e.g. A1, B2';
            }
        });

        // Auto-set estimated completion time based on current time + 30 minutes
        if (!document.getElementById('estimated_completion_time').value && !@json($order->id)) {
            const now = new Date();
            now.setMinutes(now.getMinutes() + 30);
            const estimatedTime = now.toISOString().slice(0, 16);
            document.getElementById('estimated_completion_time').value = estimatedTime;
        }

        // Validate completion times
        document.getElementById('estimated_completion_time').addEventListener('change', function() {
            const estimatedTime = new Date(this.value);
            const now = new Date();
            
            if (estimatedTime < now) {
                alert('Estimated completion time should be in the future');
                this.value = '';
            }
        });

        // Auto-set actual completion time when order status is set to completed
        document.getElementById('order_status').addEventListener('change', function() {
            const actualTimeInput = document.getElementById('actual_completion_time');
            
            if (this.value === 'completed' && !actualTimeInput.value) {
                const now = new Date();
                const currentTime = now.toISOString().slice(0, 16);
                actualTimeInput.value = currentTime;
            }
        });

        // Remove empty special instructions before form submission
        document.querySelector('form').addEventListener('submit', function() {
            const instructions = document.querySelectorAll('input[name="special_instructions[]"]');
            instructions.forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });
        });
    </script>
</x-app-layout>