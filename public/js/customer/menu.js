// ===== Menu Page JavaScript =====

// Load menu data from window (set by Blade template)
const menuData = window.menuData || [];
let food = [], setMeals = [], drinks = [], allCategories = [];
let currentMenuType = 'all';

// Debug: Log the raw menuData from window
console.log('Debug [menu.js init]: window.menuData =', window.menuData);
console.log('Debug [menu.js init]: menuData length =', menuData ? menuData.length : 'null/undefined');

// Helper function to safely format price
function formatPrice(price) {
  const num = parseFloat(price);
  return isNaN(num) ? '0.00' : num.toFixed(2);
}

// Process database data into the expected format
if (menuData && menuData.length > 0) {
  console.log('Debug [menu.js]: Processing', menuData.length, 'categories');

  menuData.forEach((category, catIndex) => {
    console.log(`Debug [menu.js]: Category ${catIndex}: ${category.name}, type: ${category.type}, items: ${category.menu_items ? category.menu_items.length : 0}`);

    if (category.menu_items && category.menu_items.length > 0) {
      category.menu_items.forEach((item, itemIndex) => {
        // Debug first item of first category
        if (catIndex === 0 && itemIndex === 0) {
          console.log('Debug [menu.js]: First menu item raw data:', item);
          console.log('Debug [menu.js]: First menu item ID:', item.id, 'Type:', typeof item.id);
        }

        const menuItem = {
          id: item.id,
          name: item.name,
          description: item.description || '',
          price: parseFloat(item.price),
          category: category.name,
          image: item.image || null,
          is_available: item.availability !== undefined ? item.availability : item.is_available,
          preparation_time: item.preparation_time,
          stock_quantity: item.stock_quantity,
          // Item discount support
          has_discount: item.has_discount || false,
          discount_info: item.discount_info || null
        };

        // Debug first processed item
        if (catIndex === 0 && itemIndex === 0) {
          console.log('Debug [menu.js]: First menu item processed:', menuItem);
        }

        if (category.type === 'food') {
          food.push(menuItem);
        } else if (category.type === 'drink') {
          drinks.push(menuItem);
        } else if (category.type === 'set-meal') {
          setMeals.push(menuItem);
        }
      });
    }

    // Collect category names
    if (!allCategories.includes(category.name)) {
      allCategories.push(category.name);
    }
  });

  console.log('Debug [menu.js]: Processed - Food:', food.length, 'Drinks:', drinks.length, 'Set Meals:', setMeals.length);
  if (food.length > 0) {
    console.log('Debug [menu.js]: First food item after processing:', food[0]);
  }
}

allCategories.sort();

// Render menu type toggle functionality
function renderMenuTypeToggle() {
  const allBtn = document.getElementById('allMenuBtn');
  const foodBtn = document.getElementById('foodMenuBtn');
  const drinksBtn = document.getElementById('drinksMenuBtn');

  [allBtn, foodBtn, drinksBtn].forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      currentMenuType = btn.dataset.type;
      renderMenuItems();
      updateCategoryTitle();
    });
  });
}

// Render menu items
function renderMenuItems(searchTerm = '') {
  const container = document.getElementById('menu-container');
  if (!container) return;
  container.innerHTML = '';

  let itemsToShow = [];

  // Filter items based on menu type
  switch(currentMenuType) {
    case 'food':
      itemsToShow = [...food, ...setMeals];
      break;
    case 'drinks':
      itemsToShow = drinks;
      break;
    default: // 'all'
      itemsToShow = [...food, ...setMeals, ...drinks];
  }

  // Filter by search
  const lowerCaseSearch = searchTerm.toLowerCase();
  const filteredItems = itemsToShow.filter(item => {
    return item.name.toLowerCase().includes(lowerCaseSearch);
  });

  // Check if we have results
  if (filteredItems.length === 0) {
    document.getElementById('noResults').style.display = 'block';
    return;
  }
  document.getElementById('noResults').style.display = 'none';

  // Group by category
  const categories = {};
  filteredItems.forEach(item => {
    if (!categories[item.category]) {
      categories[item.category] = [];
    }
    categories[item.category].push(item);
  });

  // Render sections
  for (const catName in categories) {
    const section = document.createElement('div');
    section.className = 'category-section';
    section.dataset.category = catName;

    const title = document.createElement('h2');
    title.className = 'subcategory-title';
    title.textContent = catName;
    section.appendChild(title);

    const grid = document.createElement('div');
    grid.className = 'food-grid';

    categories[catName].forEach(item => {
      const card = createMenuCard(item);
      grid.appendChild(card);
    });

    section.appendChild(grid);
    container.appendChild(section);
  }
}

