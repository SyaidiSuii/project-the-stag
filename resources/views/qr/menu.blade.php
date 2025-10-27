@extends('layouts.qr')

@section('title', 'Menu - Table {{ $session->table->table_number }}')

@section('styles')
<style>
    :root {
        --brand: #6366f1;
        --brand-2: #5856eb;
        --accent: #ff6b35;
        --bg: #f8fafc;
        --card: #ffffff;
        --muted: #e2e8f0;
        --text: #1e293b;
        --text-2: #64748b;
        --text-3: #94a3b8;
        --radius: 20px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
        background: var(--bg);
        color: var(--text);
        line-height: 1.6;
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Search Bar */
    .search-bar-container {
        margin-bottom: 20px;
    }

    .search-bar {
        position: relative;
        background: white;
        border-radius: 25px;
        box-shadow: var(--shadow);
        border: 1px solid var(--muted);
        max-width: 600px;
        margin: 0 auto;
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-2);
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 45px;
        border: none;
        border-radius: 25px;
        font-size: 15px;
        outline: none;
    }

    .clear-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-3);
        display: none;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 30px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .category-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0 10px;
    }
    .tab {
        padding: 10px 16px;
        background: white;
        border: 2px solid var(--muted);
        border-radius: 20px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.3s ease;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .tab.active, .tab[aria-current="page"] {
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        color: white;
        box-shadow: var(--shadow);
        border-color: var(--brand);
    }
    .tab:hover {
        background: var(--bg);
        transform: translateY(-1px);
    }
    .tab.active:hover, .tab[aria-current="page"]:hover {
        background: linear-gradient(135deg, var(--brand-2), var(--brand));
    }

    /* Category Section */
    .category-section {
        margin-bottom: 40px;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .category-section.hidden {
        opacity: 0;
        transform: translateY(10px);
        pointer-events: none;
    }

    .subcategory-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text);
        border-left: 6px solid var(--accent);
        padding-left: 16px;
        margin: 30px 0 20px;
    }

    /* Menu Grid */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 20px;
    }

    @media (min-width: 640px) {
        .menu-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .menu-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1400px) {
        .menu-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Menu Item Card */
    .menu-item {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--muted);
    }

    .menu-item:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .item-image {
        height: 200px;
        background: linear-gradient(135deg, #f0f4ff, #e0e7ff);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        position: relative;
        overflow: hidden;
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-popular {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fbbf24;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .item-content {
        padding: 20px;
    }

    .item-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }

    .item-price {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--accent);
        margin-bottom: 10px;
    }

    .item-description {
        color: var(--text-2);
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .add-to-cart {
        display: flex;
        gap: 10px;
    }

    .quantity-control {
        display: flex;
        border: 2px solid var(--muted);
        border-radius: 12px;
        overflow: hidden;
    }

    .quantity-btn {
        width: 38px;
        height: 38px;
        background: var(--bg);
        border: none;
        font-size: 1.2rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        color: var(--text);
    }

    .quantity-btn:hover {
        background: var(--brand);
        color: white;
    }

    .quantity-input {
        width: 45px;
        height: 38px;
        text-align: center;
        border: none;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
    }

    .add-btn {
        flex: 1;
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 20px;
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    /* Cart FAB */
    .cart-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        color: white;
        font-size: 30px;
    }

    .cart-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6);
    }

    /* Cart bounce animation - copied from customer menu */
    .cart-fab.bounce {
        animation: bounce 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes bounce {
        0% { transform: scale(1); }
        40% { transform: scale(1.3); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }

    .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--accent);
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-2);
    }

    /* Modal styles from customer menu */
    .addon-modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 1002;
        backdrop-filter: blur(5px);
    }
    .modal-content {
        background: white;
        border-radius: 20px;
        width: min(420px, 90vw);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--muted);
        max-height: 85vh;
        display: flex;
        flex-direction: column;
    }
    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('content')
