@extends('layouts.customer')

@section('title', 'Menu - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/food.css') }}">
<link rel="stylesheet" href="{{ asset('css/customer/promotion-cart.css') }}">
@endsection

@section('content')
<!-- Header Section -->
<div class="header-section">
  @if(isset($tableNumber) && $tableNumber)
  <!-- Table Number Display (From QR) -->
  <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 16px 24px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);">
    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; color: white;">
      <i class="fas fa-chair" style="font-size: 24px;"></i>
      <div>
        <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">You're ordering for</div>
        <div style="font-size: 20px; font-weight: 800;">Table {{ $tableNumber }}</div>
      </div>
    </div>
  </div>
  @endif

  <!-- Search Bar -->
  <div class="search-bar-container" role="search">
    <div class="search-bar">
      <span class="search-icon" aria-hidden="true">🔎</span>
      <input type="text" class="search-input" placeholder="Search menu..." id="searchInput" aria-label="Search menu" />
      <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
    </div>
  </div>

  @auth
  <!-- Promotions Banner -->
  <div class="promotions-banner" id="promotionsBanner">
    <div class="promo-carousel">
      <a href="{{ route('customer.promotions.index') }}" class="promo-slide">
        <div class="promo-icon">🎉</div>
        <div class="promo-content">
          <div class="promo-title">Check Out Our Promotions!</div>
          <div class="promo-subtitle">Amazing deals and happy hour specials</div>
        </div>
        <div class="promo-arrow">→</div>
      </a>
    </div>
  </div>

  <!-- Kitchen Smart Banner -->
  @if(isset($kitchenStatus) && count($kitchenStatus['busy_types']) > 0 && $kitchenStatus['recommended_items']->count() > 0)
  <div class="kitchen-smart-banner" id="kitchenBanner" style="margin: 20px auto; max-width: 800px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 20px; color: white; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3); animation: slideDown 0.5s ease-out;">
      <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; align-items: center; gap: 16px;">
              <div style="flex: 1;">
                  <div style="font-size: 14px; opacity: 0.95; line-height: 1.5; text-align: center;">
                      ⚡ <strong>Want your food faster?</strong> These items are ready quickly:
                  </div>
              </div>
              <button onclick="dismissKitchenBanner()" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 20px; line-height: 1; transition: background 0.3s;">
                  ✕
              </button>
          </div>

          @if($kitchenStatus['recommended_items']->count() > 0)
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 8px;">
              @foreach($kitchenStatus['recommended_items']->take(4) as $item)
              <div class="quick-add-item" data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}" data-item-price="{{ $item->price }}" data-item-image="{{ $item->image ?? '' }}"
                   style="background: rgba(255,255,255,0.15); border-radius: 12px; padding: 10px; cursor: pointer; transition: all 0.3s; backdrop-filter: blur(10px);">
                  <div style="position: relative; width: 100%; padding-top: 100%; border-radius: 8px; overflow: hidden; margin-bottom: 8px; background: rgba(255,255,255,0.1);">
                      @if($item->image)
                      <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                           style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                      @else
                      <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 40px;">
                          🍽️
                      </div>
                      @endif
                      <div style="position: absolute; top: 6px; right: 6px; background: rgba(16, 185, 129, 0.9); color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">
                          ~{{ $item->estimated_wait ?? 5 }} min
                      </div>
                  </div>
                  <div style="font-size: 12px; font-weight: 600; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->name }}</div>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                      <span style="font-size: 13px; font-weight: 700;">RM {{ number_format($item->price, 2) }}</span>
                      <div style="background: rgba(255,255,255,0.3); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; line-height: 1; font-weight: 400;">+</div>
                  </div>
              </div>
              @endforeach
          </div>
          @if($kitchenStatus['recommended_items']->count() > 4)
          <a href="{{ route('customer.menu.fast-items') }}" style="margin-top: 12px; width: 100%; padding: 10px 16px; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.3); border-radius: 10px; color: white; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
              <span>See All {{ $kitchenStatus['recommended_items']->count() }} Fast Items</span>
              <i class="fas fa-arrow-right"></i>
          </a>
          @endif
          @endif
      </div>
  </div>
  @endif
  @endauth

  <!-- AI Recommendations Section -->
  @if(Auth::check() && isset($recommendedItems) && count($recommendedItems) > 0)
  <div class="recommendations-section" style="margin: 24px auto; max-width: 800px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
      <div style="font-size: 24px;">🤖</div>
      <div>
        <h2 style="font-size: 20px; font-weight: 700; color: #1f2937; margin: 0;">Recommended For You</h2>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Based on your order history and preferences</p>
      </div>
    </div>
    <div class="recommendations-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;">
      @foreach(array_slice($recommendedItems->all(), 0, 4) as $item)
      <div class="recommendation-card" onclick="quickAddToCart({{ $item->id }}, {{ json_encode($item->name) }}, {{ $item->price }}, {{ json_encode($item->image ?? '') }})"
           style="background: white; border-radius: 12px; padding: 12px; cursor: pointer; transition: all 0.3s; border: 2px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div style="position: relative; width: 100%; padding-top: 100%; border-radius: 8px; overflow: hidden; margin-bottom: 8px; background: #f3f4f6;">
          @if($item->image)
          <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
               style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; font-size: 48px; background: #f3f4f6;">
            {{ $item->category && strpos(strtolower($item->category->type), 'drink') !== false ? '🍹' : '🍽️' }}
          </div>
          @else
          <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px; background: #f3f4f6;">
            {{ $item->category && strpos(strtolower($item->category->type), 'drink') !== false ? '🍹' : '🍽️' }}
          </div>
          @endif
          <div style="position: absolute; top: 8px; right: 8px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);">
            AI Pick
          </div>
        </div>
        <div style="font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->name }}</div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 16px; font-weight: 700; color: #6366f1;">RM {{ number_format($item->price, 2) }}</span>
          <div style="background: #6366f1; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; line-height: 1; font-weight: 400;">+</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  <style>
    .recommendation-card:hover {
      border-color: #6366f1;
      box-shadow: 0 8px 16px rgba(99, 102, 241, 0.15);
      transform: translateY(-2px);
    }
  </style>
  @endif

  <!-- Menu Type Toggle -->
  <div class="category-tabs">
    <button class="tab active" data-type="all" id="allMenuBtn">
      <i class="fas fa-list"></i> All Items
    </button>
    <button class="tab" data-type="food" id="foodMenuBtn">
      <i class="fas fa-utensils"></i> Food
    </button>
    <button class="tab" data-type="drinks" id="drinksMenuBtn">
      <i class="fas fa-cocktail"></i> Drinks
    </button>
  </div>

  <!-- Dynamic Category Title -->
  <h1 class="category-title" id="categoryTitle">Menu</h1>
  <p class="no-results" id="noResults">No results found. Try another keyword.</p>
