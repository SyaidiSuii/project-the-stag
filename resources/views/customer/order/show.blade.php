@extends('layouts.customer')

@section('title', 'Order Details - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
<style>
/* Full screen container */
.order-details-fullscreen {
    min-height: calc(100vh - 80px);
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    padding: 32px 0;
}

.order-details-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Back button - simple text */
.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    color: #6b7280;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 24px;
    text-decoration: none;
}

.back-button:hover {
    border-color: #6366f1;
    color: #6366f1;
    text-decoration: none;
}

/* Main card */
.order-details-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

/* Order header */
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding-bottom: 32px;
    border-bottom: 2px solid #f3f4f6;
    margin-bottom: 32px;
}

.order-id-large {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.order-date-time {
    font-size: 15px;
    color: #6b7280;
    margin-bottom: 16px;
}

.order-meta {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 16px;
}

.meta-item {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.meta-label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 4px;
}

.meta-value {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

/* Progress Stepper Styles */
.progress-section {
    margin: 32px 0;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 24px;
}

.progress-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32px;
    background: white;
    border-radius: 16px;
    border: 2px solid #e5e7eb;
    position: relative;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
    z-index: 2;
}

.step-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 4px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 12px;
    transition: all 0.3s;
}

.step.active .step-circle {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-color: #6366f1;
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.step.completed .step-circle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-color: #10b981;
    color: white;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }
    50% {
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.6);
    }
}

.step-label {
    font-size: 14px;
    font-weight: 600;
    color: #9ca3af;
    text-align: center;
}

.step.active .step-label {
    color: #6366f1;
}

.step.completed .step-label {
    color: #10b981;
}

.step-time {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 4px;
}

/* Progress line */
.progress-stepper::before {
    content: '';
    position: absolute;
    top: calc(32px + 28px - 2px);
    left: 10%;
    right: 10%;
    height: 4px;
    background: #e5e7eb;
    z-index: 1;
}

.progress-line-fill {
    position: absolute;
    top: calc(32px + 28px - 2px);
    left: 10%;
    height: 4px;
    background: linear-gradient(90deg, #10b981 0%, #6366f1 100%);
    z-index: 1;
    transition: width 0.5s ease;
}

/* Payment stepper - 2 steps */
.payment-stepper {
    padding: 24px 60px;
}

.payment-stepper::before {
    left: calc(25% + 28px);
    right: calc(25% + 28px);
}

.payment-stepper .progress-line-fill {
    left: calc(25% + 28px);
}

/* Order items */
.order-items-section {
    margin: 32px 0;
}

/* Promotion Group Styles */
.order-promotion-group {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #38bdf8;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(56, 189, 248, 0.15);
}

.promotion-group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid rgba(56, 189, 248, 0.3);
}

.promotion-group-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}

.promotion-group-title i {
    color: #8b5cf6;
    font-size: 20px;
}

.promotion-type-badge {
    background: #8b5cf6;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.promotion-savings {
    font-size: 14px;
    font-weight: 700;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
}

.promotion-savings i {
    font-size: 16px;
}

.promotion-group-items {
    margin-bottom: 16px;
}

.promotion-item {
    background: rgba(255, 255, 255, 0.8) !important;
    border: 1px solid rgba(56, 189, 248, 0.3) !important;
    margin-bottom: 12px;
}

.promotion-item:last-child {
    margin-bottom: 0;
}

.free-badge {
    display: inline-block;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    margin-left: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.original-price {
    display: inline-block;
    margin-left: 8px;
    color: #9ca3af;
    text-decoration: line-through;
    font-size: 13px;
}

.promotion-group-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 2px solid rgba(56, 189, 248, 0.3);
    font-size: 18px;
    font-weight: 700;
    color: #8b5cf6;
}

/* Item Discount Styles */
.item-discount-group {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 2px solid #fbbf24;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.15);
}

.item-discount-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid rgba(251, 191, 36, 0.3);
}

.item-discount-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}

.item-discount-title i {
    color: #f59e0b;
    font-size: 20px;
}

.item-discount-badge {
    background: #f59e0b;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.item-discount-savings {
    font-size: 14px;
    font-weight: 700;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
}

.item-discount-savings i {
    font-size: 16px;
}

.item-discount-items {
    margin-bottom: 16px;
}