// Create menu card element
function createMenuCard(item) {
  const card = document.createElement('div');
  card.className = 'food-card';
  if (item.outOfStock) card.classList.add('out-of-stock-card');

  // Debug: Log item to check if ID exists
  if (!item.id) {
    console.error('Menu item missing ID:', item);
  }

  card.dataset.id = item.id;
  card.dataset.name = item.name;
  card.dataset.price = item.price;

  // Determine item type and styling
  const isFood = food.some(f => f.id === item.id) || setMeals.some(s => s.id === item.id);
  const isDrink = drinks.some(d => d.id === item.id);
  const isSetMeal = setMeals.some(s => s.id === item.id);

  let imageSrc = item.image;
  if (imageSrc && !imageSrc.startsWith('http') && !imageSrc.startsWith('data:')) {
    if (imageSrc.startsWith('storage/')) {
      imageSrc = `/${imageSrc}`;
    } else if (!imageSrc.startsWith('/')) {
      imageSrc = `/storage/${imageSrc}`;
    }
  }

  const imageHtml = imageSrc
    ? `<img src="${imageSrc}" alt="${item.name}" style="width:100%; height:100%; object-fit:cover; border-radius: 15px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
       <div class="fallback-icon" style="display:none; width:100%; height:100%; align-items:center; justify-content:center; font-size:3rem; background:var(--muted); border-radius: 15px;">${isDrink ? '🍹' : '🍽️'}</div>`
    : `<div class="fallback-icon" style="display:flex; width:100%; height:100%; align-items:center; justify-content:center; font-size:3rem; background:var(--muted); border-radius: 15px;">${isDrink ? '🍹' : '🍽️'}</div>`;

  let description = item.description || '';

  let actionsHtml;
  if (!item.is_available) {
    actionsHtml = `<div class="food-actions">
        <button class="btn btn-maintenance" disabled><span class="spanner-icon">🔧</span> This item is under maintenance</button>
    </div>`;
  } else {
    actionsHtml = `<div class="food-actions">
        <button class="btn btn-order">Order Now</button>
        <button class="btn btn-cart">Add to Cart</button>
    </div>`;
  }

  // Build price HTML (with discount support)
  let priceHtml;
  if (item.has_discount && item.discount_info) {
    const discount = item.discount_info;
    const discountLabel = discount.discount_type === 'percentage'
      ? `${discount.discount_value}% OFF`
      : `RM ${formatPrice(discount.discount_value)} OFF`;

    priceHtml = `
      <div class="food-price-container">
        <div class="discount-badge">${discountLabel}</div>
        <div class="food-price original-price">RM ${formatPrice(discount.original_price)}</div>
        <div class="food-price discounted-price">RM ${formatPrice(discount.discounted_price)}</div>
      </div>
    `;
  } else {
    priceHtml = `<div class="food-price">RM ${formatPrice(item.price)}</div>`;
  }

  let cardContent = `
    <div class="food-image ${isDrink ? 'drink-style' : ''}">${imageHtml}</div>
    <div class="food-name">${item.name}</div>
    ${priceHtml}
    ${description ? `<div class="food-description">${description}</div>` : ''}
    ${actionsHtml}
  `;

  // Add badges
  if (currentMenuType === 'all') {
    // In "All" view: Set meals show "SET MEAL", drinks show "DRINK", regular food shows "FOOD"
    let typeText;
    if (isSetMeal) {
      typeText = 'SET MEAL';
    } else if (isDrink) {
      typeText = 'DRINK';
    } else {
      typeText = 'FOOD';
    }
    cardContent = `<div class="item-type-badge">${typeText}</div>` + cardContent;
  } else if (isSetMeal && currentMenuType === 'food') {
    // In "Food" view only: Show SET MEAL badge for set meals
    cardContent = `<div class="set-meal-badge">SET MEAL</div>` + cardContent;
  }

  if (item.outOfStock) {
    cardContent += `
      <div class="out-of-stock-overlay">
          <div style="font-size: 2rem;">😔</div>
          <div>Out of Stock</div>
      </div>
    `;
  }

  card.innerHTML = cardContent;

  if (item.outOfStock) {
    card.querySelectorAll('.btn').forEach(btn => {
      btn.disabled = true;
      btn.style.opacity = 0.6;
      btn.style.cursor = 'not-allowed';
    });
  }

  return card;
}

// Update category title
function updateCategoryTitle() {
  let title = 'Menu';

  if (currentMenuType !== 'all') {
    const typeText = currentMenuType.charAt(0).toUpperCase() + currentMenuType.slice(1);
    title = typeText;
  }

  document.getElementById('categoryTitle').textContent = title;
}

// Initialize search functionality
function initializeSearch() {
  const searchInput = document.getElementById('searchInput');
  const clearSearch = document.getElementById('clearSearch');
  if (!searchInput) return;

  const performSearch = () => {
    renderMenuItems(searchInput.value);
    if (clearSearch) clearSearch.style.display = searchInput.value ? 'block' : 'none';
  };

  searchInput.addEventListener('input', performSearch);
  if (clearSearch) {
    clearSearch.addEventListener('click', () => {
      searchInput.value = '';
      performSearch();
    });
  }
}

// Initialize menu data and render items
function initializeMenuData() {
  renderMenuTypeToggle();
  renderMenuItems();
}

// Initialize cart manager
window.cartManager = new CartManager();

// Cart FAB elements
const cartFab = document.getElementById('cartFab');
const cartBadge = document.getElementById('cartBadge');

// Update cart badge
async function updateCartBadge() {
  const cartData = await window.cartManager.getCart();

  // Handle both formats: array (localStorage) or object (database)
  const cart = Array.isArray(cartData) ? cartData : (cartData.cart || []);
  const totalItems = Array.isArray(cartData)
    ? cart.reduce((sum, item) => sum + item.quantity, 0)
    : (cartData.count || 0);

  console.log('Debug [updateCartBadge]: Cart data:', cartData);
  console.log('Debug [updateCartBadge]: Total quantity:', totalItems);

  cartBadge.textContent = totalItems;
  cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
}

// Add to cart and order now button handlers
document.addEventListener('click', async function(e) {
  // Add to Cart button - now shows modal instead of direct add
  if (e.target.classList.contains('btn-cart')) {
    const itemCard = e.target.closest('.food-card');
    if (!itemCard) return;

    const itemId = parseInt(itemCard.dataset.id);
    const itemName = itemCard.querySelector('.food-name').textContent;
    const itemDescription = itemCard.querySelector('.food-description')?.textContent || '';
    const imgElement = itemCard.querySelector('.food-image img');
    const itemImage = imgElement ? imgElement.src : null;

    // Find the item from arrays to get actual price (including discount)
    const allItems = [...food, ...drinks, ...setMeals];
    const itemData = allItems.find(item => item.id === itemId);

    // Use discounted price if available, otherwise use original price
    let itemPriceText;
    if (itemData && itemData.has_discount && itemData.discount_info) {
      itemPriceText = `RM ${itemData.discount_info.discounted_price.toFixed(2)}`;
    } else if (itemData) {
      itemPriceText = `RM ${itemData.price.toFixed(2)}`;
    } else {
      // Fallback: read from card
      itemPriceText = itemCard.querySelector('.food-price.discounted-price')?.textContent ||
                      itemCard.querySelector('.food-price')?.textContent || 'RM 0.00';
    }

    // Show add to cart modal (from food_and_drink.js)
    if (typeof window.showAddToCartModal === 'function') {
      window.showAddToCartModal(itemId, itemName, itemPriceText, itemDescription, itemImage);
    } else {
      console.error('showAddToCartModal function not found');
    }
  }

  // Order Now button
  if (e.target.classList.contains('btn-order')) {
    const itemCard = e.target.closest('.food-card');
    if (!itemCard) return;

    const itemId = parseInt(itemCard.dataset.id);
    const itemName = itemCard.querySelector('.food-name').textContent;
    const imgElement = itemCard.querySelector('.food-image img');
    const itemImage = imgElement ? imgElement.src : null;
    const itemDescription = itemCard.querySelector('.food-description')?.textContent || '';

    // Debug: Check if itemId exists
    console.log('Debug [Order Now clicked]: itemId =', itemId, 'itemCard.dataset =', itemCard.dataset);

    if (!itemId || isNaN(itemId)) {
      console.error('Error: Menu item ID is missing! Cannot proceed with order.');
      alert('Sorry, there was an error loading this item. Please refresh the page and try again.');
      return;
    }

    // Find the item from arrays to get actual price (including discount)
    const allItems = [...food, ...drinks, ...setMeals];
    const itemData = allItems.find(item => item.id === itemId);

    // Use discounted price if available, otherwise use original price
    let itemPriceText;
    if (itemData && itemData.has_discount && itemData.discount_info) {
      itemPriceText = `RM ${itemData.discount_info.discounted_price.toFixed(2)}`;
    } else if (itemData) {
      itemPriceText = `RM ${itemData.price.toFixed(2)}`;
    } else {
      // Fallback: read from card
      itemPriceText = itemCard.querySelector('.food-price.discounted-price')?.textContent ||
                      itemCard.querySelector('.food-price')?.textContent || 'RM 0.00';
    }

    // Show order modal
    showOrderModal(itemId, itemName, itemPriceText, itemDescription, itemImage);
  }
});