</div>

<div id="menu-container">
  <!-- Menu items will be dynamically inserted here -->
</div>

<!-- Modern Floating Action Button (FAB) for Cart -->
<button class="cart-fab {{ Auth::check() ? '' : 'logged-out' }}" id="cartFab" aria-label="Open cart">
  🛒
  <span class="cart-badge" id="cartBadge">0</span>
</button>

@auth
<!-- Floating Order Type Button -->
<button class="ordertype-fab" id="ordertypeFab" aria-label="Change order type" style="position: fixed; top: 24px; right: 24px; min-width: 140px; height: 56px; border-radius: 28px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; font-size: 15px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3); z-index: 999; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; transition: all 0.3s; opacity: 0;">
  <i class="fas fa-utensils" id="ordertypeIcon" style="font-size: 18px;"></i>
  <span id="ordertypeText">Dine In</span>
</button>
@endauth

<!-- Modern Centered Cart Modal -->
<div class="cart-modal" id="cartModal">
  <div class="cart-modal-backdrop" id="cartModalBackdrop"></div>
  <div class="cart-modal-container">
    <div class="cart-modal-header">
      <button class="cart-modal-close" id="cartModalClose" aria-label="Close cart">✕</button>
      <h2 class="cart-modal-title">🛒 My Cart</h2>
    </div>
    <div class="cart-modal-body">
      <div class="cart-modal-content">
        <div class="cart-modal-toolbar">
          <div class="cart-modal-count">Items: <span id="cart-count">0</span></div>
          <button class="cart-modal-clear" id="clearAllBtn" style="cursor: pointer; transition: all 0.2s ease;">Clear All</button>
        </div>
        <div class="cart-modal-items" id="cart-items">
          <!-- Cart items will be displayed here -->
          <div class="empty-cart" id="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <div class="empty-cart-text">Your cart is empty</div>
            <div class="empty-cart-subtext">Add some delicious items to get started!</div>
          </div>
        </div>
      </div>
      <div class="cart-modal-footer">
        <!-- Title for Summary Section -->
        <div style="margin-bottom: 1rem;">
          <h3 style="font-size: 1.1rem; font-weight: 800; color: #1f2937; margin: 0 0 0.5rem 0;">Your Subtotal</h3>
          <div style="height: 3px; width: 40px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); border-radius: 2px;"></div>
        </div>

        <!-- Voucher Selection Section (NEW) -->
        @if(Auth::check())
        <div class="voucher-selection-section" style="margin-bottom: 1rem; padding: 1rem; background: #fef3c7; border: 2px dashed #f59e0b; border-radius: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-ticket-alt" style="color: #f59e0b; font-size: 1.2rem;"></i>
              <span style="font-weight: 700; color: #92400e; font-size: 0.95rem;">My Vouchers</span>
            </div>
            <button id="select-voucher-btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; padding: 6px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
              Select
            </button>
          </div>

          <!-- Applied Voucher Display -->
          <div id="voucher-applied-container" style="display: none; background: white; padding: 10px; border-radius: 8px; margin-top: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <div style="font-size: 0.85rem; color: #92400e; font-weight: 600;" id="voucher-name">RM10 OFF</div>
                <div style="font-size: 0.75rem; color: #d97706;" id="voucher-desc">Minimum spend RM50</div>
              </div>
              <button id="remove-voucher-btn" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                Remove
              </button>
            </div>
          </div>

          <!-- No Vouchers Message -->
          <div id="no-vouchers-message" style="font-size: 0.85rem; color: #92400e; text-align: center; padding: 8px;">
            You have no vouchers available
          </div>
        </div>
        @endif

        <!-- Promo Code Section -->
        <div class="promo-code-section" style="margin-bottom: 1rem; padding: 1rem; background: #f9fafb; border-radius: 12px;">
          <div id="promo-input-container">
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
              <input
                type="text"
                id="promo-code-input"
                placeholder="Enter promo code"
                style="flex: 1; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
              />
              <button
                id="apply-promo-btn"
                style="padding: 10px 20px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: transform 0.2s;"
                onclick="this.style.transform = 'scale(0.95)'; setTimeout(() => this.style.transform = 'scale(1)', 100);"
              >
                Apply
              </button>
            </div>
            <div id="promo-error-message" style="display: none; color: #ef4444; font-size: 0.85rem; margin-top: 4px;">
              <i class="fas fa-exclamation-circle"></i> <span></span>
            </div>
          </div>

          <div id="promo-applied-container" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #ecfdf5; border: 2px solid #10b981; border-radius: 8px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.1rem;"></i>
                <div>
                  <div style="font-weight: 600; color: #065f46; font-size: 0.9rem;">Promo Applied!</div>
                  <div style="font-size: 0.85rem; color: #047857;" id="promo-code-text">CODE123</div>
                </div>
              </div>
              <button
                id="remove-promo-btn"
                style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px 8px; font-size: 0.85rem; font-weight: 600;"
              >
                Remove
              </button>
            </div>
          </div>
        </div>

        <!-- Cart Total -->
        <div class="cart-modal-total">
          <div style="display: flex; flex-direction: column; gap: 6px; width: 100%;">
            <!-- Subtotal Row -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div class="cart-total-label" style="font-size: 0.8rem;">Subtotal</div>
              <div class="cart-total-amount" id="subtotal-amount" style="font-size: 1rem;">RM 0.00</div>
            </div>

            <!-- Voucher Discount Row (hidden by default) -->
            <div id="voucher-discount-row" style="display: none; justify-content: space-between; align-items: center;">
              <div class="cart-total-label" style="font-size: 0.8rem; color: #f59e0b;">
                <i class="fas fa-ticket-alt"></i> Voucher Discount
              </div>
              <div class="cart-total-amount" id="voucher-discount-amount" style="font-size: 1rem; color: #f59e0b;">-RM 0.00</div>
            </div>

            <!-- Promo Discount Row (hidden by default) -->
            <div id="promo-discount-row" style="display: none; justify-content: space-between; align-items: center;">
              <div class="cart-total-label" style="font-size: 0.8rem; color: #10b981;">
                <i class="fas fa-tag"></i> Promo Discount
              </div>
              <div class="cart-total-amount" id="promo-discount-amount" style="font-size: 1rem; color: #10b981;">-RM 0.00</div>
            </div>
            <!-- Divider -->
            <div style="height: 1px; background: #e5e7eb; margin: 6px 0;"></div>
            <!-- Total Row -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div class="cart-total-label" style="font-weight: 800; color: #1f2937; font-size: 0.9rem;">Total</div>
              <div class="cart-total-amount" id="total-amount" style="font-size: 1.4rem; font-weight: 900; background: linear-gradient(135deg, var(--brand), var(--brand-2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">RM 0.00</div>
            </div>
          </div>
        </div>
        <button class="cart-modal-checkout" style="padding: 1rem; font-size: 1rem;">Proceed to Checkout</button>
      </div>
    </div>
  </div>