.item-discount-item {
    background: rgba(255, 255, 255, 0.8) !important;
    border: 1px solid rgba(251, 191, 36, 0.3) !important;
    margin-bottom: 12px;
}

.item-discount-item:last-child {
    margin-bottom: 0;
}

.order-item {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 16px;
    margin-bottom: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s;
}

.order-item:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
}

.item-image {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.item-details {
    flex: 1;
}

.item-name {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.item-quantity-price {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 8px;
}

.item-quantity {
    font-size: 14px;
    color: #6b7280;
    background: white;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.item-unit-price {
    font-size: 14px;
    color: #6b7280;
}

.item-notes {
    font-size: 13px;
    color: #6b7280;
    background: white;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-top: 8px;
}

.item-price {
    font-size: 20px;
    font-weight: 700;
    color: #6366f1;
    align-self: center;
}

/* Order summary */
.order-summary-card {
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    border-radius: 20px;
    padding: 32px;
    border: 2px solid #e5e7eb;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    font-size: 16px;
    color: #6b7280;
}

.summary-row.total {
    border-top: 2px solid #e5e7eb;
    margin-top: 12px;
    padding-top: 20px;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

/* ETA Section */
.eta-card {
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    border: 2px solid #6366f1;
    border-radius: 16px;
    padding: 24px;
    margin: 24px 0;
    text-align: center;
}

.eta-icon {
    font-size: 48px;
    margin-bottom: 12px;
}

.eta-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.eta-time {
    font-size: 32px;
    font-weight: 900;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.eta-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-top: 8px;
}

/* Review Section Styles */
.review-section {
    margin-top: 32px;
    padding-top: 32px;
    border-top: 2px solid #e5e7eb;
}

.review-items-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 24px;
}

.review-item-card {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s;
}

.review-item-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
}

.review-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.review-item-name {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}

.review-item-quantity {
    background: white;
    padding: 6px 16px;
    border-radius: 999px;
    font-size: 14px;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

.rating-section {
    margin-bottom: 16px;
}

.rating-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.star-rating {
    display: flex;
    gap: 8px;
    font-size: 32px;
}

.star {
    cursor: pointer;
    color: #d1d5db;
    transition: all 0.2s;
}

.star:hover {
    transform: scale(1.15);
}

.star.active {
    color: #fbbf24;
}

.review-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 14px;
    resize: vertical;
    font-family: inherit;
    transition: border-color 0.2s;
}

.review-textarea:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.anonymous-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
}

.anonymous-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.anonymous-checkbox label {
    font-size: 14px;
    color: #6b7280;
    cursor: pointer;
    margin: 0;
}

.submit-review-section {
    margin-top: 32px;
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-submit-review {
    padding: 14px 32px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit-review:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.btn-submit-review:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

.success-message, .error-message {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 600;
}

.success-message {
    background: #d1fae5;
    border: 2px solid #10b981;
    color: #065f46;
}

.error-message {
    background: #fee2e2;
    border: 2px solid #ef4444;
    color: #991b1b;
}

.already-reviewed-badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    text-align: center;
    font-weight: 600;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
    }

    .order-meta {
        grid-template-columns: 1fr;
    }

    .progress-stepper {
        padding: 24px 16px;
    }

    .step-circle {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    .step-label {
        font-size: 12px;
    }

    .submit-review-section {
        flex-direction: column;
    }

    .btn-submit-review {
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="order-details-fullscreen">
    <div class="order-details-container">
        <a href="{{ route('customer.orders.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>

        <div class="order-details-card">
            <!-- Order Header -->
            <div class="order-header">
                <div>
                    <div class="order-id-large">Order #{{ $order->confirmation_code ?? 'ORD-' . $order->id }}</div>
                    <div class="order-date-time">
                        <i class="fas fa-calendar"></i> {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </div>

                    <div class="order-meta">
                        <div class="meta-item">
                            <div class="meta-label">Order Type</div>
                            <div class="meta-value">
                                @if($order->order_type === 'dine_in')
                                    <i class="fas fa-utensils"></i> Dine In
                                @else
                                    <i class="fas fa-shopping-bag"></i> Takeaway
                                @endif
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Payment Method</div>
                            <div class="meta-value">
                                @if($order->payment_method === 'online')
                                    <i class="fas fa-credit-card"></i> Online Payment
                                @else
                                    <i class="fas fa-money-bill"></i> Pay at Restaurant
                                @endif
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Total Amount</div>
                            <div class="meta-value" style="color: #6366f1;">RM {{ number_format($order->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Progress -->
            <div class="progress-section">
                <div class="section-title">
                    <i class="fas fa-tasks"></i> Order Status
                </div>
                <div class="progress-stepper">
                    @php
                        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
                        $statusLabels = [
                            'pending' => 'Order Placed',
                            'confirmed' => 'Confirmed',
                            'preparing' => 'Preparing',
                            'ready' => 'Ready',
                            'completed' => 'Completed'
                        ];
                        $statusIcons = [
                            'pending' => '📝',
                            'confirmed' => '✅',
                            'preparing' => '👨‍🍳',
                            'ready' => '🔔',
                            'completed' => '✨'
                        ];
                        $currentIndex = array_search($order->order_status, $statuses);
                        if ($currentIndex === false) $currentIndex = 0;
                        $progressPercent = ($currentIndex / (count($statuses) - 1)) * 80;
                    @endphp

                    <div class="progress-line-fill" style="width: {{ $progressPercent }}%;"></div>

                    @foreach($statuses as $index => $status)
                        <div class="step {{ $index < $currentIndex ? 'completed' : ($index === $currentIndex ? 'active' : '') }}">
                            <div class="step-circle">
                                @if($index < $currentIndex)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $statusIcons[$status] }}
                                @endif
                            </div>
                            <div class="step-label">{{ $statusLabels[$status] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Status Progress -->
            <div class="progress-section">
                <div class="section-title">
                    <i class="fas fa-credit-card"></i> Payment Status
                </div>
                <div class="progress-stepper payment-stepper">
                    @php
                        $paymentStatuses = ['unpaid', 'paid'];
                        $paymentLabels = ['Unpaid', 'Paid'];
                        $paymentIcons = ['💳', '✅'];
                        $paymentIndex = $order->payment_status === 'paid' ? 1 : 0;
                        $paymentProgress = $paymentIndex * 100;
                    @endphp

                    <div class="progress-line-fill" style="width: {{ $paymentProgress === 100 ? 'calc(50% - 56px)' : '0%' }};"></div>

                    @foreach($paymentStatuses as $index => $status)
                        <div class="step {{ $index < $paymentIndex ? 'completed' : ($index === $paymentIndex ? 'active' : '') }}">
                            <div class="step-circle">
                                @if($index < $paymentIndex)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $paymentIcons[$index] }}
                                @endif
                            </div>
                            <div class="step-label">{{ $paymentLabels[$index] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ETA if exists -->
            @if($order->estimated_completion_time)
            <div class="eta-card">
                <div class="eta-icon">⏱️</div>
                <div class="eta-title">Estimated Completion Time</div>
                <div class="eta-time">{{ $order->estimated_completion_time->format('g:i A') }}</div>
                <div class="eta-subtitle">{{ $order->estimated_completion_time->diffForHumans() }}</div>
            </div>
            @endif

            <!-- Order Items -->
            <div class="order-items-section">
                <div class="section-title">
                    <i class="fas fa-shopping-cart"></i> Order Items
                </div>

                {{-- Promotion Groups --}}
                @foreach($promotionGroups as $group)
                <div class="order-promotion-group">
                    <div class="promotion-group-header">
                        <div class="promotion-group-title">
                            <i class="fas fa-gift"></i>
                            <span>{{ $group['promotion']->name ?? 'Promotion' }}</span>
                            <span class="promotion-type-badge">{{ $group['promotion']->type_label ?? 'Bundle' }}</span>
                        </div>
                        @if($group['savings'] > 0)
                        <div class="promotion-savings">
                            <i class="fas fa-tag"></i> Saved RM {{ number_format($group['savings'], 2) }}
                        </div>
                        @endif
                    </div>

                    <div class="promotion-group-items">
                        @foreach($group['items'] as $item)
                        <div class="order-item promotion-item">
                            @if($item->menuItem && $item->menuItem->image_url)
                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                            @else
                            <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name">
                                    {{ $item->menuItem->name ?? 'Unknown Item' }}
                                    @if($item->is_free_item)
                                    <span class="free-badge">FREE</span>
                                    @endif
                                </div>
                                <div class="item-unit-price">
                                    RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                                    @if($item->original_price && $item->original_price > $item->unit_price)
                                    <span class="original-price">RM {{ number_format($item->original_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="promotion-group-total">
                        <span>Bundle Total:</span>
                        <span>RM {{ number_format($group['total_price'], 2) }}</span>
                    </div>
                </div>
                @endforeach

                {{-- Item Discounts --}}
                @if(count($itemDiscounts) > 0)
                    @php
                        $totalItemDiscountSavings = 0;
                        foreach($itemDiscounts as $item) {
                            $totalItemDiscountSavings += ($item->discount_amount * $item->quantity);
                        }
                    @endphp
                    <div class="item-discount-group">
                        <div class="item-discount-header">
                            <div class="item-discount-title">
                                <i class="fas fa-percent"></i>
                                <span>Item Discounts</span>
                                <span class="item-discount-badge">Discount</span>
                            </div>
                            @if($totalItemDiscountSavings > 0)
                            <div class="item-discount-savings">
                                <i class="fas fa-tag"></i> Saved RM {{ number_format($totalItemDiscountSavings, 2) }}
                            </div>
                            @endif
                        </div>

                        <div class="item-discount-items">
                            @foreach($itemDiscounts as $item)
                            <div class="order-item item-discount-item">
                                @if($item->menuItem && $item->menuItem->image_url)
                                <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                                @else
                                <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                                </div>
                                @endif
                                <div class="item-details">
                                    <div class="item-name">
                                        {{ $item->menuItem->name ?? 'Unknown Item' }}
                                    </div>
                                    <div class="item-unit-price">
                                        RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                                        @if($item->original_price && $item->original_price > $item->unit_price)
                                        <span class="original-price">RM {{ number_format($item->original_price, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($item->promotion)
                                    <div class="item-notes">
                                        <i class="fas fa-tag"></i> {{ $item->promotion->name }}
                                    </div>
                                    @endif
                                </div>
                                <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Order-Level Promotions --}}
                @if($orderPromotions->count() > 0)
                    @foreach($orderPromotions as $promotion)
                        @if($promotion)
                        <div class="item-discount-group">
                            <div class="item-discount-header">
                                <div class="item-discount-title">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>{{ $promotion->name }}</span>
                                    <span class="item-discount-badge">{{ $promotion->type_label }}</span>
                                </div>
                                @php
                                    $promoUsage = $order->promotionUsageLogs->where('promotion_id', $promotion->id)->first();
                                    $discountAmount = $promoUsage ? $promoUsage->discount_amount : 0;
                                @endphp
                                @if($discountAmount > 0)
                                <div class="item-discount-savings">
                                    <i class="fas fa-tag"></i> Saved RM {{ number_format($discountAmount, 2) }}
                                </div>
                                @endif
                            </div>

                            <div class="item-discount-items">
                                <div class="order-item item-discount-item">
                                    <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-ticket-alt" style="font-size: 32px; color: #9ca3af;"></i>
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name">
                                            Promo Code Applied
                                        </div>
                                        <div class="item-unit-price">
                                            Code: <strong>{{ $promotion->promo_code ?? 'N/A' }}</strong>
                                        </div>
                                        @if($promotion->discount_type === 'percentage')
                                        <div class="item-notes">
                                            <i class="fas fa-percent"></i> {{ number_format($promotion->discount_value, 0) }}% OFF
                                        </div>
                                        @else
                                        <div class="item-notes">
                                            <i class="fas fa-money-bill"></i> RM {{ number_format($promotion->discount_value, 2) }} OFF
                                        </div>
                                        @endif
                                    </div>
                                    <div class="item-price">
                                        @if($discountAmount > 0)
                                            -RM {{ number_format($discountAmount, 2) }}
                                        @else
                                            RM 0.00
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif

                {{-- Voucher Discount --}}
                @if($order->customer_voucher_id && $order->voucher_discount > 0)
                    <div class="item-discount-group">
                        <div class="item-discount-header">
                            <div class="item-discount-title">
                                <i class="fas fa-gift"></i>
                                <span>{{ $order->customerVoucher && $order->customerVoucher->voucherTemplate ? $order->customerVoucher->voucherTemplate->name : 'Reward Voucher' }}</span>
                                <span class="item-discount-badge">VOUCHER</span>
                            </div>
                            <div class="item-discount-savings">
                                <i class="fas fa-tag"></i> Saved RM {{ number_format($order->voucher_discount, 2) }}
                            </div>
                        </div>

                        <div class="item-discount-items">
                            <div class="order-item item-discount-item">
                                <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-gift" style="font-size: 32px; color: #9ca3af;"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-name">
                                        Voucher Discount Applied
                                    </div>
                                    <div class="item-unit-price">
                                        Code: <strong>{{ $order->voucher_code ?? ($order->customerVoucher ? $order->customerVoucher->voucher_code : 'N/A') }}</strong>
                                    </div>
                                    @if($order->customerVoucher && $order->customerVoucher->voucherTemplate)
                                        @php
                                            $template = $order->customerVoucher->voucherTemplate;
                                        @endphp
                                        @if($template->discount_type === 'percentage')
                                        <div class="item-notes">
                                            <i class="fas fa-percent"></i> {{ number_format($template->discount_value, 0) }}% OFF
                                        </div>
                                        @else
                                        <div class="item-notes">
                                            <i class="fas fa-money-bill"></i> RM {{ number_format($template->discount_value, 2) }} OFF
                                        </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="item-price">
                                    -RM {{ number_format($order->voucher_discount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Regular Items --}}
                @foreach($regularItems as $item)
                <div class="order-item">
                    @if($item->menuItem && $item->menuItem->image_url)
                    <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                    @else
                    <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                    </div>
                    @endif
                    <div class="item-details">
                        <div class="item-name">{{ $item->menuItem->name ?? 'Unknown Item' }}</div>
                        <div class="item-unit-price">
                            RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                        </div>
                        @if($item->special_note)
                        <div class="item-notes">
                            <i class="fas fa-comment"></i> {{ $item->special_note }}
                        </div>
                        @endif
                    </div>
                    <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="order-summary-card">
                <div class="section-title">
                    <i class="fas fa-receipt"></i> Order Summary
                </div>
                <div class="summary-row">
                    <span>Subtotal ({{ $order->items->count() }} items)</span>
                    <span>RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Tax & Service</span>
                    <span>RM 0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Review Section (Only for completed/served orders and logged in users) -->
            @auth
            @if(false && in_array($order->order_status, ['completed', 'served']))
                <div class="review-section" id="review-section" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-star"></i> Rate Your Order
                    </div>

                    @if(session('review_success'))
                        <div class="success-message">
                            <i class="fas fa-check-circle"></i> {{ session('review_success') }}
                        </div>
                    @endif

                    @if(session('review_error'))
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ session('review_error') }}
                        </div>
                    @endif

                    @php
                        $hasReviews = $order->reviews()->exists();
                        $reviewableItems = [];

                        if (!$hasReviews) {
                            foreach ($order->items as $orderItem) {
                                if (!$orderItem->menuItem) continue;

                                $existingReview = $order->reviews()
                                    ->where('menu_item_id', $orderItem->menu_item_id)
                                    ->first();

                                if (!$existingReview) {
                                    $reviewableItems[] = [
                                        'order_item_id' => $orderItem->id,
                                        'menu_item_id' => $orderItem->menu_item_id,
                                        'menu_item' => $orderItem->menuItem,
                                        'quantity' => $orderItem->quantity
                                    ];
                                }
                            }
                        }
                    @endphp

                    @if($hasReviews)
                        <div class="already-reviewed-badge">
                            <i class="fas fa-check-circle"></i> Thank you! You've already reviewed this order.
                        </div>
                    @elseif(!empty($reviewableItems))
                        <form id="reviewForm" method="POST" action="#" data-disabled-route="customer.reviews.store-batch">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="review-items-list">
                                @foreach($reviewableItems as $index => $item)
                                    <div class="review-item-card">
                                        <div class="review-item-header">
                                            <span class="review-item-name">{{ $item['menu_item']->name }}</span>
                                            <span class="review-item-quantity">x{{ $item['quantity'] }}</span>
                                        </div>

                                        <input type="hidden" name="reviews[{{ $index }}][menu_item_id]" value="{{ $item['menu_item_id'] }}">

                                        <div class="rating-section">
                                            <label class="rating-label">Rating *</label>
                                            <div class="star-rating" data-item-index="{{ $index }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="star" data-rating="{{ $i }}">★</span>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="reviews[{{ $index }}][rating]" class="rating-input" required>
                                        </div>

                                        <div>
                                            <label for="review_text_{{ $index }}" class="rating-label">Your Review (Optional)</label>
                                            <textarea
                                                name="reviews[{{ $index }}][review_text]"
                                                id="review_text_{{ $index }}"
                                                class="review-textarea"
                                                placeholder="Tell us about your experience with this dish..."></textarea>
                                        </div>

                                        <div class="anonymous-checkbox">
                                            <input
                                                type="checkbox"
                                                name="reviews[{{ $index }}][is_anonymous]"
                                                id="anonymous_{{ $index }}"
                                                value="1">
                                            <label for="anonymous_{{ $index }}">Post anonymously</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="submit-review-section">
                                <button type="submit" class="btn-submit-review" id="submitBtn">
                                    <i class="fas fa-paper-plane"></i> Submit Reviews
                                </button>
                            </div>
                        </form>
                    @else
                        <p style="text-align: center; color: #6b7280; padding: 20px;">
                            No items available to review.
                        </p>
                    @endif
                </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 30 seconds for pending/preparing orders
    const orderStatus = '{{ $order->order_status }}';
    if (['pending', 'confirmed', 'preparing'].includes(orderStatus)) {
        setTimeout(() => {
            location.reload();
        }, 30000);
    }

    // Smooth scroll to review section if hash present
    if (window.location.hash === '#review-section') {
        setTimeout(() => {
            const reviewSection = document.getElementById('review-section');
            if (reviewSection) {
                reviewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }

    // Review form star rating functionality
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        const starRatings = document.querySelectorAll('.star-rating');
        const submitBtn = document.getElementById('submitBtn');

        // Handle star rating clicks
        starRatings.forEach(ratingContainer => {
            const stars = ratingContainer.querySelectorAll('.star');
            const itemIndex = ratingContainer.dataset.itemIndex;
            const ratingInput = document.querySelector(`input[name="reviews[${itemIndex}][rating]"]`);

            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;

                    // Update star display
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                // Hover effect
                star.addEventListener('mouseenter', function() {
                    const rating = this.dataset.rating;
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.style.color = '#fbbf24';
                        } else {
                            s.style.color = '#d1d5db';
                        }
                    });
                });
            });

            // Reset hover effect
            ratingContainer.addEventListener('mouseleave', function() {
                const currentRating = ratingInput.value;
                stars.forEach((s, i) => {
                    if (i < currentRating) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#d1d5db';
                    }
                });
            });
        });

        // Form submission
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate that all items have ratings
            const ratingInputs = document.querySelectorAll('.rating-input');
            let allRated = true;

            ratingInputs.forEach(input => {
                if (!input.value) {
                    allRated = false;
                }
            });

            if (!allRated) {
                alert('Please rate all items before submitting.');
                return;
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            // Submit via AJAX
            const formData = new FormData(reviewForm);

            // Convert to JSON structure
            const reviews = [];
            ratingInputs.forEach((input, index) => {
                const reviewTextArea = document.querySelector(`textarea[name="reviews[${index}][review_text]"]`);
                const isAnonymousCheckbox = document.querySelector(`input[name="reviews[${index}][is_anonymous]"]`);
                const menuItemIdInput = document.querySelector(`input[name="reviews[${index}][menu_item_id]"]`);

                reviews.push({
                    menu_item_id: menuItemIdInput.value,
                    rating: input.value,
                    review_text: reviewTextArea.value,
                    is_anonymous: isAnonymousCheckbox.checked ? 1 : 0
                });
            });

            const data = {
                order_id: document.querySelector('input[name="order_id"]').value,
                reviews: reviews
            };

            // Rating feature disabled - route removed
            console.warn('Rating feature is currently disabled');
            alert('Rating feature is currently unavailable.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
            return;
            
            /* DISABLED - Rating feature hidden
            fetch('#', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            */
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Reload page to show "already reviewed" badge
                    location.reload();
                } else {
                    alert(data.message || 'Failed to submit reviews. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
            });
        });
    }
});
</script>
@endsection