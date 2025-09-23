@extends('layouts.admin')

@section('title', $order->id ? 'Edit Order' : 'Create Order')
@section('page-title', $order->id ? 'Edit Order' : 'Create Order')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($order->id)
                Edit Order - #{{ $order->id }}
            @else
                Create New Order
            @endif
        </h2>
        <a href="{{ route('admin.order.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    @if($order->id)
        <form method="POST" action="{{ route('admin.order.update', $order->id) }}" class="menu-item-form">
            @method('PUT')
    @else
        <form method="POST" action="{{ route('admin.order.store') }}" class="menu-item-form">
    @endif
        @csrf

        <!-- Basic Order Details -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Basic Order Details</h3> --}}
            
            <div class="form-row">
                <div class="form-group">
                    <label for="user_id" class="form-label">Customer *</label>
                    <select 
                        id="user_id" 
                        name="user_id" 
                        class="form-control @error('user_id') is-invalid @enderror"
                        required>
                        <option value="">Select Customer</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="total_amount" class="form-label">Total Amount (RM) *</label>
                    <input 
                        type="number" 
                        id="total_amount" 
                        name="total_amount" 
                        class="form-control @error('total_amount') is-invalid @enderror"
                        value="{{ old('total_amount', $order->total_amount) }}"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required>
                    @error('total_amount')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Order Type and Source -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Order Type & Source</h3> --}}
            
            <div class="form-row">
            <div class="form-group">
                <label for="order_type" class="form-label">Order Type *</label>
                <select 
                    id="order_type" 
                    name="order_type" 
                    class="form-control @error('order_type') is-invalid @enderror"
                    required>
                    <option value="">Select Order Type</option>
                    <option value="dine_in" {{ old('order_type', $order->order_type) == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                    <option value="takeaway" {{ old('order_type', $order->order_type) == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                    <option value="delivery" {{ old('order_type', $order->order_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="event" {{ old('order_type', $order->order_type) == 'event' ? 'selected' : '' }}>Event</option>
                </select>
                @error('order_type')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="order_source" class="form-label">Order Source *</label>
                <select 
                    id="order_source" 
                    name="order_source" 
                    class="form-control @error('order_source') is-invalid @enderror"
                    required>
                    <option value="counter" {{ old('order_source', $order->order_source) == 'counter' ? 'selected' : '' }}>Counter</option>
                    <option value="web" {{ old('order_source', $order->order_source) == 'web' ? 'selected' : '' }}>Web</option>
                    <option value="mobile" {{ old('order_source', $order->order_source) == 'mobile' ? 'selected' : '' }}>Mobile</option>
                    <option value="waiter" {{ old('order_source', $order->order_source) == 'waiter' ? 'selected' : '' }}>Waiter</option>
                    <option value="qr_scan" {{ old('order_source', $order->order_source) == 'qr_scan' ? 'selected' : '' }}>QR Scan</option>
                </select>
                @error('order_source')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            </div>
        </div>

        <!-- Table Information -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Table Information</h3> --}}
            
            <div class="form-row">
                <div class="form-group">
                    <label for="table_id" class="form-label">Table (Optional)</label>
                    <select 
                        id="table_id" 
                        name="table_id" 
                        class="form-control @error('table_id') is-invalid @enderror">
                        <option value="">Select Table (Optional)</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" 
                                    {{ old('table_id', $order->table_id) == $table->id ? 'selected' : '' }}>
                                {{ $table->table_number }} - {{ $table->status }} ({{ $table->capacity }} capacity)
                            </option>
                        @endforeach
                    </select>
                    @error('table_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="table_number" class="form-label">Table Number (Manual)</label>
                    <input 
                        type="text" 
                        id="table_number" 
                        name="table_number" 
                        class="form-control @error('table_number') is-invalid @enderror"
                        value="{{ old('table_number', $order->table_number) }}"
                        placeholder="e.g. A1, B2">
                    @error('table_number')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint" style="font-size: 12px; color: #6b7280; margin-top: 4px;">Use this for custom table numbers or delivery orders</div>
                </div>
            </div>

            <div class="form-group">
                <label for="reservation_id" class="form-label">Related Reservation (Optional)</label>
                <select 
                    id="reservation_id" 
                    name="reservation_id" 
                    class="form-control @error('reservation_id') is-invalid @enderror">
                    <option value="">Select Reservation (Optional)</option>
                    @foreach($reservations as $reservation)
                        <option value="{{ $reservation->id }}" 
                                {{ old('reservation_id', $order->reservation_id) == $reservation->id ? 'selected' : '' }}>
                            {{ $reservation->confirmation_code }} - {{ $reservation->guest_name }} 
                            ({{ $reservation->booking_date->format('M d') }} {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
                @error('reservation_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Status Information -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Status Information</h3> --}}
            
            <div class="form-row">
                <div class="form-group">
                    <label for="order_status" class="form-label">Order Status *</label>
                    <select 
                        id="order_status" 
                        name="order_status" 
                        class="form-control @error('order_status') is-invalid @enderror"
                        required>
                        <option value="pending" {{ old('order_status', $order->order_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ old('order_status', $order->order_status) == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ old('order_status', $order->order_status) == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ old('order_status', $order->order_status) == 'served' ? 'selected' : '' }}>Served</option>
                        <option value="completed" {{ old('order_status', $order->order_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('order_status', $order->order_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('order_status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_status" class="form-label">Payment Status *</label>
                    <select 
                        id="payment_status" 
                        name="payment_status" 
                        class="form-control @error('payment_status') is-invalid @enderror"
                        required>
                        <option value="unpaid" {{ old('payment_status', $order->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    @error('payment_status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Timing Information -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Timing Information</h3> --}}
            
            <div class="form-row">
                <div class="form-group">
                    <label for="estimated_completion_time" class="form-label">Estimated Completion Time</label>
                    <input 
                        type="datetime-local" 
                        id="estimated_completion_time" 
                        name="estimated_completion_time" 
                        class="form-control @error('estimated_completion_time') is-invalid @enderror"
                        value="{{ old('estimated_completion_time', $order->estimated_completion_time ? $order->estimated_completion_time->timezone(config('app.timezone'))->format('Y-m-d\TH:i') : '') }}"~>
                    @error('estimated_completion_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="actual_completion_time" class="form-label">Actual Completion Time</label>
                    <input 
                        type="datetime-local" 
                        id="actual_completion_time" 
                        name="actual_completion_time" 
                        class="form-control @error('actual_completion_time') is-invalid @enderror"
                        value="{{ old('actual_completion_time', $order->actual_completion_time ? $order->actual_completion_time->format('Y-m-d\TH:i') : '') }}">
                    @error('actual_completion_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="is_rush_order" 
                        name="is_rush_order" 
                        value="1"
                        {{ old('is_rush_order', $order->is_rush_order) ? 'checked' : '' }}>
                    <label for="is_rush_order" class="checkbox-label">
                        <i class="fas fa-bolt"></i>
                        Rush Order (Priority Processing)
                    </label>
                </div>
            </div>
        </div>

        <!-- Special Instructions -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Special Instructions</h3> --}}
            
            <div id="special-instructions-container">
                @if(old('special_instructions', $order->special_instructions))
                    @foreach(old('special_instructions', $order->special_instructions ?? []) as $index => $instruction)
                        <div class="instruction-row">
                            <input 
                                type="text" 
                                name="special_instructions[]" 
                                class="form-control" 
                                value="{{ $instruction }}" 
                                placeholder="Enter special instruction...">
                            <button type="button" onclick="removeInstruction(this)" class="btn-remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="instruction-row">
                        <input 
                            type="text" 
                            name="special_instructions[]" 
                            class="form-control" 
                            placeholder="Enter special instruction...">
                        <button type="button" onclick="removeInstruction(this)" class="btn-remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
            
            <button type="button" onclick="addInstruction()" class="btn-add-instruction">
                <i class="fas fa-plus"></i> Add Instruction
            </button>
            @error('special_instructions')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Order Items -->
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-subtitle">Order Items</h3>
                <button type="button" onclick="addOrderItem()" class="btn-add-item">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            
            <div id="order-items-container">
                @if($order->id && $order->items && $order->items->count() > 0)
                    @foreach($order->items as $index => $item)
                        <div class="order-item-row" data-index="{{ $index }}">
                            <div class="item-details">
                                <div class="form-group">
                                    <label class="form-label">Menu Item *</label>
                                    <select name="items[{{ $index }}][menu_item_id]" class="form-control menu-item-select" required>
                                        <option value="">Select Menu Item</option>
                                        @foreach($menuItems as $menuItem)
                                            <option value="{{ $menuItem->id }}" 
                                                    data-price="{{ $menuItem->price }}"
                                                    data-name="{{ $menuItem->name }}"
                                                    {{ $item->menu_item_id == $menuItem->id ? 'selected' : '' }}>
                                                {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Hidden fields for backend processing -->
                                <input type="hidden" name="items[{{ $index }}][name]" class="item-name" value="{{ $item->menuItem->name ?? '' }}">
                                <input type="hidden" name="items[{{ $index }}][price]" class="item-price" value="{{ $item->unit_price ?? '' }}">
                                
                                <div class="form-group">
                                    <label class="form-label">Quantity *</label>
                                    <input type="number" 
                                           name="items[{{ $index }}][quantity]" 
                                           class="form-control item-quantity" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           placeholder="1"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Notes</label>
                                    <textarea name="items[{{ $index }}][notes]" 
                                              class="form-control" 
                                              rows="2" 
                                              placeholder="Special notes for this item">{{ $item->notes }}</textarea>
                                </div>
                                
                                <div class="item-total">
                                    <span class="total-label">Total: RM</span>
                                    <span class="total-value">{{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            </div>
                            
                            <button type="button" onclick="removeOrderItem(this)" class="btn-remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="order-item-row" data-index="0">
                        <div class="item-details">
                            <div class="form-group">
                                <label class="form-label">Menu Item *</label>
                                <select name="items[0][menu_item_id]" class="form-control menu-item-select" required>
                                    <option value="">Select Menu Item</option>
                                    @foreach($menuItems as $menuItem)
                                        <option value="{{ $menuItem->id }}" 
                                                data-price="{{ $menuItem->price }}"
                                                data-name="{{ $menuItem->name }}">
                                            {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Hidden fields for backend processing -->
                            <input type="hidden" name="items[0][name]" class="item-name">
                            <input type="hidden" name="items[0][price]" class="item-price">
                            
                            <div class="form-group">
                                <label class="form-label">Quantity *</label>
                                <input type="number" 
                                       name="items[0][quantity]" 
                                       class="form-control item-quantity" 
                                       value="1" 
                                       min="1" 
                                       placeholder="1"
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Notes</label>
                                <textarea name="items[0][notes]" 
                                          class="form-control" 
                                          rows="2" 
                                          placeholder="Special notes for this item"></textarea>
                            </div>
                            
                            <div class="item-total">
                                <span class="total-label">Total: RM</span>
                                <span class="total-value">0.00</span>
                            </div>
                        </div>
                        
                        <button type="button" onclick="removeOrderItem(this)" class="btn-remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endif
            </div>
            
            <div class="order-summary">
                <div class="summary-row">
                    <span class="summary-label">Total Items:</span>
                    <span id="total-items">{{ $order->items ? $order->items->count() : 1 }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span id="order-subtotal">RM {{ number_format($order->total_amount ?? 0, 2) }}</span>
                </div>
                <div class="summary-row" style="border-top: 1px solid #e5e7eb; padding-top: 12px; margin-top: 12px;">
                    <span class="summary-label">Estimated Prep Time:</span>
                    <span id="estimated-prep-time" style="color: #10b981; font-weight: 600;">
                        @if($order->id && $order->items && $order->items->count() > 0)
                            Calculating...
                        @else
                            No items selected
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- ETA Management -->
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-subtitle">Estimated Time of Arrival (ETA)</h3>
                <button type="button" onclick="addETA()" class="btn-add-item">
                    <i class="fas fa-clock"></i> Add ETA
                </button>
            </div>
            
            <div id="eta-container">
                @if($order->id && $order->etas && $order->etas->count() > 0)
                    @foreach($order->etas as $index => $eta)
                        <div class="eta-row" data-index="{{ $index }}">
                            <div class="form-group">
                                <label class="form-label">ETA Type</label>
                                <select name="etas[{{ $index }}][eta_type]" class="form-control">
                                    <option value="preparation" {{ $eta->eta_type == 'preparation' ? 'selected' : '' }}>Preparation</option>
                                    <option value="delivery" {{ $eta->eta_type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                    <option value="pickup" {{ $eta->eta_type == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                    <option value="serving" {{ $eta->eta_type == 'serving' ? 'selected' : '' }}>Serving</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Estimated Time *</label>
                                <input type="datetime-local" 
                                       name="etas[{{ $index }}][estimated_time]" 
                                       class="form-control" 
                                       value="{{ $eta->estimated_time ? $eta->estimated_time->format('Y-m-d\TH:i') : '' }}"
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Notes</label>
                                <input type="text" 
                                       name="etas[{{ $index }}][notes]" 
                                       class="form-control" 
                                       value="{{ $eta->notes }}" 
                                       placeholder="ETA notes">
                            </div>
                            
                            <button type="button" onclick="removeETA(this)" class="btn-remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="eta-row" data-index="0">
                        <div class="form-group">
                            <label class="form-label">ETA Type</label>
                            <select name="etas[0][eta_type]" class="form-control">
                                <option value="preparation">Preparation</option>
                                <option value="delivery">Delivery</option>
                                <option value="pickup">Pickup</option>
                                <option value="serving">Serving</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Estimated Time *</label>
                            <input type="datetime-local" 
                                   name="etas[0][estimated_time]" 
                                   class="form-control" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <input type="text" 
                                   name="etas[0][notes]" 
                                   class="form-control" 
                                   placeholder="ETA notes">
                        </div>
                        
                        <button type="button" onclick="removeETA(this)" class="btn-remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Confirmation Code -->
        <div class="form-section">
            {{-- <h3 class="section-subtitle">Additional Information</h3> --}}
            
            <div class="form-group">
                <label for="confirmation_code" class="form-label">Confirmation Code (Optional)</label>
                <input 
                    type="text" 
                    id="confirmation_code" 
                    name="confirmation_code" 
                    class="form-control @error('confirmation_code') is-invalid @enderror"
                    value="{{ old('confirmation_code', $order->confirmation_code) }}"
                    placeholder="Auto-generated if empty">
                @error('confirmation_code')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <div class="form-hint" style="font-size: 12px; color: #6b7280; margin-top: 4px;">Leave empty to auto-generate a unique confirmation code</div>
            </div>
        </div>

        <!-- Current Order Information (for edit) -->
        @if($order->id)
            <div class="form-section">
                {{-- <h3 class="section-subtitle">Current Order Information</h3> --}}
                <div class="order-info-grid">
                    <div class="info-item">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#{{ $order->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Time:</span>
                        <span class="info-value">{{ $order->order_time->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($order->confirmation_code)
                    <div class="info-item">
                        <span class="info-label">Confirmation Code:</span>
                        <span class="info-value confirmation-code">{{ $order->confirmation_code }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                {{ $order->id ? 'Update Order' : 'Save Order' }}
            </button>
            <a href="{{ route('admin.order.index') }}" class="btn-cancel">
                Cancel
            </a>
            @if($order->id)
                <a href="{{ route('admin.order.duplicate', $order->id) }}" class="btn-duplicate">
                    <i class="fas fa-copy"></i>
                    Duplicate Order
                </a>
            @endif
        </div>
    </form>
</div>

<!-- All styling now handled by menu-managements.css for consistency -->
@endsection

@section('scripts')
<script>
// Special Instructions Management
function addInstruction() {
    const container = document.getElementById('special-instructions-container');
    const newInstruction = document.createElement('div');
    newInstruction.className = 'instruction-row';
    newInstruction.innerHTML = `
        <input type="text" name="special_instructions[]" 
               class="form-control" 
               placeholder="Enter special instruction...">
        <button type="button" onclick="removeInstruction(this)" class="btn-remove">
            <i class="fas fa-times"></i>
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

    // Format ikut local timezone (YYYY-MM-DDTHH:MM)
    const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);

    document.getElementById('estimated_completion_time').value = local;
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

// Order Items Management
let itemIndex = {{ $order->items ? $order->items->count() : 1 }};

function addOrderItem() {
    const container = document.getElementById('order-items-container');
    const newItem = document.createElement('div');
    newItem.className = 'order-item-row';
    newItem.setAttribute('data-index', itemIndex);
    
    newItem.innerHTML = `
        <div class="item-details">
            <div class="form-group">
                <label class="form-label">Menu Item *</label>
                <select name="items[${itemIndex}][menu_item_id]" class="form-control menu-item-select" required>
                    <option value="">Select Menu Item</option>
                    @foreach($menuItems as $menuItem)
                        <option value="{{ $menuItem->id }}" 
                                data-price="{{ $menuItem->price }}"
                                data-name="{{ $menuItem->name }}">
                            {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Hidden fields for backend processing -->
            <input type="hidden" name="items[${itemIndex}][name]" class="item-name">
            <input type="hidden" name="items[${itemIndex}][price]" class="item-price">
            
            <div class="form-group">
                <label class="form-label">Quantity *</label>
                <input type="number" 
                       name="items[${itemIndex}][quantity]" 
                       class="form-control item-quantity" 
                       value="1" 
                       min="1" 
                       placeholder="1"
                       required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="items[${itemIndex}][notes]" 
                          class="form-control" 
                          rows="2" 
                          placeholder="Special notes for this item"></textarea>
            </div>
            
            <div class="item-total">
                <span class="total-label">Total: RM</span>
                <span class="total-value">0.00</span>
            </div>
        </div>
        
        <button type="button" onclick="removeOrderItem(this)" class="btn-remove-item">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(newItem);
    itemIndex++;
    
    // Add event listeners for the new item
    setupItemEventListenersEnhanced(newItem);
    updateOrderTotals();
    calculateRealTimeETA();
}

function removeOrderItem(button) {
    const container = document.getElementById('order-items-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
        updateOrderTotals();
        calculateRealTimeETA();
    } else {
        alert('At least one item is required');
    }
}


function updateItemTotal(itemRow) {
    const price = parseFloat(itemRow.querySelector('.item-price').value) || 0;
    const quantity = parseInt(itemRow.querySelector('.item-quantity').value) || 0;
    const total = price * quantity;
    
    itemRow.querySelector('.total-value').textContent = total.toFixed(2);
    updateOrderTotals();
}

function updateOrderTotals() {
    const itemRows = document.querySelectorAll('.order-item-row');
    let totalItems = 0;
    let subtotal = 0;
    
    itemRows.forEach(row => {
        const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        totalItems += quantity;
        subtotal += (price * quantity);
    });
    
    document.getElementById('total-items').textContent = totalItems;
    document.getElementById('order-subtotal').textContent = 'RM ' + subtotal.toFixed(2);
    document.getElementById('total_amount').value = subtotal.toFixed(2);
}

// ETA Management
let etaIndex = {{ $order->etas ? $order->etas->count() : 1 }};

function addETA() {
    const container = document.getElementById('eta-container');
    const newETA = document.createElement('div');
    newETA.className = 'eta-row';
    newETA.setAttribute('data-index', etaIndex);
    
    newETA.innerHTML = `
        <div class="form-group">
            <label class="form-label">ETA Type</label>
            <select name="etas[${etaIndex}][eta_type]" class="form-control">
                <option value="preparation">Preparation</option>
                <option value="delivery">Delivery</option>
                <option value="pickup">Pickup</option>
                <option value="serving">Serving</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Estimated Time *</label>
            <input type="datetime-local" 
                   name="etas[${etaIndex}][estimated_time]" 
                   class="form-control" 
                   required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Notes</label>
            <input type="text" 
                   name="etas[${etaIndex}][notes]" 
                   class="form-control" 
                   placeholder="ETA notes">
        </div>
        
        <button type="button" onclick="removeETA(this)" class="btn-remove-item">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(newETA);
    etaIndex++;
}

function removeETA(button) {
    const container = document.getElementById('eta-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        // Clear the fields instead of removing if it's the last one
        const inputs = button.parentElement.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.type !== 'datetime-local') {
                input.value = '';
            }
        });
    }
}

// Real-time ETA Calculation
function calculateRealTimeETA() {
    const items = [];
    
    document.querySelectorAll('.order-item-row').forEach(row => {
        const menuItemId = row.querySelector('.menu-item-select').value;
        const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
        
        if (menuItemId && quantity > 0) {
            items.push({
                menu_item_id: menuItemId,
                quantity: quantity
            });
        }
    });
    
    if (items.length === 0) {
        document.getElementById('estimated-prep-time').textContent = 'No items selected';
        return;
    }
    
    fetch('{{ route("admin.order.calculateETA") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ items: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const prepTimeDisplay = document.getElementById('estimated-prep-time');
            if (prepTimeDisplay) {
                prepTimeDisplay.textContent = `${data.total_prep_time} minutes (${data.estimated_time_formatted})`;
                prepTimeDisplay.style.color = '#10b981';
                prepTimeDisplay.style.fontWeight = '600';
            }
            
            // Auto-populate first ETA field if exists
            const firstETATime = document.querySelector('input[name="etas[0][estimated_time]"]');
            if (firstETATime && !firstETATime.value) {
                // Convert to local datetime format for input
                const etaDate = new Date(data.estimated_time);
                const localDateTime = new Date(etaDate.getTime() - etaDate.getTimezoneOffset() * 60000)
                    .toISOString()
                    .slice(0, 16);
                firstETATime.value = localDateTime;
            }
        }
    })
    .catch(error => {
        console.error('Error calculating ETA:', error);
        const prepTimeDisplay = document.getElementById('estimated-prep-time');
        if (prepTimeDisplay) {
            prepTimeDisplay.textContent = 'Error calculating ETA';
            prepTimeDisplay.style.color = '#ef4444';
        }
    });
}

// Update setupItemEventListeners to include ETA calculation
function setupItemEventListenersEnhanced(itemRow) {
    const menuSelect = itemRow.querySelector('.menu-item-select');
    const nameInput = itemRow.querySelector('.item-name');
    const priceInput = itemRow.querySelector('.item-price');
    const quantityInput = itemRow.querySelector('.item-quantity');
    
    // Auto-populate when menu item is selected
    menuSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Use data-name attribute if available, otherwise parse from text
            const itemName = selectedOption.dataset.name || selectedOption.textContent.split(' (RM')[0];
            nameInput.value = itemName;
            priceInput.value = selectedOption.dataset.price;
            updateItemTotal(itemRow);
            calculateRealTimeETA(); // Calculate ETA when item changes
        } else {
            // Clear fields if no item selected
            nameInput.value = '';
            priceInput.value = '';
            updateItemTotal(itemRow);
            calculateRealTimeETA();
        }
    });
    
    // Update totals and ETA when quantity changes
    quantityInput.addEventListener('input', () => {
        updateItemTotal(itemRow);
        calculateRealTimeETA();
    });
}

// Initialize event listeners for existing items
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-item-row').forEach(setupItemEventListenersEnhanced);
    updateOrderTotals();
    calculateRealTimeETA(); // Calculate initial ETA
});
</script>
@endsection