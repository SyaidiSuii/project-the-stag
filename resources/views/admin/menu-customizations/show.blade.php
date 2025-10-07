@extends('layouts.admin')

@section('title', 'Menu Customization Details')
@section('page-title', 'Menu Customization Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Menu Customization #{{ $menuCustomization->id }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.menu-customizations.edit', $menuCustomization->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Customization
            </a>
            <a href="{{ route('admin.menu-customizations.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Customizations
            </a>
        </div>
    </div>

    <!-- Customization Summary -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Customization Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CUSTOMIZATION ID</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">#{{ $menuCustomization->id }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CUSTOMIZATION TYPE</span>
                        <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $menuCustomization->customization_type }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ADDITIONAL PRICE</span>
                        <p style="font-size: 24px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">
                            RM {{ number_format($menuCustomization->additional_price, 2) }}
                        </p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                        <p style="margin: 4px 0 0 0;">{{ $menuCustomization->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customization Details -->
    <div class="form-group">
        <label class="form-label">Customization Value</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <p style="font-size: 16px; line-height: 1.5; margin: 0; white-space: pre-wrap;">{{ $menuCustomization->customization_value }}</p>
        </div>
    </div>

    <!-- Related Order Item Information -->
    @if($menuCustomization->orderItem)
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Related Order Item</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                @if($menuCustomization->orderItem->menuItem)
                <div style="margin-bottom: 12px;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">MENU ITEM</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">
                        <i class="fas fa-utensils" style="color: #6b7280; margin-right: 8px;"></i>
                        {{ $menuCustomization->orderItem->menuItem->name }}
                    </p>
                    @if($menuCustomization->orderItem->menuItem->description)
                    <p style="font-size: 14px; color: #6b7280; margin: 4px 0 0 0;">
                        {{ $menuCustomization->orderItem->menuItem->description }}
                    </p>
                    @endif
                </div>
                @endif

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">QUANTITY</span>
                        <p style="margin: 4px 0 0 0;">{{ $menuCustomization->orderItem->quantity }}</p>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">UNIT PRICE</span>
                        <p style="margin: 4px 0 0 0;">RM {{ number_format($menuCustomization->orderItem->unit_price, 2) }}</p>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TOTAL PRICE</span>
                        <p style="margin: 4px 0 0 0; font-weight: 600;">RM {{ number_format($menuCustomization->orderItem->total_price, 2) }}</p>
                    </div>
                </div>

                @if($menuCustomization->orderItem->notes)
                <div style="margin-top: 12px; background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <span style="font-size: 12px; color: #92400e; font-weight: 600;">ORDER ITEM NOTES</span>
                    <p style="color: #92400e; margin: 4px 0 0 0;">{{ $menuCustomization->orderItem->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Order Information -->
        @if($menuCustomization->orderItem->order)
        <div class="form-group">
            <label class="form-label">Related Order</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="margin-bottom: 12px;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER ID</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">
                        <a href="{{ route('admin.order.show', $menuCustomization->orderItem->order->id) }}"
                            style="color: #3b82f6; text-decoration: none;">
                            <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
                            Order #{{ $menuCustomization->orderItem->order->id }}
                        </a>
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER STATUS</span>
                        <p style="margin: 4px 0 0 0;">
                            <span style="
                                background: 
                                    @if($menuCustomization->orderItem->order->order_status == 'pending') #fef3c7
                                    @elseif($menuCustomization->orderItem->order->order_status == 'preparing') #dbeafe
                                    @elseif($menuCustomization->orderItem->order->order_status == 'ready') #e9d5ff
                                    @elseif($menuCustomization->orderItem->order->order_status == 'served') #e0e7ff
                                    @elseif($menuCustomization->orderItem->order->order_status == 'completed') #d1fae5
                                    @elseif($menuCustomization->orderItem->order->order_status == 'cancelled') #fee2e2
                                    @else #f3f4f6 @endif;
                                color: 
                                    @if($menuCustomization->orderItem->order->order_status == 'pending') #92400e
                                    @elseif($menuCustomization->orderItem->order->order_status == 'preparing') #1e40af
                                    @elseif($menuCustomization->orderItem->order->order_status == 'ready') #6b21a8
                                    @elseif($menuCustomization->orderItem->order->order_status == 'served') #3730a3
                                    @elseif($menuCustomization->orderItem->order->order_status == 'completed') #065f46
                                    @elseif($menuCustomization->orderItem->order->order_status == 'cancelled') #991b1b
                                    @else #374151 @endif;
                                padding: 4px 8px; 
                                border-radius: 12px; 
                                font-size: 12px; 
                                font-weight: 600;
                                text-transform: capitalize;">
                                {{ str_replace('_', ' ', $menuCustomization->orderItem->order->order_status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TYPE</span>
                        <p style="margin: 4px 0 0 0; text-transform: capitalize;">
                            {{ str_replace('_', ' ', $menuCustomization->orderItem->order->order_type) }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TOTAL</span>
                        <p style="margin: 4px 0 0 0; font-weight: 600;">
                            RM {{ number_format($menuCustomization->orderItem->order->total_amount, 2) }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORDER TIME</span>
                        <p style="margin: 4px 0 0 0;">
                            {{ $menuCustomization->orderItem->order->order_time->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>

                @if($menuCustomization->orderItem->order->user)
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CUSTOMER</span>
                    <p style="margin: 4px 0 0 0; font-weight: 600;">
                        <i class="fas fa-user" style="color: #6b7280; margin-right: 8px;"></i>
                        {{ $menuCustomization->orderItem->order->user->name }}
                    </p>
                    @if($menuCustomization->orderItem->order->user->email)
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">
                        <a href="mailto:{{ $menuCustomization->orderItem->order->user->email }}"
                            style="color: #3b82f6; text-decoration: none;">
                            {{ $menuCustomization->orderItem->order->user->email }}
                        </a>
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Timing Information -->
    <div class="form-group">
        <label class="form-label">Timing Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $menuCustomization->created_at->format('M d, Y h:i A') }}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">
                        {{ $menuCustomization->created_at->diffForHumans() }}
                    </p>
                </div>

                @if($menuCustomization->updated_at && $menuCustomization->updated_at != $menuCustomization->created_at)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">LAST UPDATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $menuCustomization->updated_at->format('M d, Y h:i A') }}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">
                        {{ $menuCustomization->updated_at->diffForHumans() }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pricing Impact -->
    <div class="form-group">
        <label class="form-label">Pricing Impact</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: center;">
                @if($menuCustomization->orderItem)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ORIGINAL ITEM PRICE</span>
                    <p style="margin: 4px 0 0 0; font-size: 18px;">
                        RM {{ number_format($menuCustomization->orderItem->unit_price, 2) }}
                    </p>
                </div>
                @endif

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CUSTOMIZATION PRICE</span>
                    <p style="margin: 4px 0 0 0; font-size: 18px; font-weight: 600; color: {{ $menuCustomization->additional_price > 0 ? '#10b981' : '#6b7280' }};">
                        @if($menuCustomization->additional_price > 0)
                        +RM {{ number_format($menuCustomization->additional_price, 2) }}
                        @else
                        FREE
                        @endif
                    </p>
                </div>

                @if($menuCustomization->orderItem)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">FINAL PRICE</span>
                    <p style="margin: 4px 0 0 0; font-size: 20px; font-weight: 700; color: #059669;">
                        RM {{ number_format($menuCustomization->orderItem->unit_price + $menuCustomization->additional_price, 2) }}
                    </p>
                </div>
                @endif
            </div>

            @if($menuCustomization->additional_price > 0)
            <div style="margin-top: 12px; padding: 12px; background: #ecfdf5; border-radius: 8px; border-left: 4px solid #10b981;">
                <p style="font-size: 14px; color: #065f46; margin: 0; font-weight: 500;">
                    <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                    This customization adds RM {{ number_format($menuCustomization->additional_price, 2) }} to the base price
                </p>
            </div>
            @elseif($menuCustomization->additional_price == 0)
            <div style="margin-top: 12px; padding: 12px; background: #f3f4f6; border-radius: 8px; border-left: 4px solid #6b7280;">
                <p style="font-size: 14px; color: #374151; margin: 0; font-weight: 500;">
                    <i class="fas fa-gift" style="margin-right: 8px;"></i>
                    This customization is provided at no additional cost
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.menu-customizations.edit', $menuCustomization->id) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Customization
        </a>
        @if($menuCustomization->orderItem && $menuCustomization->orderItem->order)
        <a href="{{ route('admin.order.show', $menuCustomization->orderItem->order->id) }}" class="btn-save" style="background: #3b82f6;">
            <i class="fas fa-shopping-cart"></i>
            View Order
        </a>
        @endif
        <a href="{{ route('admin.menu-customizations.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
        <form method="POST" action="{{ route('admin.menu-customizations.destroy', $menuCustomization->id) }}"
            style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this customization?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-save" style="background: #ef4444;">
                <i class="fas fa-trash"></i>
                Delete Customization
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Show session messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('message'))
        showNotification('{{ session('
            message ') }}', 'success');
        @endif

        @if(session('success'))
        showNotification('{{ session('
            success ') }}', 'success');
        @endif

        @if(session('error'))
        showNotification('{{ session('
            error ') }}', 'error');
        @endif
    });
</script>
@endsection