<!-- QR Session Info Banner -->
<div style="background: linear-gradient(135deg, #6366f1, #5856eb); color: white; padding: 20px; margin-bottom: 20px; border-radius: 16px; text-align: center; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);">
    <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;">Our Menu</h1>
    <p style="font-size: 1rem; opacity: 0.9;">Welcome to Table {{ $session->table->table_number }}</p>
</div>

<!-- Header Section -->
<div class="header-section">
  <!-- Search Bar -->
  <div class="search-bar-container" role="search">
    <div class="search-bar">
      <span class="search-icon" aria-hidden="true">🔎</span>
      <input type="text" class="search-input" placeholder="Search menu..." id="searchInput" aria-label="Search menu" />
      <button class="clear-btn" id="clearSearch" style="display: none;" aria-label="Clear search">✕</button>
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
          <button onclick="showAllRecommendationsModal()" style="margin-top: 12px; width: 100%; padding: 10px 16px; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.3); border-radius: 10px; color: white; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
              <span>See All {{ $kitchenStatus['recommended_items']->count() }} Fast Items</span>
              <i class="fas fa-arrow-right"></i>
          </button>
          @endif
          @endif
      </div>
  </div>
  @endif

  <!-- Popular Items Recommendations -->
  @if(isset($recommendedItems) && count($recommendedItems) > 0)
  <div class="recommendations-section" style="margin: 24px auto; max-width: 800px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
      <div style="font-size: 24px;">⭐</div>
      <div>
        <h2 style="font-size: 20px; font-weight: 700; color: #1f2937; margin: 0;">Popular Items</h2>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Customer favorites this week</p>
      </div>
    </div>
    <div class="recommendations-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;">
      @foreach(array_slice($recommendedItems->all(), 0, 4) as $item)
      <div class="recommendation-card" onclick="showAddToCartModal({{ $item->id }}, {{ json_encode($item->name) }}, {{ $item->price }}, {{ json_encode($item->image ?? '') }})"
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
          <div style="position: absolute; top: 8px; right: 8px; background: #fbbf24; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);">
            ⭐ Popular
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

  <!-- Menu Type Toggle (Same as Customer Menu) -->
  <div class="category-tabs">
    <button class="tab active" data-type="all" id="allMenuBtn">
      <i class="fas fa-list"></i> All Items
    </button>
    <button class="tab" data-type="food" id="foodMenuBtn">
      <i class="fas fa-utensils"></i> Food
    </button>
    <button class="tab" data-type="drink" id="drinksMenuBtn">
      <i class="fas fa-cocktail"></i> Drinks
    </button>
  </div>
</div>

<!-- Menu Items -->
<div class="menu-items-container" id="menuContainer">
  <div class="no-results" id="noResults" style="display: none;">
    <div class="no-results-icon">🔍</div>
    <div class="no-results-text">No items found</div>
    <div class="no-results-subtext">Try searching for something else</div>
  </div>

  <!-- Menu by Categories -->
  @foreach($categories as $category)
    @if(!in_array(strtolower($category->name), ['food', 'drink', 'set meal']))
    <div class="category-section" data-category-type="{{ strtolower($category->type) }}">
      <h2 class="subcategory-title">{{ $category->name }}</h2>
      <div class="menu-grid">
      @foreach($category->menuItems as $item)
      <div class="menu-item" data-item-name="{{ strtolower($item->name) }}" data-item-type="{{ strtolower($category->type) }}">
        <div class="item-image">
          @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" loading="lazy">
          @else
            <div style="font-size: 3.5rem;">🍽️</div>
          @endif

          <!-- Type Badge (NO ICON) -->
          <span class="item-type-badge" style="position: absolute; top: 12px; left: 12px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: white; font-size: 0.7rem; font-weight: 700; padding: 3px 6px; border-radius: 8px; z-index: 2; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);">
            @if(strtolower($category->type) === 'drink')
              DRINK
            @elseif(strtolower($category->type) === 'set-meal')
              SET MEAL
            @else
              FOOD
            @endif
          </span>

          @if($item->is_popular)
            <span class="badge-popular">⭐ Popular</span>
          @endif
        </div>

        <div class="item-content">
          <div class="item-name">{{ $item->name }}</div>
          <div class="item-price">RM {{ number_format($item->price, 2) }}</div>
          @if($item->description)
            <p class="item-description">{{ $item->description }}</p>
          @endif

          <div class="add-to-cart">
            <div class="quantity-control">
              <button type="button" class="quantity-btn" onclick="decreaseQty(this)">−</button>
              <input type="number" class="quantity-input" value="1" min="1" max="10" readonly>
              <button type="button" class="quantity-btn" onclick="increaseQty(this)">+</button>
            </div>
            <button type="button" class="add-btn" onclick="showAddModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->image ?? '' }}', this)">
              <i class="fas fa-plus"></i> Add
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
    @endif
  @endforeach
