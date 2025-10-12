@extends('layouts.customer')

@section('title', 'Promotions & Deals - The Stag SmartDine')

@section('styles')
<style>
/* Promotions Page Styling */
body {
    display: block !important;
    background: #f9fafb;
}

.promotions-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Hero Header */
.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 60px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    color: white;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
    background-size: 100px 100px;
}

.page-header h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 12px;
    position: relative;
    z-index: 1;
}

.page-header p {
    font-size: 1.2rem;
    opacity: 0.95;
    position: relative;
    z-index: 1;
}

/* Type Filter Tabs */
.type-filters {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 8px 0 24px;
    margin-bottom: 32px;
    scrollbar-width: thin;
}

.type-filters::-webkit-scrollbar {
    height: 6px;
}

.type-filters::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.type-filters::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-size: 14px;
}

.filter-btn:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.filter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.filter-btn i {
    font-size: 16px;
}

/* Section Headers */
.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 48px 0 20px;
}

.section-header .icon {
    font-size: 2.2rem;
}

.section-header h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.section-description {
    color: #6b7280;
    font-size: 1.05rem;
    margin-bottom: 24px;
}

/* Promotions Grid */
.promotions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 28px;
    margin-bottom: 48px;
}

/* Promotion Card */
.promotion-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    border: 2px solid transparent;
}

.promotion-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
    border-color: #667eea;
}

