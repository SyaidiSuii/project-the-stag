@extends('layouts.customer')

@section('title', 'Food Menu - The Stag SmartDine')

@section('styles')
<!-- Food-specific CSS -->
<link rel="stylesheet" href="{{ asset('css/customer-food.css') }}">
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
    <!-- Tabs will be dynamically inserted here -->
  </div>
  
  <!-- Dynamic Category Title -->
  <h1 class="category-title" id="categoryTitle">All</h1>
  <p class="no-results" id="noResults">No results found. Try another keyword.</p>
</div>

<div id="food-menu-container">
  <!-- Food items will be dynamically inserted here -->
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
<!-- Food-specific JavaScript -->
<script src="{{ asset('js/customer-food.js') }}"></script>

<script>
// Page-specific initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize food page functionality
    if (window.FoodPageApp) {
        window.foodPageApp = new window.FoodPageApp();
        window.foodPageApp.init();
    }
    
    console.log('<} Food page loaded successfully');
});
</script>
@endsection