</div>

<!-- Cart Button (Fixed Bottom Right) -->
@php
    $cartItemCount = array_sum(array_column($cart, 'quantity'));
@endphp
<a href="{{ route('qr.cart', ['session' => $session->session_code]) }}" class="cart-fab" id="cartFab" aria-label="View cart" style="position: fixed; bottom: 24px; right: 24px; width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; font-size: 24px; cursor: pointer; box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4); z-index: 1000; display: flex; align-items: center; justify-content: center; text-decoration: none;">
  <i class="fas fa-shopping-cart"></i>
  <span class="cart-badge" id="cartBadge" style="display: {{ $cartItemCount > 0 ? 'flex' : 'none' }}; position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; width: 24px; height: 24px; border-radius: 50%; font-size: 12px; font-weight: 700; align-items: center; justify-content: center; border: 2px solid white;">{{ $cartItemCount }}</span>
</a>



<!-- Add to Cart Modal -->
<div id="add-to-cart-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 420px; border-radius: 24px; background: white;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 24px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
      <button id="close-add-modal" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;">✕</button>
      <div style="text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; margin-bottom: 12px;">
          <i class="fas fa-cart-plus" style="font-size: 28px; color: white;"></i>
        </div>
        <h3 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Add to Cart</h3>
      </div>
    </div>

    <div class="modal-body-scrollable" style="padding: 24px; max-height: calc(90vh - 200px); overflow-y: auto;">
      <!-- Item Info -->
      <div style="background: #f9fafb; border-radius: 16px; padding: 16px; margin-bottom: 20px;">
        <div style="display: flex; gap: 16px; align-items: center;">
          <img id="modal-item-image" src="" alt="Item" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; flex-shrink: 0;">
          <div style="flex: 1; min-width: 0;">
            <div id="modal-item-name" style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">Item Name</div>
            <div id="modal-item-price" style="font-size: 18px; font-weight: 700; color: #6366f1;">RM 0.00</div>
          </div>
        </div>
      </div>

      <!-- Quantity (Read-only display) -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Quantity:</label>
        <div style="display: flex; align-items: center; justify-content: center; padding: 12px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px;">
          <span id="modal-quantity-display" style="font-size: 20px; font-weight: 700; color: #1f2937;">1</span>
        </div>
      </div>

      <!-- Add-ons Section -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add-ons:</label>
        <div style="padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb;">
          <p style="font-size: 13px; color: #6b7280; margin: 0;">No add-ons available for this item</p>
        </div>
      </div>

      <!-- Special Instructions -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Special Instructions</label>
        <textarea id="modal-special-notes" placeholder="Any special requests or dietary requirements..." rows="3" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; resize: vertical; font-family: inherit; color: #1f2937;"></textarea>
      </div>

      <!-- Total Section -->
      <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 16px; padding: 20px; border: 2px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 16px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Total:</span>
          <span id="modal-total-amount" style="font-size: 28px; font-weight: 900; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">RM 0.00</span>
        </div>
      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div style="padding: 20px 24px 24px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px;">
      <button id="modal-cancel-btn" style="flex: 1; padding: 14px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s;">Cancel</button>
      <button id="modal-confirm-btn" style="flex: 2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); font-size: 15px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">ADD TO CART</button>
    </div>
  </div>
