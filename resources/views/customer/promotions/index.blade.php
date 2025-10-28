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
    position: relative;
}

/* Guest Blur Styles */
.promotions-grid.guest-blur .promotion-card {
    filter: blur(4px);
    transition: filter 0.3s ease;
}

.promotions-grid.guest-blur .promotion-card:hover {
    filter: blur(3px);
}

/* Login Modal */
.login-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.login-modal-overlay.show {
    display: flex;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.login-modal {
    background: white;
    padding: 48px 40px;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    max-width: 480px;
    width: 90%;
    position: relative;
    animation: slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.login-modal .close-modal {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #f3f4f6;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: #6b7280;
    font-size: 18px;
}

.login-modal .close-modal:hover {
    background: #e5e7eb;
    color: #1f2937;
    transform: rotate(90deg);
}

.login-modal .lock-icon {
    font-size: 4rem;
    color: #667eea;
    margin-bottom: 24px;
    animation: lockPulse 2s ease-in-out infinite;
}

@keyframes lockPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.login-modal h3 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 16px;
    line-height: 1.3;
}

.login-modal p {
    font-size: 1.05rem;
    color: #6b7280;
    margin-bottom: 32px;
    line-height: 1.6;
}

.login-modal .login-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.guest-login-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
}

.guest-login-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
}

.guest-signup-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: white;
    color: #667eea;
    text-decoration: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    border: 2px solid #667eea;
}

.guest-signup-btn:hover {
    background: #f0f4ff;
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
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

/* Success Toast Notification */
.copy-toast {
    position: fixed;
    top: 24px;
    right: 24px;
    background: white;
    padding: 20px 28px;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 16px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 5px solid #10b981;
}

.copy-toast.show {
    opacity: 1;
    transform: translateX(0);
}

.copy-toast.hide {
    opacity: 0;
    transform: translateX(400px);
}

.copy-toast-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    animation: successPulse 0.6s ease;
}

.copy-toast-icon i {
    color: white;
    font-size: 24px;
}