// Cart FAB click handler
if (cartFab) {
  cartFab.addEventListener('click', function() {
    showCartModal();
  });
}

// Cart Modal Functions
async function showCartModal() {
  const modal = document.getElementById('cartModal');
  if (modal) {
    await updateCartDisplay();
    await loadExistingPromo(); // Load promo code status
    modal.classList.add('open');
  }
}

function hideCartModal() {
  const modal = document.getElementById('cartModal');
  if (modal) {
    modal.classList.remove('open');
  }
}

async function updateCartDisplay() {
  const cartItemsContainer = document.getElementById('cart-items');
  const cartCount = document.getElementById('cart-count');
  const totalItems = document.getElementById('total-items');
  const totalAmount = document.getElementById('total-amount');
  const checkoutButton = document.querySelector('.cart-modal-checkout');

  if (!cartItemsContainer) return;

  const cartData = await window.cartManager.getCart();

  // DEBUG: Log full cart data structure
  console.log('🔍 Debug [updateCartDisplay]: Full cartData:', cartData);
  console.log('🔍 Debug [updateCartDisplay]: promotion_groups:', cartData.promotion_groups);
  console.log('🔍 Debug [updateCartDisplay]: regular_items:', cartData.regular_items);

  // Handle both formats: array (localStorage) or object with cart property (database)
  let cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);
  const unavailableCount = cartData.unavailable_count || 0;
  const availableTotal = cartData.available_total || window.cartManager.getTotalPrice(cartItems);

  console.log('Debug [updateCartDisplay]: Cart items:', cartItems);
  console.log('Debug [updateCartDisplay]: Unavailable count:', unavailableCount);
  console.log('Debug [updateCartDisplay]: Available total:', availableTotal);

  const totalCartItems = window.cartManager.getTotalQuantity(cartItems);
  const availableItemsCount = cartItems.filter(item => item.is_available !== false).length;

  if (cartCount) cartCount.textContent = totalCartItems;
  if (totalItems) totalItems.textContent = totalCartItems;

  // Use available_total from API to exclude unavailable items
  const subtotal = availableTotal;
  const promoDiscount = parseFloat(cartData.promo_discount) || 0;
  const finalTotal = cartData.final_total !== undefined ? cartData.final_total : (subtotal - promoDiscount);

  console.log('Cart totals:', {
    subtotal,
    promoDiscount,
    finalTotal,
    cartData_final_total: cartData.final_total
  });

  // Update subtotal display
  const subtotalElement = document.getElementById('subtotal-amount');
  if (subtotalElement) subtotalElement.textContent = `RM ${subtotal.toFixed(2)}`;

  // Update promo discount display
  if (promoDiscount > 0) {
    const promoDiscountRow = document.getElementById('promo-discount-row');
    const promoDiscountAmount = document.getElementById('promo-discount-amount');
    if (promoDiscountRow) promoDiscountRow.style.display = 'flex';
    if (promoDiscountAmount) promoDiscountAmount.textContent = `-RM ${promoDiscount.toFixed(2)}`;
  } else {
    const promoDiscountRow = document.getElementById('promo-discount-row');
    if (promoDiscountRow) promoDiscountRow.style.display = 'none';
  }

  // Get voucher discount from applied voucher (ensure it's a number)
  const appliedVoucher = window.getAppliedVoucher ? window.getAppliedVoucher() : null;
  const voucherDiscount = appliedVoucher ? (parseFloat(appliedVoucher.discount) || 0) : 0;

  // Update voucher discount display
  if (voucherDiscount > 0) {
    const voucherDiscountRow = document.getElementById('voucher-discount-row');
    const voucherDiscountAmount = document.getElementById('voucher-discount-amount');
    if (voucherDiscountRow) voucherDiscountRow.style.display = 'flex';
    if (voucherDiscountAmount) voucherDiscountAmount.textContent = `-RM ${voucherDiscount.toFixed(2)}`;
  } else {
    const voucherDiscountRow = document.getElementById('voucher-discount-row');
    if (voucherDiscountRow) voucherDiscountRow.style.display = 'none';
  }

  // Update final total - calculate from subtotal - promo discount - voucher discount
  const calculatedTotal = Math.max(0, subtotal - promoDiscount - voucherDiscount);
  if (totalAmount) totalAmount.textContent = `RM ${calculatedTotal.toFixed(2)}`;

  // IMPORTANT: Disable promo code input if cart has promotional items (prevent double discount)
  const promotionGroups = cartData.promotion_groups || [];
  const regularItems = cartData.regular_items || cartData.cart || [];

  // Check for bundle/combo/buy1free1 items
  const hasBundleItems = promotionGroups.length > 0;

  // Check for item_discount items (regular items with promotion_id)
  const hasItemDiscountItems = regularItems.some(item => item.promotion_id);

  // Has promotional items if either condition is true
  const hasPromotionItems = hasBundleItems || hasItemDiscountItems;

  const promoCodeInput = document.getElementById('promo-code-input');
  const applyPromoBtn = document.getElementById('apply-promo-btn');
  const promoInputContainer = document.getElementById('promo-input-container');

  if (promoCodeInput && applyPromoBtn) {
    if (hasPromotionItems) {
      // Disable promo code - cart has promotional items
      promoCodeInput.disabled = true;
      promoCodeInput.placeholder = 'Cannot use with promotional items';
      promoCodeInput.style.background = '#f3f4f6';
      promoCodeInput.style.cursor = 'not-allowed';
      applyPromoBtn.disabled = true;
      applyPromoBtn.style.opacity = '0.5';
      applyPromoBtn.style.cursor = 'not-allowed';

      // Show info message
      if (promoInputContainer && !document.getElementById('promo-disabled-message')) {
        const messageHTML = `
          <div id="promo-disabled-message" style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 10px; margin-top: 8px; font-size: 13px; color: #92400e;">
            <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
            Promo code cannot be used with promotional items (bundle/combo/item discount)
          </div>
        `;
        promoInputContainer.insertAdjacentHTML('afterend', messageHTML);
      }
    } else {
      // Enable promo code - no bundle items
      promoCodeInput.disabled = false;
      promoCodeInput.placeholder = 'Enter promo code';
      promoCodeInput.style.background = '';
      promoCodeInput.style.cursor = '';
      applyPromoBtn.disabled = false;
      applyPromoBtn.style.opacity = '';
      applyPromoBtn.style.cursor = '';

      // Remove info message if exists
      const existingMessage = document.getElementById('promo-disabled-message');
      if (existingMessage) {
        existingMessage.remove();
      }
    }
  }

  // Disable checkout button if no available items
  if (checkoutButton) {
    if (availableItemsCount === 0 && cartItems.length > 0) {
      // All items unavailable - disable checkout
      checkoutButton.disabled = true;
      checkoutButton.style.opacity = '0.5';
      checkoutButton.style.cursor = 'not-allowed';
      checkoutButton.style.background = '#9ca3af';
      checkoutButton.textContent = 'No Available Items to Checkout';
    } else if (cartItems.length === 0) {
      // Empty cart - disable checkout
      checkoutButton.disabled = true;
      checkoutButton.style.opacity = '0.5';
      checkoutButton.style.cursor = 'not-allowed';
      checkoutButton.style.background = '#9ca3af';
      checkoutButton.textContent = 'Cart is Empty';
    } else {
      // Has available items - enable checkout
      checkoutButton.disabled = false;
      checkoutButton.style.opacity = '1';
      checkoutButton.style.cursor = 'pointer';
      checkoutButton.style.background = '';
      checkoutButton.textContent = 'Proceed to Checkout';
    }
  }

  cartItemsContainer.innerHTML = '';

  // Add unavailable items warning banner if needed
  if (unavailableCount > 0) {
    const warningHTML = `
      <div class="cart-unavailable-warning">
        <div class="cart-unavailable-warning-icon">⚠️</div>
        <div class="cart-unavailable-warning-text">
          ${unavailableCount} item${unavailableCount > 1 ? 's' : ''} no longer available
        </div>
        <button class="cart-unavailable-warning-button" id="removeUnavailableBtn">
          Remove All
        </button>
      </div>
    `;
    cartItemsContainer.innerHTML += warningHTML;
  }

  if (cartItems.length === 0 && (!cartData.promotion_groups || cartData.promotion_groups.length === 0)) {
    cartItemsContainer.innerHTML += `
      <div class="empty-cart" id="empty-cart">
        <div class="empty-cart-icon">🛒</div>
        <div class="empty-cart-text">Your cart is empty</div>
        <div class="empty-cart-subtext">Add some delicious items to get started!</div>
      </div>
    `;
  } else {
    // === RENDER PROMOTION GROUPS FIRST ===
    const promotionGroups = cartData.promotion_groups || [];
    if (promotionGroups.length > 0) {
      promotionGroups.forEach((group) => {
        const promotionHTML = renderPromotionGroup(group);
        cartItemsContainer.innerHTML += promotionHTML;
      });
    }

    // === RENDER REGULAR ITEMS ===
    const regularItems = cartData.regular_items || cartItems.filter(item => !window.cartManager.isPromotionItem(item));
    if (regularItems.length > 0) {
      regularItems.forEach((item, index) => {
        const itemHTML = renderRegularCartItem(item, index);
        cartItemsContainer.innerHTML += itemHTML;
      });
    }
  }
}

