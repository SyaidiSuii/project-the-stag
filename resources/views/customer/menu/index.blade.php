@extends('layouts.customer')

@section('title', 'Menu - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/food.css') }}">
@endsection

@section('content')
<!-- Header Section -->
<div class="header-section">
  <!-- Search Bar -->
  <div class="search-bar-container" role="search">
    <div class="search-bar">
      <span class="search-icon" aria-hidden="true">🔎</span>
      <input type="text" class="search-input" placeholder="Search menu..." id="searchInput" aria-label="Search menu" />
      <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
    </div>
  </div>

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

<!-- Modern Centered Cart Modal -->
<div class="cart-modal" id="cartModal">
  <div class="cart-modal-backdrop" id="cartModalBackdrop"></div>
  <div class="cart-modal-container">
    <div class="cart-modal-header">
      <button class="cart-modal-close" id="cartModalClose" aria-label="Close cart">✕</button>
      <h2 class="cart-modal-title">🛒 My Cart</h2>
    </div>
    <div class="cart-modal-content">
      <div class="cart-modal-toolbar">
        <div class="cart-modal-count">Items: <span id="cart-count">0</span></div>
        <button class="cart-modal-clear" id="clearAllBtn">Clear All</button>
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
      <!-- Promo Code Section -->
      <div class="promo-code-section" id="promoCodeSection" style="padding: 16px; background: #f9fafb; border-radius: 12px; margin-bottom: 16px;">
        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
          <input type="text" id="promoCodeInput" placeholder="Enter promo code"
                 style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-weight: 600; text-transform: uppercase; font-family: 'Courier New', monospace; letter-spacing: 1px; transition: all 0.3s;">
          <button id="applyPromoBtn" onclick="applyPromoCode()"
                  style="padding: 12px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.3s; font-size: 14px;">
            Apply
          </button>
        </div>

        <!-- Applied Promo Display -->
        <div id="appliedPromo" style="display: none; background: white; border-radius: 10px; padding: 12px; border-left: 4px solid #10b981;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
              <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 4px;">PROMO APPLIED</div>
              <div style="font-family: 'Courier New', monospace; font-weight: 700; color: #1f2937; font-size: 14px;" id="appliedPromoCode">—</div>
              <div style="font-size: 12px; color: #10b981; font-weight: 600; margin-top: 4px;" id="appliedPromoName">—</div>
            </div>
            <button onclick="removePromoCode()"
                    style="padding: 6px 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
              Remove
            </button>
          </div>
        </div>

        <!-- Find Best Deal Button -->
        <button id="findBestDealBtn" onclick="findBestDeal()"
                style="width: 100%; padding: 10px; background: white; border: 2px dashed #d1d5db; border-radius: 10px; color: #6b7280; font-weight: 600; cursor: pointer; margin-top: 8px; transition: all 0.3s; font-size: 13px;">
          <i class="fas fa-magic"></i> Find Best Deal for Me
        </button>
      </div>

      <!-- Cart Total -->
      <div class="cart-modal-total">
        <div>
          <div class="cart-total-label">Subtotal</div>
          <div class="cart-total-label" id="discountLabel" style="display: none; color: #10b981;">Discount</div>
          <div class="cart-total-label" style="font-weight: 700; color: #1f2937;">Total</div>
        </div>
        <div>
          <div class="cart-total-amount" id="subtotal-amount">RM 0.00</div>
          <div class="cart-total-amount" id="discount-amount" style="display: none; color: #10b981;">- RM 0.00</div>
          <div class="cart-total-amount" id="total-amount" style="font-size: 1.5rem; font-weight: 900;">RM 0.00</div>
        </div>
      </div>
      <button class="cart-modal-checkout">Proceed to Checkout</button>
    </div>
  </div>
</div>

