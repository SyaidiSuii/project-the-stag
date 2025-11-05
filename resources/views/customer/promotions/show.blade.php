@extends('layouts.customer')

@section('title', $promotion->name . ' - The Stag SmartDine')

@section('styles')
<style>
/* Override layout constraints */
body {
    display: block !important;
    background: #f9fafb;
}

.main-content {
    width: 100%;
    max-width: 100%;
}

.promotion-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 20px;
    width: 100%;
}

/* Back Button */
.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.back-button:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateX(-4px);
}

/* Hero Section */
.promotion-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 60px 40px;
    text-align: center;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

/* Hero with uploaded image */
.promotion-hero.has-image {
    padding: 0;
    height: 400px;
    display: flex;
    align-items: flex-end;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.promotion-hero.has-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
    z-index: 1;
}

.promotion-hero.has-image .hero-content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 40px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* Type-specific hero gradients */
.promotion-hero.promo-code { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.promotion-hero.combo-deal { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.promotion-hero.item-discount { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.promotion-hero.buy-x-free-y { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.promotion-hero.bundle { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.promotion-hero.seasonal { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.promotion-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.type-badge-large {
    display: inline-block;
    padding: 8px 20px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}

.promotion-icon-large {
    font-size: 5rem;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.promotion-name {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}

.promotion-discount-large {
    font-size: 4rem;
    font-weight: 900;
    margin: 20px 0;
    text-shadow: 0 4px 12px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
}

.promotion-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.validity-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

/* Promo Code Section */
.promo-code-section {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    margin-bottom: 32px;
    text-align: center;
}

.promo-code-title {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.promo-code-display {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 3px dashed #d1d5db;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.promo-code-display:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.promo-code-text {
    font-size: 2.5rem;
    font-weight: 900;
    color: #1f2937;
    font-family: 'Courier New', monospace;
    letter-spacing: 4px;
}

.copy-code-button {
    padding: 14px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.copy-code-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.copy-success {
    display: none;
    color: #16a34a;
    font-weight: 600;
    margin-top: 12px;
    font-size: 1rem;
}

.copy-success.show {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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

/* Mobile responsive */
@media (max-width: 768px) {
    .copy-toast {
        top: 16px;
        right: 16px;
        left: 16px;
        padding: 16px 20px;
    }

    .copy-toast-icon {
        width: 40px;
        height: 40px;
    }

    .copy-toast-icon i {
        font-size: 20px;
    }
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.detail-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.detail-card-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-item {
    padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.85rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 6px;
}

.detail-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1f2937;
}

/* Combo Items Section */
.combo-items-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 32px;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.item-card {
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.item-card:hover {
    background: #f3f4f6;
    transform: translateX(4px);
}

.item-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.item-price {
    color: #6b7280;
    font-size: 0.9rem;
}

/* Terms Section */
.terms-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 32px;
}

.terms-list {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

.terms-list li {
    padding: 12px 0 12px 32px;
    position: relative;
    color: #6b7280;
    line-height: 1.7;
    border-bottom: 1px solid #f3f4f6;
}

.terms-list li:last-child {
    border-bottom: none;
}

.terms-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #22c55e;
    font-weight: 700;
    font-size: 1.2rem;
}

/* CTA Section */
.cta-section {
    text-align: center;
    padding: 40px 20px;
}

.shop-now-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 14px;
    font-size: 1.3rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.shop-now-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(102, 126, 234, 0.4);
}

/* Alert Badges */
.alert-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    margin: 8px 0;
}

.alert-badge.warning {
    background: #fef3c7;
    color: #d97706;
}

.alert-badge.success {
    background: #dcfce7;
    color: #16a34a;
}

.alert-badge.info {
    background: #dbeafe;
    color: #2563eb;
}

/* Responsive */
@media (max-width: 768px) {
    .promotion-name {
        font-size: 2rem;
    }

    .promotion-discount-large {
        font-size: 3rem;
    }

    .promo-code-text {
        font-size: 1.8rem;
        letter-spacing: 2px;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .items-grid {
        grid-template-columns: 1fr;
    }

    .promotion-hero {
        padding: 40px 20px;
    }
}
</style>
@endsection

@section('content')
<div class="promotion-detail-container">
    <!-- Back Button -->
    <a href="{{ route('customer.promotions.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Promotions
    </a>

    <!-- Promotion Hero -->
    <div class="promotion-hero {{ $promotion->type }} {{ $promotion->hasImage() ? 'has-image' : '' }}"
         @if($promotion->hasImage()) style="background-image: url('{{ $promotion->image_url }}');" @endif>
        <div class="hero-content">
            <div class="type-badge-large">
                {{ ucwords(str_replace('_', ' ', $promotion->type)) }}
            </div>

            @if(!$promotion->hasImage())
                {{-- Only show icon if no uploaded image --}}
                <div class="promotion-icon-large">
                    @if($promotion->type === 'promo_code') 🎫
                    @elseif($promotion->type === 'combo_deal') 🍱
                    @elseif($promotion->type === 'item_discount') 💰
                    @elseif($promotion->type === 'buy_x_free_y') 🎁
                    @elseif($promotion->type === 'bundle') 📦
                    @elseif($promotion->type === 'seasonal') 🎊
                    @else 🎉
                    @endif
                </div>
            @endif

            <h1 class="promotion-name">{{ $promotion->name }}</h1>

            <!-- Discount Display -->
            @if($promotion->type === 'promo_code' || $promotion->type === 'item_discount')
                <div class="promotion-discount-large">
                    @if($promotion->discount_type === 'percentage')
                        {{ number_format($promotion->discount_value, 0) }}% OFF
                    @else
                        RM {{ number_format($promotion->discount_value, 2) }} OFF
                    @endif
                </div>
            @elseif($promotion->type === 'combo_deal' || $promotion->type === 'bundle' || $promotion->type === 'seasonal')
                <div class="promotion-discount-large">
                    RM {{ number_format($promotion->combo_price, 2) }}
                </div>
                @if($promotion->original_price)
                    <div class="promotion-subtitle">
                        Regular Price: <span style="text-decoration: line-through;">RM {{ number_format($promotion->original_price, 2) }}</span>
                        <br>Save RM {{ number_format($promotion->original_price - $promotion->combo_price, 2) }}
                    </div>
                @endif
            @elseif($promotion->type === 'buy_x_free_y')
                <div class="promotion-discount-large">
                    Buy {{ $promotion->buy_quantity }} Get {{ $promotion->free_quantity }} FREE
                </div>
            @endif

            <div class="validity-badge">
                <i class="fas fa-calendar"></i>
                {{ $promotion->start_date->format('M d') }} - {{ $promotion->end_date->format('M d, Y') }}
            </div>

            @php
                $daysRemaining = now()->diffInDays($promotion->end_date, false);
            @endphp
            @if($daysRemaining > 0 && $daysRemaining <= 7)
                <div class="validity-badge" style="background: rgba(239, 68, 68, 0.25); margin-left: 8px;">
                    <i class="fas fa-clock"></i> {{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }} left!
                </div>
            @endif
        </div>
    </div>

    @if($promotion->description)
        <div class="detail-card" style="margin-bottom: 32px; text-align: center;">
            <p style="font-size: 1.1rem; color: #6b7280; line-height: 1.8;">{{ $promotion->description }}</p>
        </div>
    @endif

    <!-- Promo Code Section -->
    @if($promotion->promo_code)
    <div class="promo-code-section">
        <div class="promo-code-title">🎫 Your Promo Code</div>
        <div class="promo-code-display">
            <div class="promo-code-text" id="promoCode">{{ $promotion->promo_code }}</div>
        </div>
        <button class="copy-code-button" onclick="copyPromoCode()">
            <i class="fas fa-copy"></i> Copy Code
        </button>
        <div class="copy-success" id="copySuccess">
            <i class="fas fa-check-circle"></i> Code copied to clipboard!
        </div>
        <p style="margin-top: 16px; color: #6b7280; font-size: 0.9rem;">
            Apply this code at checkout to get your discount
        </p>
    </div>
    @endif

    <!-- Combo/Bundle Items -->
    @if(($promotion->type === 'combo_deal' || $promotion->type === 'bundle' || $promotion->type === 'seasonal') && $promotion->menuItems->count() > 0)
    <div class="combo-items-card">
        <div class="detail-card-title">
            <i class="fas fa-utensils"></i> Includes {{ $promotion->menuItems->count() }} Items
        </div>
        <div class="items-grid">
            @foreach($promotion->menuItems as $item)
                <div class="item-card">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-price">Regular: RM {{ number_format($item->price, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Buy X Free Y Items -->
    @if($promotion->type === 'buy_x_free_y' && $promotion->menuItems->count() > 0)
    <div class="combo-items-card">
        <div class="detail-card-title">
            <i class="fas fa-gift"></i> Eligible Items
        </div>
        <div class="items-grid">
            @foreach($promotion->menuItems as $item)
                <div class="item-card">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-price">RM {{ number_format($item->price, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Main Details -->
        <div class="detail-card">
            <div class="detail-card-title">
                <i class="fas fa-info-circle"></i> Promotion Details
            </div>
            <div class="detail-item">
                <div class="detail-label">Promotion Type</div>
                <div class="detail-value">{{ ucwords(str_replace('_', ' ', $promotion->type)) }}</div>
            </div>
            @if($promotion->type === 'promo_code' || $promotion->type === 'item_discount')
                <div class="detail-item">
                    <div class="detail-label">Discount Type</div>
                    <div class="detail-value">
                        {{ $promotion->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}
                    </div>
                </div>
            @endif
            <div class="detail-item">
                <div class="detail-label">Valid Period</div>
                <div class="detail-value">{{ $daysRemaining }} days remaining</div>
            </div>
        </div>

        <!-- Requirements -->
        <div class="detail-card">
            <div class="detail-card-title">
                <i class="fas fa-clipboard-check"></i> Requirements
            </div>
            @if($promotion->minimum_order_value)
            <div class="detail-item">
                <div class="detail-label">Minimum Order</div>
                <div class="detail-value">RM {{ number_format($promotion->minimum_order_value, 2) }}</div>
            </div>
            @endif
            @if($promotion->usage_limit)
                @php
                    $remaining = $promotion->usage_limit - $promotion->usageLogs->count();
                @endphp
                <div class="detail-item">
                    <div class="detail-label">Usage Limit</div>
                    <div class="detail-value">{{ $remaining }} / {{ $promotion->usage_limit }} remaining</div>
                </div>
            @else
                <div class="detail-item">
                    <div class="detail-label">Usage Limit</div>
                    <div class="detail-value">Unlimited</div>
                </div>
            @endif
            @if($promotion->usage_limit_per_user)
                <div class="detail-item">
                    <div class="detail-label">Per Customer</div>
                    <div class="detail-value">{{ $promotion->usage_limit_per_user }} time(s)</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Terms & Conditions -->
    <div class="terms-card">
        <div class="detail-card-title">
            <i class="fas fa-file-contract"></i> Terms & Conditions
        </div>
        <ul class="terms-list">
            <li>Valid from {{ $promotion->start_date->format('F d, Y') }} to {{ $promotion->end_date->format('F d, Y') }}</li>
            @if($promotion->minimum_order_value)
            <li>Minimum order value of RM {{ number_format($promotion->minimum_order_value, 2) }} required</li>
            @endif
            @if($promotion->promo_code)
            <li>Promo code "{{ $promotion->promo_code }}" must be applied at checkout</li>
            @endif
            @if($promotion->usage_limit_per_user)
            <li>Limited to {{ $promotion->usage_limit_per_user }} use(s) per customer</li>
            @endif
            @if($promotion->usage_limit)
                @php $remaining = $promotion->usage_limit - $promotion->usageLogs->count(); @endphp
            <li>Only {{ $remaining }} redemptions remaining</li>
            @endif
            <li>Cannot be combined with other promotions or discounts unless specified</li>
            <li>Valid for dine-in, takeaway, and delivery orders</li>
            <li>The Stag SmartDine reserves the right to modify or cancel this promotion at any time</li>
            <li>Standard terms and conditions apply</li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div class="cta-section">
        <a href="{{ route('customer.menu.index') }}" class="shop-now-btn">
            <i class="fas fa-utensils"></i> Browse Menu & Order Now
        </a>
    </div>
</div>

<script>
function copyPromoCode() {
    const promoCode = document.getElementById('promoCode').textContent;
    const copySuccess = document.getElementById('copySuccess');

    // Try modern Clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(promoCode).then(() => {
            showCopySuccess(copySuccess);
        }).catch(err => {
            console.error('Clipboard API failed:', err);
            fallbackCopyPromoCode(promoCode, copySuccess);
        });
    } else {
        // Use fallback method for older browsers or HTTP contexts
        fallbackCopyPromoCode(promoCode, copySuccess);
    }
}

// Fallback method using textarea
function fallbackCopyPromoCode(promoCode, copySuccess) {
    const textarea = document.createElement('textarea');
    textarea.value = promoCode;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(copySuccess);
        } else {
            if (typeof Toast !== 'undefined') {
                Toast.info('Copy Code', 'Promo code: ' + promoCode + '\n\nPlease copy manually');
            } else {
                alert('Promo code: ' + promoCode + '\n\nPlease copy manually');
            }
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        if (typeof Toast !== 'undefined') {
            Toast.info('Copy Code', 'Promo code: ' + promoCode + '\n\nPlease copy manually');
        } else {
            alert('Promo code: ' + promoCode + '\n\nPlease copy manually');
        }
    } finally {
        document.body.removeChild(textarea);
    }
}

// Show success message
function showCopySuccess(copySuccess) {
    // Show inline success message
    copySuccess.classList.add('show');
    setTimeout(() => {
        copySuccess.classList.remove('show');
    }, 3000);

    // Show toast notification
    showCopyToast();
}

// Show animated toast notification
function showCopyToast() {
    const promoCode = document.getElementById('promoCode').textContent;

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
@endsection