/**
 * Render a promotion group (bundle/combo/buy-x-free-y)
 */
function renderPromotionGroup(group) {
  const savingsText = group.savings > 0 ? `
    <span style="color: #10b981; font-size: 12px; margin-left: 8px;">
      💰 Save RM ${group.savings.toFixed(2)}
    </span>
  ` : '';

  let itemsHTML = '';
  group.items.forEach(item => {
    const freeTag = item.is_free_item ? '<span class="free-tag">FREE</span>' : '';
    itemsHTML += `
      <div class="promo-item">
        <div class="promo-item-image">
          ${item.image ? `<img src="${item.image}" alt="${item.name}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">` : '<span style="font-size: 24px;">🍽️</span>'}
        </div>
        <div class="promo-item-details">
          <div style="font-weight: 500; font-size: 14px;">${item.name} ${freeTag}</div>
          <div style="font-size: 12px; color: #6b7280;">Qty: ${item.quantity} × ${item.price}</div>
        </div>
      </div>
    `;
  });

  return `
    <div class="cart-promotion-group">
      <div class="promotion-header">
        <div class="promotion-title">
          <i class="fas fa-gift" style="color: #8b5cf6; margin-right: 8px;"></i>
          <span>${group.promotion.name}</span>
          <span class="locked-badge"><i class="fas fa-lock"></i> Locked</span>
        </div>
        <div class="promotion-type-badge">${group.promotion.type_label}</div>
      </div>
      <div class="promotion-items">
        ${itemsHTML}
      </div>
      <div class="promotion-footer">
        <div class="promotion-total">
          <span>Bundle Total:</span>
          <span style="font-weight: 700; color: #8b5cf6;">RM ${group.total_price.toFixed(2)}</span>
          ${savingsText}
        </div>
        <button class="remove-promotion-btn" data-group-id="${group.group_id}" data-promotion-name="${group.promotion.name}">
          <i class="fas fa-trash"></i> Remove
        </button>
      </div>
    </div>
  `;
}

/**
 * Render a regular cart item (non-promotion)
 */
