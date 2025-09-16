@extends('layouts.admin')

@section('title', 'Order Item Details')
@section('page-title', 'Order Item Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Order Item#{{ $orderItem->id }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.order-item.edit', $orderItem->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Order Item
            </a>
            <a href="{{ route('admin.order-item.duplicate', $orderItem->id) }}" class="btn-save" style="background: #3b82f6;">
                <i class="fas fa-copy"></i> Duplicate
            </a>
            <a href="{{ route('admin.order-item.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Order Items
            </a>
        </div>
    </div>

    <!-- Quick Status Update -->
    @if(!in_array($orderItem->item_status, ['served', 'cancelled']))
    <div class="admin-section" style="margin-bottom: 20px;">
        <div class="section-header">
            <h3 class="section-title">Quick Status Update</h3>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            @if($orderItem->item_status == 'pending')
                <button onclick="updateItemStatus('preparing')" class="btn-save" style="background: #3b82f6;">
                    <i class="fas fa-utensils"></i> Start Preparing
                </button>
            @elseif($orderItem->item_status == 'preparing')
                <button onclick="updateItemStatus('ready')" class="btn-save" style="background: #8b5cf6;">
                    <i class="fas fa-bell"></i> Mark Ready
                </button>
            @elseif($orderItem->item_status == 'ready')
                <button onclick="updateItemStatus('served')" class="btn-save" style="background: #10b981;">
                    <i class="fas fa-hand-holding"></i> Mark Served
                </button>
            @endif
            
            @if(!in_array($orderItem->item_status, ['served', 'cancelled']))
                <button onclick="updateItemStatus('cancelled')" class="btn-save" style="background: #ef4444;">
                    <i class="fas fa-times"></i> Cancel Item
                </button>
            @endif
        </div>
    </div>
    @endif

    <!-- Main Information Grid -->
    <div class="form-row">
        <!-- Item Details Column -->
        <div class="form-group">
            <label class="form-label">Item Details</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ITEM ID</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">#{{ $orderItem->id }}</p>
                    </div>
                    
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">STATUS</span>
                        <span class="status status-item status-{{ str_replace('_', '-', $orderItem->item_status) }}" style="margin: 4px 0 0 0; display: inline-block;">
                            {{ str_replace('_', ' ', ucfirst($orderItem->item_status)) }}
                        </span>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">MENU ITEM</span>
                        <p style="font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $orderItem->menuItem->name ?? 'Unknown Item' }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">QUANTITY</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">{{ $orderItem->quantity }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">UNIT PRICE</span>
                        <p style="font-size: 16px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($orderItem->unit_price, 2) }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TOTAL PRICE</span>
                        <p style="font-size: 20px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($orderItem->total_price, 2) }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                        <p style="margin: 4px 0 0 0;">{{ $orderItem->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">LAST UPDATED</span>
                        <p style="margin: 4px 0 0 0;">{{ $orderItem->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                
                @if($orderItem->special_note)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #d1d5db;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">SPECIAL NOTE</span>
                    <p style="margin: 4px 0 0 0; font-style: italic;">{{ $orderItem->special_note }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Order Information Column -->
        <div class="form-group">
            <label class="form-label">Related Order Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER ID</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">
                            <a href="{{ route('admin.order.show', $orderItem->order_id) }}" style="color: #3b82f6; text-decoration: none;">
                                #{{ $orderItem->order_id }}
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CONFIRMATION CODE</span>
                        @if($orderItem->order && $orderItem->order->confirmation_code)
                            <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $orderItem->order->confirmation_code }}</p>
                        @else
                            <p style="color: #6b7280; margin: 4px 0 0 0;">Not assigned</p>
                        @endif
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CUSTOMER</span>
                        <p style="font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $orderItem->order->user->name ?? 'Guest' }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TYPE</span>
                        <p style="margin: 4px 0 0 0; text-transform: capitalize;">{{ str_replace('_', ' ', $orderItem->order->order_type ?? 'Unknown') }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER STATUS</span>
                        @if($orderItem->order)
                            <span class="status status-order status-{{ str_replace('_', '-', $orderItem->order->order_status) }}" style="margin: 4px 0 0 0; display: inline-block;">
                                {{ str_replace('_', ' ', ucfirst($orderItem->order->order_status)) }}
                            </span>
                        @else
                            <p style="color: #6b7280; margin: 4px 0 0 0;">Unknown</p>
                        @endif
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PAYMENT STATUS</span>
                        @if($orderItem->order)
                            <span class="status status-payment status-{{ str_replace('_', '-', $orderItem->order->payment_status) }}" style="margin: 4px 0 0 0; display: inline-block;">
                                {{ ucfirst($orderItem->order->payment_status) }}
                            </span>
                        @else
                            <p style="color: #6b7280; margin: 4px 0 0 0;">Unknown</p>
                        @endif
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TOTAL</span>
                        <p style="font-size: 18px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($orderItem->order->total_amount ?? 0, 2) }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TIME</span>
                        <p style="margin: 4px 0 0 0;">{{ $orderItem->order->order_time->format('M d, Y h:i A') ?? 'Unknown' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Item & Other Items Grid -->
    <div class="form-row">
        <!-- Menu Item Information Column -->
        <div class="form-group">
            <label class="form-label">Menu Item Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NAME</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">
                            <a href="{{ route('admin.menu-items.show', $orderItem->menu_item_id) }}" style="color: #3b82f6; text-decoration: none;">
                                {{ $orderItem->menuItem->name ?? 'Unknown Item' }}
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CATEGORY</span>
                        @if($orderItem->menuItem && $orderItem->menuItem->category)
                            <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $orderItem->menuItem->category->name }}</p>
                        @else
                            <p style="color: #6b7280; margin: 4px 0 0 0;">No Category</p>
                        @endif
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CURRENT PRICE</span>
                        <p style="font-size: 18px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($orderItem->menuItem->price ?? 0, 2) }}</p>
                    </div>

                    @if($orderItem->menuItem && $orderItem->menuItem->availability !== null)
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">AVAILABILITY</span>
                        <span class="status {{ $orderItem->menuItem->availability ? 'status-confirmed' : 'status-cancelled' }}" style="margin: 4px 0 0 0; display: inline-block;">
                            {{ $orderItem->menuItem->availability ? 'Available' : 'Not Available' }}
                        </span>
                    </div>
                    @endif
                </div>

                @if($orderItem->menuItem && $orderItem->menuItem->description)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #d1d5db;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">DESCRIPTION</span>
                    <p style="margin: 4px 0 0 0;">{{ $orderItem->menuItem->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Other Items in This Order Column -->
        <div class="form-group">
            <label class="form-label">Other Items in This Order</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb; max-height: 400px; overflow-y: auto;">
                @if($orderItem->order && $orderItem->order->orderItems && $orderItem->order->orderItems->count() > 1)
                    @php
                        $otherItems = $orderItem->order->orderItems->where('id', '!=', $orderItem->id);
                    @endphp
                    
                    @if($otherItems->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($otherItems as $item)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 6px; border: 1px solid #e5e7eb;">
                                    <div style="flex: 1;">
                                        <p style="font-weight: 600; margin: 0; font-size: 14px;">
                                            <a href="{{ route('admin.order-item.show', $item->id) }}" style="color: #3b82f6; text-decoration: none;">
                                                {{ $item->menuItem->name ?? 'Unknown Item' }}
                                            </a>
                                        </p>
                                        <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">
                                            Qty: {{ $item->quantity }} × RM {{ number_format($item->unit_price, 2) }}
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="status status-item status-{{ str_replace('_', '-', $item->item_status) }}" style="font-size: 10px; padding: 2px 6px;">
                                            {{ str_replace('_', ' ', ucfirst($item->item_status)) }}
                                        </span>
                                        <p style="font-weight: 700; color: #10b981; margin: 2px 0 0 0; font-size: 14px;">
                                            RM {{ number_format($item->total_price, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #d1d5db; text-align: center;">
                            <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TOTAL ORDER VALUE</span>
                            <p style="font-size: 18px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">
                                RM {{ number_format($orderItem->order->total_amount ?? 0, 2) }}
                            </p>
                        </div>
                    @else
                        <p style="text-align: center; color: #6b7280; margin: 0; font-style: italic;">No other items found in this order</p>
                    @endif
                @else
                    <p style="text-align: center; color: #6b7280; margin: 0; font-style: italic;">This is the only item in the order</p>
                @endif
            </div>
        </div>
    </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.order-item.edit', $orderItem->id) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Order Item
        </a>
        <a href="{{ route('admin.order-item.duplicate', $orderItem->id) }}" class="btn-save" style="background: #3b82f6;">
            <i class="fas fa-copy"></i>
            Duplicate Item
        </a>
        <a href="{{ route('admin.order.show', $orderItem->order_id) }}" class="btn-save" style="background: #8b5cf6;">
            <i class="fas fa-receipt"></i>
            View Full Order
        </a>
        <a href="{{ route('admin.order-item.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateItemStatus(status) {
    const statusMessages = {
        'preparing': 'start preparing this item',
        'ready': 'mark this item as ready',
        'served': 'mark this item as served',
        'cancelled': 'cancel this item'
    };
    
    const message = statusMessages[status] || `change the item status to '${status}'`;
    
    if (!confirm(`Are you sure you want to ${message}?`)) {
        return;
    }

    fetch(`{{ route('admin.order-item.updateStatus', $orderItem->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating item status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating item status. Please try again.');
    });
}
</script>
@endsection