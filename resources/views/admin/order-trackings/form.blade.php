<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(isset($orderTracking) && $orderTracking->id)
                {{ __('Edit Order Tracking') }} - Order #{{ $orderTracking->order->id }}
            @else
                {{ __('Create New Order Tracking') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if(isset($orderTracking) && $orderTracking->id)
                            {{ __('Edit Order Tracking Information') }}
                        @else
                            {{ __('Order Tracking Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Complete the order tracking details below.") }}
                    </p>
                </header>

                @if(isset($orderTracking) && $orderTracking->id)
                    <form method="post" action="{{ route('admin.order-trackings.update', $orderTracking->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('admin.order-trackings.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Order Selection -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                        
                        @if(isset($orderTracking) && $orderTracking->id)
                            <!-- Show order details for edit -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Order ID:</span>
                                        <p class="text-lg font-bold">#{{ $orderTracking->order->id }}</p>
                                    </div>
                                    @if($orderTracking->order->table)
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Table:</span>
                                        <p class="text-lg">{{ $orderTracking->order->table->table_number }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Order Date:</span>
                                        <p class="text-lg">{{ $orderTracking->order->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="order_id" value="{{ $orderTracking->order_id }}">
                            </div>
                        @else
                            <!-- Order selection for create -->
                            <div>
                                <x-input-label for="order_id" :value="__('Select Order')" />
                                <select id="order_id" name="order_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select an Order</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->id }}" 
                                            @if(old('order_id') == $order->id) selected @endif>
                                            Order #{{ $order->id }} - 
                                            @if($order->table)
                                                Table {{ $order->table->table_number }}
                                            @else
                                                No Table
                                            @endif
                                            ({{ $order->created_at->format('M d, h:i A') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('order_id')" />
                            </div>
                        @endif
                    </div>

                    <!-- Tracking Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Status</option>
                                <option value="received" @if(old('status', $orderTracking->status ?? '') == 'received') selected @endif>Received</option>
                                <option value="confirmed" @if(old('status', $orderTracking->status ?? '') == 'confirmed') selected @endif>Confirmed</option>
                                <option value="preparing" @if(old('status', $orderTracking->status ?? '') == 'preparing') selected @endif>Preparing</option>
                                <option value="cooking" @if(old('status', $orderTracking->status ?? '') == 'cooking') selected @endif>Cooking</option>
                                <option value="plating" @if(old('status', $orderTracking->status ?? '') == 'plating') selected @endif>Plating</option>
                                <option value="ready" @if(old('status', $orderTracking->status ?? '') == 'ready') selected @endif>Ready</option>
                                <option value="served" @if(old('status', $orderTracking->status ?? '') == 'served') selected @endif>Served</option>
                                <option value="completed" @if(old('status', $orderTracking->status ?? '') == 'completed') selected @endif>Completed</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="station_name" :value="__('Station')" />
                            <select id="station_name" name="station_name" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Station (Optional)</option>
                                <option value="Kitchen" @if(old('station_name', $orderTracking->station_name ?? '') == 'Kitchen') selected @endif>Kitchen</option>
                                <option value="Bar" @if(old('station_name', $orderTracking->station_name ?? '') == 'Bar') selected @endif>Bar</option>
                                <option value="Grill" @if(old('station_name', $orderTracking->station_name ?? '') == 'Grill') selected @endif>Grill</option>
                                <option value="Pastry" @if(old('station_name', $orderTracking->station_name ?? '') == 'Pastry') selected @endif>Pastry</option>
                                <option value="Cold Station" @if(old('station_name', $orderTracking->station_name ?? '') == 'Cold Station') selected @endif>Cold Station</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('station_name')" />
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Time Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="started_at" :value="__('Started At')" />
                                <input type="datetime-local" id="started_at" name="started_at" 
                                       class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       value="{{ old('started_at', isset($orderTracking) && $orderTracking->started_at ? $orderTracking->started_at->format('Y-m-d\TH:i') : '') }}"/>
                                <x-input-error class="mt-2" :messages="$errors->get('started_at')" />
                                <p class="mt-1 text-sm text-gray-500">Leave blank to set to current time</p>
                            </div>

                            <div>
                                <x-input-label for="completed_at" :value="__('Completed At (Optional)')" />
                                <input type="datetime-local" id="completed_at" name="completed_at" 
                                       class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       value="{{ old('completed_at', isset($orderTracking) && $orderTracking->completed_at ? $orderTracking->completed_at->format('Y-m-d\TH:i') : '') }}"/>
                                <x-input-error class="mt-2" :messages="$errors->get('completed_at')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="estimated_time" :value="__('Estimated Time (Minutes)')" />
                                <x-text-input id="estimated_time" name="estimated_time" type="number" min="1" max="999" class="mt-1 block w-full" 
                                    :value="old('estimated_time', $orderTracking->estimated_time ?? '')" placeholder="e.g. 15"/>
                                <x-input-error class="mt-2" :messages="$errors->get('estimated_time')" />
                            </div>

                            <div>
                                <x-input-label for="actual_time" :value="__('Actual Time (Minutes)')" />
                                <x-text-input id="actual_time" name="actual_time" type="number" min="1" max="999" class="mt-1 block w-full" 
                                    :value="old('actual_time', $orderTracking->actual_time ?? '')" placeholder="e.g. 18"/>
                                <x-input-error class="mt-2" :messages="$errors->get('actual_time')" />
                                <p class="mt-1 text-sm text-gray-500">Auto-calculated when status is updated to completed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Assignment -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Staff Assignment</h3>
                        
                        <div>
                            <x-input-label for="staff_id" :value="__('Assigned Staff')" />
                            <select id="staff_id" name="staff_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Auto-assign to current user</option>
                                @php
                                    $users = \App\Models\User::orderBy('name')->get();
                                @endphp
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        @if(old('staff_id', $orderTracking->staff_id ?? auth()->id()) == $user->id) selected @endif>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('staff_id')" />
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="4" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                      placeholder="Any additional notes about this tracking status...">{{ old('notes', $orderTracking->notes ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Tracking') }}</x-primary-button>

                        <a href="{{ route('admin.order-trackings.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Auto-set completion time when status changes to completed, served, or ready
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const completedAtField = document.getElementById('completed_at');
            
            if (['completed', 'served', 'ready'].includes(status)) {
                if (!completedAtField.value) {
                    const now = new Date();
                    const localISOTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                    completedAtField.value = localISOTime;
                }
            }
        });

        // Calculate actual time when completion time is set
        document.getElementById('completed_at').addEventListener('change', function() {
            const startedAt = document.getElementById('started_at').value;
            const completedAt = this.value;
            const actualTimeField = document.getElementById('actual_time');
            
            if (startedAt && completedAt && !actualTimeField.value) {
                const start = new Date(startedAt);
                const end = new Date(completedAt);
                const diffMinutes = Math.round((end - start) / (1000 * 60));
                
                if (diffMinutes > 0) {
                    actualTimeField.value = diffMinutes;
                }
            }
        });
    </script>
</x-app-layout>