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
<button class="cart-fab" id="cartFab" aria-label="Open cart">
  🛒
  <span class="cart-badge" id="cartBadge">0</span>
</button>

<!-- Floating Order Type Button -->
<button class="ordertype-fab" id="ordertypeFab" aria-label="Change order type" style="position: fixed; top: 24px; right: 24px; min-width: 140px; height: 56px; border-radius: 28px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; font-size: 15px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3); z-index: 999; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; transition: all 0.3s; opacity: 0;">
  <i class="fas fa-utensils" id="ordertypeIcon" style="font-size: 18px;"></i>
  <span id="ordertypeText">Dine In</span>
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
      <div class="cart-modal-total">
        <div>
          <div class="cart-total-label">Total Items</div>
          <div class="cart-total-label">Total Amount</div>
        </div>
        <div>
          <div style="font-weight: bold; font-size: 1.2rem;">x<span id="total-items">0</span></div>
          <div class="cart-total-amount" id="total-amount">RM 0.00</div>
        </div>
      </div>
      <button class="cart-modal-checkout">Proceed to Checkout</button>
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
</script>
<script src="{{ asset('js/customer/cart-manager.js') }}"></script>
<script src="{{ asset('js/customer/menu.js') }}"></script>
<script src="{{ asset('js/customer/food_and_drink.js') }}"></script>
@endsection
