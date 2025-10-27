@extends('layouts.admin')

@section('title', $order->id ? 'Edit Order' : 'Create Order')
@section('page-title', $order->id ? 'Edit Order' : 'Create New Order')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Modern Form Styling */
    .order-form-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f3f4f6;
    }

    .card-header-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .form-group-modern {
        margin-bottom: 0;
    }

    .form-label-modern {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-label-modern .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .form-input-modern {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-input-modern:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Order Items Section */
    .order-items-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-item-card {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        position: relative;
    }

    .item-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .item-number {
        background: #3b82f6;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .btn-delete-item {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-delete-item:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    .btn-add-item-modern {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .btn-add-item-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
    }

    /* Action Buttons */
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px solid #f3f4f6;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }

    .btn-cancel-modern {
        background: #f3f4f6;
        color: #6b7280;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-cancel-modern:hover {
        background: #e5e7eb;
    }

    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        height: 44px;
        padding: 6px 12px;
        background-color: white;
        transition: all 0.3s ease;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: #3b82f6;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #1f2937;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 8px;
    }

    /* Select2 Dropdown */
    .select2-dropdown {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #d1d5db;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6;
        outline: none;
    }

    .select2-results__option {
        padding: 10px 12px;
        font-size: 14px;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: white;
    }

    .select2-results__option[aria-selected=true] {
        background-color: #e0e7ff;
        color: #1f2937;
    }

    /* Rush Order Checkbox */
    .rush-order-box {
        background: #fef3c7;
        border: 2px solid #fbbf24;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rush-order-box input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .rush-order-box label {
        font-size: 14px;
        font-weight: 500;
        color: #92400e;
        cursor: pointer;
        margin: 0;
    }
</style>
@endsection

@section('content')
<div class="order-form-container">
    @if($order->id)
        <form method="POST" action="{{ route('admin.order.update', $order->id) }}" id="orderForm">
            @method('PUT')
    @else
        <form method="POST" action="{{ route('admin.order.store') }}" id="orderForm">
    @endif
        @csrf

        {{-- Basic Information Card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="card-title">Basic Information</h3>
            </div>

            <div class="form-grid">
                <div class="form-group-modern">
                    <label for="user_id" class="form-label-modern">
                        Customer<span class="required">*</span>
                    </label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="form-input-modern select2-customer"
                        required>
                        <option value="">-- Select Customer --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                    {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Start typing to search by name or email</div>
                    @error('user_id')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="order_type" class="form-label-modern">
                        Order Type<span class="required">*</span>
                    </label>
                    <select
                        id="order_type"
                        name="order_type"
                        class="form-input-modern"
                        required>
                        <option value="dine_in" {{ old('order_type', $order->order_type) == 'dine_in' ? 'selected' : '' }}>🍽️ Dine In</option>
                        <option value="takeaway" {{ old('order_type', $order->order_type) == 'takeaway' ? 'selected' : '' }}>📦 Takeaway</option>
                    </select>
                    @error('order_type')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="table_id" class="form-label-modern">
                        Select Table
                    </label>
                    <select
                        id="table_id"
                        name="table_id"
                        class="form-input-modern select2-table">
                        <option value="">No Table (Takeaway)</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}"
                                    {{ old('table_id', $order->table_id) == $table->id ? 'selected' : '' }}>
                                {{ $table->table_number }} - {{ ucfirst($table->status) }} ({{ $table->capacity }} seats)
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Select table for dine-in orders</div>
                    @error('table_id')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="reservation_id" class="form-label-modern">
                        Related Reservation
                    </label>
                    <select
                        id="reservation_id"
                        name="reservation_id"
                        class="form-input-modern select2-reservation">
                        <option value="">No Reservation</option>
                        @foreach($reservations as $reservation)
                            <option value="{{ $reservation->id }}"
                                    {{ old('reservation_id', $order->reservation_id) == $reservation->id ? 'selected' : '' }}>
                                {{ $reservation->confirmation_code }} - {{ $reservation->guest_name }}
                                ({{ $reservation->booking_date->format('M d') }} {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Link this order to a table reservation (if customer pre-booked)</div>
                    @error('reservation_id')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="total_amount" class="form-label-modern">
                        Total Amount (RM)<span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="total_amount"
                        name="total_amount"
                        class="form-input-modern"
                        value="{{ old('total_amount', $order->total_amount) }}"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required>
                    @error('total_amount')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Order Status Card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="card-title">Status & Payment</h3>
            </div>

            <div class="form-grid">
                <div class="form-group-modern">
                    <label for="order_status" class="form-label-modern">
                        Order Status<span class="required">*</span>
                    </label>
                    <select
                        id="order_status"
                        name="order_status"
                        class="form-input-modern"
                        required>
                        <option value="pending" {{ old('order_status', $order->order_status ?? 'pending') == 'pending' ? 'selected' : '' }}>
                            🟡 Pending
                        </option>
                        <option value="preparing" {{ old('order_status', $order->order_status) == 'preparing' ? 'selected' : '' }}>
                            🍳 Preparing
                        </option>
                        <option value="ready" {{ old('order_status', $order->order_status) == 'ready' ? 'selected' : '' }}>
                            ✅ Ready
                        </option>
                        <option value="completed" {{ old('order_status', $order->order_status) == 'completed' ? 'selected' : '' }}>
                            🎉 Completed
                        </option>
                        <option value="cancelled" {{ old('order_status', $order->order_status) == 'cancelled' ? 'selected' : '' }}>
                            ❌ Cancelled
                        </option>
                    </select>
                    <div class="form-hint">New orders start as "Pending" and auto-send to kitchen</div>
                    @error('order_status')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="payment_status" class="form-label-modern">
                        Payment Status<span class="required">*</span>
                    </label>
                    <select
                        id="payment_status"
                        name="payment_status"
                        class="form-input-modern"
                        required>
                        <option value="unpaid" {{ old('payment_status', $order->payment_status ?? 'unpaid') == 'unpaid' ? 'selected' : '' }}>
                            💳 Unpaid
                        </option>
                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>
                            ✅ Paid
                        </option>
                        <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>
                            ⏳ Partial Payment
                        </option>
                    </select>
                    @error('payment_status')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-modern" style="grid-column: span 2;">
                    <label for="actual_completion_time" class="form-label-modern">
                        Actual Completion Time
                    </label>
                    <input
                        type="text"
                        id="actual_completion_time"
                        class="form-input-modern"
                        value="{{ $order->actual_completion_time ? $order->actual_completion_time->format('d M Y, h:i A') : 'Not completed yet' }}"
                        readonly
                        style="background-color: #f3f4f6; cursor: not-allowed; color: #6b7280;">
                    <div class="form-hint">This is automatically recorded when the order is marked as completed</div>
                </div>

                <div class="form-group-modern" style="grid-column: span 2;">
                    <div class="rush-order-box">
                        <input
                            type="checkbox"
                            id="is_rush_order"
                            name="is_rush_order"
                            value="1"
                            {{ old('is_rush_order', $order->is_rush_order) ? 'checked' : '' }}>
                        <label for="is_rush_order">
                            <i class="fas fa-bolt"></i>
                            <strong>Rush Order</strong> - This order will be prioritized in the kitchen
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Source Card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3 class="card-title">Order Source</h3>
            </div>

            <div class="form-grid">
                <div class="form-group-modern" style="grid-column: span 2;">
                    <label for="order_source" class="form-label-modern">
                        Where is this order from?<span class="required">*</span>
                    </label>
                    <select
                        id="order_source"
                        name="order_source"
                        class="form-input-modern"
                        required>
                        <option value="counter" {{ old('order_source', $order->order_source ?? 'counter') == 'counter' ? 'selected' : '' }}>Counter (Staff at counter)</option>
                        <option value="waiter" {{ old('order_source', $order->order_source) == 'waiter' ? 'selected' : '' }}>Waiter (Staff at table)</option>
                        <option value="qr_scan" {{ old('order_source', $order->order_source) == 'qr_scan' ? 'selected' : '' }}>QR Scan (Customer scanned QR)</option>
                        <option value="web" {{ old('order_source', $order->order_source) == 'web' ? 'selected' : '' }}>Web (Online website)</option>
                        <option value="mobile" {{ old('order_source', $order->order_source) == 'mobile' ? 'selected' : '' }}>Mobile (Mobile app)</option>
                    </select>
                    <div class="form-hint">This tracks where the order originated from for analytics</div>
                    @error('order_source')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Hidden Fields --}}
        <input type="hidden" name="order_time" value="{{ now() }}">

        {{-- Order Items Card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="card-title">Order Items</h3>
            </div>

            <div id="order-items-container" class="order-items-list">
                @if($order->id && $order->items && $order->items->count() > 0)
                    @foreach($order->items as $index => $item)
                        <div class="order-item-card" data-index="{{ $index }}">
                            <div class="item-card-header">
                                <div class="item-number">{{ $index + 1 }}</div>
                                <button type="button" onclick="removeOrderItem(this)" class="btn-delete-item">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Menu Item<span class="required">*</span></label>
                                    <select name="items[{{ $index }}][menu_item_id]" class="form-input-modern menu-item-select" required>
                                        <option value="">Select Item</option>
                                        @foreach($menuItemsByCategory as $category => $items)
                                            <optgroup label="📁 {{ $category }}">
                                                @foreach($items as $menuItem)
                                                    <option value="{{ $menuItem->id }}"
                                                            data-price="{{ $menuItem->price }}"
                                                            {{ $item->menu_item_id == $menuItem->id ? 'selected' : '' }}>
                                                        {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="hidden" name="items[{{ $index }}][price]" class="item-price" value="{{ $item->unit_price }}">

                                <div class="form-group-modern">
                                    <label class="form-label-modern">Quantity<span class="required">*</span></label>
                                    <input type="number"
                                           name="items[{{ $index }}][quantity]"
                                           class="form-input-modern item-quantity"
                                           value="{{ $item->quantity }}"
                                           min="1"
                                           required>
                                </div>

                                <div class="form-group-modern" style="grid-column: span 2;">
                                    <label class="form-label-modern">Special Notes</label>
                                    <input type="text"
                                           name="items[{{ $index }}][notes]"
                                           class="form-input-modern"
                                           value="{{ $item->special_note }}"
                                           placeholder="e.g. No onions, extra spicy">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="order-item-card" data-index="0">
                        <div class="item-card-header">
                            <div class="item-number">1</div>
                            <button type="button" onclick="removeOrderItem(this)" class="btn-delete-item">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>

                        <div class="form-grid">
                            <div class="form-group-modern">
                                <label class="form-label-modern">Menu Item<span class="required">*</span></label>
                                <select name="items[0][menu_item_id]" class="form-input-modern menu-item-select" required onchange="updateItemPrice(this)">
                                    <option value="">Select Item</option>
                                    @foreach($menuItemsByCategory as $category => $items)
                                        <optgroup label="📁 {{ $category }}">
                                            @foreach($items as $menuItem)
                                                <option value="{{ $menuItem->id }}" data-price="{{ $menuItem->price }}">
                                                    {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="items[0][price]" class="item-price" value="">

                            <div class="form-group-modern">
                                <label class="form-label-modern">Quantity<span class="required">*</span></label>
                                <input type="number"
                                       name="items[0][quantity]"
                                       class="form-input-modern item-quantity"
                                       value="1"
                                       min="1"
                                       required>
                            </div>

                            <div class="form-group-modern" style="grid-column: span 2;">
                                <label class="form-label-modern">Special Notes</label>
                                <input type="text"
                                       name="items[0][notes]"
                                       class="form-input-modern"
                                       placeholder="e.g. No onions, extra spicy">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div style="margin-top: 20px;">
                <button type="button" onclick="addOrderItem()" class="btn-add-item-modern">
                    <i class="fas fa-plus-circle"></i> Add Another Item
                </button>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ route('admin.order.index') }}" class="btn-cancel-modern">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary-modern">
                <i class="fas fa-check"></i>
                {{ $order->id ? 'Update Order' : 'Create Order' }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let itemIndex = {{ $order->items->count() ?? 1 }};

    // Initialize Select2 for searchable dropdowns
    $(document).ready(function() {
        // Customer dropdown with search
        $('.select2-customer').select2({
            placeholder: 'Type to search customer by name or email...',
            allowClear: true,
            width: '100%',
            theme: 'default'
        });

        // Table dropdown with search
        $('.select2-table').select2({
            placeholder: 'Type to search table number...',
            allowClear: true,
            width: '100%',
            theme: 'default'
        });

        // Reservation dropdown with search
        $('.select2-reservation').select2({
            placeholder: 'Type to search reservation by code or guest name...',
            allowClear: true,
            width: '100%',
            theme: 'default'
        });
    });

    // Add new order item
    function addOrderItem() {
        const container = document.getElementById('order-items-container');
        const newItem = `
            <div class="order-item-card" data-index="${itemIndex}">
                <div class="item-card-header">
                    <div class="item-number">${itemIndex + 1}</div>
                    <button type="button" onclick="removeOrderItem(this)" class="btn-delete-item">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>

                <div class="form-grid">
                    <div class="form-group-modern">
                        <label class="form-label-modern">Menu Item<span class="required">*</span></label>
                        <select name="items[${itemIndex}][menu_item_id]" class="form-input-modern menu-item-select" required onchange="updateItemPrice(this)">
                            <option value="">Select Item</option>
                            @foreach($menuItemsByCategory as $category => $items)
                                <optgroup label="📁 {{ $category }}">
                                    @foreach($items as $menuItem)
                                        <option value="{{ $menuItem->id }}" data-price="{{ $menuItem->price }}">
                                            {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="items[${itemIndex}][price]" class="item-price" value="">

                    <div class="form-group-modern">
                        <label class="form-label-modern">Quantity<span class="required">*</span></label>
                        <input type="number"
                               name="items[${itemIndex}][quantity]"
                               class="form-input-modern item-quantity"
                               value="1"
                               min="1"
                               required>
                    </div>

                    <div class="form-group-modern" style="grid-column: span 2;">
                        <label class="form-label-modern">Special Notes</label>
                        <input type="text"
                               name="items[${itemIndex}][notes]"
                               class="form-input-modern"
                               placeholder="e.g. No onions, extra spicy">
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', newItem);
        itemIndex++;

        // Renumber items
        renumberItems();
    }

    // Remove order item
    function removeOrderItem(button) {
        const itemCard = button.closest('.order-item-card');
        const container = document.getElementById('order-items-container');

        // Don't remove if it's the last item
        if (container.children.length <= 1) {
            alert('Order must have at least one item!');
            return;
        }

        itemCard.remove();
        renumberItems();

        // Recalculate total after removing item
        updateTotalAmount();
    }

    // Renumber items after add/remove
    function renumberItems() {
        const items = document.querySelectorAll('.order-item-card');
        items.forEach((item, index) => {
            item.querySelector('.item-number').textContent = index + 1;
        });
    }

    // Update item price when menu item is selected
    function updateItemPrice(select) {
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const priceInput = select.closest('.order-item-card').querySelector('.item-price');
        priceInput.value = price || '';

        // Update total amount after updating item price
        updateTotalAmount();
    }

    // Calculate and update total amount
    function updateTotalAmount() {
        let total = 0;

        // Loop through all order items
        document.querySelectorAll('.order-item-card').forEach(function(itemCard) {
            const priceInput = itemCard.querySelector('.item-price');
            const quantityInput = itemCard.querySelector('input[name*="[quantity]"]');

            const price = parseFloat(priceInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 0;

            total += price * quantity;
        });

        // Update the total amount field
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    // Auto-update price on menu item change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('menu-item-select')) {
            updateItemPrice(e.target);
        }

        // Update total when quantity changes
        if (e.target.name && e.target.name.includes('[quantity]')) {
            updateTotalAmount();
        }
    });

    // Calculate total on page load (for edit mode or if items already exist)
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize prices for existing items
        document.querySelectorAll('.menu-item-select').forEach(function(select) {
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const priceInput = select.closest('.order-item-card').querySelector('.item-price');
                if (priceInput && !priceInput.value) {
                    priceInput.value = price || '';
                }
            }
        });

        // Calculate total
        updateTotalAmount();
    });
</script>
@endsection
