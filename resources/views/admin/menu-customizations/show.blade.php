<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Customization Details') }} - #{{ $menuCustomization->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Customization #{{ $menuCustomization->id }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ $menuCustomization->customization_type }} - {{ $menuCustomization->customization_value }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.menu-customizations.edit', $menuCustomization->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Customization
                    </a>
                    <a href="{{ route('admin.menu-customizations.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Customization Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customization Details</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Customization ID:</span>
                                <p class="font-bold text-lg">#{{ $menuCustomization->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Created:</span>
                                <p class="font-medium">{{ $menuCustomization->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Customization Type:</span>
                            <p class="font-medium">
                                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                    {{ $menuCustomization->customization_type }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Customization Value:</span>
                            <p class="font-bold text-xl">{{ $menuCustomization->customization_value }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Additional Price:</span>
                            <p class="font-bold text-xl {{ $menuCustomization->additional_price > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                @if($menuCustomization->additional_price > 0)
                                    +RM {{ number_format($menuCustomization->additional_price, 2) }}
                                @else
                                    FREE
                                @endif
                            </p>
                        </div>

                        @if($menuCustomization->updated_at != $menuCustomization->created_at)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <p class="font-medium">{{ $menuCustomization->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order Item Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Related Order Item</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($menuCustomization->orderItem)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Order ID:</span>
                                    <p class="font-bold text-lg">
                                        <a href="{{ route('admin.order.show', $menuCustomization->orderItem->order_id) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            #{{ $menuCustomization->orderItem->order_id }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Quantity:</span>
                                    <p class="font-medium">{{ $menuCustomization->orderItem->quantity }}</p>
                                </div>
                            </div>

                            @if($menuCustomization->orderItem->menuItem)
                            <div>
                                <span class="text-sm text-gray-600">Menu Item:</span>
                                <p class="font-bold text-lg">{{ $menuCustomization->orderItem->menuItem->name }}</p>
                                @if($menuCustomization->orderItem->menuItem->description)
                                    <p class="text-sm text-gray-600">{{ $menuCustomization->orderItem->menuItem->description }}</p>
                                @endif
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Unit Price:</span>
                                    <p class="font-medium">RM {{ number_format($menuCustomization->orderItem->unit_price, 2) }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Total Price:</span>
                                    <p class="font-bold text-green-600">RM {{ number_format($menuCustomization->orderItem->total_price, 2) }}</p>
                                </div>
                            </div>

                            @if($menuCustomization->orderItem->special_note)
                            <div class="border-t pt-4">
                                <span class="text-sm text-gray-600">Special Notes:</span>
                                <p class="font-medium bg-yellow-50 p-3 rounded-md border border-yellow-200">
                                    {{ $menuCustomization->orderItem->special_note }}
                                </p>
                            </div>
                            @endif

                            <div class="border-t pt-4">
                                <span class="text-sm text-gray-600">Item Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($menuCustomization->orderItem->item_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($menuCustomization->orderItem->item_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($menuCustomization->orderItem->item_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($menuCustomization->orderItem->item_status == 'served') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $menuCustomization->orderItem->item_status) }}
                                    </span>
                                </p>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Order item information not available</p>
                        @endif
                    </div>
                </div>

                <!-- Customer Information -->
                @if($menuCustomization->orderItem && $menuCustomization->orderItem->order && $menuCustomization->orderItem->order->user)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customer Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Customer Name:</span>
                            <p class="font-medium text-lg">{{ $menuCustomization->orderItem->order->user->name }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Email:</span>
                            <p class="font-medium">
                                <a href="mailto:{{ $menuCustomization->orderItem->order->user->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $menuCustomization->orderItem->order->user->email }}
                                </a>
                            </p>
                        </div>

                        @if($menuCustomization->orderItem->order->user->phone)
                        <div>
                            <span class="text-sm text-gray-600">Phone:</span>
                            <p class="font-medium">
                                <a href="tel:{{ $menuCustomization->orderItem->order->user->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $menuCustomization->orderItem->order->user->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Order Summary -->
                @if($menuCustomization->orderItem && $menuCustomization->orderItem->order)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Summary</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @php $order = $menuCustomization->orderItem->order; @endphp
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($order->order_status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->order_status == 'preparing') bg-blue-100 text-blue-800
                                        @elseif($order->order_status == 'ready') bg-purple-100 text-purple-800
                                        @elseif($order->order_status == 'served') bg-indigo-100 text-indigo-800
                                        @elseif($order->order_status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $order->order_status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Payment Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status == 'unpaid') bg-red-100 text-red-800
                                        @elseif($order->payment_status == 'refunded') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->payment_status }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Order Type:</span>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $order->order_type) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <p class="font-bold text-xl text-green-600">RM {{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        @if($order->table || $order->table_number)
                        <div>
                            <span class="text-sm text-gray-600">Table:</span>
                            <p class="font-medium">
                                @if($order->table)
                                    Table {{ $order->table->table_number }} ({{ ucfirst($order->table->table_type) }})
                                @else
                                    {{ $order->table_number }}
                                @endif
                            </p>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Order Time:</span>
                            <p class="font-medium">{{ $order->order_time->format('M d, Y h:i A') }}</p>
                        </div>

                        @if($order->confirmation_code)
                        <div>
                            <span class="text-sm text-gray-600">Confirmation Code:</span>
                            <p class="font-mono font-bold">{{ $order->confirmation_code }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            <!-- Related Customizations -->
            @if($menuCustomization->orderItem)
            <div class="mt-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Other Customizations for this Order Item</h4>
                    </div>
                    <div class="p-6">
                        <div id="other-customizations">
                            <p class="text-gray-500 text-center">Loading other customizations...</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <script>
        // Load other customizations for the same order item
        @if($menuCustomization->orderItem)
        document.addEventListener('DOMContentLoaded', function() {
            fetch(`{{ route('admin.menu-customizations.get-customizations') }}?order_item_id={{ $menuCustomization->order_item_id }}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('other-customizations');
                    
                    if (data.customizations && data.customizations.length > 0) {
                        // Filter out current customization
                        const otherCustomizations = data.customizations.filter(c => c.id != {{ $menuCustomization->id }});
                        
                        if (otherCustomizations.length > 0) {
                            let html = '<div class="space-y-3">';
                            
                            otherCustomizations.forEach(customization => {
                                const additionalPrice = parseFloat(customization.additional_price);
                                const priceText = additionalPrice > 0 ? 
                                    `+RM ${additionalPrice.toFixed(2)}` : 
                                    'FREE';
                                const priceClass = additionalPrice > 0 ? 'text-green-600' : 'text-gray-500';
                                
                                html += `
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                                ${customization.customization_type}
                                            </span>
                                            <p class="font-medium mt-1">${customization.customization_value}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold ${priceClass}">${priceText}</p>
                                            <div class="flex space-x-2 mt-2">
                                                <a href="{{ route('admin.menu-customizations.show', '') }}/${customization.id}" 
                                                   class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                    View
                                                </a>
                                                <a href="{{ route('admin.menu-customizations.edit', '') }}/${customization.id}" 
                                                   class="px-2 py-1 bg-gray-800 text-white text-xs rounded hover:bg-gray-900">
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            html += '</div>';
                            
                            // Add total additional price
                            const totalAdditional = parseFloat(data.total_additional_price || 0);
                            if (totalAdditional > 0) {
                                html += `
                                    <div class="border-t mt-4 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold">Total Additional Price:</span>
                                            <span class="text-xl font-bold text-green-600">+RM ${totalAdditional.toFixed(2)}</span>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<p class="text-gray-500 text-center">No other customizations for this order item</p>';
                        }
                    } else {
                        container.innerHTML = '<p class="text-gray-500 text-center">No other customizations for this order item</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading customizations:', error);
                    document.getElementById('other-customizations').innerHTML = 
                        '<p class="text-red-500 text-center">Error loading other customizations</p>';
                });
        });
        @endif
    </script>
</x-app-layout>