</div>

<!-- All Recommendations Modal -->
<div id="all-recommendations-modal" class="addon-modal" style="display: none;" aria-modal="true" role="dialog">
  <div class="modal-content" style="max-width: 900px; border-radius: 24px; background: white; max-height: 90vh; display: flex; flex-direction: column;">
    <!-- Modal Header -->
    <div style="position: relative; padding: 24px; border-bottom: 2px solid #e5e7eb; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px 24px 0 0;">
      <button id="close-recommendations-modal" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">✕</button>
      <div style="display: flex; align-items: center; gap: 16px; color: white;">
        <div style="font-size: 48px;">⚡</div>
        <div>
          <h3 style="font-size: 24px; font-weight: 700; margin: 0 0 4px 0;">Fast Items Available</h3>
          <p style="font-size: 14px; opacity: 0.9; margin: 0;">These items can be prepared quickly</p>
        </div>
      </div>
    </div>

    <!-- Modal Body - Scrollable -->
    <div style="padding: 24px; overflow-y: auto; flex: 1; max-height: calc(90vh - 200px);">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
        @if(isset($kitchenStatus['recommended_items']))
        @foreach($kitchenStatus['recommended_items'] as $item)
        <div class="quick-add-item" data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}" data-item-price="{{ $item->price }}" data-item-image="{{ $item->image ?? '' }}"
             style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 12px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
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

@endsection

@section('scripts')
<script>
  // QR Session Data
  const sessionCode = '{{ $session->session_code }}';

  // Pass menu data from server to JavaScript
  window.menuData = @json($categories ?? []);

  // Inline Quantity Controls
  function decreaseQty(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value);
    if (value > 1) {
      input.value = value - 1;
    }
  }

  function increaseQty(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value);
    if (value < 10) {
      input.value = value + 1;
    }
  }

  // Show Add to Cart Modal
  let currentItemData = {};

  function showAddModal(itemId, itemName, itemPrice, itemImage, button) {
    const input = button.parentElement.parentElement.querySelector('.quantity-input');
    const quantity = parseInt(input.value);

    // Store item data
    currentItemData = {
      id: itemId,
      name: itemName,
      price: itemPrice,
      image: itemImage,
      quantity: quantity,
      inputElement: input
    };

    // Fill modal
    document.getElementById('modal-item-name').textContent = itemName;
    document.getElementById('modal-item-price').textContent = 'RM ' + itemPrice.toFixed(2);
    document.getElementById('modal-quantity-display').textContent = quantity;
    document.getElementById('modal-total-amount').textContent = 'RM ' + (itemPrice * quantity).toFixed(2);

    const imgEl = document.getElementById('modal-item-image');
    if (itemImage) {
      imgEl.src = '{{ asset("storage") }}/' + itemImage;
      imgEl.style.display = 'block';
    } else {
      imgEl.style.display = 'none';
    }

    // Show modal
    const modal = document.getElementById('add-to-cart-modal');
    modal.style.display = 'flex';
    // Don't prevent body scroll
  }

  // Close modal
  function closeAddModal() {
    const modal = document.getElementById('add-to-cart-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.getElementById('modal-special-notes').value = '';
  }

  document.getElementById('close-add-modal')?.addEventListener('click', closeAddModal);
  document.getElementById('modal-cancel-btn')?.addEventListener('click', closeAddModal);

  // Add to cart from modal
  document.getElementById('modal-confirm-btn')?.addEventListener('click', function() {
    const notes = document.getElementById('modal-special-notes').value;

    fetch('{{ route("qr.cart.add") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        session_code: sessionCode,
        menu_item_id: currentItemData.id,
        quantity: currentItemData.quantity,
        notes: notes
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Reset quantity to 1
        currentItemData.inputElement.value = 1;

        // Update cart badge with animation (ONLY animation here)
        updateCartBadge(data.cartCount || 0, false);

        // Close modal
        closeAddModal();

        // Show success message
        showToast(`Added ${currentItemData.quantity}x ${currentItemData.name} to cart!`, 'success');
      } else {
        showToast(data.error || 'Failed to add to cart', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('An error occurred', 'error');
    });
  });

  function updateCartBadge(count, skipAnimation = false) {
    console.log('updateCartBadge called:', count, 'skipAnimation:', skipAnimation);
    const badge = document.getElementById('cartBadge');
    const cartFab = document.getElementById('cartFab');

    if (!badge || !cartFab) {
      console.error('Badge or CartFab not found!');
      return;
    }

    // Update badge text
    badge.textContent = count;

    // Show or hide badge based on count
    if (count > 0) {
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }

    // Add bounce animation only when explicitly adding items
    if (!skipAnimation && count > 0) {
      console.log('Adding bounce animation to cart!');

      // Remove existing animation class if present
      cartFab.classList.remove('bounce');

      // Force reflow to restart animation
      void cartFab.offsetWidth;

      // Add animation class
      cartFab.classList.add('bounce');
      console.log('Bounce class added, classList:', cartFab.classList.toString());

      // Remove after animation completes
      setTimeout(() => {
        cartFab.classList.remove('bounce');
        console.log('Bounce class removed');
      }, 600);
    }
  }

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

            // Open Add to Cart modal with item details
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
                    } else if (typeof Toast !== 'undefined') {
                        Toast.error('Error', error.message || 'Failed to add item to cart');
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

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize cart count on page load
function loadCartCount(skipAnimation = true) {
    fetch('{{ route("qr.cart") }}?session=' + sessionCode, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.cart) {
            const count = data.cart.reduce((sum, item) => sum + item.quantity, 0);
            updateCartBadge(count, skipAnimation);
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
    });
}

