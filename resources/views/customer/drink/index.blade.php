@extends('layouts.customer')

@section('title', 'Drink Menu - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/drink.css') }}">
@endsection

@section('content')
<!-- Header Section (Sticky) -->
<div class="header-section">
  <!-- Search Bar -->
  <div class="search-bar-container" role="search">
    <div class="search-bar">
      <span class="search-icon" aria-hidden="true">🔎</span>
      <input type="text" class="search-input" placeholder="Search for drink…" id="searchInput" aria-label="Search menu" />
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

<div id="drink-menu-container">
  @if($featuredItems->count() > 0)
    <div class="category-section featured-section">
      <h2 class="subcategory-title">Featured Items</h2>
      <div class="drink-grid">
        @foreach($featuredItems as $item)
          <div class="drink-card featured" data-category="{{ $item->category_id }}">
            @if($item->is_featured)
              <div class="featured-badge">FEATURED</div>
            @endif
            <div class="drink-image">
              <img src="{{ $item->image_url ? asset($item->image_url) : asset('images/drink/placeholder.jpg') }}" 
                   alt="{{ $item->name }}" 
                   style="width:100%; height:100%; object-fit:cover; border-radius: 15px;">
            </div>
            <div class="drink-name">{{ $item->name }}</div>
            <div class="drink-price">{{ $item->formatted_price }}</div>
            @if($item->description)
              <div class="drink-description">{{ Str::limit($item->description, 80) }}</div>
            @endif
            @if($item->rating_count > 0)
              <div class="drink-rating">{{ $item->rating_display }}</div>
            @endif
            <div class="drink-actions">
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
        <div class="drink-grid">
          @foreach($category->menuItems as $item)
            <div class="drink-card" data-category="{{ $category->id }}">
              <div class="drink-image">
                <img src="{{ $item->image_url ? asset($item->image_url) : asset('images/drink/placeholder.jpg') }}" 
                     alt="{{ $item->name }}" 
                     style="width:100%; height:100%; object-fit:cover; border-radius: 15px;">
              </div>
              <div class="drink-name">{{ $item->name }}</div>
              <div class="drink-price">{{ $item->formatted_price }}</div>
              @if($item->description)
                <div class="drink-description">{{ Str::limit($item->description, 80) }}</div>
              @endif
              @if($item->rating_count > 0)
                <div class="drink-rating">{{ $item->rating_display }}</div>
              @endif
              @if($item->hasAllergens())
                <div class="drink-allergens">
                  <small>Allergens: {{ $item->allergens_string }}</small>
                </div>
              @endif
              <div class="drink-actions">
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
<button class="cart-fab" id="cartFab" aria-label="Open cart">
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
  <!-- Order Now Modal -->
  <div id="order-modal" class="addon-modal" aria-modal="true" role="dialog">
    <div class="modal-content">
      <div class="modal-body-scrollable">
        <h3 id="order-modal-title">🍽️ Order Details</h3>
        <div class="modal-item-info">
          <img id="order-item-image" src="" alt="Item image" style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;">
          <div class="modal-item-name" id="order-item-name">Item Name</div>
          <div class="modal-item-price" id="order-item-price">RM 0.00</div>
          <div id="order-item-description" style="font-size: 0.85rem; color: var(--text-2); margin-top: 0.5rem;"></div>
        </div>
        
        <div class="addon-options">
          <h4>Quantity:</h4>
          <div class="quantity-controls">
            <button class="qty-btn" id="order-qty-minus">−</button>
            <span class="quantity" id="order-quantity">1</span>
            <button class="qty-btn" id="order-qty-plus">+</button>
          </div>
        </div>

        <div class="addon-options">
          <h4>Special Instructions:</h4>
          <textarea id="order-notes" placeholder="Any special requests or notes for this order..." rows="3" style="width: 100%; padding: 0.8rem; border: 2px solid var(--muted); border-radius: 12px; font-size: 0.85rem; resize: vertical; min-height: 60px; font-family: inherit;"></textarea>
        </div>

        <div class="modal-item-info" style="text-align: center;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total:</span>
            <span id="order-total-amount" style="font-size: 1.2rem; font-weight: 900; color: var(--accent);">RM 0.00</span>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button id="order-confirm-btn" class="btn">Place Order</button>
        <button id="order-cancel-btn" class="btn">Cancel</button>
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
<script src="{{ asset('js/customer/food_and_drink.js') }}"></script>
<script>
    /* --- Animations --- */
  @keyframes fadeInCard {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
  }

  /* Hover effect untuk cart item */
  .cart-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
    transition: background-color 0.2s ease-in-out;
  }

  // --- UX Enhancements ---

  // Animate cards fade in bila render 
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

  /* FAB bounce trigger */
  .bounce {
    animation: bounce 0.6s;
  }
</script>
@endsection