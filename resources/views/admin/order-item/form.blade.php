@extends('layouts.admin')

@section('title', $orderItem->id ? 'Edit Order Item' : 'Create Order Item')
@section('page-title', $orderItem->id ? 'Edit Order Item' : 'Create Order Item')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($orderItem->id)
                Edit Order Item - #{{ $orderItem->id }}
            @else
                Create New Order Item
            @endif
        </h2>
        <a href="{{ route('admin.order-item.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Order Items
        </a>
    </div>

    @if($orderItem->id)
        <form method="POST" action="{{ route('admin.order-item.update', $orderItem->id) }}" class="menu-item-form">
            @method('PUT')
    @else
        <form method="POST" action="{{ route('admin.order-item.store') }}" class="menu-item-form">
    @endif
        @csrf

        <!-- Order Item Details -->
        <div class="form-section">
            <div class="section-subtitle">Order Item Information</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="order_id" class="form-label">Order</label>
                    <select 
                        id="order_id" 
                        name="order_id" 
                        class="form-control @error('order_id') is-invalid @enderror"
                        required>
                        <option value="">Select Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" 
                                    {{ old('order_id', $orderItem->order_id) == $order->id ? 'selected' : '' }}>
                                #{{ $order->id }} - {{ $order->confirmation_code ?? 'No Code' }} - {{ $order->user->name ?? 'Guest' }} (RM {{ number_format($order->total_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="menu_item_id" class="form-label">Menu Item</label>
                    <select 
                        id="menu_item_id" 
                        name="menu_item_id" 
                        class="form-control @error('menu_item_id') is-invalid @enderror"
                        required>
                        <option value="">Select Menu Item</option>
                        @foreach($menuItems as $menuItem)
                            <option value="{{ $menuItem->id }}" 
                                    data-price="{{ $menuItem->price }}"
                                    {{ old('menu_item_id', $orderItem->menu_item_id) == $menuItem->id ? 'selected' : '' }}>
                                {{ $menuItem->name }} - RM {{ number_format($menuItem->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_item_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input 
                        type="number" 
                        id="quantity" 
                        name="quantity" 
                        class="form-control @error('quantity') is-invalid @enderror"
                        value="{{ old('quantity', $orderItem->quantity) }}"
                        min="1"
                        max="999"
                        placeholder="1"
                        required>
                    @error('quantity')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="unit_price" class="form-label">Unit Price (RM)</label>
                    <input 
                        type="number" 
                        id="unit_price" 
                        name="unit_price" 
                        class="form-control @error('unit_price') is-invalid @enderror"
                        value="{{ old('unit_price', $orderItem->unit_price) }}"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required>
                    @error('unit_price')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_price" class="form-label">Total Price (RM)</label>
                    <input 
                        type="number" 
                        id="total_price" 
                        name="total_price" 
                        class="form-control @error('total_price') is-invalid @enderror"
                        value="{{ old('total_price', $orderItem->total_price) }}"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        readonly>
                    @error('total_price')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint" style="font-size: 12px; color: #6b7280; margin-top: 4px;">Auto-calculated based on quantity × unit price</div>
                </div>

                <div class="form-group">
                    <label for="item_status" class="form-label">Item Status</label>
                    <select 
                        id="item_status" 
                        name="item_status" 
                        class="form-control @error('item_status') is-invalid @enderror"
                        required>
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('item_status', $orderItem->item_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ old('item_status', $orderItem->item_status) == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ old('item_status', $orderItem->item_status) == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ old('item_status', $orderItem->item_status) == 'served' ? 'selected' : '' }}>Served</option>
                        <option value="cancelled" {{ old('item_status', $orderItem->item_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('item_status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="special_note" class="form-label">Special Note (Optional)</label>
                <textarea 
                    id="special_note" 
                    name="special_note" 
                    rows="3"
                    class="form-control @error('special_note') is-invalid @enderror"
                    placeholder="Any special instructions or notes for this item...">{{ old('special_note', $orderItem->special_note) }}</textarea>
                @error('special_note')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Current Order Item Information (for edit) -->
        @if($orderItem->id)
            <div class="form-section">
                <div class="section-subtitle">Order Item Details</div>
                <div class="order-info-grid">
                    <div class="info-item">
                        <span class="info-label">Item ID:</span>
                        <span class="info-value">#{{ $orderItem->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#{{ $orderItem->order_id }}</span>
                    </div>
                    @if($orderItem->order && $orderItem->order->confirmation_code)
                    <div class="info-item">
                        <span class="info-label">Order Code:</span>
                        <span class="info-value confirmation-code">{{ $orderItem->order->confirmation_code }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $orderItem->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($orderItem->updated_at != $orderItem->created_at)
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value">{{ $orderItem->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                {{ $orderItem->id ? 'Update Order Item' : 'Save Order Item' }}
            </button>
            <a href="{{ route('admin.order-item.index') }}" class="btn-cancel">
                Cancel
            </a>
            @if($orderItem->id)
                <a href="{{ route('admin.order-item.duplicate', $orderItem->id) }}" class="btn-duplicate">
                    <i class="fas fa-copy"></i>
                    Duplicate Item
                </a>
            @endif
        </div>
    </form>
</div>

<!-- All styling now handled by menu-managements.css for consistency -->
@endsection

@section('scripts')
<script>
// Auto-populate unit price when menu item is selected
document.getElementById('menu_item_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const price = selectedOption.getAttribute('data-price');
        document.getElementById('unit_price').value = price;
        calculateTotalPrice();
    } else {
        document.getElementById('unit_price').value = '';
        document.getElementById('total_price').value = '';
    }
});

// Calculate total price when quantity or unit price changes
function calculateTotalPrice() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const totalPrice = quantity * unitPrice;
    
    document.getElementById('total_price').value = totalPrice.toFixed(2);
}

// Add event listeners for price calculation
document.getElementById('quantity').addEventListener('input', calculateTotalPrice);
document.getElementById('unit_price').addEventListener('input', calculateTotalPrice);

// Initialize total price calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotalPrice();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const orderId = document.getElementById('order_id').value;
    const menuItemId = document.getElementById('menu_item_id').value;
    const quantity = document.getElementById('quantity').value;
    const unitPrice = document.getElementById('unit_price').value;
    const itemStatus = document.getElementById('item_status').value;
    
    if (!orderId || !menuItemId || !quantity || !unitPrice || !itemStatus) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return false;
    }
    
    if (quantity <= 0) {
        e.preventDefault();
        alert('Quantity must be greater than 0');
        return false;
    }
    
    if (unitPrice < 0) {
        e.preventDefault();
        alert('Unit price cannot be negative');
        return false;
    }
});
</script>
@endsection