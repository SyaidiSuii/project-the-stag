@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Order #{{ $order->id }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.order.edit', $order->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Order
            </a>
            <a href="{{ route('admin.order.duplicate', $order->id) }}" class="btn-save" style="background: #3b82f6;">
                <i class="fas fa-copy"></i> Duplicate
            </a>
            <a href="{{ route('admin.order.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <!-- Quick Status Update -->
    @if(!in_array($order->order_status, ['completed', 'cancelled']))
    <div class="admin-section" style="margin-bottom: 20px;">
        <div class="section-header">
            <h3 class="section-title">Quick Actions</h3>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            @if($order->order_status == 'pending')
                <button onclick="updateOrderStatus('confirmed')" class="btn-save" style="background: #10b981;">
                    <i class="fas fa-check"></i> Confirm Order
                </button>
            @elseif($order->order_status == 'confirmed')
                <button onclick="updateOrderStatus('preparing')" class="btn-save" style="background: #3b82f6;">
                    <i class="fas fa-utensils"></i> Start Preparing
                </button>
            @elseif($order->order_status == 'preparing')
                <button onclick="updateOrderStatus('ready')" class="btn-save" style="background: #8b5cf6;">
                    <i class="fas fa-bell"></i> Mark Ready
                </button>
            @elseif($order->order_status == 'ready')
                <button onclick="updateOrderStatus('served')" class="btn-save" style="background: #6366f1;">
                    <i class="fas fa-hand-holding"></i> Mark Served
                </button>
            @elseif($order->order_status == 'served')
                <button onclick="updateOrderStatus('completed')" class="btn-save" style="background: #10b981;">
                    <i class="fas fa-check-circle"></i> Complete Order
                </button>
            @endif
            
            @if(!in_array($order->order_status, ['completed', 'cancelled']))
                <a href="{{ route('admin.order.cancel', $order->id) }}" 
                   onclick="return confirm('Are you sure you want to cancel this order?')"
                   class="btn-save" style="background: #ef4444;">
                    <i class="fas fa-times"></i> Cancel Order
                </a>
            @endif

            @if($order->payment_status != 'paid')
            <div style="display: flex; align-items: center; gap: 8px; margin-left: 20px; padding-left: 20px; border-left: 1px solid #d1d5db;">
                <label class="form-label" style="margin: 0; font-size: 14px;">Payment:</label>
                <select onchange="updatePaymentStatus(this.value)" class="form-control" style="width: auto; padding: 6px 12px;">
                    <option value="unpaid" @if($order->payment_status == 'unpaid') selected @endif>Unpaid</option>
                    <option value="partial" @if($order->payment_status == 'partial') selected @endif>Partial</option>
                    <option value="paid" @if($order->payment_status == 'paid') selected @endif>Paid</option>
                </select>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Order Summary -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Order Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER ID</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">#{{ $order->id }}</p>
                    </div>
                    
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CONFIRMATION CODE</span>
                        @if($order->confirmation_code)
                            <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $order->confirmation_code }}</p>
                        @else
                            <p style="color: #6b7280; margin: 4px 0 0 0;">Not assigned</p>
                        @endif
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TOTAL AMOUNT</span>
                        <p style="font-size: 24px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($order->total_amount, 2) }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TIME</span>
                        <p style="margin: 4px 0 0 0;">{{ $order->order_time->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Customer Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                @if($order->user)
                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NAME</span>
                        <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $order->user->name }}</p>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">EMAIL</span>
                        <p style="margin: 4px 0 0 0;">
                            <a href="mailto:{{ $order->user->email }}" style="color: #3b82f6; text-decoration: none;">
                                {{ $order->user->email }}
                            </a>
                        </p>
                    </div>

                    @if($order->user->phone)
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PHONE</span>
                        <p style="margin: 4px 0 0 0;">
                            <a href="tel:{{ $order->user->phone }}" style="color: #3b82f6; text-decoration: none;">
                                {{ $order->user->phone }}
                            </a>
                        </p>
                    </div>
                    @endif
                @else
                    <div style="color: #6b7280; text-align: center; padding: 20px;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="font-weight: 600;">Customer information not available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Information -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Order Status</label>
            <div class="checkbox-group" style="background: 
                @if($order->order_status == 'confirmed') #d1fae5
                @elseif($order->order_status == 'pending') #fef3c7
                @elseif($order->order_status == 'preparing') #dbeafe
                @elseif($order->order_status == 'ready') #e9d5ff
                @elseif($order->order_status == 'served') #e0e7ff
                @elseif($order->order_status == 'completed') #d1fae5
                @elseif($order->order_status == 'cancelled') #fee2e2
                @else #f3f4f6 @endif;">
                <i class="fas fa-
                    @if($order->order_status == 'confirmed') check-circle
                    @elseif($order->order_status == 'pending') clock
                    @elseif($order->order_status == 'preparing') utensils
                    @elseif($order->order_status == 'ready') bell
                    @elseif($order->order_status == 'served') hand-holding
                    @elseif($order->order_status == 'completed') check-double
                    @elseif($order->order_status == 'cancelled') times-circle
                    @else info-circle @endif" 
                   style="color: 
                    @if($order->order_status == 'confirmed') #10b981
                    @elseif($order->order_status == 'pending') #d97706
                    @elseif($order->order_status == 'preparing') #3b82f6
                    @elseif($order->order_status == 'ready') #8b5cf6
                    @elseif($order->order_status == 'served') #6366f1
                    @elseif($order->order_status == 'completed') #10b981
                    @elseif($order->order_status == 'cancelled') #ef4444
                    @else #6b7280 @endif;"></i>
                <span style="color: 
                    @if($order->order_status == 'confirmed') #065f46
                    @elseif($order->order_status == 'pending') #92400e
                    @elseif($order->order_status == 'preparing') #1e40af
                    @elseif($order->order_status == 'ready') #6b21a8
                    @elseif($order->order_status == 'served') #3730a3
                    @elseif($order->order_status == 'completed') #065f46
                    @elseif($order->order_status == 'cancelled') #991b1b
                    @else #374151 @endif; font-weight: 600; text-transform: capitalize;">
                    {{ str_replace('_', ' ', $order->order_status) }}
                    @if($order->is_rush_order)
                        <span style="margin-left: 8px; background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                            RUSH
                        </span>
                    @endif
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Payment Status</label>
            <div class="checkbox-group" style="background: 
                @if($order->payment_status == 'paid') #d1fae5
                @elseif($order->payment_status == 'partial') #fef3c7
                @elseif($order->payment_status == 'unpaid') #fee2e2
                @elseif($order->payment_status == 'refunded') #f3f4f6
                @else #f3f4f6 @endif;">
                <i class="fas fa-
                    @if($order->payment_status == 'paid') check-circle
                    @elseif($order->payment_status == 'partial') exclamation-circle
                    @elseif($order->payment_status == 'unpaid') times-circle
                    @elseif($order->payment_status == 'refunded') undo
                    @else question-circle @endif" 
                   style="color: 
                    @if($order->payment_status == 'paid') #10b981
                    @elseif($order->payment_status == 'partial') #d97706
                    @elseif($order->payment_status == 'unpaid') #ef4444
                    @elseif($order->payment_status == 'refunded') #6b7280
                    @else #6b7280 @endif;"></i>
                <span style="color: 
                    @if($order->payment_status == 'paid') #065f46
                    @elseif($order->payment_status == 'partial') #92400e
                    @elseif($order->payment_status == 'unpaid') #991b1b
                    @elseif($order->payment_status == 'refunded') #374151
                    @else #374151 @endif; font-weight: 600; text-transform: capitalize;">
                    {{ $order->payment_status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Order Type and Source -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Order Type</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <p style="font-size: 18px; font-weight: 600; text-transform: capitalize; margin: 0;">
                    <i class="fas fa-
                        @if($order->order_type == 'dine_in') utensils
                        @elseif($order->order_type == 'takeaway') shopping-bag
                        @elseif($order->order_type == 'delivery') truck
                        @else clipboard @endif" style="margin-right: 8px; color: #6b7280;"></i>
                    {{ str_replace('_', ' ', $order->order_type) }}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Order Source</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <p style="font-size: 18px; font-weight: 600; text-transform: capitalize; margin: 0;">
                    <i class="fas fa-
                        @if($order->order_source == 'website') globe
                        @elseif($order->order_source == 'mobile_app') mobile-alt
                        @elseif($order->order_source == 'in_person') store
                        @elseif($order->order_source == 'phone') phone
                        @else question @endif" style="margin-right: 8px; color: #6b7280;"></i>
                    {{ str_replace('_', ' ', $order->order_source) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Table Information -->
    @if($order->table || $order->table_number)
    <div class="form-group">
        <label class="form-label">Table Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            @if($order->table)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0;">Table {{ $order->table->table_number }}</p>
                    <p style="color: #6b7280; margin: 0;">{{ ucfirst($order->table->table_type) }} ({{ $order->table->capacity }} capacity)</p>
                </div>
            @elseif($order->table_number)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $order->table_number }}</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Reservation Information -->
    @if($order->reservation)
    <div class="form-group">
        <label class="form-label">Related Reservation</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div>
                <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CONFIRMATION CODE</span>
                <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0;">{{ $order->reservation->confirmation_code }}</p>
            </div>
            <div style="margin-top: 8px;">
                <span style="font-size: 12px; color: #6b7280; font-weight: 600;">GUEST NAME</span>
                <p style="margin: 4px 0 0 0;">{{ $order->reservation->guest_name }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Special Instructions -->
    @if($order->special_instructions && count($order->special_instructions) > 0)
    <div class="form-group">
        <label class="form-label">Special Instructions</label>
        <div style="border: 1px solid #f59e0b; border-radius: 12px; padding: 16px; background: #fffbeb;">
            @foreach($order->special_instructions as $instruction)
                @if($instruction)
                    <div style="background: #fef3c7; padding: 12px; border-radius: 8px; margin-bottom: 8px; border-left: 4px solid #f59e0b;">
                        <i class="fas fa-sticky-note" style="color: #d97706; margin-right: 8px;"></i>
                        <span style="color: #92400e; font-weight: 500;">{{ $instruction }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Timing Information -->
    <div class="form-group">
        <label class="form-label">Timing Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER PLACED</span>
                    <p style="margin: 4px 0 0 0;">{{ $order->order_time->format('M d, Y h:i A') }}</p>
                </div>

                @if($order->estimated_completion_time)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ESTIMATED COMPLETION</span>
                    <p style="margin: 4px 0 0 0;">{{ $order->estimated_completion_time->format('M d, Y h:i A') }}</p>
                    @php
                        $now = now();
                        $isOverdue = $order->estimated_completion_time < $now && !$order->actual_completion_time;
                    @endphp
                    @if($isOverdue)
                        <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; margin-top: 4px; display: inline-block;">
                            OVERDUE
                        </span>
                    @endif
                </div>
                @endif

                @if($order->actual_completion_time)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ACTUAL COMPLETION</span>
                    <p style="margin: 4px 0 0 0;">{{ $order->actual_completion_time->format('M d, Y h:i A') }}</p>
                </div>
                @endif

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>

                @if($order->updated_at != $order->created_at)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">LAST UPDATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="form-group">
        <label class="form-label">Order Items</label>
        @if($order->items && $order->items->count() > 0)
            <div style="border: 1px solid #d1d5db; border-radius: 12px; background: #f9fafb; overflow: hidden;">
                @foreach($order->items as $item)
                <div style="padding: 16px; @if(!$loop->last) border-bottom: 1px solid #e5e7eb; @endif">
                    <div style="display: flex; justify-content: between; align-items: start;">
                        <div style="flex: 1;">
                            <p style="font-size: 16px; font-weight: 600; margin: 0 0 4px 0;">{{ $item->name ?? 'Item #' . $item->id }}</p>
                            <p style="font-size: 14px; color: #6b7280; margin: 0;">
                                <span style="background: #e5e7eb; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Qty: {{ $item->quantity ?? 1 }}
                                </span>
                            </p>
                            @if($item->notes)
                                <div style="margin-top: 8px; background: #fef3c7; padding: 8px; border-radius: 6px; border-left: 3px solid #f59e0b;">
                                    <small style="color: #92400e; font-weight: 500;">
                                        <i class="fas fa-sticky-note" style="margin-right: 4px;"></i>
                                        {{ $item->notes }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div style="text-align: right; margin-left: 16px;">
                            <p style="font-size: 16px; font-weight: 600; margin: 0;">RM {{ number_format($item->price ?? 0, 2) }}</p>
                            <p style="font-size: 14px; color: #6b7280; margin: 4px 0 0 0;">
                                Total: RM {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div style="padding: 16px; background: #ecfdf5; border-top: 1px solid #10b981;">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 700;">Total Amount:</span>
                        <span style="font-size: 24px; font-weight: 700; color: #10b981;">RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        @else
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 40px; background: #f9fafb; text-align: center;">
                <i class="fas fa-shopping-cart" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p style="color: #6b7280; font-weight: 600; margin: 0;">No items found for this order</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.order.edit', $order->id) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Order
        </a>
        <a href="{{ route('admin.order.duplicate', $order->id) }}" class="btn-save" style="background: #3b82f6;">
            <i class="fas fa-copy"></i>
            Duplicate Order
        </a>
        <a href="{{ route('admin.order.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateOrderStatus(status) {
    if (!confirm(`Are you sure you want to change the order status to '${status}'?`)) {
        return;
    }

    fetch(`{{ route('admin.order.updateStatus', $order->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating order status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order status');
    });
}

function updatePaymentStatus(status) {
    fetch(`{{ route('admin.order.updatePaymentStatus', $order->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating payment status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment status');
    });
}
</script>
@endsection