@extends('layouts.customer')

@section('title', 'Fast Items - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/food.css') }}">
<link rel="stylesheet" href="{{ asset('css/customer/promotion-cart.css') }}">
<style>
/* Back Button - Fixed position at top left corner (like order-type-fab) */
.back-button {
    position: fixed;
    top: 85px; /* Below sidebar navbar on desktop */
    left: 115px; /* Aligned with main content area */
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    color: #000000;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.back-button:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    color: #000000;
}

.back-button i {
    font-size: 16px;
}

/* Wait Time Badge */
.wait-time-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
    z-index: 2;
}

/* Adjust main content padding to match menu page */
.main-content {
    padding: 20px;
}

/* Add spacing between elements */
.food-card .food-name {
    margin-top: 12px;
    margin-bottom: 8px;
}

.food-card .food-desc {
    margin-bottom: 12px;
}

.food-card .food-price {
    margin-bottom: 12px;
}

.food-card .food-actions {
    margin-top: 12px;
}

/* Responsive Styles */
/* Tablet (769px - 1199px) - 2 columns grid */
@media (max-width: 1199px) {
    .main-content {
        padding: 15px;
    }

    .food-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    /* Back Button - Tablet */
    .back-button {
        padding: 9px 16px;
        font-size: 12px;
        border-radius: 22px;
        top: 28px;
        left: 28px;
    }
    .back-button i {
        font-size: 15px;
    }
}

/* Mobile (481px - 768px) - 2 columns grid */
@media (max-width: 768px) {
    .main-content {
        padding: 10px;
    }

    .food-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    /* Back Button - Mobile */
    .back-button {
        padding: 8px 14px;
        font-size: 11px;
        border-radius: 20px;
        top: 18px;
        left: 18px;
        gap: 5px;
    }
    .back-button i {
        font-size: 14px;
    }
}

/* Small Mobile (< 480px) - 2 columns grid */
@media (max-width: 480px) {
    .main-content {
        padding: 10px;
    }

    .food-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    /* Back Button - Small Mobile */
    .back-button {
        padding: 7px 13px;
        font-size: 10.5px;
        border-radius: 19px;
        top: 16px;
        left: 16px;
        gap: 4px;
    }
    .back-button i {
        font-size: 13px;
    }
}

/* Order Type FAB - Mobile: Make smaller */
@media (max-width: 768px) {
  .ordertype-fab {
    top: 10px !important;
    right: 10px !important;
    min-width: 95px !important;
    height: 36px !important;
    border-radius: 18px !important;
    font-size: 10px !important;
    gap: 4px !important;
    padding: 0 10px !important;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25) !important;
  }
  .ordertype-fab i {
    font-size: 11px !important;
  }
}
</style>
@endsection

@section('content')
<div class="main-content">
    <!-- Fast Items Banner -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3); position: relative;">
        <a href="{{ route('customer.menu.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Menu</span>
        </a>
        <div style="text-align: center; color: white;">
            <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; justify-content: center; gap: 12px;">
                <i class="fas fa-bolt" style="color: #fbbf24;"></i>
                Fast Items Available
            </h1>
            <p style="font-size: 14px; opacity: 0.95; margin: 0;">
                These items can be prepared quickly from stations with low workload
            </p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar-container" role="search">
        <div class="search-bar">
            <span class="search-icon" aria-hidden="true">🔎</span>
            <input type="text" class="search-input" placeholder="Search fast items..." id="searchInput" aria-label="Search fast items" />
            <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
        </div>
    </div>

    <!-- Menu Type Toggle -->
    <div class="category-tabs">
        <button class="tab active" data-type="all" id="allItemsBtn">
            <i class="fas fa-list"></i> All Items
        </button>
        <button class="tab" data-type="food" id="foodBtn">
            <i class="fas fa-utensils"></i> Food
        </button>
        <button class="tab" data-type="drinks" id="drinksBtn">
            <i class="fas fa-cocktail"></i> Drinks
        </button>
    </div>

    <!-- Items Grouped by Category -->
    @if($recommendedItems->count() > 0)
        @foreach($itemsByCategory as $categoryId => $categoryItems)
            @php
                $category = $categories->firstWhere('id', $categoryId);
            @endphp
            @if($category)
            <div class="category-section" data-type="{{ strtolower($category->type ?? 'food') }}">
                <!-- Category Title with Orange Line -->
                <h2 class="subcategory-title">{{ $category->name }}</h2>

                <!-- Items Grid -->
                <div class="food-grid">
                    @foreach($categoryItems as $item)
                    <div class="food-card" data-id="{{ $item->id }}" data-category="{{ $item->category_id }}" data-type="{{ strtolower($item->category->type ?? 'food') }}" data-name="{{ strtolower($item->name) }}">
                        <div class="food-image" style="position: relative;">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
                            @else
                                @if($item->category && strpos(strtolower($item->category->type), 'drink') !== false)
                                    🍹
                                @else
                                    🍽️
                                @endif
                            @endif

                            <!-- Item Type Badge -->
                            <div style="position: absolute; top: 8px; left: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); z-index: 2;">
                                @php
                                    $categoryType = $item->category->type ?? 'food';
                                    if (strpos(strtolower($categoryType), 'drink') !== false) {
                                        echo 'DRINK';
                                    } elseif (strpos(strtolower($categoryType), 'set') !== false || strpos(strtolower($item->name), 'set') !== false) {
                                        echo 'SET MEAL';
                                    } else {
                                        echo 'FOOD';
                                    }
                                @endphp
                            </div>

                            <!-- Wait Time Badge -->
                            <div class="wait-time-badge">
                                <i class="fas fa-clock"></i> ~{{ $item->estimated_wait ?? 5 }} min
                            </div>
                        </div>

                        <div class="food-name">{{ $item->name }}</div>
                        @if($item->description)
                            <div class="food-desc">{{ $item->description }}</div>
                        @endif
                        <div class="food-price">RM {{ number_format($item->price, 2) }}</div>
                        <div class="food-actions">
                            <button type="button" class="btn btn-cart" onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->image ?? '' }}', '{{ addslashes($item->description ?? '') }}'); return false;">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    @else
        <div style="text-align: center; padding: 64px 32px; color: #6b7280;">
            <i class="fas fa-inbox" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;"></i>
            <h3 style="font-size: 20px; color: #374151; margin: 0 0 8px 0;">No Fast Items Available</h3>
            <p style="font-size: 14px; margin: 0;">All kitchen stations are currently busy. Please check back later or browse our full menu.</p>
        </div>
    @endif

    <p class="no-results" id="noResults" style="display: none; text-align: center; padding: 32px; color: #6b7280;">No results found. Try another keyword.</p>