</div>

<!-- Voucher Selection Modal -->
<div id="voucherSelectionModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 16px; max-width: 500px; width: 90%; max-height: 80vh; overflow: hidden; display: flex; flex-direction: column;">
    <!-- Header -->
    <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: #1f2937;">
        <i class="fas fa-ticket-alt" style="color: #f59e0b; margin-right: 8px;"></i>
        Select Voucher
      </h3>
      <button id="closeVoucherModal" style="background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px;">✕</button>
    </div>

    <!-- Voucher List -->
    <div id="voucherListContainer" style="flex: 1; overflow-y: auto; padding: 16px;">
      <!-- Vouchers will be loaded here via JavaScript -->
      <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
        <p>Loading vouchers...</p>
      </div>
    </div>
  </div>
</div>

<!-- Order Now Modal - Beautiful Design -->
<div id="order-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 600px; border-radius: 24px; background: white;">
    <!-- Modal Header with Close Button -->
    <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
      <button id="order-modal-close-x" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
      <div style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; margin-bottom: 12px;">
          <i class="fas fa-receipt" style="font-size: 28px; color: white;"></i>
        </div>
        <h3 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Order Details</h3>
      </div>
    </div>

    <div class="modal-body-scrollable" style="padding: 24px; max-height: calc(90vh - 200px); overflow-y: auto;">
      <!-- Item Info Card -->
      <div style="background: #f9fafb; border-radius: 16px; padding: 16px; margin-bottom: 20px;">
        <div style="display: flex; gap: 16px; align-items: center;">
          <img id="order-item-image" src="" alt="Item" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; flex-shrink: 0;">
          <div style="flex: 1; min-width: 0;">
            <div id="order-item-name" style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">Item Name</div>
            <div id="order-item-price" style="font-size: 18px; font-weight: 700; color: #6366f1;">RM 0.00</div>
          </div>
        </div>
      </div>

      <!-- Quantity Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Quantity:</label>
        <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
          <button class="qty-btn" id="order-qty-minus" style="width: 40px; height: 40px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 20px; font-weight: 600; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">−</button>
          <span id="order-quantity" style="font-size: 20px; font-weight: 700; color: #1f2937; min-width: 40px; text-align: center;">1</span>
          <button class="qty-btn" id="order-qty-plus" style="width: 40px; height: 40px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 20px; font-weight: 600; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">+</button>
        </div>
      </div>

      <!-- Add-ons Section (Placeholder) -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add-ons:</label>
        <div style="padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb;">
          <p style="font-size: 13px; color: #6b7280; margin: 0;">No add-ons available for this item</p>
        </div>
      </div>

      <!-- Special Instructions -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Special Instructions</label>
        <textarea id="order-notes" placeholder="Any special requests or dietary requirements..." rows="3" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; resize: vertical; font-family: inherit; color: #1f2937;"></textarea>
      </div>

      <!-- Total Section -->
      <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 16px; padding: 20px; border: 2px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 16px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Total:</span>
          <span id="order-total-amount" style="font-size: 28px; font-weight: 900; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">RM 0.00</span>
        </div>
      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div style="padding: 20px 24px 24px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px;">
      <button id="order-cancel-btn" style="flex: 1; padding: 14px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s;">Cancel</button>
      <button id="order-confirm-btn" style="flex: 2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 15px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">ORDER NOW</button>
    </div>
  </div>
