<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($menuCustomization->id)
                {{ __('Edit Menu Customization') }} - #{{ $menuCustomization->id }}
            @else
                {{ __('Create New Menu Customization') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($menuCustomization->id)
                            {{ __('Edit Customization Details') }}
                        @else
                            {{ __('Customization Details') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Configure the menu customization options below.") }}
                    </p>
                </header>

                @if($menuCustomization->id)
                    <form method="post" action="{{ route('admin.menu-customizations.update', $menuCustomization->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('admin.menu-customizations.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Order Item Selection -->
                    <div>
                        <x-input-label for="order_item_id" :value="__('Order Item')" />
                        <select id="order_item_id" name="order_item_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select Order Item</option>
                            @foreach($orderItems as $orderItem)
                                <option value="{{ $orderItem->id }}" 
                                    @if(old('order_item_id', $menuCustomization->order_item_id) == $orderItem->id) selected @endif
                                    data-menu-item="{{ $orderItem->menuItem->name ?? 'Unknown Item' }}"
                                    data-order-id="{{ $orderItem->order_id }}"
                                    data-quantity="{{ $orderItem->quantity }}">
                                    Order #{{ $orderItem->order_id }} - {{ $orderItem->menuItem->name ?? 'Unknown Item' }} 
                                    (Qty: {{ $orderItem->quantity }})
                                    @if($orderItem->order && $orderItem->order->user)
                                        - {{ $orderItem->order->user->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('order_item_id')" />
                        
                        <!-- Order Item Info Display -->
                        <div id="order-item-info" class="mt-2 p-3 bg-gray-50 rounded-md hidden">
                            <div class="text-sm text-gray-600">
                                <div><strong>Menu Item:</strong> <span id="selected-menu-item"></span></div>
                                <div><strong>Order ID:</strong> #<span id="selected-order-id"></span></div>
                                <div><strong>Quantity:</strong> <span id="selected-quantity"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Customization Type and Value -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="customization_type" :value="__('Customization Type')" />
                            <select id="customization_type" name="customization_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Type</option>
                                <option value="Spice Level" @if(old('customization_type', $menuCustomization->customization_type) == 'Spice Level') selected @endif>Spice Level</option>
                                <option value="Sauce" @if(old('customization_type', $menuCustomization->customization_type) == 'Sauce') selected @endif>Sauce</option>
                                <option value="Topping" @if(old('customization_type', $menuCustomization->customization_type) == 'Topping') selected @endif>Topping</option>
                                <option value="Size" @if(old('customization_type', $menuCustomization->customization_type) == 'Size') selected @endif>Size</option>
                                <option value="Temperature" @if(old('customization_type', $menuCustomization->customization_type) == 'Temperature') selected @endif>Temperature</option>
                                <option value="Cooking Style" @if(old('customization_type', $menuCustomization->customization_type) == 'Cooking Style') selected @endif>Cooking Style</option>
                                <option value="Dietary" @if(old('customization_type', $menuCustomization->customization_type) == 'Dietary') selected @endif>Dietary Preference</option>
                                <option value="Removal" @if(old('customization_type', $menuCustomization->customization_type) == 'Removal') selected @endif>Ingredient Removal</option>
                                <option value="Extra" @if(old('customization_type', $menuCustomization->customization_type) == 'Extra') selected @endif>Extra Portion</option>
                                <option value="Special Request" @if(old('customization_type', $menuCustomization->customization_type) == 'Special Request') selected @endif>Special Request</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('customization_type')" />
                        </div>

                        <div>
                            <x-input-label for="customization_value" :value="__('Customization Value')" />
                            <input type="text" id="customization_value" name="customization_value" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ old('customization_value', $menuCustomization->customization_value) }}" 
                                   placeholder="e.g., Extra Spicy, No Onions, Large Size" required>
                            <x-input-error class="mt-2" :messages="$errors->get('customization_value')" />
                        </div>
                    </div>

                    <!-- Additional Price -->
                    <div>
                        <x-input-label for="additional_price" :value="__('Additional Price (RM)')" />
                        <x-text-input id="additional_price" name="additional_price" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                            :value="old('additional_price', $menuCustomization->additional_price ?? 0.00)" placeholder="0.00"/>
                        <x-input-error class="mt-2" :messages="$errors->get('additional_price')" />
                        <p class="mt-1 text-sm text-gray-500">Leave as 0.00 if no additional charge applies</p>
                    </div>

                    <!-- Quick Customization Templates (for new customizations) -->
                    @if(!$menuCustomization->id)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Templates</h3>
                        <p class="text-sm text-gray-600 mb-4">Click a template to quickly fill the form:</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <button type="button" onclick="applyTemplate('spicy')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-red-600">Extra Spicy</div>
                                <div class="text-sm text-gray-500">+RM 2.00</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('cheese')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-yellow-600">Extra Cheese</div>
                                <div class="text-sm text-gray-500">+RM 3.00</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('large')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-blue-600">Large Size</div>
                                <div class="text-sm text-gray-500">+RM 5.00</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('no_onion')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-gray-600">No Onion</div>
                                <div class="text-sm text-gray-500">Free</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('gluten_free')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-green-600">Gluten Free</div>
                                <div class="text-sm text-gray-500">+RM 2.50</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('extra_sauce')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-orange-600">Extra Sauce</div>
                                <div class="text-sm text-gray-500">+RM 1.50</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('well_done')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-purple-600">Well Done</div>
                                <div class="text-sm text-gray-500">Free</div>
                            </button>
                            
                            <button type="button" onclick="applyTemplate('no_ice')" 
                                    class="p-3 border border-gray-300 rounded-md hover:bg-gray-50 text-left">
                                <div class="font-medium text-indigo-600">No Ice</div>
                                <div class="text-sm text-gray-500">Free</div>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Current Customization Information (for edit) -->
                    @if($menuCustomization->id)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Customization Information</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Customization ID:</span>
                                        <span class="font-medium">#{{ $menuCustomization->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Order Item:</span>
                                        <span class="font-medium">
                                            @if($menuCustomization->orderItem)
                                                Order #{{ $menuCustomization->orderItem->order_id }} - {{ $menuCustomization->orderItem->menuItem->name ?? 'Unknown' }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-medium">{{ $menuCustomization->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @if($menuCustomization->updated_at != $menuCustomization->created_at)
                                    <div>
                                        <span class="text-gray-600">Last Updated:</span>
                                        <span class="font-medium">{{ $menuCustomization->updated_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Customization') }}</x-primary-button>

                        <a href="{{ route('admin.menu-customizations.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>

                        @if($menuCustomization->id)
                            <form method="POST" action="{{ route('admin.menu-customizations.destroy', $menuCustomization->id) }}" 
                                  onsubmit="return confirm('Are you sure to delete this customization?');" class="inline">
                                <input type="hidden" name="_method" value="DELETE">
                                @csrf
                                <x-danger-button class="text-xs">
                                    Delete Customization
                                </x-danger-button>
                            </form>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Update order item info display
        document.getElementById('order_item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const infoDiv = document.getElementById('order-item-info');
            
            if (selectedOption.value) {
                document.getElementById('selected-menu-item').textContent = selectedOption.dataset.menuItem;
                document.getElementById('selected-order-id').textContent = selectedOption.dataset.orderId;
                document.getElementById('selected-quantity').textContent = selectedOption.dataset.quantity;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        });

        // Initialize display if edit mode
        @if($menuCustomization->id)
            document.addEventListener('DOMContentLoaded', function() {
                const orderSelect = document.getElementById('order_item_id');
                if (orderSelect.value) {
                    orderSelect.dispatchEvent(new Event('change'));
                }
            });
        @endif

        // Quick template functions
        function applyTemplate(templateType) {
            const templates = {
                'spicy': {
                    type: 'Spice Level',
                    value: 'Extra Spicy',
                    price: '2.00'
                },
                'cheese': {
                    type: 'Topping',
                    value: 'Extra Cheese',
                    price: '3.00'
                },
                'large': {
                    type: 'Size',
                    value: 'Large Size',
                    price: '5.00'
                },
                'no_onion': {
                    type: 'Removal',
                    value: 'No Onion',
                    price: '0.00'
                },
                'gluten_free': {
                    type: 'Dietary',
                    value: 'Gluten Free',
                    price: '2.50'
                },
                'extra_sauce': {
                    type: 'Sauce',
                    value: 'Extra Sauce',
                    price: '1.50'
                },
                'well_done': {
                    type: 'Cooking Style',
                    value: 'Well Done',
                    price: '0.00'
                },
                'no_ice': {
                    type: 'Temperature',
                    value: 'No Ice',
                    price: '0.00'
                }
            };

            const template = templates[templateType];
            if (template) {
                document.getElementById('customization_type').value = template.type;
                document.getElementById('customization_value').value = template.value;
                document.getElementById('additional_price').value = template.price;
            }
        }

        // Smart suggestions based on customization type
        document.getElementById('customization_type').addEventListener('change', function() {
            const suggestions = {
                'Spice Level': ['Mild', 'Medium', 'Spicy', 'Extra Spicy', 'No Spice'],
                'Sauce': ['Extra Sauce', 'Less Sauce', 'No Sauce', 'Hot Sauce', 'Garlic Sauce'],
                'Topping': ['Extra Cheese', 'Extra Vegetables', 'Extra Meat', 'Mushrooms', 'Pepperoni'],
                'Size': ['Small', 'Medium', 'Large', 'Extra Large'],
                'Temperature': ['Hot', 'Warm', 'Cold', 'No Ice', 'Extra Ice'],
                'Cooking Style': ['Rare', 'Medium Rare', 'Medium', 'Well Done', 'Crispy'],
                'Dietary': ['Gluten Free', 'Vegan', 'Vegetarian', 'Halal', 'No Sugar'],
                'Removal': ['No Onion', 'No Garlic', 'No Mushrooms', 'No Cheese', 'No Sauce'],
                'Extra': ['Extra Portion', 'Extra Side', 'Extra Garnish'],
                'Special Request': ['Cut in Half', 'Separate Container', 'Extra Napkins']
            };

            const valueInput = document.getElementById('customization_value');
            const selectedType = this.value;
            
            if (suggestions[selectedType] && !valueInput.value) {
                // Show placeholder with first suggestion
                valueInput.placeholder = suggestions[selectedType][0];
            }
        });

        // Auto-suggest pricing based on common customizations
        document.getElementById('customization_value').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            const priceInput = document.getElementById('additional_price');
            
            // Only suggest if current price is 0
            if (parseFloat(priceInput.value) === 0) {
                const priceSuggestions = {
                    'extra spicy': 2.00,
                    'extra cheese': 3.00,
                    'large size': 5.00,
                    'extra large': 7.00,
                    'gluten free': 2.50,
                    'extra sauce': 1.50,
                    'extra portion': 4.00,
                    'extra vegetables': 2.00,
                    'extra meat': 5.00
                };

                for (const [key, price] of Object.entries(priceSuggestions)) {
                    if (value.includes(key)) {
                        priceInput.value = price.toFixed(2);
                        break;
                    }
                }
            }
        });
    </script>
</x-app-layout>