function renderRegularCartItem(item, index) {
  const isUnavailable = item.is_available === false;
  const unavailableClass = isUnavailable ? ' unavailable' : '';
  const unavailableBadge = isUnavailable ? `
    <div class="cart-item-unavailable-badge">
      ${item.availability_message || 'Tidak Tersedia'}
    </div>
  ` : '';

  return `
    <div class="cart-item${unavailableClass}">
      <div class="cart-item-image">
        ${item.image ? `<img src="${item.image}" alt="${item.name}">` : '🍽️'}
      </div>
      <div class="cart-item-details">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">${item.price}</div>
      </div>
      <div class="quantity-controls">
        <button class="qty-btn" data-index="${index}" data-change="-1">−</button>
        <span class="quantity">${item.quantity}</span>
        <button class="qty-btn" data-index="${index}" data-change="1">+</button>
      </div>
      ${unavailableBadge}
    </div>
  `;
}

// Cart Modal Event Listeners
document.addEventListener('click', async function(e) {
  // Close modal when clicking backdrop
  if (e.target.id === 'cartModalBackdrop') {
    hideCartModal();
  }

  // Close button
  if (e.target.id === 'cartModalClose') {
    hideCartModal();
  }

  // Clear all button
  if (e.target.id === 'clearAllBtn') {
    const cartData = await window.cartManager.getCart();
    const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);

    if (cartItems.length > 0) {
      const confirmed = await showConfirm(
        'Clear All Items?',
        'Are you sure you want to remove all items from your cart?',
        'warning',
        'Clear All',
        'Cancel'
      );
      if (confirmed) {
        await window.cartManager.clearCart();
        updateCartBadge();
        updateCartDisplay();
        Toast.success('Cart Cleared', 'All items have been removed from your cart');
      }
    }
  }

  // Remove unavailable items button (Shopee-style)
  if (e.target.id === 'removeUnavailableBtn') {
    try {
      const response = await fetch('/customer/cart/remove-unavailable', {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      });

      const data = await response.json();

      if (data.success) {
        Toast.success('Items Removed', data.message);
        updateCartBadge();
        updateCartDisplay();
      } else {
        Toast.error('Error', data.error || 'Failed to remove unavailable items');
      }
    } catch (error) {
      console.error('Error removing unavailable items:', error);
      Toast.error('Error', 'Failed to remove unavailable items');
    }
  }

  // Remove entire promotion group (locked items)
  if (e.target.closest('.remove-promotion-btn')) {
    const button = e.target.closest('.remove-promotion-btn');
    const groupId = button.dataset.groupId;
    const promotionName = button.dataset.promotionName;

    const confirmed = await showConfirm(
      'Remove Promotion?',
      `Remove "${promotionName}" from cart? All items in this promotion will be removed.`,
      'warning',
      'Remove',
      'Cancel'
    );

    if (confirmed) {
      const result = await window.cartManager.removePromotionGroup(groupId);

      if (result.success) {
        Toast.success('Promotion Removed', result.message);
        updateCartBadge();
        updateCartDisplay();
      } else {
        Toast.error('Error', result.message || 'Failed to remove promotion');
      }
    }
  }

  // Checkout button - redirect directly to payment page
  if (e.target.classList.contains('cart-modal-checkout')) {
    const cartData = await window.cartManager.getCart();
    const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);

    // Filter out unavailable items before checkout (Shopee-style)
    const availableItems = cartItems.filter(item => item.is_available !== false);

    if (availableItems.length > 0) {
      // Get order type from sessionStorage
      const orderType = sessionStorage.getItem('selectedOrderType') || 'dine_in';

      // Clear currentOrder to avoid conflicts
      sessionStorage.removeItem('currentOrder');

      // Store only AVAILABLE cart items and order type for payment page
      sessionStorage.setItem('checkoutCart', JSON.stringify(availableItems));
      sessionStorage.setItem('selectedOrderType', orderType);
      sessionStorage.setItem('selectedPaymentMethod', 'online'); // Default payment method

      // --- NEW: Store promo discount and final total for payment page ---
      // These values are already calculated and displayed in updateCartDisplay
      const subtotal = cartData.available_total || window.cartManager.getTotalPrice(cartItems);
      const promoDiscount = parseFloat(cartData.promo_discount) || 0;

      // Get voucher discount from applied voucher (ensure it's a number)
      const appliedVoucher = window.getAppliedVoucher ? window.getAppliedVoucher() : null;
      const voucherDiscount = appliedVoucher ? (parseFloat(appliedVoucher.discount) || 0) : 0;

      const calculatedFinalTotal = Math.max(0, subtotal - promoDiscount - voucherDiscount);
      sessionStorage.setItem('checkoutPromoDiscount', promoDiscount.toFixed(2));
      sessionStorage.setItem('checkoutAppliedPromoCode', cartData.applied_promo_code || ''); // Store the applied promo code
      sessionStorage.setItem('checkoutVoucherDiscount', voucherDiscount.toFixed(2)); // Store voucher discount
      sessionStorage.setItem('checkoutFinalTotal', calculatedFinalTotal.toFixed(2));
      // Hide cart modal immediately before redirect
      hideCartModal();

      // Redirect directly to payment page
      window.location.href = '/customer/payment';
    }
  }

  // Quantity buttons
  if (e.target.classList.contains('qty-btn')) {
    const index = parseInt(e.target.dataset.index);
    const change = parseInt(e.target.dataset.change);
    const cartData = await window.cartManager.getCart();
    const regularItems = cartData.regular_items || [];

    if (index >= 0 && index < regularItems.length) {
      const item = regularItems[index];
      const newQuantity = item.quantity + change;

      // Call updateItem - backend will check if locked
      const result = await window.cartManager.updateItem(item.id, newQuantity);

      // Check if item is locked (backend returned error)
      if (result && !result.success && result.is_locked) {
        Toast.error('Cannot Modify', result.message || 'This item is part of a promotion and cannot be modified individually.');
        return;
      }

      updateCartBadge();
      updateCartDisplay();
    }
  }
});

// Order Modal Variables
let currentOrderItem = null;
let orderQuantity = 1;

