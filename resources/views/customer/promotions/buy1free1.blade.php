@extends('layouts.customer')

@section('title', $promotion->name)

@section('content')
<style>
    /* Override parent flex layout */
    body {
        display: block !important;
    }

    .b1f1-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        text-decoration: none;
        margin-bottom: 24px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: #0ea5e9;
        transform: translateX(-4px);
    }

    .hero-banner {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 24px;
        padding: 48px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(14, 165, 233, 0.3);
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-header {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .hero-icon {
        font-size: 48px;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .hero-title {
        margin: 0;
        font-size: 42px;
        font-weight: 800;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .hero-description {
        margin: 12px 0 0 0;
        font-size: 18px;
        opacity: 0.95;
        line-height: 1.6;
    }

    .hero-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 16px;
        margin-top: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .card-title {
        margin: 0 0 28px 0;
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-title i {
        color: #0ea5e9;
        font-size: 32px;
    }

    .price-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .price-main {
        font-size: 64px;
        font-weight: 800;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .price-regular {
        font-size: 20px;
        color: #9ca3af;
        text-decoration: line-through;
        margin-top: 8px;
        font-weight: 500;
    }

    .savings-badge {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
        padding: 24px 32px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
        animation: savesPulse 2s ease-in-out infinite;
    }

    @keyframes savesPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .savings-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }

    .savings-amount {
        font-size: 36px;
        font-weight: 800;
        margin-top: 8px;
    }

    .savings-percent {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 4px;
        font-weight: 600;
    }

    .b1f1-items-grid {
        display: grid;
        gap: 20px;
    }

    .b1f1-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border: 2px solid #f3f4f6;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: linear-gradient(to right, #ffffff 0%, #fafafa 100%);
    }

    .b1f1-item:hover {
        border-color: #0ea5e9;
        transform: translateX(8px);
        box-shadow: 0 4px 16px rgba(14, 165, 233, 0.15);
    }

    .item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .item-placeholder {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .item-description {
        margin: 0;
        font-size: 14px;
        color: #6b7280;
        line-height: 1.5;
    }

    .item-tags {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .tag-price {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
    }

    .tag-quantity {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #2563eb;
    }

    .item-total {
        text-align: right;
        min-width: 120px;
    }

    .item-total-price {
        font-size: 24px;
        font-weight: 800;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .item-total-label {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 3px solid #f3f4f6;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        margin-bottom: 12px;
    }

    .summary-row.discount {
        color: #16a34a;
        font-weight: 600;
    }

    .summary-row.total {
        font-size: 28px;
        font-weight: 800;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #f3f4f6;
    }

    .validity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .validity-card {
        padding: 28px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 16px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .validity-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #0ea5e9;
    }

    .validity-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .validity-value {
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
    }

    .cta-button {
        width: 100%;
        padding: 24px;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(14, 165, 233, 0.4);
    }

    .cta-button:active {
        transform: translateY(0);
    }

    .cta-info {
        text-align: center;
        margin: 16px 0 0 0;
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        color: #9ca3af;
        padding: 60px 20px;
        font-size: 16px;
    }

    /* Buy 1 Free 1 Special Badge */
    .b1f1-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #a5f3fc 0%, #67e8f9 100%);
        color: #0891b2;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
        margin-top: 16px;
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    @media (max-width: 768px) {
        .hero-banner {
            padding: 32px 24px;
        }

        .hero-title {
            font-size: 32px;
        }

        .price-main {
            font-size: 48px;
        }

        .card {
            padding: 24px;
        }

        .b1f1-item {
            flex-direction: column;
            text-align: center;
        }

        .item-total {
            text-align: center;
        }

        .hero-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }
</style>

<div class="b1f1-container">
    {{-- Back Button --}}
    <a href="{{ route('customer.promotions.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Promotions
    </a>

    {{-- Hero Banner --}}
    <div class="hero-banner">
        <div class="hero-content">
            <div class="hero-header">
                <i class="fas fa-gift hero-icon"></i>
                <div>
                    <h1 class="hero-title">{{ $promotion->name }}</h1>
                    @php
                        // Get configuration from promo_config
                        $config = $promotion->promo_config ?? [];
                        $buyQty = $config['buy_quantity'] ?? 1;
                        $freeQty = $config['get_quantity'] ?? $config['free_quantity'] ?? 1;
                    @endphp
                    <p class="hero-description">
                        {{ $promotion->description ?? "Buy {$buyQty} item(s), get {$freeQty} item(s) free!" }}
                    </p>
                    <div class="b1f1-badge">
                        <i class="fas fa-gift"></i>
                        Buy {{ $buyQty }} Get {{ $freeQty }} Free
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 15px; font-size: 14px;">
                        <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; padding: 6px 12px; border-radius: 16px; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-shopping-cart"></i>
                            {{ $buyQty }} Paid Item(s)
                        </div>
                        <div style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; padding: 6px 12px; border-radius: 16px; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-gift"></i>
                            {{ $freeQty }} Free Item(s)
                        </div>
                    </div>
                </div>
            </div>

            @if($promotion->banner_image)
                <img src="{{ asset('storage/' . $promotion->banner_image) }}"
                     alt="{{ $promotion->name }}"
                     class="hero-image">
            @endif
        </div>
    </div>

    {{-- B1F1 Price --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-tags"></i>
            Buy {{ $buyQty }} Get {{ $freeQty }} Free Deal
        </h2>

        @php
            $b1f1Price = $promotion->getB1F1Price();
            $b1f1Items = $promotion->getB1F1Items() ?? [];
            $totalRegularPrice = 0;

            // Calculate total regular price (sum of all item prices regardless of free status)
            foreach($promotion->menuItems as $item) {
                $pivot = $item->pivot;
                $quantity = $pivot->quantity ?? 1;
                $totalRegularPrice += $item->price * $quantity;
            }
            
            $savings = 0;
            $savingsPercentage = 0;
        @endphp

        <div class="price-container">
            <div>
                <div class="price-main">
                    RM {{ number_format($b1f1Price ?? 0, 2) }}
                </div>
                @if($totalRegularPrice > ($b1f1Price ?? 0))
                    <div class="price-regular">
                        Regular Price: RM {{ number_format($totalRegularPrice, 2) }}
                    </div>
                @endif
            </div>

            @if($totalRegularPrice > ($b1f1Price ?? 0))
                @php
                    $savings = $totalRegularPrice - ($b1f1Price ?? 0);
                    $savingsPercentage = $totalRegularPrice > 0 ? ($savings / $totalRegularPrice) * 100 : 0;
                @endphp
                <div class="savings-badge">
                    <div class="savings-label">You Save</div>
                    <div class="savings-amount">RM {{ number_format($savings, 2) }}</div>
                    <div class="savings-percent">({{ number_format($savingsPercentage, 0) }}% off)</div>
                </div>
            @endif
        </div>
    </div>

    {{-- B1F1 Items --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-utensils"></i>
            Items in This Deal
        </h2>
        
        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #bae6fd;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 24px; color: #0ea5e9;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #0c4a6e;">How This Deal Works</h3>
                    <p style="margin: 0; color: #0369a1; line-height: 1.5;">
                        Add <strong>{{ $buyQty }} paid item(s)</strong> to your cart and <strong>{{ $freeQty }} item(s)</strong> will be added for free automatically.
                        You only pay for the items marked as "PAID".
                    </p>
                </div>
            </div>
        </div>

        <div class="b1f1-items-grid">
            @forelse($promotion->menuItems as $item)
                @php
                    $pivot = $item->pivot;
                    $quantity = $pivot->quantity ?? 1;
                    $isFreeItem = $pivot->is_free ?? false;
                    
                    // For B1F1, only charge for items that are not free
                    $chargedQuantity = $isFreeItem ? 0 : $quantity;
                    $totalPrice = $item->price * $chargedQuantity;
                @endphp
                <div class="b1f1-item">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}"
                             alt="{{ $item->name }}"
                             class="item-image">
                    @else
                        <div class="item-placeholder">
                            <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                        </div>
                    @endif

                    <div class="item-details">
                        <h3 class="item-name">{{ $item->name }}</h3>
                        @if($item->description)
                            <p class="item-description">
                                {{ Str::limit($item->description, 120) }}
                            </p>
                        @endif
                        <div class="item-tags">
                            <span class="tag tag-price">
                                <i class="fas fa-tag"></i>
                                RM {{ number_format($item->price, 2) }}
                            </span>
                            @if($quantity > 1)
                                <span class="tag tag-quantity">
                                    <i class="fas fa-times"></i>
                                    {{ $quantity }}x
                                </span>
                            @endif
                            @if($isFreeItem)
                                <span class="tag" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a;">
                                    <i class="fas fa-gift"></i>
                                    FREE
                                </span>
                            @else
                                <span class="tag" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb;">
                                    <i class="fas fa-shopping-cart"></i>
                                    PAID
                                </span>
                            @endif
                        </div>
                        <div style="margin-top: 8px; font-size: 13px; color: #6b7280;">
                            @if($isFreeItem)
                                <i class="fas fa-check-circle" style="color: #16a34a;"></i> This item is free with your purchase
                            @else
                                <i class="fas fa-credit-card" style="color: #2563eb;"></i> You pay for this item
                            @endif
                        </div>
                    </div>

                    <div class="item-total">
                        <div class="item-total-price">
                            RM {{ number_format($totalPrice, 2) }}
                        </div>
                        <div class="item-total-label">Subtotal</div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-gift" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No items configured for this deal.</p>
                </div>
            @endforelse
        </div>

        {{-- Total Summary --}}
        @if($promotion->menuItems->count() > 0)
            <div class="summary-section">
                <div class="summary-row">
                    <span style="color: #6b7280;">Total Regular Price:</span>
                    <span style="font-weight: 600; color: #1f2937;">RM {{ number_format($totalRegularPrice, 2) }}</span>
                </div>
                <div class="summary-row discount">
                    <span>B{{ $buyQty }}G{{ $freeQty }} Discount ({{ number_format($savingsPercentage, 0) }}%):</span>
                    <span style="font-weight: 700;">- RM {{ number_format($savings, 2) }}</span>
                </div>
                <div class="summary-row total">
                    <span style="color: #1f2937;">Final Price:</span>
                    <span style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        RM {{ number_format($b1f1Price ?? 0, 2) }}
                    </span>
                </div>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e5e7eb; font-size: 14px; color: #6b7280;">
                    <i class="fas fa-lightbulb" style="color: #f59e0b;"></i>
                    You pay RM {{ number_format($b1f1Price ?? 0, 2) }} for {{ $buyQty }} item(s) and get {{ $freeQty }} item(s) free
                </div>
            </div>
        @endif
    </div>

    {{-- Validity Period --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-calendar-check"></i>
            Valid Period
        </h2>

        <div class="validity-grid">
            <div class="validity-card">
                <div class="validity-label">Start Date</div>
                <div class="validity-value">
                    {{ $promotion->start_date->format('d M Y') }}
                </div>
            </div>
            <div class="validity-card">
                <div class="validity-label">End Date</div>
                <div class="validity-value">
                    {{ $promotion->end_date->format('d M Y') }}
                </div>
            </div>
            <div class="validity-card">
                <div class="validity-label">Status</div>
                <div class="validity-value" style="color: {{ $promotion->is_active ? '#16a34a' : '#dc2626' }};">
                    {{ $promotion->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add to Cart --}}
    <div class="card">
        <button onclick="addB1F1ToCart()" class="cta-button">
            <i class="fas fa-shopping-cart"></i>
            Add Deal to Cart - RM {{ number_format($b1f1Price ?? 0, 2) }}
        </button>
        <p class="cta-info">
            <i class="fas fa-info-circle"></i> 
            @if($buyQty == 1 && $freeQty == 1)
                Add 1 paid item to your cart and 1 free item will be added automatically
            @else
                Add {{ $buyQty }} paid item(s) to your cart and {{ $freeQty }} free item(s) will be added automatically
            @endif
        </p>
    </div>
</div>

<script>
async function addB1F1ToCart() {
    const promotionId = {{ $promotion->id }};
    const button = event.target;

    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding to cart...';

    try {
        const response = await fetch('{{ route("customer.cart.add-promotion") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                promotion_id: promotionId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            alert('✅ ' + data.message + '\n\nItems added:\n' + data.items_added.join('\n'));

            // Redirect to menu or cart
            window.location.href = '{{ route("customer.menu.index") }}';
        } else {
            alert('❌ ' + (data.message || 'Failed to add deal to cart'));
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add Deal to Cart - RM {{ number_format($b1f1Price ?? 0, 2) }}';
        }
    } catch (error) {
        console.error('Error adding buy-x-free-y to cart:', error);
        alert('❌ An error occurred. Please try again.');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add Deal to Cart - RM {{ number_format($b1f1Price ?? 0, 2) }}';
    }
}
</script>
@endsection