</div>

<!-- Modern Floating Action Button (FAB) for Cart -->
<button class="cart-fab {{ Auth::check() ? '' : 'logged-out' }}" id="cartFab" aria-label="Open cart">
  🛒
  <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
</button>

@auth
<!-- Floating Order Type Button -->
<button class="ordertype-fab" id="ordertypeFab" aria-label="Change order type" style="position: fixed; top: 24px; right: 24px; min-width: 140px; height: 56px; border-radius: 28px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; font-size: 15px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3); z-index: 999; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; transition: all 0.3s; opacity: 0;">
  <i class="fas fa-utensils" id="ordertypeIcon" style="font-size: 18px;"></i>
  <span id="ordertypeText">Dine In</span>
</button>
@endauth

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

      <!-- Add-ons Section -->
      <div id="addtocart-addons-section" style="margin-bottom: 24px; display: none;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">
          <i class="fas fa-puzzle-piece" style="margin-right: 6px; color: #6366f1;"></i>
          Add-ons (Optional)
        </label>
        <div id="addtocart-addons-container" style="padding: 16px; border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb; display: grid; gap: 10px;">
          <!-- Add-ons checkboxes will be inserted here by JavaScript -->
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

@endsection

@section('scripts')
<script>
// Define auth status BEFORE loading menu.js so it's available
window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/customer/cart-manager.js') }}"></script>
<script src="{{ asset('js/customer/cart-voucher.js') }}"></script>
<script src="{{ asset('js/customer/menu.js') }}"></script>
<script>
// State management
let currentType = 'all';
let currentSearchTerm = '';

// Search functionality
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearch');

searchInput.addEventListener('input', function() {
    currentSearchTerm = this.value.toLowerCase().trim();

    // Show/hide clear button
    if (currentSearchTerm) {
        clearSearchBtn.classList.add('show');
    } else {
        clearSearchBtn.classList.remove('show');
    }

    filterItems();
});

clearSearchBtn.addEventListener('click', function() {
    searchInput.value = '';
    currentSearchTerm = '';
    this.classList.remove('show');
    filterItems();
});

// Type filter (All Items, Food, Drinks)
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        currentType = this.dataset.type;

        // Update active tab
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        filterItems();
    });
});

// Combined filter function
function filterItems() {
    const categorySections = document.querySelectorAll('.category-section');
    const noResults = document.getElementById('noResults');
    let totalVisibleCount = 0;

    categorySections.forEach(section => {
        const sectionType = (section.dataset.type || 'food').toLowerCase();

        // More flexible type matching - check if type contains the filter word
        let typeMatch = false;
        if (currentType === 'all') {
            typeMatch = true;
        } else if (currentType === 'food') {
            // Match 'food', 'foods', or anything that's not drinks
            typeMatch = sectionType.includes('food') || (!sectionType.includes('drink') && !sectionType.includes('beverage'));
        } else if (currentType === 'drinks') {
            // Match 'drink', 'drinks', 'beverage', etc.
            typeMatch = sectionType.includes('drink') || sectionType.includes('beverage');
        } else {
            typeMatch = sectionType === currentType;
        }

        if (!typeMatch) {
            section.style.display = 'none';
            return;
        }

        // Filter items within this section
        const items = section.querySelectorAll('.food-card');
        let sectionVisibleCount = 0;

        items.forEach(item => {
            const itemName = item.dataset.name || '';
            const searchMatch = !currentSearchTerm || itemName.includes(currentSearchTerm);

            if (searchMatch) {
                item.style.display = '';
                sectionVisibleCount++;
                totalVisibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Hide section if no items are visible
        if (sectionVisibleCount === 0) {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    });

    // Show/hide no results message
    if (totalVisibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Add to cart function (will use existing menu.js function if available)
function addToCart(id, name, price, image, description = '') {
    // Try to use the showAddToCartModal from menu.js
    if (typeof window.showAddToCartModal === 'function') {
        const imageUrl = image ? `/storage/${image}` : '';
        window.showAddToCartModal(id, name, `RM ${price.toFixed(2)}`, description, imageUrl);
    } else {
        if (typeof Toast !== 'undefined') {
            Toast.error('Unavailable', 'Add to cart functionality not available');
        } else {
            alert('Add to cart functionality not available');
        }
    }
}
</script>
@endsection