// Show order modal for "Order Now" button
function showOrderModal(itemId, itemName, itemPrice, itemDescription, itemImage) {
  const modal = document.getElementById('order-modal');
  const modalItemName = document.getElementById('order-item-name');
  const modalItemPrice = document.getElementById('order-item-price');
  const modalItemImage = document.getElementById('order-item-image');
  const modalQuantity = document.getElementById('order-quantity');

  // Reset quantity
  orderQuantity = 1;

  // Store current item
  currentOrderItem = {
    id: itemId,
    name: itemName,
    price: itemPrice,
    description: itemDescription,
    image: itemImage
  };

  // Update modal content
  if (modalItemName) modalItemName.textContent = itemName;
  if (modalItemPrice) modalItemPrice.textContent = itemPrice;
  if (modalItemImage && itemImage) modalItemImage.src = itemImage;
  if (modalQuantity) modalQuantity.textContent = orderQuantity;

  // Show modal
  if (modal) modal.style.display = 'flex';
}

// Close order modal
function closeOrderModal() {
  const modal = document.getElementById('order-modal');
  if (modal) modal.style.display = 'none';
  currentOrderItem = null;
  orderQuantity = 1;
}

// Modal quantity controls
document.addEventListener('click', function(e) {
  // Increase quantity
  if (e.target.id === 'order-qty-plus') {
    orderQuantity++;
    document.getElementById('order-quantity').textContent = orderQuantity;
    updateOrderTotal();
  }

  // Decrease quantity
  if (e.target.id === 'order-qty-minus') {
    if (orderQuantity > 1) {
      orderQuantity--;
      document.getElementById('order-quantity').textContent = orderQuantity;
      updateOrderTotal();
    }
  }

  // Close modal button
  if (e.target.id === 'order-modal-close-x' || e.target.id === 'order-cancel-btn') {
    closeOrderModal();
  }

  // Confirm order button (this is for Order Now - goes directly to payment page)
  if (e.target.id === 'order-confirm-btn') {
    if (currentOrderItem) {
      // Get order type from sessionStorage (set when user entered menu page)
      const orderType = sessionStorage.getItem('selectedOrderType') || 'dine_in';

      // Payment method will be selected on payment page, default to 'online'
      const paymentMethod = 'online';

      // Get special instructions
      const specialNotes = document.getElementById('order-notes')?.value || '';

      // Calculate total amount
      const price = parseFloat(currentOrderItem.price.replace(/[^\d.]/g, '')) || 0;
      const totalAmount = price * orderQuantity;

      console.log('Debug [menu.js]: Creating order data');
      console.log('Debug [menu.js]: currentOrderItem:', currentOrderItem);
      console.log('Debug [menu.js]: currentOrderItem.id:', currentOrderItem.id);

      // Validate that currentOrderItem has an ID
      if (!currentOrderItem || !currentOrderItem.id) {
        console.error('Error: Cannot create order - menu item ID is missing!');
        alert('Sorry, there was an error with this item. Please refresh the page and try again.');
        return;
      }

      // Create order data for single item (Order Now flow)
      const orderData = {
        item_id: currentOrderItem.id,
        item_name: currentOrderItem.name,
        item_price: currentOrderItem.price,
        quantity: orderQuantity,
        notes: specialNotes,
        payment_method: paymentMethod,
        order_type: orderType,
        total_amount: `RM ${totalAmount.toFixed(2)}`
      };

      console.log('Debug [menu.js]: orderData created:', orderData);
      console.log('Debug [menu.js]: orderData.item_id:', orderData.item_id);

      // Clear checkoutCart to avoid conflicts (Order Now is for single item only)
      sessionStorage.removeItem('checkoutCart');

      // Store as currentOrder (for single item Order Now flow)
      sessionStorage.setItem('currentOrder', JSON.stringify(orderData));

      console.log('Debug [menu.js]: Stored in sessionStorage:', sessionStorage.getItem('currentOrder'));

      sessionStorage.setItem('selectedPaymentMethod', paymentMethod);
      sessionStorage.setItem('selectedOrderType', orderType);

      // Hide the modal immediately before redirect to prevent flash
      const modal = document.getElementById('order-modal');
      if (modal) {
        modal.style.display = 'none';
      }

      // Redirect to payment
      window.location.href = '/customer/payment';
    }
  }
});

// Update order total in modal
function updateOrderTotal() {
  if (!currentOrderItem) return;

  const totalElement = document.getElementById('order-total-amount');
  if (totalElement) {
    const price = parseFloat(currentOrderItem.price.replace(/[^\d.]/g, '')) || 0;
    const total = price * orderQuantity;
    totalElement.textContent = `RM ${total.toFixed(2)}`;
  }
}

// Add to Cart Modal Variables
let currentAddToCartItem = null;
let addToCartQuantity = 1;

// Show Add to Cart modal
function showAddToCartModal(itemId, itemName, itemPrice, itemDescription, itemImage) {
  const modal = document.getElementById('addtocart-modal');
  const modalItemName = document.getElementById('addtocart-item-name');
  const modalItemPrice = document.getElementById('addtocart-item-price');
  const modalItemImage = document.getElementById('addtocart-item-image');
  const modalQuantity = document.getElementById('addtocart-quantity');

  // Reset quantity
  addToCartQuantity = 1;

  // Store current item
  currentAddToCartItem = {
    id: itemId,
    name: itemName,
    price: itemPrice,
    description: itemDescription,
    image: itemImage
  };

  // Update modal content
  if (modalItemName) modalItemName.textContent = itemName;
  if (modalItemPrice) modalItemPrice.textContent = itemPrice;
  if (modalItemImage && itemImage) modalItemImage.src = itemImage;
  if (modalQuantity) modalQuantity.textContent = addToCartQuantity;

  // Update total
  updateAddToCartTotal();

  // Show modal
  if (modal) modal.style.display = 'flex';
}

// Close Add to Cart modal
function closeAddToCartModal() {
  const modal = document.getElementById('addtocart-modal');
  if (modal) modal.style.display = 'none';
  currentAddToCartItem = null;
  addToCartQuantity = 1;
}

// Update Add to Cart total in modal
function updateAddToCartTotal() {
  if (!currentAddToCartItem) return;

  const totalElement = document.getElementById('addtocart-total-amount');
  if (totalElement) {
    const price = parseFloat(currentAddToCartItem.price.replace(/[^\d.]/g, '')) || 0;
    const total = price * addToCartQuantity;
    totalElement.textContent = `RM ${total.toFixed(2)}`;
  }
}

