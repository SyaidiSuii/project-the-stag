// ===== Menu Page JavaScript =====

// Load menu data from window (set by Blade template)
const menuData = window.menuData || [];
let food = [], setMeals = [], drinks = [], allCategories = [];
let currentMenuType = 'all';

// Helper function to safely format price
function formatPrice(price) {
  const num = parseFloat(price);
  return isNaN(num) ? '0.00' : num.toFixed(2);
}

// Process database data into the expected format
if (menuData && menuData.length > 0) {
  menuData.forEach(category => {
    if (category.menu_items && category.menu_items.length > 0) {
      category.menu_items.forEach(item => {
        const menuItem = {
          id: item.id,
          name: item.name,
          description: item.description || '',
          price: parseFloat(item.price),
          category: category.name,
          image: item.image || null,
          is_available: item.availability !== undefined ? item.availability : item.is_available,
          preparation_time: item.preparation_time,
          stock_quantity: item.stock_quantity
        };

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

  let cardContent = `
    <div class="food-image ${isDrink ? 'drink-style' : ''}">${imageHtml}</div>
    <div class="food-name">${item.name}</div>
    <div class="food-price">RM ${formatPrice(item.price)}</div>
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
  const cart = await window.cartManager.getCart();
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  cartBadge.textContent = totalItems;
  cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
}

// Add to cart and order now button handlers
document.addEventListener('click', async function(e) {
  // Add to Cart button
  if (e.target.classList.contains('btn-cart')) {
    const itemCard = e.target.closest('.food-card');
    if (!itemCard) return;

    const itemId = itemCard.dataset.id;
    const itemName = itemCard.querySelector('.food-name').textContent;
    const itemPriceText = itemCard.querySelector('.food-price').textContent;
    const imgElement = itemCard.querySelector('.food-image img');
    const itemImage = imgElement ? imgElement.src : null;

    const itemData = {
      id: itemId,
      name: itemName,
      price: itemPriceText,
      quantity: 1,
      image: itemImage
    };

    const result = await window.cartManager.addItem(itemData);

    if (result.success) {
      updateCartBadge();

      // Bounce animation for FAB
      cartFab.classList.remove('bounce');
      void cartFab.offsetWidth;
      cartFab.classList.add('bounce');

      // Success feedback
      e.target.textContent = 'Added!';
      e.target.style.backgroundColor = '#28a745';

      setTimeout(() => {
        e.target.textContent = 'Add to Cart';
        e.target.style.backgroundColor = '';
      }, 1500);
    }
  }

  // Order Now button
  if (e.target.classList.contains('btn-order')) {
    const itemCard = e.target.closest('.food-card');
    if (!itemCard) return;

    const itemId = itemCard.dataset.id;
    const itemName = itemCard.querySelector('.food-name').textContent;
    const itemPriceText = itemCard.querySelector('.food-price').textContent;
    const imgElement = itemCard.querySelector('.food-image img');
    const itemImage = imgElement ? imgElement.src : null;
    const itemDescription = itemCard.querySelector('.food-description')?.textContent || '';

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
function showCartModal() {
  const modal = document.getElementById('cartModal');
  if (modal) {
    updateCartDisplay();
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

  if (!cartItemsContainer) return;

  const cartItems = await window.cartManager.getCart();
  const totalCartItems = window.cartManager.getTotalQuantity(cartItems);

  if (cartCount) cartCount.textContent = totalCartItems;
  if (totalItems) totalItems.textContent = totalCartItems;

  const total = window.cartManager.getTotalPrice(cartItems);
  if (totalAmount) totalAmount.textContent = `RM ${total.toFixed(2)}`;

  cartItemsContainer.innerHTML = '';

  if (cartItems.length === 0) {
    cartItemsContainer.innerHTML = `
      <div class="empty-cart" id="empty-cart">
        <div class="empty-cart-icon">🛒</div>
        <div class="empty-cart-text">Your cart is empty</div>
        <div class="empty-cart-subtext">Add some delicious items to get started!</div>
      </div>
    `;
  } else {
    cartItems.forEach((item, index) => {
      const cartItemHTML = `
        <div class="cart-item">
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
        </div>
      `;
      cartItemsContainer.innerHTML += cartItemHTML;
    });
  }
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
    const cartItems = await window.cartManager.getCart();
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

  // Checkout button - show payment method modal first
  if (e.target.classList.contains('cart-modal-checkout')) {
    const cartItems = await window.cartManager.getCart();
    if (cartItems.length > 0) {
      // Store cart items temporarily
      window.tempCheckoutCart = cartItems;
      // Hide cart modal
      hideCartModal();
      // Show payment method modal
      showPaymentMethodModal();
    }
  }

  // Quantity buttons
  if (e.target.classList.contains('qty-btn')) {
    const index = parseInt(e.target.dataset.index);
    const change = parseInt(e.target.dataset.change);
    const cartItems = await window.cartManager.getCart();

    if (index >= 0 && index < cartItems.length) {
      const item = cartItems[index];
      const newQuantity = item.quantity + change;
      await window.cartManager.updateItem(item.id, newQuantity);
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
  if (e.target.id === 'order-modal-close-x' || e.target.id === 'cancel-order-btn') {
    closeOrderModal();
  }

  // Confirm order button (this is for Order Now - goes directly to payment after selecting order type)
  if (e.target.id === 'confirm-order-btn') {
    if (currentOrderItem) {
      // Get selected order type
      const orderTypeRadio = document.querySelector('input[name="order-type"]:checked');
      const orderType = orderTypeRadio ? orderTypeRadio.value : 'dine_in';

      // Get payment method
      const paymentMethodRadio = document.querySelector('input[name="order-payment-method"]:checked');
      const paymentMethod = paymentMethodRadio ? paymentMethodRadio.value : 'online';

      // Get special instructions
      const specialNotes = document.getElementById('order-special-instructions')?.value || '';

      // Store order type and payment method in sessionStorage
      sessionStorage.setItem('selectedOrderType', orderType);
      sessionStorage.setItem('selectedPaymentMethod', paymentMethod);

      // Create order with only this item (don't include existing cart items)
      const orderItem = {
        id: currentOrderItem.id,
        name: currentOrderItem.name,
        price: currentOrderItem.price,
        quantity: orderQuantity,
        image: currentOrderItem.image,
        notes: specialNotes
      };

      // Store only this item for checkout (not the full cart)
      sessionStorage.setItem('checkoutCart', JSON.stringify([orderItem]));

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

// Payment Method Modal Functions
function showPaymentMethodModal() {
  const modal = document.getElementById('payment-method-modal');
  if (modal) {
    modal.style.display = 'flex';
  }
}

function hidePaymentMethodModal() {
  const modal = document.getElementById('payment-method-modal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Payment Method Modal Event Handlers
document.addEventListener('click', function(e) {
  // Close payment method modal
  if (e.target.id === 'payment-modal-close-x' || e.target.id === 'cancel-payment-method-btn') {
    hidePaymentMethodModal();
    window.tempCheckoutCart = null;
  }

  // Confirm payment method and proceed to checkout
  if (e.target.id === 'confirm-payment-method-btn') {
    if (window.tempCheckoutCart && window.tempCheckoutCart.length > 0) {
      // Get selected order type
      const orderTypeRadio = document.querySelector('input[name="cart-order-type"]:checked');
      const orderType = orderTypeRadio ? orderTypeRadio.value : 'dine_in';

      // Get payment method
      const paymentMethodRadio = document.querySelector('input[name="cart-payment-method"]:checked');
      const paymentMethod = paymentMethodRadio ? paymentMethodRadio.value : 'online';

      // Store in sessionStorage
      sessionStorage.setItem('selectedOrderType', orderType);
      sessionStorage.setItem('selectedPaymentMethod', paymentMethod);
      sessionStorage.setItem('checkoutCart', JSON.stringify(window.tempCheckoutCart));

      // Redirect to payment
      window.location.href = '/customer/payment';
    }
  }
});

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initializeMenuData();
  initializeSearch();
  updateCartBadge();
});
