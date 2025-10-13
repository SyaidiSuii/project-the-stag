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
    animation: pulse 2s infinite;
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

                    <div class="progress-line-fill" style="width: {{ $paymentProgress === 100 ? '50%' : '0%' }};"></div>

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
                @foreach($order->items as $item)
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
});
</script>
@endsection