</div>

<!-- Add to Cart Modal - Beautiful Design -->
<div id="addtocart-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 600px; border-radius: 24px; background: white;">
    <!-- Modal Header with Close Button -->
    <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
      <button id="addtocart-modal-close-x" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
      <div style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; margin-bottom: 12px;">
          <i class="fas fa-cart-plus" style="font-size: 28px; color: white;"></i>
        </div>
        <h3 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Add to Cart</h3>
      </div>
    </div>

    <div class="modal-body-scrollable" style="padding: 24px; max-height: calc(90vh - 200px); overflow-y: auto;">
      <!-- Item Info Card -->
      <div style="background: #f9fafb; border-radius: 16px; padding: 16px; margin-bottom: 20px;">
        <div style="display: flex; gap: 16px; align-items: center;">
          <img id="addtocart-item-image" src="" alt="Item" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; flex-shrink: 0;">
          <div style="flex: 1; min-width: 0;">
            <div id="addtocart-item-name" style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">Item Name</div>
            <div id="addtocart-item-price" style="font-size: 18px; font-weight: 700; color: #6366f1;">RM 0.00</div>
          </div>
        </div>
      </div>

      <!-- Quantity Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Quantity:</label>
        <div style="display: flex; align-items: center; gap: 16px; justify-content: center;">
          <button class="qty-btn" id="addtocart-qty-minus" style="width: 40px; height: 40px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 20px; font-weight: 600; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">−</button>
          <span id="addtocart-quantity" style="font-size: 20px; font-weight: 700; color: #1f2937; min-width: 40px; text-align: center;">1</span>
          <button class="qty-btn" id="addtocart-qty-plus" style="width: 40px; height: 40px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 20px; font-weight: 600; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">+</button>
        </div>
      </div>

      <!-- Add-ons Section (Placeholder) -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add-ons:</label>
        <div style="padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb;">
          <p style="font-size: 13px; color: #6b7280; margin: 0;">No add-ons available for this item</p>
        </div>
      </div>

      <!-- Special Instructions -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Special Instructions</label>
        <textarea id="addtocart-notes" placeholder="Any special requests or dietary requirements..." rows="3" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; resize: vertical; font-family: inherit; color: #1f2937;"></textarea>
      </div>

      <!-- Total Section -->
      <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 16px; padding: 20px; border: 2px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 16px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Total:</span>
          <span id="addtocart-total-amount" style="font-size: 28px; font-weight: 900; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">RM 0.00</span>
        </div>
      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div style="padding: 20px 24px 24px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px;">
      <button id="addtocart-cancel-btn" style="flex: 1; padding: 14px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s;">Cancel</button>
      <button id="addtocart-confirm-btn" style="flex: 2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 15px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">ADD TO CART</button>
    </div>
  </div>
</div>