// Add to Cart Modal event handlers
document.addEventListener('click', async function(e) {
  // Increase quantity
  if (e.target.id === 'addtocart-qty-plus') {
    addToCartQuantity++;
    document.getElementById('addtocart-quantity').textContent = addToCartQuantity;
    updateAddToCartTotal();
  }

  // Decrease quantity
  if (e.target.id === 'addtocart-qty-minus') {
    if (addToCartQuantity > 1) {
      addToCartQuantity--;
      document.getElementById('addtocart-quantity').textContent = addToCartQuantity;
      updateAddToCartTotal();
    }
  }

  // Close modal button
  if (e.target.id === 'addtocart-modal-close-x' || e.target.id === 'addtocart-cancel-btn') {
    closeAddToCartModal();
  }

  // Confirm add to cart button
  if (e.target.id === 'addtocart-confirm-btn') {
    if (currentAddToCartItem) {
      // Get special instructions
      const specialNotes = document.getElementById('addtocart-notes')?.value || '';

      // Calculate price
      const price = parseFloat(currentAddToCartItem.price.replace(/[^\d.]/g, '')) || 0;

      // Add item to cart
      await window.cartManager.addItem({
        id: currentAddToCartItem.id,
        name: currentAddToCartItem.name,
        price: currentAddToCartItem.price,
        image: currentAddToCartItem.image,
        quantity: addToCartQuantity,
        notes: specialNotes
      });

      // Update cart badge
      updateCartBadge();

      // Add bounce animation to cart FAB
      const cartFab = document.getElementById('cartFab');
      if (cartFab) {
        cartFab.classList.remove('bounce');
        void cartFab.offsetWidth; // Force reflow
        cartFab.classList.add('bounce');
      }

      // Show success toast
      Toast.success('Added to Cart', `${currentAddToCartItem.name} has been added to your cart`);

      // Close modal
      closeAddToCartModal();
    }
  }
});

// Expose showAddToCartModal globally for use in menu.js event delegation
window.showAddToCartModal = showAddToCartModal;

// Order Type Selection Modal (First Visit)
function checkAndShowOrderTypeModal() {
  // Check if user just placed an order - if so, clear the order type selection flag
  const justPlacedOrder = sessionStorage.getItem('justPlacedOrder');
  if (justPlacedOrder === 'true') {
    sessionStorage.removeItem('orderTypeSelected');
    sessionStorage.removeItem('justPlacedOrder');
  }

  // Check if user has already selected order type in this session
  const hasSelectedOrderType = sessionStorage.getItem('orderTypeSelected');

  if (!hasSelectedOrderType) {
    // Show the order type selection modal
    const modal = document.getElementById('ordertype-selection-modal');
    if (modal) {
      modal.style.display = 'flex';
    }
  }
}