/* Type-specific card borders */
.promotion-card.promo-code { border-color: #3b82f6; }
.promotion-card.combo-deal { border-color: #8b5cf6; }
.promotion-card.item-discount { border-color: #10b981; }
.promotion-card.buy-x-free-y { border-color: #f59e0b; }
.promotion-card.bundle { border-color: #ef4444; }
.promotion-card.seasonal { border-color: #ec4899; }

/* Type Badge */
.type-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    z-index: 10;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
}

.type-badge.promo-code { background: rgba(59, 130, 246, 0.95); color: white; }
.type-badge.combo-deal { background: rgba(139, 92, 246, 0.95); color: white; }
.type-badge.item-discount { background: rgba(16, 185, 129, 0.95); color: white; }
.type-badge.buy-x-free-y { background: rgba(245, 158, 11, 0.95); color: white; }
.type-badge.bundle { background: rgba(239, 68, 68, 0.95); color: white; }
.type-badge.seasonal { background: rgba(236, 72, 153, 0.95); color: white; }

/* Banner Section */
.promotion-banner {
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.promotion-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.promotion-banner .icon-placeholder {
    font-size: 5rem;
    opacity: 0.9;
}

/* Type-specific gradients */
.promotion-banner.promo-code { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.promotion-banner.combo-deal { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.promotion-banner.item-discount { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.promotion-banner.buy-x-free-y { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.promotion-banner.bundle { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.promotion-banner.seasonal { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

/* Content Section */
.promotion-content {
    padding: 24px;
}

.promotion-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
    line-height: 1.3;
}

.promotion-discount {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 12px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.promotion-description {
    color: #6b7280;
    margin-bottom: 16px;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* Promo Code Box */
.promo-code-box {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 2px dashed #d1d5db;
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    margin: 16px 0;
    transition: all 0.3s ease;
}

.promo-code-box:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.promo-code-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    font-weight: 600;
}

.promo-code {
    font-size: 1.6rem;
    font-weight: 900;
    color: #1f2937;
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    margin-bottom: 12px;
}

.copy-code-btn {
    padding: 10px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.copy-code-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

/* Combo/Bundle Items Display */
.combo-items {
    background: #f9fafb;
    border-radius: 12px;
    padding: 14px;
    margin: 16px 0;
}

.combo-items-title {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.combo-items-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.combo-item-tag {
    padding: 4px 10px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #374151;
}

/* Promotion Meta */
.promotion-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
    margin-top: 16px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
}

.meta-item i {
    color: #9ca3af;
    font-size: 14px;
}

.meta-item strong {
    color: #1f2937;
}

/* Stats Badge */
.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #fef3c7;
    color: #d97706;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
    background: white;
    border-radius: 20px;
    margin: 40px 0;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 24px;
    opacity: 0.4;
}

.empty-state-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 12px;
}

/* Action Button */
.action-btn {
    display: inline-block;
    padding: 14px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 24px;
    font-size: 1rem;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(102, 126, 234, 0.3);
}

/* Notification Toast */
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
}

.toast.success {
    border-left: 4px solid #10b981;
}

.toast i {
    color: #10b981;
    font-size: 20px;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .promotions-grid {
        grid-template-columns: 1fr;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .page-header {
        padding: 40px 20px;
    }

    .section-header h2 {
        font-size: 1.5rem;
    }

    .type-filters {
        gap: 8px;
    }

    .filter-btn {
        padding: 10px 18px;
        font-size: 13px;
    }
}
</style>
@endsection

@section('content')
<div class="promotions-container">
    <!-- Hero Header -->
    <div class="page-header">
        <h1>🎉 Promotions & Deals</h1>
        <p>Discover amazing discounts and special offers crafted just for you</p>
    </div>

    <!-- Type Filters -->
    <div class="type-filters">
        <button class="filter-btn active" onclick="filterByType('all')">
            <i class="fas fa-th"></i> All Promotions
        </button>
        <button class="filter-btn" onclick="filterByType('promo_code')">
            <i class="fas fa-ticket-alt"></i> Promo Codes
        </button>
        <button class="filter-btn" onclick="filterByType('combo_deal')">
            <i class="fas fa-layer-group"></i> Combo Deals
        </button>
        <button class="filter-btn" onclick="filterByType('item_discount')">
            <i class="fas fa-percent"></i> Item Discounts
        </button>
        <button class="filter-btn" onclick="filterByType('buy_x_free_y')">
            <i class="fas fa-gift"></i> Buy X Free Y
        </button>
        <button class="filter-btn" onclick="filterByType('bundle')">
            <i class="fas fa-box-open"></i> Bundles
        </button>
        <button class="filter-btn" onclick="filterByType('seasonal')">
            <i class="fas fa-calendar-alt"></i> Seasonal
        </button>
    </div>

    <!-- Promotions Grid -->
    @if($promotions->count() > 0)
        <div class="promotions-grid" id="promotionsGrid">
            @foreach($promotions as $promo)
            <div class="promotion-card {{ $promo->type }}" data-type="{{ $promo->type }}" onclick="window.location.href='{{ route('customer.promotions.show', $promo->id) }}'">
                <!-- Type Badge -->
                <span class="type-badge {{ $promo->type }}">
                    {{ ucwords(str_replace('_', ' ', $promo->type)) }}
                </span>

                <!-- Banner -->
                <div class="promotion-banner {{ $promo->type }}">
                    @if($promo->hasImage())
                        {{-- Display uploaded image --}}
                        <img src="{{ $promo->image_url }}" alt="{{ $promo->name }}">
                    @else
                        {{-- Display default gradient banner with icon --}}
                        <div class="icon-placeholder">
                            @if($promo->type === 'promo_code') 🎫
                            @elseif($promo->type === 'combo_deal') 🍱
                            @elseif($promo->type === 'item_discount') 💰
                            @elseif($promo->type === 'buy_x_free_y') 🎁
                            @elseif($promo->type === 'bundle') 📦
                            @elseif($promo->type === 'seasonal') 🎊
                            @else 🎉
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="promotion-content">
                    <h3 class="promotion-title">{{ $promo->name }}</h3>

                    <!-- Discount Display -->
                    @if($promo->type === 'promo_code' || $promo->type === 'item_discount')
                        <div class="promotion-discount">
                            @if($promo->discount_type === 'percentage')
                                {{ number_format($promo->discount_value, 0) }}% OFF
                            @else
                                RM {{ number_format($promo->discount_value, 2) }} OFF
                            @endif
                        </div>
                    @elseif($promo->type === 'combo_deal' || $promo->type === 'bundle' || $promo->type === 'seasonal')
                        <div class="promotion-discount">
                            RM {{ number_format($promo->combo_price, 2) }}
                        </div>
                        @if($promo->original_price)
                            <p class="promotion-description">
                                <span style="text-decoration: line-through; color: #9ca3af;">
                                    RM {{ number_format($promo->original_price, 2) }}
                                </span>
                                <span class="stats-badge">
                                    Save RM {{ number_format($promo->original_price - $promo->combo_price, 2) }}
                                </span>
                            </p>
                        @endif
                    @elseif($promo->type === 'buy_x_free_y')
                        <div class="promotion-discount">
                            Buy {{ $promo->buy_quantity }} Get {{ $promo->free_quantity }} FREE
                        </div>
                    @endif

                    @if($promo->description)
                        <p class="promotion-description">{{ Str::limit($promo->description, 100) }}</p>
                    @endif

                    <!-- Promo Code Box -->
                    @if($promo->promo_code)
                        <div class="promo-code-box">
                            <div class="promo-code-label">Promo Code</div>
                            <div class="promo-code">{{ $promo->promo_code }}</div>
                            <button class="copy-code-btn" onclick="event.stopPropagation(); copyPromoCode('{{ $promo->promo_code }}')">
                                <i class="fas fa-copy"></i> Copy Code
                            </button>
                        </div>
                    @endif

                    <!-- Combo Items -->
                    @if(($promo->type === 'combo_deal' || $promo->type === 'bundle') && $promo->menuItems->count() > 0)
                        <div class="combo-items">
                            <div class="combo-items-title">Includes {{ $promo->menuItems->count() }} Items:</div>
                            <div class="combo-items-list">
                                @foreach($promo->menuItems->take(5) as $item)
                                    <span class="combo-item-tag">{{ $item->name }}</span>
                                @endforeach
                                @if($promo->menuItems->count() > 5)
                                    <span class="combo-item-tag">+{{ $promo->menuItems->count() - 5 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Meta Info -->
                    <div class="promotion-meta">
                        @if($promo->minimum_order_value)
                            <span class="meta-item">
                                <i class="fas fa-shopping-cart"></i>
                                Min: <strong>RM {{ number_format($promo->minimum_order_value, 2) }}</strong>
                            </span>
                        @endif

                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            Until <strong>{{ $promo->end_date->format('M d, Y') }}</strong>
                        </span>

                        @if($promo->usage_limit)
                            @php
                                $remaining = $promo->usage_limit - $promo->usageLogs->count();
                            @endphp
                            @if($remaining > 0 && $remaining <= 10)
                                <span class="meta-item" style="color: #ef4444;">
                                    <i class="fas fa-fire"></i>
                                    Only <strong>{{ $remaining }}</strong> left!
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">🎉</div>
            <div class="empty-state-title">No Promotions Available</div>
            <p>Check back soon for exciting deals and offers!</p>
            <a href="{{ route('customer.menu.index') }}" class="action-btn">
                <i class="fas fa-utensils"></i> Browse Menu
            </a>
        </div>
    @endif
</div>

<script>
// Filter by type
function filterByType(type) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.filter-btn').classList.add('active');

    // Filter cards
    const cards = document.querySelectorAll('.promotion-card');
    cards.forEach(card => {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Copy promo code
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        showToast('Promo code "' + code + '" copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Promo code: ' + code);
    });
}

// Show toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast success';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endsection