// Don't load on initial page load - badge is server-rendered with correct count

document.addEventListener('DOMContentLoaded', function() {

    // Filter tabs functionality
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');

            // Get filter type
            const type = this.dataset.type;

            // Show/hide category sections based on type
            const sections = document.querySelectorAll('.category-section');
            sections.forEach(section => {
                const categoryType = section.dataset.categoryType;

                if (type === 'all') {
                    section.style.display = '';
                } else if (categoryType === type) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        // Show/hide clear button
        if (searchTerm) {
            clearSearch.style.display = 'block';
        } else {
            clearSearch.style.display = 'none';
        }

        // Filter items
        const items = document.querySelectorAll('.menu-item');
        const sections = document.querySelectorAll('.category-section');
        let hasResults = false;

        sections.forEach(section => {
            const sectionItems = section.querySelectorAll('.menu-item');
            let sectionHasVisibleItems = false;

            sectionItems.forEach(item => {
                const itemName = item.dataset.itemName || '';

                if (searchTerm === '' || itemName.includes(searchTerm)) {
                    item.style.display = '';
                    sectionHasVisibleItems = true;
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide section based on visible items
            if (sectionHasVisibleItems) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (searchTerm && !hasResults) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    });

    // Clear search
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });
});

// Load cart count when page becomes visible (browser back button)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        loadCartCount();
    }
});

// Load cart count when navigating back with popstate
window.addEventListener('popstate', function() {
    loadCartCount();
});

// Load cart count when page is shown from cache (bfcache)
window.addEventListener('pageshow', function(event) {
    // Always reload on pageshow for better reliability
    loadCartCount();

    // Show welcome back message if coming from bfcache (browser back)
    if (event.persisted) {
        setTimeout(() => {
            showToast('Welcome back! Continue browsing our menu', 'success');
        }, 300);
    }
});

// Periodic check as fallback (every 10 seconds when page is visible)
setInterval(function() {
    if (!document.hidden) {
        loadCartCount();
    }
}, 10000);
</script>
@endsection