<!-- All Recommendations Modal -->
<div id="all-recommendations-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 900px; border-radius: 24px; background: white; max-height: 90vh; display: flex; flex-direction: column;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 24px; border-bottom: 2px solid #e5e7eb; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px 24px 0 0;">
      <button id="close-recommendations-modal" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
              onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.transform='rotate(90deg)';"
              onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='rotate(0deg)';">✕</button>
      <div style="display: flex; align-items: center; gap: 16px; color: white;">
        <div style="font-size: 48px;">⚡</div>
        <div>
          <h3 style="font-size: 24px; font-weight: 700; margin: 0 0 4px 0;">Fast Items Available</h3>
          <p style="font-size: 14px; opacity: 0.9; margin: 0;">These items can be prepared quickly</p>
        </div>
      </div>
    </div>

    <!-- Modal Body - Scrollable -->
    <div style="padding: 24px; overflow-y: auto; flex: 1;">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
        @if(isset($kitchenStatus['recommended_items']))
        @foreach($kitchenStatus['recommended_items'] as $item)
        <div class="quick-add-item" data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}" data-item-price="{{ $item->price }}" data-item-image="{{ $item->image ?? '' }}"
             style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 12px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(99,102,241,0.25)'; this.style.borderColor='#6366f1';"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'; this.style.borderColor='#e5e7eb';">
          <div style="position: relative; width: 100%; padding-top: 100%; border-radius: 12px; overflow: hidden; margin-bottom: 12px; background: #f3f4f6;">
            @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
            @else
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
              🍽️
            </div>
            @endif
            <div style="position: absolute; top: 8px; right: 8px; background: rgba(16, 185, 129, 0.95); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
              ~{{ $item->estimated_wait ?? 5 }} min
            </div>
          </div>
          <div style="font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->name }}</div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 16px; font-weight: 700; color: #6366f1;">RM {{ number_format($item->price, 2) }}</span>
            <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; line-height: 1; font-weight: 400; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);">+</div>
          </div>
        </div>
        @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Order Type Selection Modal (First Visit) -->
<div id="ordertype-selection-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 500px; border-radius: 24px; background: white;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 32px 24px 24px 24px; text-align: center; border-bottom: 1px solid #e5e7eb;">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 20px; margin-bottom: 16px;">
        <i class="fas fa-concierge-bell" style="font-size: 32px; color: white;"></i>
      </div>
      <h3 style="font-size: 26px; font-weight: 700; color: #1f2937; margin: 0 0 8px 0;">Welcome to Our Menu!</h3>
      <p style="font-size: 14px; color: #6b7280; margin: 0;">How would you like to enjoy your meal today?</p>
    </div>

    <!-- Modal Body -->
    <div style="padding: 32px 24px;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <!-- Dine In Option -->
        <label class="ordertype-selection-option" style="cursor: pointer; position: relative;">
          <input type="radio" name="initial-order-type" value="dine_in" checked style="position: absolute; opacity: 0;">
          <div class="ordertype-selection-card" data-type="dine_in" style="border: 3px solid #6366f1; border-radius: 20px; padding: 32px 20px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);">
            <div style="display: flex; justify-content: center; margin-bottom: 16px;">
              <i class="fas fa-utensils" style="font-size: 48px; color: #6366f1;"></i>
            </div>
            <div style="font-weight: 800; font-size: 18px; color: #1f2937; margin-bottom: 8px;">🍽️ Dine In</div>
            <div style="font-size: 13px; color: #6b7280; line-height: 1.4;">Enjoy your meal at our restaurant</div>
          </div>
        </label>

        <!-- Takeaway Option -->
        <label class="ordertype-selection-option" style="cursor: pointer; position: relative;">
          <input type="radio" name="initial-order-type" value="takeaway" style="position: absolute; opacity: 0;">
          <div class="ordertype-selection-card" data-type="takeaway" style="border: 3px solid #e5e7eb; border-radius: 20px; padding: 32px 20px; text-align: center; transition: all 0.3s; background: white;">
            <div style="display: flex; justify-content: center; margin-bottom: 16px;">
              <i class="fas fa-shopping-bag" style="font-size: 48px; color: #9ca3af;"></i>
            </div>
            <div style="font-weight: 800; font-size: 18px; color: #1f2937; margin-bottom: 8px;">🥡 Takeaway</div>
            <div style="font-size: 13px; color: #6b7280; line-height: 1.4;">Pick up and enjoy anywhere</div>
          </div>
        </label>
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 24px 24px 32px 24px;">
      <button id="confirm-ordertype-btn" style="width: 100%; padding: 16px; border-radius: 16px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 16px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);">
        Continue to Menu
      </button>
    </div>
  </div>
</div>