<!-- Cart Checkout Modal - Beautiful Design -->
<div id="payment-method-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 550px; border-radius: 24px; background: white;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
      <button id="payment-modal-close-x" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
      <div style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; margin-bottom: 12px;">
          <i class="fas fa-shopping-cart" style="font-size: 28px; color: white;"></i>
        </div>
        <h3 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Order Details</h3>
      </div>
    </div>

    <div class="modal-body-scrollable" style="padding: 24px;">
      <!-- Order Type Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Order Type</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <label class="cart-order-type-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="cart-order-type" value="dine_in" checked style="position: absolute; opacity: 0;">
            <div class="cart-order-type-card" data-type="dine_in" style="border: 2px solid #6366f1; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);">
              <i class="fas fa-utensils" style="font-size: 32px; color: #6366f1; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">🍽️ Dine In</div>
              <div style="font-size: 12px; color: #6b7280;">Eat at restaurant</div>
            </div>
          </label>
          <label class="cart-order-type-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="cart-order-type" value="takeaway" style="position: absolute; opacity: 0;">
            <div class="cart-order-type-card" data-type="takeaway" style="border: 2px solid #e5e7eb; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: white;">
              <i class="fas fa-shopping-bag" style="font-size: 32px; color: #9ca3af; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">🥡 Takeaway</div>
              <div style="font-size: 12px; color: #6b7280;">Pick up order</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Payment Method Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Payment Method</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <label class="payment-method-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="cart-payment-method" value="online" checked style="position: absolute; opacity: 0;">
            <div class="payment-method-card cart-payment-card" data-method="online" style="border: 2px solid #6366f1; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);">
              <i class="fas fa-globe" style="font-size: 32px; color: #6366f1; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay Online</div>
              <div style="font-size: 12px; color: #6b7280;">Online Banking / E-Wallet</div>
            </div>
          </label>
          <label class="payment-method-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="cart-payment-method" value="counter" style="position: absolute; opacity: 0;">
            <div class="payment-method-card cart-payment-card" data-method="counter" style="border: 2px solid #e5e7eb; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: white;">
              <i class="fas fa-store" style="font-size: 32px; color: #9ca3af; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay at Counter</div>
              <div style="font-size: 12px; color: #6b7280;">Cash / Card</div>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 20px 24px 24px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px;">
      <button id="cancel-payment-method-btn" style="flex: 1; padding: 14px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s;">Cancel</button>
      <button id="confirm-payment-method-btn" style="flex: 2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 15px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">CONTINUE TO CHECKOUT</button>
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

      <!-- Order Type Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Order Type</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <label class="order-type-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="order-type" value="dine_in" checked style="position: absolute; opacity: 0;">
            <div class="order-type-card" data-type="dine_in" style="border: 2px solid #6366f1; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);">
              <i class="fas fa-utensils" style="font-size: 32px; color: #6366f1; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">🍽️ Dine In</div>
              <div style="font-size: 12px; color: #6b7280;">Eat at restaurant</div>
            </div>
          </label>
          <label class="order-type-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="order-type" value="takeaway" style="position: absolute; opacity: 0;">
            <div class="order-type-card" data-type="takeaway" style="border: 2px solid #e5e7eb; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: white;">
              <i class="fas fa-shopping-bag" style="font-size: 32px; color: #9ca3af; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">🥡 Takeaway</div>
              <div style="font-size: 12px; color: #6b7280;">Pick up order</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Special Instructions -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Special Instructions</label>
        <textarea id="order-notes" placeholder="Any special requests or dietary requirements..." rows="3" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; resize: vertical; font-family: inherit; color: #1f2937;"></textarea>
      </div>

      <!-- Payment Method Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Payment Method</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <label class="payment-method-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="payment-method" value="online" checked style="position: absolute; opacity: 0;">
            <div class="payment-method-card" data-method="online" style="border: 2px solid #6366f1; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);">
              <i class="fas fa-globe" style="font-size: 32px; color: #6366f1; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay Online</div>
              <div style="font-size: 12px; color: #6b7280;">Online Banking / E-Wallet</div>
            </div>
          </label>
          <label class="payment-method-option" style="position: relative; cursor: pointer;">
            <input type="radio" name="payment-method" value="counter" style="position: absolute; opacity: 0;">
            <div class="payment-method-card" data-method="counter" style="border: 2px solid #e5e7eb; border-radius: 16px; padding: 20px 16px; text-align: center; transition: all 0.3s; background: white;">
              <i class="fas fa-store" style="font-size: 32px; color: #9ca3af; display: block; margin-bottom: 8px;"></i>
              <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay at Counter</div>
              <div style="font-size: 12px; color: #6b7280;">Cash / Card</div>
            </div>
          </label>
        </div>
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

  // Promo Code Management
  let appliedPromotion = null;

  // Apply Promo Code
  async function applyPromoCode() {
    const promoCodeInput = document.getElementById('promoCodeInput');
    const promoCode = promoCodeInput.value.trim().toUpperCase();
    const applyBtn = document.getElementById('applyPromoBtn');

    if (!promoCode) {
      showToast('Please enter a promo code', 'error');
      return;
    }

    // Get cart items from localStorage or cart manager
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length === 0) {
      showToast('Your cart is empty', 'error');
      return;
    }

    // Show loading state
    applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
    applyBtn.disabled = true;

    try {
      const response = await fetch('{{ route("customer.promotions.apply-promo") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          promo_code: promoCode,
          cart_items: cart.map(item => ({
            menu_item_id: item.id,
            quantity: item.quantity,
            price: item.price
          }))
        })
      });

      const data = await response.json();

      if (data.success) {
        appliedPromotion = data;
        displayAppliedPromo(data);
        updateCartTotals();
        showToast('Promo code applied successfully!', 'success');
        promoCodeInput.value = '';
      } else {
        showToast(data.message || 'Invalid promo code', 'error');
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
    const appliedPromoDiv = document.getElementById('appliedPromo');
    const promoCodeSpan = document.getElementById('appliedPromoCode');
    const promoNameSpan = document.getElementById('appliedPromoName');
    const promoInput = document.getElementById('promoCodeInput');
    const applyBtn = document.getElementById('applyPromoBtn');

    appliedPromoDiv.style.display = 'block';
    promoCodeSpan.textContent = data.promotion.code || '';
    promoNameSpan.textContent = data.promotion.name || '';

    // Hide input and apply button
    promoInput.style.display = 'none';
    applyBtn.style.display = 'none';
  }

  // Remove Promo Code
  async function removePromoCode() {
    try {
      const response = await fetch('{{ route("customer.promotions.remove-promo") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });

      const data = await response.json();
      if (data.success) {
        appliedPromotion = null;

        // Hide applied promo display
        document.getElementById('appliedPromo').style.display = 'none';

        // Show input and button again
        const promoInput = document.getElementById('promoCodeInput');
        const applyBtn = document.getElementById('applyPromoBtn');
        promoInput.style.display = 'block';
        applyBtn.style.display = 'block';
        promoInput.value = '';

        updateCartTotals();
        showToast('Promo code removed', 'info');
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
        // Auto-apply the best promotion
        appliedPromotion = {
          success: true,
          discount: data.promotion.discount,
          promotion: {
            code: data.promotion.id,
            name: data.promotion.name
          }
        };
        displayAppliedPromo(appliedPromotion);
        updateCartTotals();
        showToast(`Best deal found! Saving RM ${data.promotion.discount.toFixed(2)}`, 'success');
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
    const discountEl = document.getElementById('discount-amount');
    const discountLabelEl = document.getElementById('discountLabel');
    const totalEl = document.getElementById('total-amount');

    if (subtotalEl) subtotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `RM ${total.toFixed(2)}`;

    if (discount > 0) {
      if (discountEl) {
        discountEl.textContent = `- RM ${discount.toFixed(2)}`;
        discountEl.style.display = 'block';
      }
      if (discountLabelEl) discountLabelEl.style.display = 'block';
    } else {
      if (discountEl) discountEl.style.display = 'none';
      if (discountLabelEl) discountLabelEl.style.display = 'none';
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
</script>
<script src="{{ asset('js/customer/cart-manager.js') }}"></script>
<script src="{{ asset('js/customer/menu.js') }}"></script>
<script src="{{ asset('js/customer/food_and_drink.js') }}"></script>
@endsection