@keyframes successPulse {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.copy-toast-content h4 {
    margin: 0 0 4px 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
}

.copy-toast-content p {
    margin: 0;
    font-size: 0.9rem;
    color: #6b7280;
}

.copy-toast-code {
    font-family: 'Courier New', monospace;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 700;
    color: #667eea;
}

</style>
@endsection

@section('content')
<div class="promotions-container">
    @guest
    <!-- Guest Message -->
    <div style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: white; padding: 2rem; border-radius: 20px; margin: 2rem; text-align: center;">
        <h2 style="margin-bottom: 1rem;">🎉 Promotions & Deals</h2>
        <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">Please login to view exclusive promotions, special deals, and limited-time offers.</p>
        <a href="{{ route('login') }}" style="background: white; color: #ea580c; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
            <i class="fas fa-sign-in-alt"></i> Login to View Promotions
        </a>
    </div>
    @else
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
        <div class="promotions-grid @guest guest-blur @endguest" id="promotionsGrid">
            @foreach($promotions as $promo)
            <div class="promotion-card {{ $promo->promotion_type }}" data-type="{{ $promo->promotion_type }}"
                 onclick="@auth window.location.href='{{ route('customer.promotions.show', $promo->id) }}' @else showLoginModal(event) @endauth">
                <!-- Type Badge -->
                <span class="type-badge {{ $promo->promotion_type }}">
                    {{ ucwords(str_replace('_', ' ', $promo->promotion_type)) }}
                </span>

                <!-- Banner -->
                <div class="promotion-banner {{ $promo->promotion_type }}">
                    @if($promo->hasBannerImage())
                        {{-- Display banner image for combo/bundle/seasonal --}}
                        <img src="{{ $promo->banner_image_url }}" alt="{{ $promo->name }}">
                    @elseif($promo->hasImage())
                        {{-- Display regular image for other types --}}
                        <img src="{{ $promo->image_url }}" alt="{{ $promo->name }}">
                    @else
                        {{-- Display default gradient banner with icon --}}
                        <div class="icon-placeholder">
                            @if($promo->promotion_type === 'promo_code') 🎫
                            @elseif($promo->promotion_type === 'combo_deal') 🍱
                            @elseif($promo->promotion_type === 'item_discount') 💰
                            @elseif($promo->promotion_type === 'buy_x_free_y') 🎁
                            @elseif($promo->promotion_type === 'bundle') 📦
                            @elseif($promo->promotion_type === 'seasonal') 🎊
                            @else 🎉
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="promotion-content">
                    <h3 class="promotion-title">{{ $promo->name }}</h3>

                    <!-- Discount Display -->
                    @if($promo->promotion_type === 'promo_code' || $promo->promotion_type === 'item_discount')
                        <div class="promotion-discount">
                            @if($promo->discount_type === 'percentage')
                                {{ number_format($promo->discount_value, 0) }}% OFF
                            @else
                                RM {{ number_format($promo->discount_value, 2) }} OFF
                            @endif
                        </div>
                    @elseif($promo->promotion_type === 'combo_deal')
                        @php $comboPrice = $promo->getComboPrice(); @endphp
                        @if($comboPrice)
                            <div class="promotion-discount">
                                RM {{ number_format($comboPrice, 2) }}
                            </div>
                        @endif
                    @elseif($promo->promotion_type === 'bundle')
                        @php $bundlePrice = $promo->getBundlePrice(); @endphp
                        @if($bundlePrice)
                            <div class="promotion-discount">
                                RM {{ number_format($bundlePrice, 2) }}
                            </div>
                        @endif
                    @elseif($promo->promotion_type === 'seasonal')
                        <div class="promotion-discount">
                            @if($promo->discount_type === 'percentage')
                                {{ number_format($promo->discount_value, 0) }}% OFF
                            @else
                                RM {{ number_format($promo->discount_value, 2) }} OFF
                            @endif
                        </div>
                    @elseif($promo->promotion_type === 'buy_x_free_y')
                        @php $config = $promo->getBuyXGetYConfig(); @endphp
                        @if($config && $config['buy_quantity'] && $config['get_quantity'])
                            <div class="promotion-discount">
                                Buy {{ $config['buy_quantity'] }} Get {{ $config['get_quantity'] }} FREE
                            </div>
                        @endif
                    @endif

                    @if($promo->description)
                        <p class="promotion-description">{{ Str::limit($promo->description, 100) }}</p>
                    @endif

                    {{-- Scarcity & Usage Indicators --}}
                    @php
                        $currentUses = $promo->current_usage_count ?? 0;
                        $totalLimit = $promo->total_usage_limit;
                        $perUserLimit = $promo->usage_limit_per_customer;
                        $remaining = $totalLimit ? ($totalLimit - $currentUses) : null;
                        $percentageUsed = $totalLimit && $totalLimit > 0 ? ($currentUses / $totalLimit) * 100 : 0;
                        $isLimited = $totalLimit !== null;
                        $isAlmostGone = $remaining !== null && $remaining <= 20 && $remaining > 0;
                        $isVeryLimited = $remaining !== null && $remaining <= 5 && $remaining > 0;

                        // Get user's personal usage (if logged in)
                        $userUsage = null;
                        $userRemaining = null;
                        if (auth()->check() && $perUserLimit) {
                            $userUsage = \App\Models\PromotionUsageLog::where('promotion_id', $promo->id)
                                ->where('user_id', auth()->id())
                                ->count();
                            $userRemaining = max(0, $perUserLimit - $userUsage);
                        }
                    @endphp

                    @if($isLimited || $perUserLimit)
                        <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                            {{-- Scarcity Badge for Limited Total Uses --}}
                            @if($isVeryLimited)
                                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 12px; border-left: 3px solid #ef4444;">
                                    <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1rem;"></i>
                                    <span style="color: #991b1b; font-weight: 700; font-size: 0.85rem;">
                                        HURRY! Only {{ $remaining }} {{ Str::plural('use', $remaining) }} left!
                                    </span>
                                </div>
                            @elseif($isAlmostGone)
                                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; border-left: 3px solid #f59e0b;">
                                    <i class="fas fa-fire" style="color: #d97706; font-size: 1rem;"></i>
                                    <span style="color: #92400e; font-weight: 600; font-size: 0.85rem;">
                                        LIMITED! {{ $remaining }} {{ Str::plural('use', $remaining) }} remaining
                                    </span>
                                </div>
                            @elseif($isLimited && $remaining > 0)
                                <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #6b7280;">
                                    <i class="fas fa-users"></i>
                                    <span>{{ number_format($remaining) }} {{ Str::plural('use', $remaining) }} available</span>
                                </div>
                            @endif

                            {{-- Personal Usage Indicator (for logged-in users) --}}
                            @auth
                                @if($perUserLimit && $userUsage !== null)
                                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: #f0f9ff; border-radius: 10px; border-left: 3px solid #3b82f6;">
                                        <i class="fas fa-user-check" style="color: #2563eb; font-size: 0.9rem;"></i>
                                        <span style="color: #1e40af; font-weight: 500; font-size: 0.8rem;">
                                            You've used this {{ $userUsage }}/{{ $perUserLimit }} {{ Str::plural('time', $userUsage) }}
                                            @if($userRemaining > 0)
                                                <span style="color: #059669;">({{ $userRemaining }} {{ Str::plural('use', $userRemaining) }} left)</span>
                                            @else
                                                <span style="color: #dc2626;">(Limit reached)</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endauth

                            {{-- Time/Day Restrictions Display --}}
                            @if($promo->applicable_days && count($promo->applicable_days) > 0)
                                <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #6b7280;">
                                    <i class="fas fa-calendar-day"></i>
                                    <span>Available: {{ implode(', ', array_map('ucfirst', $promo->applicable_days)) }}</span>
                                </div>
                            @endif

                            @if($promo->applicable_start_time && $promo->applicable_end_time)
                                <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #6b7280;">
                                    <i class="fas fa-clock"></i>
                                    <span>
                                        {{ date('g:i A', strtotime($promo->applicable_start_time)) }} -
                                        {{ date('g:i A', strtotime($promo->applicable_end_time)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Promo Code Box -->
                    @if($promo->promo_code)
                        <div class="promo-code-box">
                            <div class="promo-code-label">Promo Code</div>
                            <div class="promo-code">{{ $promo->promo_code }}</div>
                            <button class="copy-code-btn" onclick="event.stopPropagation(); @auth copyPromoCode('{{ $promo->promo_code }}') @else showLoginModal(event) @endauth">
                                <i class="fas fa-copy"></i> Copy Code
                            </button>
                        </div>
                    @endif

                    <!-- Combo/Bundle Items -->
                    @if($promo->promotion_type === 'combo_deal')
                        @php $comboItems = $promo->getComboItems(); @endphp
                        @if($comboItems && count($comboItems) > 0)
                            <div class="combo-items">
                                <div class="combo-items-title">Includes {{ count($comboItems) }} Items:</div>
                                <div class="combo-items-list">
                                    @foreach(array_slice($comboItems, 0, 5) as $comboItem)
                                        @php
                                            $menuItem = \App\Models\MenuItem::find($comboItem['item_id']);
                                        @endphp
                                        @if($menuItem)
                                            <span class="combo-item-tag">
                                                {{ $menuItem->name }}
                                                @if(isset($comboItem['quantity']) && $comboItem['quantity'] > 1)
                                                    (×{{ $comboItem['quantity'] }})
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                    @if(count($comboItems) > 5)
                                        <span class="combo-item-tag">+{{ count($comboItems) - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @elseif($promo->promotion_type === 'bundle')
                        @php $bundleItems = $promo->getBundleItems(); @endphp
                        @if($bundleItems && count($bundleItems) > 0)
                            <div class="combo-items">
                                <div class="combo-items-title">Includes {{ count($bundleItems) }} Items:</div>
                                <div class="combo-items-list">
                                    @foreach(array_slice($bundleItems, 0, 5) as $bundleItem)
                                        @php
                                            $menuItem = \App\Models\MenuItem::find($bundleItem['item_id']);
                                        @endphp
                                        @if($menuItem)
                                            <span class="combo-item-tag">
                                                {{ $menuItem->name }}
                                                @if(isset($bundleItem['quantity']) && $bundleItem['quantity'] > 1)
                                                    (×{{ $bundleItem['quantity'] }})
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                    @if(count($bundleItems) > 5)
                                        <span class="combo-item-tag">+{{ count($bundleItems) - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @elseif($promo->promotion_type === 'item_discount')
                        @php $itemIds = $promo->getDiscountedItemIds(); @endphp
                        @if($itemIds && count($itemIds) > 0)
                            <div class="combo-items">
                                <div class="combo-items-title">Applies to {{ count($itemIds) }} Items:</div>
                                <div class="combo-items-list">
                                    @foreach(array_slice($itemIds, 0, 5) as $itemId)
                                        @php
                                            $menuItem = \App\Models\MenuItem::find($itemId);
                                        @endphp
                                        @if($menuItem)
                                            <span class="combo-item-tag">{{ $menuItem->name }}</span>
                                        @endif
                                    @endforeach
                                    @if(count($itemIds) > 5)
                                        <span class="combo-item-tag">+{{ count($itemIds) - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
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

<!-- Login Modal (for guests only) -->
@guest
<div class="login-modal-overlay" id="loginModal" onclick="closeLoginModal(event)">
    <div class="login-modal" onclick="event.stopPropagation()">
        <button class="close-modal" onclick="closeLoginModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="lock-icon">
            <i class="fas fa-lock"></i>
        </div>
        <h3>Login to Enjoy Promotions and Deals</h3>
        <p>Get access to all exclusive promotions, special discounts and exciting offers by signing up!</p>
        <div class="login-buttons">
            <a href="{{ route('login') }}" class="guest-login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login Now
            </a>
            <a href="{{ route('register') }}" class="guest-signup-btn">
                <i class="fas fa-user-plus"></i>
                Register Account
            </a>
        </div>
    </div>
</div>
@endguest
@endsection

@section('scripts')
<script>
// Show login modal for guests
function showLoginModal(event) {
    event.preventDefault();
    event.stopPropagation();
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scroll
    }
}

// Close login modal
function closeLoginModal(event) {
    if (event) {
        event.preventDefault();
    }
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = ''; // Re-enable scroll
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLoginModal();
    }
});

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

// Copy promo code with fallback method
function copyPromoCode(code) {
    // Try modern Clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('Promo code "' + code + '" copied to clipboard!');
        }).catch(err => {
            console.error('Clipboard API failed:', err);
            fallbackCopyCode(code);
        });
    } else {
        // Use fallback method for older browsers or HTTP contexts
        fallbackCopyCode(code);
    }
}

// Fallback method using textarea
function fallbackCopyCode(code) {
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Promo code "' + code + '" copied to clipboard!');
        } else {
            alert('Promo code: ' + code + '\n\nPlease copy manually');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        alert('Promo code: ' + code + '\n\nPlease copy manually');
    } finally {
        document.body.removeChild(textarea);
    }
}

// Show animated toast notification
function showToast(message) {
    // Extract promo code from message
    const codeMatch = message.match(/"([^"]+)"/);
    const promoCode = codeMatch ? codeMatch[1] : '';

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'copy-toast';
    toast.innerHTML = `
        <div class="copy-toast-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="copy-toast-content">
            <h4>Code Copied Successfully!</h4>
            <p>Promo code <span class="copy-toast-code">${promoCode}</span> copied to clipboard</p>
        </div>
    `;

    // Add to body
    document.body.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Remove after 4 seconds
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 400);
    }, 4000);
}
</script>
{{-- End of promotions index script section --}}
@endguest
@endsection