<!-- Change Order Type Modal (Floating Button) -->
<div id="change-ordertype-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 500px; border-radius: 24px; background: white;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
      <button id="change-ordertype-close-x" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
      <div style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; margin-bottom: 12px;">
          <i class="fas fa-exchange-alt" style="font-size: 28px; color: white;"></i>
        </div>
        <h3 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Change Order Type</h3>
        <p style="font-size: 13px; color: #6b7280; margin: 8px 0 0 0;">Select how you'd like to enjoy your meal</p>
      </div>
    </div>

    <!-- Modal Body -->
    <div style="padding: 28px 24px;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <!-- Dine In Option -->
        <label class="change-ordertype-option" style="cursor: pointer; position: relative;">
          <input type="radio" name="change-order-type" value="dine_in" checked style="position: absolute; opacity: 0;">
          <div class="change-ordertype-card" data-type="dine_in" style="border: 3px solid #6366f1; border-radius: 18px; padding: 28px 16px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);">
            <div style="display: flex; justify-content: center; margin-bottom: 12px;">
              <i class="fas fa-utensils" style="font-size: 40px; color: #6366f1;"></i>
            </div>
            <div style="font-weight: 800; font-size: 16px; color: #1f2937; margin-bottom: 6px;">🍽️ Dine In</div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1.3;">Eat at restaurant</div>
          </div>
        </label>

        <!-- Takeaway Option -->
        <label class="change-ordertype-option" style="cursor: pointer; position: relative;">
          <input type="radio" name="change-order-type" value="takeaway" style="position: absolute; opacity: 0;">
          <div class="change-ordertype-card" data-type="takeaway" style="border: 3px solid #e5e7eb; border-radius: 18px; padding: 28px 16px; text-align: center; transition: all 0.3s; background: white;">
            <div style="display: flex; justify-content: center; margin-bottom: 12px;">
              <i class="fas fa-shopping-bag" style="font-size: 40px; color: #9ca3af;"></i>
            </div>
            <div style="font-weight: 800; font-size: 16px; color: #1f2937; margin-bottom: 6px;">🥡 Takeaway</div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1.3;">Pick up order</div>
          </div>
        </label>
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 20px 24px 24px 24px; display: flex; gap: 12px;">
      <button id="cancel-change-ordertype-btn" style="flex: 1; padding: 14px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s;">Cancel</button>
      <button id="confirm-change-ordertype-btn" style="flex: 2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 15px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">Update Order Type</button>
    </div>
  </div>
</div>

