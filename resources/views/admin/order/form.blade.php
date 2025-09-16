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
                            ({{ $reservation->reservation_date->format('M d') }} {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }})
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
                        <option value="confirmed" {{ old('order_status', $order->order_status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
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
                        value="{{ old('estimated_completion_time', $order->estimated_completion_time ? $order->estimated_completion_time->format('Y-m-d\TH:i') : '') }}">
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
@endsection