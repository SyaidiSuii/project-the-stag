@extends('layouts.customer')

@section('title', 'Food Menu - The Stag SmartDine')

@section('styles')
<!-- Food-specific CSS -->
<link rel="stylesheet" href="{{ asset('css/customer/food.css') }}">
@endsection

@section('content')
<!-- Header Section (Sticky) -->
<div class="header-section">
  <!-- Search Bar -->
  <div class="search-bar-container" role="search">
    <div class="search-bar">
      <span class="search-icon" aria-hidden="true">🔎</span>
      <input type="text" class="search-input" placeholder="Search for food…" id="searchInput" aria-label="Search menu" />
      <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
    </div>
  </div>
  
  <!-- Category Tabs -->
  <div class="category-tabs" role="tablist" aria-label="Categories">
    <button class="tab" aria-current="page" data-category="all">All</button>
    @foreach($categories as $category)
      <button class="tab" data-category="{{ $category->id }}">{{ $category->name }}</button>
    @endforeach
  </div>
  
  <!-- Dynamic Category Title -->
  <h1 class="category-title" id="categoryTitle">All</h1>
  <p class="no-results" id="noResults">No results found. Try another keyword.</p>
</div>

<div id="food-menu-container">
  @if($featuredItems->count() > 0)
    <div class="category-section featured-section">
      <h2 class="subcategory-title">Featured Items</h2>
      <div class="food-grid">
        @foreach($featuredItems as $item)
          <div class="food-card featured" data-category="{{ $item->category_id }}">
            @if($item->is_featured)
              <div class="featured-badge">FEATURED</div>
            @endif
            <div class="food-image">
              @if($item->image_url)
                <img src="{{ $item->image_url }}" 
                     alt="{{ $item->name }}" 
                     style="width:100%; height:100%; object-fit:cover; border-radius: 15px;">
              @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:3rem;">🍽️</div>
              @endif
            </div>
            <div class="food-name">{{ $item->name }}</div>
            <div class="food-price">{{ $item->formatted_price }}</div>
            @if($item->description)
              <div class="food-description">{{ Str::limit($item->description, 80) }}</div>
            @endif
            @if($item->rating_count > 0)
              <div class="food-rating">{{ $item->rating_display }}</div>
            @endif
            <div class="food-actions">
              <button class="btn btn-order" data-item-id="{{ $item->id }}">Order Now</button>
              <button class="btn btn-cart" data-item-id="{{ $item->id }}">Add to Cart</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @foreach($categories as $category)
    @if($category->menuItems->count() > 0)
      <div class="category-section" data-category="{{ $category->id }}">
        <h2 class="subcategory-title">{{ $category->name }}</h2>
        <div class="food-grid">
          @foreach($category->menuItems as $item)
            <div class="food-card" data-category="{{ $category->id }}">
              <div class="food-image">
                @if($item->image_url)
                  <img src="{{ $item->image_url }}" 
                       alt="{{ $item->name }}" 
                       style="width:100%; height:100%; object-fit:cover; border-radius: 15px;">
                @else
                  <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:3rem;">🍽️</div>
                @endif
              </div>
              <div class="food-name">{{ $item->name }}</div>
              <div class="food-price">{{ $item->formatted_price }}</div>
              @if($item->description)
                <div class="food-description">{{ Str::limit($item->description, 80) }}</div>
              @endif
              @if($item->rating_count > 0)
                <div class="food-rating">{{ $item->rating_display }}</div>
              @endif
              @if($item->hasAllergens())
                <div class="food-allergens">
                  <small>Allergens: {{ $item->allergens_string }}</small>
                </div>
              @endif
              <div class="food-actions">
                <button class="btn btn-order" data-item-id="{{ $item->id }}">Order Now</button>
                <button class="btn btn-cart" data-item-id="{{ $item->id }}">Add to Cart</button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  @endforeach

  @if($allMenuItems->count() == 0)
    <div class="no-items-message">
      <p>No menu items available at the moment. Please check back later!</p>
    </div>
  @endif
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

  <!-- Cart Checkout Modal - Beautiful Design -->
  <div id="payment-method-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
    <div class="modal-content" style="max-width: 550px; border-radius: 24px; background: white;">
      <!-- Modal Header -->
      <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
        <button id="cart-modal-close-x" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
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
                <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay at Restaurant</div>
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
        </div>
        <div class="addon-options">
          <h4>Select Add-Ons:</h4>
          <!-- Add-on options will be dynamically inserted here -->
        </div>
      </div>
      <div class="modal-actions">
        <button id="add-to-cart-modal" class="btn">Add to Cart</button>
        <button id="close-modal" class="btn">Cancel</button>
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
                <div style="font-weight: 700; font-size: 15px; color: #1f2937; margin-bottom: 4px;">Pay at Restaurant</div>
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

  <!-- Modern Confirmation Modal -->
  <div id="confirmation-modal" class="confirmation-modal" aria-modal="true" role="dialog">
    <div class="confirmation-modal-backdrop"></div>
    <div class="confirmation-modal-content">
      <div class="confirmation-modal-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h3 class="confirmation-modal-title">Are you sure?</h3>
      <p class="confirmation-modal-text">This action cannot be undone. Do you want to proceed?</p>
      <div class="confirmation-modal-actions">
        <button id="confirm-cancel-btn" class="btn">Cancel</button>
        <button id="confirm-action-btn" class="btn">Confirm</button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<!-- Cart Manager -->
<script src="{{ asset('js/customer/cart-manager.js') }}"></script>
<!-- Food-specific JavaScript -->
<script src="{{ asset('js/customer/food_and_drink.js') }}"></script>
<script>
  //   /* --- Animations --- */
  // @keyframes fadeInCard {
  //   from { opacity: 0; transform: translateY(20px); }
  //   to   { opacity: 1; transform: translateY(0); }
  // }

  // @keyframes bounce {
  //   0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  //   40% { transform: translateY(-10px); }
  //   60% { transform: translateY(-5px); }
  // }

  // /* Hover effect untuk cart item */
  // .cart-item:hover {
  //   background-color: rgba(0, 0, 0, 0.05);
  //   transition: background-color 0.2s ease-in-out;
  // }

  // --- UX Enhancements ---

  // Animate cards fade in bila render food/set meals
  function animateCards() {
    document.querySelectorAll('.menu-card').forEach((card, index) => {
      card.style.animation = `fadeInCard 0.3s ease forwards`;
      card.style.animationDelay = `${index * 0.05}s`; // delay ikut index
    });
  }

  // Bounce effect untuk FAB Cart bila item masuk cart
  function bounceFAB() {
    const fab = document.getElementById('cartFab');
    fab.classList.remove('bounce'); // reset kalau ada
    void fab.offsetWidth;           // reflow hack untuk restart animation
    fab.classList.add('bounce');
  }

  // /* FAB bounce trigger */
  // .bounce {
  //   animation: bounce 0.6s;
  // }
</script>
@endsection