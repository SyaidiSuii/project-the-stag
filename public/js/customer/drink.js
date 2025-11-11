document.addEventListener('DOMContentLoaded', function() {
    console.log('Debug: DOMContentLoaded triggered in drink.js');
    console.log('Debug: Initial window.cartManager check:', !!window.cartManager);
    // Category filtering functionality
    const categoryTabs = document.querySelectorAll('.tab');
    const categoryTitle = document.getElementById('categoryTitle');
    const drinkContainer = document.getElementById('drink-menu-container');
    const noResults = document.getElementById('noResults');

    // Handle category tab clicks
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const selectedCategory = this.dataset.category;
            const categoryName = this.textContent;

            // Update active tab
            categoryTabs.forEach(t => {
                t.classList.remove('active');
                t.removeAttribute('aria-current');
            });
            this.classList.add('active');
            this.setAttribute('aria-current', 'page');

            // Update category title
            categoryTitle.textContent = categoryName;

            // Filter content
            filterByCategory(selectedCategory);
        });
    });

    function filterByCategory(categoryId) {
        const allSections = drinkContainer.querySelectorAll('.category-section');
        const allCards = drinkContainer.querySelectorAll('.drink-card');
        let hasVisibleItems = false;

        if (categoryId === 'all') {
            // Show all sections and cards
            allSections.forEach(section => {
                section.style.display = 'block';
                hasVisibleItems = true;
            });
            allCards.forEach(card => {
                card.style.display = 'block';
            });
        } else {
            // Hide all sections first
            allSections.forEach(section => {
                section.style.display = 'none';
            });

            // Show only the selected category section
            const targetSection = drinkContainer.querySelector(`.category-section[data-category="${categoryId}"]`);
            if (targetSection) {
                targetSection.style.display = 'block';
                hasVisibleItems = true;
            }

            // Handle featured items - show only those matching the category
            const featuredSection = drinkContainer.querySelector('.featured-section');
            if (featuredSection) {
                const featuredCards = featuredSection.querySelectorAll('.drink-card');
                let hasFeaturedItems = false;

                featuredCards.forEach(card => {
                    if (card.dataset.category === categoryId) {
                        card.style.display = 'block';
                        hasFeaturedItems = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide entire featured section based on whether it has visible items
                featuredSection.style.display = hasFeaturedItems ? 'block' : 'none';
                
                if (hasFeaturedItems) {
                    hasVisibleItems = true;
                }
            }
        }

        // Show/hide no results message
        noResults.style.display = hasVisibleItems ? 'none' : 'block';

        // Add animation to visible cards
        setTimeout(() => {
            const visibleCards = drinkContainer.querySelectorAll('.drink-card[style="display: block;"], .drink-card:not([style*="display: none"])');
            visibleCards.forEach((card, index) => {
                card.style.animation = `fadeInCard 0.3s ease forwards`;
                card.style.animationDelay = `${index * 0.05}s`;
            });
        }, 50);
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                clearButton.style.display = 'none';
                // Reset to current category filter
                const activeTab = document.querySelector('.tab.active');
                if (activeTab) {
                    filterByCategory(activeTab.dataset.category);
                }
            } else {
                clearButton.style.display = 'block';
                performSearch(searchTerm);
            }
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            clearButton.style.display = 'none';
            
            // Reset to current category filter
            const activeTab = document.querySelector('.tab.active');
            if (activeTab) {
                filterByCategory(activeTab.dataset.category);
            }
        });
    }

    function performSearch(searchTerm) {
        const allSections = drinkContainer.querySelectorAll('.category-section');
        const allCards = drinkContainer.querySelectorAll('.drink-card');
        let hasResults = false;

        // Show all sections first
        allSections.forEach(section => {
            section.style.display = 'block';
        });

        // Filter cards based on search term
        allCards.forEach(card => {
            const drinkName = card.querySelector('.drink-name')?.textContent.toLowerCase() || '';
            const drinkDescription = card.querySelector('.drink-description')?.textContent.toLowerCase() || '';
            
            if (drinkName.includes(searchTerm) || drinkDescription.includes(searchTerm)) {
                card.style.display = 'block';
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Hide sections that have no visible cards
        allSections.forEach(section => {
            const visibleCards = section.querySelectorAll('.drink-card[style="display: block;"], .drink-card:not([style*="display: none"])');
            if (visibleCards.length === 0) {
                section.style.display = 'none';
            }
        });

        // Update category title for search
        categoryTitle.textContent = `Search results for "${searchTerm}"`;
        
        // Show/hide no results message
        noResults.style.display = hasResults ? 'none' : 'block';
    }

    // Cart functionality using hybrid cart manager (copied from food.js)
    const cartBadge = document.getElementById('cartBadge');
    const cartFab = document.getElementById('cartFab');

    // Update cart badge
    async function updateCartBadge() {
        const cartItems = await window.cartManager.getCart();
        const totalItems = window.cartManager.getTotalQuantity(cartItems);
        cartBadge.textContent = totalItems;
        cartBadge.style.display = totalItems > 0 ? 'inline' : 'none';
    }

    // Add to cart functionality
    document.addEventListener('click', async function(e) {
        console.log('Debug: Document click detected, target:', e.target);
        
        if (e.target.classList.contains('btn-cart')) {
            console.log('Debug: btn-cart clicked!');
            console.log('Debug: window.cartManager exists:', !!window.cartManager);
            
            const itemId = e.target.dataset.itemId;
            const drinkCard = e.target.closest('.drink-card');
            const itemName = drinkCard.querySelector('.drink-name').textContent;
            const itemPrice = drinkCard.querySelector('.drink-price').textContent;

            const itemData = {
                id: itemId,
                name: itemName,
                price: itemPrice,
                quantity: 1
            };

            console.log('Debug: Item data prepared:', itemData);

            // Add to cart using hybrid cart manager
            console.log('Debug: Calling window.cartManager.addItem...');
            const result = await window.cartManager.addItem(itemData);
            console.log('Debug: CartManager result:', result);
            
            if (result.success) {
                // Update badge
                updateCartBadge();
                
                // Bounce animation for FAB
                cartFab.classList.remove('bounce');
                void cartFab.offsetWidth; // Force reflow
                cartFab.classList.add('bounce');

                // Show success feedback
                e.target.textContent = 'Added!';
                e.target.style.backgroundColor = '#28a745';
                setTimeout(() => {
                    e.target.textContent = 'Add to Cart';
                    e.target.style.backgroundColor = '';
                }, 1500);
            } else {
                console.error('Failed to add item to cart');
                Toast.error('Cart Error', 'Failed to add item to cart. Please try again.');
            }
        }
        
        if (e.target.classList.contains('btn-order')) {
            const itemId = e.target.dataset.itemId;
            const drinkCard = e.target.closest('.drink-card');
            const itemName = drinkCard.querySelector('.drink-name').textContent;
            const itemPrice = drinkCard.querySelector('.drink-price').textContent;
            const itemDescription = drinkCard.querySelector('.drink-description')?.textContent || '';
            const itemImage = drinkCard.querySelector('.drink-image img')?.src || '';

            // Show order modal
            showOrderModal(itemId, itemName, itemPrice, itemDescription, itemImage);
        }
    });

    // Initialize cart badge on page load
    console.log('Debug: window.cartManager exists:', !!window.cartManager);
    console.log('Debug: CartManager object:', window.cartManager);
    updateCartBadge();

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
        const emptyCart = document.getElementById('empty-cart');
        const cartCount = document.getElementById('cart-count');
        const totalItems = document.getElementById('total-items');
        const totalAmount = document.getElementById('total-amount');

        if (!cartItemsContainer) return;

        // Get cart items from hybrid cart manager
        const cartItems = await window.cartManager.getCart();

        // Update cart count
        const totalCartItems = window.cartManager.getTotalQuantity(cartItems);
        if (cartCount) cartCount.textContent = totalCartItems;
        if (totalItems) totalItems.textContent = totalCartItems;

        // Calculate total amount
        const total = window.cartManager.getTotalAmount(cartItems);
        if (totalAmount) totalAmount.textContent = `RM ${total.toFixed(2)}`;

        // Clear existing items
        cartItemsContainer.innerHTML = '';
        
        if (cartItems.length === 0) {
            // Show empty cart message
            cartItemsContainer.innerHTML = `
                <div class="empty-cart" id="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <div class="empty-cart-text">Your cart is empty</div>
                    <div class="empty-cart-subtext">Add some delicious items to get started!</div>
                </div>
            `;
        } else {
            // Display cart items
            cartItems.forEach((item, index) => {
                const cartItemHTML = `
                    <div class="cart-item">
                        <div class="cart-item-image">🍽️</div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${item.price}</div>
                            ${item.notes ? `<div class="cart-item-addons">${item.notes}</div>` : ''}
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateCartQuantity(${index}, -1)">−</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateCartQuantity(${index}, 1)">+</button>
                        </div>
                    </div>
                `;
                cartItemsContainer.innerHTML += cartItemHTML;
            });
        }
    }

    async function updateCartQuantity(itemIndex, change) {
        const cartItems = await window.cartManager.getCart();
        if (itemIndex >= 0 && itemIndex < cartItems.length) {
            const item = cartItems[itemIndex];
            const newQuantity = item.quantity + change;
            
            if (newQuantity <= 0) {
                await window.cartManager.removeItem(item.id);
            } else {
                await window.cartManager.updateItemQuantity(item.id, newQuantity);
            }
            
            // Update displays
            updateCartBadge();
            updateCartDisplay();
        }
    }

    async function clearCart() {
        await window.cartManager.clearCart();
        updateCartBadge();
        updateCartDisplay();
    }

    // Cart Modal Event Listeners
    document.addEventListener('click', function(e) {
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
            if (cartItems.length > 0) {
                console.log('Clear All button clicked, showing modal...');
                showConfirmationModal(
                    'Clear Cart?',
                    'Are you sure you want to remove all items from your cart? This action cannot be undone.',
                    function() {
                        console.log('Clear cart confirmed');
                        clearCart();
                    },
                    'CANCEL',
                    'CLEAR ALL'
                );
            } else {
                console.log('Cart is empty, no need to clear');
            }
        }

        // Checkout button
        if (e.target.classList.contains('cart-modal-checkout')) {
            if (cartItems.length > 0) {
                // Store cart data for checkout
                sessionStorage.setItem('checkoutCart', JSON.stringify(cartItems));
                window.location.href = '/customer/payment';
            } else {
                Toast.warning('Cart Empty', 'Your cart is empty!');
            }
        }
    });

    // Make updateCartQuantity available globally
    window.updateCartQuantity = updateCartQuantity;

    // Confirmation Modal Functions
    let confirmationCallback = null;

    function showConfirmationModal(title, message, callback, cancelText = 'CANCEL', confirmText = 'CONFIRM') {
        const modal = document.getElementById('confirmation-modal');
        const titleEl = modal?.querySelector('.confirmation-modal-title');
        const textEl = modal?.querySelector('.confirmation-modal-text');
        const cancelBtn = modal?.querySelector('#confirm-cancel-btn');
        const confirmBtn = modal?.querySelector('#confirm-action-btn');

        if (modal && titleEl && textEl && cancelBtn && confirmBtn) {
            titleEl.textContent = title;
            textEl.textContent = message;
            cancelBtn.textContent = cancelText;
            confirmBtn.textContent = confirmText;
            confirmationCallback = callback;
            modal.classList.add('open');
            
            // Debug log
            console.log('Confirmation modal should be visible now');
            console.log('Modal element:', modal);
            console.log('Modal classes:', modal.className);
        } else {
            console.log('Modal elements not found:', { modal, titleEl, textEl, cancelBtn, confirmBtn });
        }
    }

    function hideConfirmationModal() {
        const modal = document.getElementById('confirmation-modal');
        if (modal) {
            modal.classList.remove('open');
            confirmationCallback = null;
            console.log('Confirmation modal hidden');
        }
    }

    // Confirmation Modal Event Listeners
    document.addEventListener('click', function(e) {
        // Close modal when clicking backdrop
        if (e.target.classList.contains('confirmation-modal-backdrop')) {
            hideConfirmationModal();
        }

        // Cancel button
        if (e.target.id === 'confirm-cancel-btn') {
            hideConfirmationModal();
        }

        // Confirm button
        if (e.target.id === 'confirm-action-btn') {
            if (confirmationCallback) {
                confirmationCallback();
            }
            hideConfirmationModal();
        }
    });

    // Order Modal Functions
    let currentOrderItem = null;
    let orderQuantity = 1;

    function showOrderModal(itemId, itemName, itemPrice, itemDescription, itemImage) {
        const modal = document.getElementById('order-modal');
        const itemNameEl = document.getElementById('order-item-name');
        const itemPriceEl = document.getElementById('order-item-price');
        const itemDescEl = document.getElementById('order-item-description');
        const itemImageEl = document.getElementById('order-item-image');
        const quantityEl = document.getElementById('order-quantity');
        const totalAmountEl = document.getElementById('order-total-amount');

        // Reset quantity
        orderQuantity = 1;
        
        // Store current item data
        currentOrderItem = {
            id: itemId,
            name: itemName,
            price: itemPrice,
            description: itemDescription,
            image: itemImage
        };

        // Update modal content
        if (itemNameEl) itemNameEl.textContent = itemName;
        if (itemPriceEl) itemPriceEl.textContent = itemPrice;
        if (itemDescEl) itemDescEl.textContent = itemDescription;
        if (itemImageEl) itemImageEl.src = itemImage;
        if (quantityEl) quantityEl.textContent = orderQuantity;
        
        // Calculate and update total
        updateOrderTotal();
        
        // Show modal
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function hideOrderModal() {
        const modal = document.getElementById('order-modal');
        if (modal) {
            modal.style.display = 'none';
        }
        // Reset form
        const notesEl = document.getElementById('order-notes');
        if (notesEl) notesEl.value = '';
        orderQuantity = 1;
        currentOrderItem = null;
    }

    function updateOrderTotal() {
        const totalAmountEl = document.getElementById('order-total-amount');
        if (currentOrderItem && totalAmountEl) {
            // Extract numeric value from price string (assuming format like "RM 15.00")
            const priceMatch = currentOrderItem.price.match(/[\d.]+/);
            const unitPrice = priceMatch ? parseFloat(priceMatch[0]) : 0;
            const total = (unitPrice * orderQuantity).toFixed(2);
            totalAmountEl.textContent = `RM ${total}`;
        }
    }

    // Order Modal Event Listeners
    document.addEventListener('click', function(e) {
        // Close modal when clicking backdrop
        if (e.target.id === 'order-modal') {
            hideOrderModal();
        }

        // Quantity controls
        if (e.target.id === 'order-qty-plus') {
            orderQuantity++;
            const quantityEl = document.getElementById('order-quantity');
            if (quantityEl) quantityEl.textContent = orderQuantity;
            updateOrderTotal();
        }

        if (e.target.id === 'order-qty-minus' && orderQuantity > 1) {
            orderQuantity--;
            const quantityEl = document.getElementById('order-quantity');
            if (quantityEl) quantityEl.textContent = orderQuantity;
            updateOrderTotal();
        }

        // Cancel button
        if (e.target.id === 'order-cancel-btn') {
            hideOrderModal();
        }

        // Confirm order button
        if (e.target.id === 'order-confirm-btn') {
            if (currentOrderItem) {
                const notes = document.getElementById('order-notes')?.value || '';
                
                // Create order data
                const orderData = {
                    item_id: currentOrderItem.id,
                    item_name: currentOrderItem.name,
                    item_price: currentOrderItem.price,
                    quantity: orderQuantity,
                    notes: notes,
                    total_amount: document.getElementById('order-total-amount')?.textContent || 'RM 0.00'
                };

                // Store order data in sessionStorage for payment page
                sessionStorage.setItem('currentOrder', JSON.stringify(orderData));
                
                // Redirect to payment page
                window.location.href = '/customer/payment';
            }
        }
    });
});