// Handle order type selection card clicks
document.addEventListener('click', function(e) {
  // Order type selection card clicks
  if (e.target.closest('.ordertype-selection-card')) {
    const card = e.target.closest('.ordertype-selection-card');
    const option = card.closest('.ordertype-selection-option');
    const radio = option.querySelector('input[type="radio"]');

    // Check the radio button
    radio.checked = true;

    // Update visual styling for all cards
    document.querySelectorAll('.ordertype-selection-card').forEach(c => {
      const orderType = c.dataset.type;
      const icon = c.querySelector('i');

      if (c === card) {
        // Selected card - Purple theme
        c.style.border = '3px solid #6366f1';
        c.style.background = 'linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)';
        c.style.boxShadow = '0 4px 16px rgba(99, 102, 241, 0.25)';
        icon.style.color = '#6366f1';
      } else {
        // Unselected card
        c.style.border = '3px solid #e5e7eb';
        c.style.background = 'white';
        c.style.boxShadow = 'none';
        icon.style.color = '#9ca3af';
      }
    });
  }

  // Confirm order type button
  if (e.target.id === 'confirm-ordertype-btn') {
    const selectedRadio = document.querySelector('input[name="initial-order-type"]:checked');
    const orderType = selectedRadio ? selectedRadio.value : 'dine_in';

    // Store in sessionStorage
    sessionStorage.setItem('selectedOrderType', orderType);
    sessionStorage.setItem('orderTypeSelected', 'true');

    // Hide the modal
    const modal = document.getElementById('ordertype-selection-modal');
    if (modal) {
      modal.style.display = 'none';
    }

    // Update floating button
    updateOrderTypeFab(orderType);

    // Show success toast
    const orderTypeText = orderType === 'dine_in' ? 'Dine In' : 'Takeaway';
    Toast.success('Order Type Set', `You've selected ${orderTypeText}`);
  }

  // Change order type card clicks
  if (e.target.closest('.change-ordertype-card')) {
    const card = e.target.closest('.change-ordertype-card');
    const option = card.closest('.change-ordertype-option');
    const radio = option.querySelector('input[type="radio"]');

    // Check the radio button
    radio.checked = true;

    // Update visual styling for all cards
    document.querySelectorAll('.change-ordertype-card').forEach(c => {
      const orderType = c.dataset.type;
      const icon = c.querySelector('i');

      if (c === card) {
        // Selected card - Purple theme
        c.style.border = '3px solid #6366f1';
        c.style.background = 'linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)';
        c.style.boxShadow = '0 4px 16px rgba(99, 102, 241, 0.25)';
        icon.style.color = '#6366f1';
      } else {
        // Unselected card
        c.style.border = '3px solid #e5e7eb';
        c.style.background = 'white';
        c.style.boxShadow = 'none';
        icon.style.color = '#9ca3af';
      }
    });
  }

  // Close change order type modal
  if (e.target.id === 'change-ordertype-close-x' || e.target.id === 'cancel-change-ordertype-btn') {
    const modal = document.getElementById('change-ordertype-modal');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  // Confirm change order type
  if (e.target.id === 'confirm-change-ordertype-btn') {
    const selectedRadio = document.querySelector('input[name="change-order-type"]:checked');
    const orderType = selectedRadio ? selectedRadio.value : 'dine_in';

    // Store in sessionStorage
    sessionStorage.setItem('selectedOrderType', orderType);

    // Update floating button
    updateOrderTypeFab(orderType);

    // Hide the modal
    const modal = document.getElementById('change-ordertype-modal');
    if (modal) {
      modal.style.display = 'none';
    }

    // Show success toast
    const orderTypeText = orderType === 'dine_in' ? 'Dine In' : 'Takeaway';
    Toast.success('Order Type Updated', `Changed to ${orderTypeText}`);
  }
});

// Floating Order Type Button
const ordertypeFab = document.getElementById('ordertypeFab');
const ordertypeIcon = document.getElementById('ordertypeIcon');
const ordertypeText = document.getElementById('ordertypeText');

// Show change order type modal
if (ordertypeFab) {
  ordertypeFab.addEventListener('click', function() {
    const currentOrderType = sessionStorage.getItem('selectedOrderType') || 'dine_in';

    // Pre-select current order type in modal
    const radios = document.querySelectorAll('input[name="change-order-type"]');
    radios.forEach(radio => {
      if (radio.value === currentOrderType) {
        radio.checked = true;
        // Update card styling - Purple theme
        const card = radio.closest('.change-ordertype-option').querySelector('.change-ordertype-card');
        const icon = card.querySelector('i');
        card.style.border = '3px solid #6366f1';
        card.style.background = 'linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)';
        card.style.boxShadow = '0 4px 16px rgba(99, 102, 241, 0.25)';
        icon.style.color = '#6366f1';
      } else {
        // Reset other card
        const card = radio.closest('.change-ordertype-option').querySelector('.change-ordertype-card');
        const icon = card.querySelector('i');
        card.style.border = '3px solid #e5e7eb';
        card.style.background = 'white';
        card.style.boxShadow = 'none';
        icon.style.color = '#9ca3af';
      }
    });

    // Show modal
    const modal = document.getElementById('change-ordertype-modal');
    if (modal) {
      modal.style.display = 'flex';
    }
  });
}

// Update floating order type button
function updateOrderTypeFab(orderType) {
  if (!ordertypeIcon || !ordertypeFab || !ordertypeText) return;

  if (orderType === 'dine_in') {
    ordertypeIcon.className = 'fas fa-utensils';
    ordertypeText.textContent = 'Dine In';
  } else if (orderType === 'takeaway') {
    ordertypeIcon.className = 'fas fa-shopping-bag';
    ordertypeText.textContent = 'Takeaway';
  }

  // Show the button with animation
  ordertypeFab.style.opacity = '1';
}

// === PROMO CODE FUNCTIONALITY ===

// Initialize promo code event listeners
function initPromoCodeListeners() {
  const applyBtn = document.getElementById('apply-promo-btn');
  const removeBtn = document.getElementById('remove-promo-btn');
  const promoInput = document.getElementById('promo-code-input');

  if (applyBtn) {
    applyBtn.addEventListener('click', async () => {
      const promoCode = promoInput?.value?.trim();

      if (!promoCode) {
        showPromoError('Please enter a promo code');
        return;
      }

      // Disable button during processing
      applyBtn.disabled = true;
      applyBtn.textContent = 'Applying...';

      try {
        const result = await window.cartManager.applyPromoCode(promoCode);

        if (result.success) {
          // Show success state
          showPromoApplied(result.promo_code, result.discount);

          // Refresh cart display to show updated totals
          await updateCartDisplay();

          Toast.success('Promo Applied!', result.message);

          // Refresh cart to show updated totals
          await updateCart();

        } else {
          showPromoError(result.message);
        }
      } catch (error) {
        showPromoError('An error occurred. Please try again.');
      } finally {
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
      }
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', async () => {
      removeBtn.disabled = true;

      try {
        const result = await window.cartManager.removePromoCode();

        if (result.success) {
          // Hide applied promo, show input
          hidePromoApplied();

          // Refresh cart display
          await updateCartDisplay();

          Toast.info('Promo Removed', result.message);

          // Refresh cart
          await updateCart();
        }
      } catch (error) {
        alert('An error occurred');
      } finally {
        removeBtn.disabled = false;
      }
    });
  }

  // Enter key support
  if (promoInput) {
    promoInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        applyBtn?.click();
      }
    });
  }
}

async function updateCart() {
  await updateCartBadge();
  if (document.getElementById('cartModal').classList.contains('open')) {
    await updateCartDisplay();
  }
}

function showPromoError(message) {
  const errorContainer = document.getElementById('promo-error-message');
  if (errorContainer) {
    const span = errorContainer.querySelector('span');
    if (span) span.textContent = message;
    errorContainer.style.display = 'block';

    // Auto-hide after 4 seconds
    setTimeout(() => {
      errorContainer.style.display = 'none';
    }, 4000);
  }
}

function showPromoApplied(promoCode, discount) {
  const inputContainer = document.getElementById('promo-input-container');
  const appliedContainer = document.getElementById('promo-applied-container');
  const promoCodeText = document.getElementById('promo-code-text');

  if (inputContainer) inputContainer.style.display = 'none';
  if (appliedContainer) appliedContainer.style.display = 'block';
  if (promoCodeText) promoCodeText.textContent = promoCode;

  // Show discount row - handle both number and string
  const discountRow = document.getElementById('promo-discount-row');
  const discountAmount = document.getElementById('promo-discount-amount');
  const discountValue = parseFloat(discount) || 0;

  if (discountRow) discountRow.style.display = 'flex';
  if (discountAmount) discountAmount.textContent = `-RM ${discountValue.toFixed(2)}`;
}

function hidePromoApplied() {
  const inputContainer = document.getElementById('promo-input-container');
  const appliedContainer = document.getElementById('promo-applied-container');

  if (inputContainer) inputContainer.style.display = 'block';
  if (appliedContainer) appliedContainer.style.display = 'none';

  // Hide discount row
  const discountRow = document.getElementById('promo-discount-row');
  if (discountRow) discountRow.style.display = 'none';
}

// Check for existing promo code on cart load
async function loadExistingPromo() {
  try {
    const promoDetails = await window.cartManager.getPromoCodeDetails();

    if (promoDetails.has_promo) {
      showPromoApplied(promoDetails.promo_code, promoDetails.discount_amount);
    } else {
      hidePromoApplied();
    }
  } catch (error) {
    console.error('Error loading promo details:', error);
  }
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initializeMenuData();
  initializeSearch();
  updateCartBadge();

  // Check and show order type modal on first visit
  checkAndShowOrderTypeModal();

  // Update floating button if order type already selected
  const existingOrderType = sessionStorage.getItem('selectedOrderType');
  if (existingOrderType) {
    updateOrderTypeFab(existingOrderType);
  }

  // Initialize promo code functionality
  initPromoCodeListeners();
});