<!-- Enhanced Modal for Add-Ons -->
<div id="addon-modal" class="addon-modal" aria-modal="true" role="dialog">
  <div class="modal-content">
    <div class="modal-body-scrollable">
      <h3 id="modal-title">Customize Your Order</h3>
      <div class="modal-item-info">
        <img id="modal-item-image" src="" alt="Item image" style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;">
        <div class="modal-item-name" id="modal-item-name">Item Name</div>
        <div class="modal-item-price" id="modal-item-price">RM 0.00</div>
        <div id="modal-item-description" style="font-size: 0.85rem; color: var(--text-2); margin-top: 0.5rem;"></div>
        <div style="display: flex; align-items: center; justify-content: center; margin: 1rem 0; gap: 1rem;">
          <span style="font-weight: 600; color: var(--text);">Quantity:</span>
          <div class="quantity-controls">
            <button class="qty-btn" id="modal-qty-decrease">-</button>
            <span class="quantity" id="modal-quantity">1</span>
            <button class="qty-btn" id="modal-qty-increase">+</button>
          </div>
        </div>
      </div>
      <div class="addon-options">
        <h4>Select Add-Ons:</h4>
        <!-- Add-on options will be dynamically inserted here -->
      </div>
    </div>
    <div class="modal-actions">
      <button id="add-to-cart-modal" class="btn">Proceed to Checkout</button>
      <button id="close-modal" class="btn">Cancel</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Pass menu data from server to JavaScript
  window.menuData = @json($categories ?? []);

  // QR Parameters: Handle table number and order type from QR scan
  @if(isset($orderType) && $orderType)
  // Order type is set (from QR or session), mark as selected to skip modal
  sessionStorage.setItem('selectedOrderType', '{{ $orderType }}');
  sessionStorage.setItem('orderTypeSelected', 'true');

  @if(isset($tableNumber) && $tableNumber)
  // Store table number for display
  sessionStorage.setItem('qrTableNumber', '{{ $tableNumber }}');
  @endif
  @endif

  // Promo Code Management
  let appliedPromotion = null;

  // Apply Promo Code
  async function applyPromoCode() {
    const promoCodeInput = document.getElementById('promo-code-input');
    const promoCode = promoCodeInput.value.trim().toUpperCase();
    const applyBtn = document.getElementById('apply-promo-btn');

    if (!promoCode) {
      showToast('Please enter a promo code', 'error');
      return;
    }

    // Show loading state
    applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
    applyBtn.disabled = true;

    try {
      // Use the cart manager to handle promo code application
      const result = await window.cartManager.applyPromoCode(promoCode);

      if (result.success) {
        // Refresh the cart to get updated data from server
        await updateCartDisplay();
        showToast('Promo code applied successfully!', 'success');
        promoCodeInput.value = '';
      } else {
        showToast(result.message || 'Invalid promo code', 'error');
      }
    } catch (error) {
      console.error('Error applying promo code:', error);
      showToast('Failed to apply promo code', 'error');
    } finally {
      applyBtn.innerHTML = 'Apply';
      applyBtn.disabled = false;
    }
  }

  // Display Applied Promo
  function displayAppliedPromo(data) {
    const inputContainer = document.getElementById('promo-input-container');
    const appliedContainer = document.getElementById('promo-applied-container');
    const promoCodeText = document.getElementById('promo-code-text');

    if (inputContainer) inputContainer.style.display = 'none';
    if (appliedContainer) appliedContainer.style.display = 'block';
    if (promoCodeText) promoCodeText.textContent = data.promotion.code || '';

    // Show discount row
    const discountRow = document.getElementById('promo-discount-row');
    const discountAmount = document.getElementById('promo-discount-amount');
    const discountValue = parseFloat(data.discount || 0);
    
    if (discountRow) discountRow.style.display = 'flex';
    if (discountAmount) discountAmount.textContent = `-RM ${discountValue.toFixed(2)}`;
  }

  // Remove Promo Code
  async function removePromoCode() {
    try {
      // Use the cart manager to handle promo code removal
      const result = await window.cartManager.removePromoCode();

      if (result.success) {
        // Show input container, hide applied container
        const inputContainer = document.getElementById('promo-input-container');
        const appliedContainer = document.getElementById('promo-applied-container');
        
        if (inputContainer) inputContainer.style.display = 'block';
        if (appliedContainer) appliedContainer.style.display = 'none';

        // Clear and show the input
        const promoInput = document.getElementById('promo-code-input');
        if (promoInput) {
          promoInput.value = '';
        }

        // Refresh the cart to get updated data from server
        await updateCartDisplay();
        showToast('Promo code removed', 'info');
      } else {
        showToast(result.message || 'Failed to remove promo code', 'error');
      }
    } catch (error) {
      console.error('Error removing promo code:', error);
      showToast('Failed to remove promo code', 'error');
    }
  }

  // Find Best Deal
  async function findBestDeal() {
    const findBestDealBtn = document.getElementById('findBestDealBtn');

    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length === 0) {
      showToast('Your cart is empty', 'error');
      return;
    }

    // Show loading state
    findBestDealBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finding Best Deal...';
    findBestDealBtn.disabled = true;

    try {
      const response = await fetch('{{ route("customer.promotions.best-promotion") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          cart_items: cart.map(item => ({
            menu_item_id: item.id,
            quantity: item.quantity,
            price: item.price
          }))
        })
      });

      const data = await response.json();

      if (data.success && data.promotion) {
        // Apply the best promotion code using cart manager
        const result = await window.cartManager.applyPromoCode(data.promotion.code);
        
        if (result.success) {
          // Refresh the cart to get updated data from server
          await updateCartDisplay();
          showToast(`Best deal found! Saving RM ${data.promotion.discount.toFixed(2)}`, 'success');
        } else {
          showToast(result.message || 'Failed to apply best promotion', 'error');
        }
      } else {
        showToast(data.message || 'No applicable promotions found', 'info');
      }
    } catch (error) {
      console.error('Error finding best deal:', error);
      showToast('Failed to find best deal', 'error');
    } finally {
      findBestDealBtn.innerHTML = '<i class="fas fa-magic"></i> Find Best Deal for Me';
      findBestDealBtn.disabled = false;
    }
  }

  // Update Cart Totals with Discount
  function updateCartTotals() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = 0;
    let total = subtotal;

    if (appliedPromotion && appliedPromotion.discount) {
      discount = parseFloat(appliedPromotion.discount);
      total = subtotal - discount;
    }

    // Update display
    const subtotalEl = document.getElementById('subtotal-amount');
    const discountEl = document.getElementById('promo-discount-amount');
    const discountRowEl = document.getElementById('promo-discount-row');
    const totalEl = document.getElementById('total-amount');

    if (subtotalEl) subtotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `RM ${total.toFixed(2)}`;

    if (discount > 0) {
      if (discountEl) {
        discountEl.textContent = `-RM ${discount.toFixed(2)}`;
      }
      if (discountRowEl) discountRowEl.style.display = 'flex';
    } else {
      if (discountRowEl) discountRowEl.style.display = 'none';
    }
  }

  // Toast Notification
  function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const colors = {
      success: '#10b981',
      error: '#ef4444',
      info: '#3b82f6'
    };

    toast.style.cssText = `
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: white;
      padding: 16px 24px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      border-left: 4px solid ${colors[type]};
      z-index: 10000;
      animation: slideIn 0.3s ease-out;
      max-width: 400px;
      font-weight: 600;
    `;

    toast.innerHTML = `
      <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"
           style="color: ${colors[type]}; font-size: 20px;"></i>
        <span>${message}</span>
      </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease-out';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Auto-uppercase promo code input
  document.getElementById('promoCodeInput')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
  });

  // Allow Enter key to apply promo
  document.getElementById('promoCodeInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      applyPromoCode();
    }
  });

  // Kitchen Banner Functions
  function dismissKitchenBanner() {
    const banner = document.getElementById('kitchenBanner');
    if (banner) {
      banner.style.animation = 'slideUp 0.3s ease-in';
      setTimeout(() => {
        banner.style.display = 'none';
      }, 300);
    }
  }

  // Optional: Auto-refresh kitchen status every 30 seconds
  const kitchenStatus = @json($kitchenStatus ?? null);
  if (kitchenStatus) {
    setInterval(async function() {
      try {
        const response = await fetch('{{ route("customer.menu.kitchen-status") }}');
        const data = await response.json();

        // Update banner if status changed significantly
        const hasRecommendations = data.recommended_types && data.recommended_types.length > 0;
        const hasBusy = data.busy_types && data.busy_types.length > 0;

        if ((hasRecommendations || hasBusy) && !document.getElementById('kitchenBanner')) {
          // Kitchen got busy - could reload page or inject banner dynamically
          console.log('Kitchen status updated:', data);
        }
      } catch (error) {
        console.error('Kitchen status update failed:', error);
      }
    }, 30000); // 30 seconds
  }
</script>

<style>
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideUp {
    from {
      opacity: 1;
      transform: translateY(0);
    }
    to {
      opacity: 0;
      transform: translateY(-20px);
    }
  }

  .kitchen-smart-banner button:hover {
    background: rgba(255,255,255,0.3) !important;
  }

  @media (max-width: 768px) {
    .kitchen-smart-banner {
      margin: 16px !important;
      padding: 16px !important;
    }

    .kitchen-smart-banner > div {
      flex-direction: column !important;
      text-align: center !important;
    }

    .kitchen-smart-banner > div > div:first-child {
      font-size: 36px !important;
    }

    .kitchen-smart-banner span {
      display: block !important;
      margin: 4px 0 !important;
    }
  }
</style>

<script>
// Define auth status BEFORE loading menu.js so it's available
window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

// Pass menu data from server to JavaScript
window.menuData = @json($categories);
</script>

<script src="{{ asset('js/customer/cart-manager.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/customer/cart-voucher.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/customer/menu.js') }}?v={{ time() }}"></script>
<script>
// Handle quick add item clicks from kitchen recommendations
document.addEventListener('click', async function(e) {
    const quickAddItem = e.target.closest('.quick-add-item');
    if (quickAddItem) {
        const itemId = parseInt(quickAddItem.dataset.itemId);
        const itemName = quickAddItem.dataset.itemName;
        const itemPrice = parseFloat(quickAddItem.dataset.itemPrice);
        const itemImage = quickAddItem.dataset.itemImage;

        // Try to find the item in the rendered menu first
        const menuCards = document.querySelectorAll('.food-card');
        let found = false;

        menuCards.forEach(card => {
            const cardTitle = card.querySelector('.food-title');
            if (cardTitle && cardTitle.textContent.trim() === itemName) {
                const addButton = card.querySelector('.btn-cart');
                if (addButton) {
                    addButton.click();
                    found = true;
                }
            }
        });

        // If not found in current view, open Add to Cart modal directly
        if (!found) {
            // Get item image URL
            const imageUrl = itemImage ? `/storage/${itemImage}` : '';

            // Open Add to Cart modal with item details (it will appear on top due to z-index: 1010)
            if (typeof window.showAddToCartModal === 'function') {
                window.showAddToCartModal(itemId, itemName, `RM ${itemPrice.toFixed(2)}`, '', imageUrl);
            } else {
                console.error('showAddToCartModal function not found');
                // Fallback: try to add directly
                try {
                    const response = await fetch('/customer/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            menu_item_id: itemId,
                            quantity: 1
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update cart badge
                        const cartBadge = document.getElementById('cartBadge');
                        if (cartBadge && data.cart_count !== undefined) {
                            cartBadge.textContent = data.cart_count;
                            cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
                        }

                        // Show success toast
                        if (typeof showToast === 'function') {
                            showToast('Added to cart!', 'success');
                        }
                    } else {
                        throw new Error(data.message || 'Failed to add item');
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    if (typeof showToast === 'function') {
                        showToast('Failed to add item to cart', 'error');
                    } else {
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    }
});

// Show All Recommendations Modal
function showAllRecommendationsModal() {
    const modal = document.getElementById('all-recommendations-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Attach event listener to "See All" button
document.getElementById('show-all-recommendations-btn')?.addEventListener('click', showAllRecommendationsModal);

// Close Recommendations Modal
function closeAllRecommendationsModal() {
    const modal = document.getElementById('all-recommendations-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Event listener for close button
document.getElementById('close-recommendations-modal')?.addEventListener('click', closeAllRecommendationsModal);

// Close modal when clicking outside
document.getElementById('all-recommendations-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAllRecommendationsModal();
    }
});

// Intercept add to cart and order now buttons for guests - redirect to login
document.addEventListener('click', function(e) {
    const orderBtn = e.target.closest('.order-now-btn');
    const addToCartBtn = e.target.closest('.add-to-cart-btn');
    const cartFab = e.target.closest('#cartFab');

    if ((orderBtn || addToCartBtn || cartFab) && !window.isAuthenticated) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href);
        return false;
    }
}, true); // Use capture phase to intercept before other handlers
</script>
{{-- food_and_drink.js is NOT loaded here - menu.js handles all functionality for the unified menu page --}}
@endsection
