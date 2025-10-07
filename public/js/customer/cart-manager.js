/**
 * Hybrid Cart Manager
 * Handles cart operations for both guest users (localStorage) and logged-in users (database + localStorage)
 */

class CartManager {
    constructor() {
        this.cartStorageKey = 'cartItems';
        this.isLoggedIn = false; // Will be set by init()
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('Debug: CSRF Token:', this.csrfToken);
        this.loginCheckPromise = this.init();
    }

    async init() {
        this.isLoggedIn = await this.checkLoginStatus();
        return this.isLoggedIn;
    }

    // Check if user is logged in by making API call to auth endpoint
    async checkLoginStatus() {
        try {
            const response = await fetch('/customer/cart/', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            
            const isLoggedIn = response.status !== 401;
            console.log('Debug: Login status check via API:', { status: response.status, isLoggedIn });
            return isLoggedIn;
        } catch (error) {
            console.log('Debug: Login check error, assuming guest:', error);
            return false;
        }
    }

    // Get current cart items
    async getCart() {
        await this.loginCheckPromise; // Wait for login check to complete
        if (this.isLoggedIn) {
            return await this.getDatabaseCart();
        } else {
            return this.getLocalStorageCart();
        }
    }

    // Get cart from localStorage
    getLocalStorageCart() {
        try {
            const cartData = localStorage.getItem(this.cartStorageKey);
            return cartData ? JSON.parse(cartData) : [];
        } catch (error) {
            console.error('Error reading localStorage cart:', error);
            return [];
        }
    }

    // Get cart from database (for logged-in users)
    async getDatabaseCart() {
        try {
            const response = await fetch('/customer/cart/', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                return data.cart || [];
            } else {
                console.warn('Failed to fetch database cart, falling back to localStorage');
                return this.getLocalStorageCart();
            }
        } catch (error) {
            console.error('Error fetching database cart:', error);
            return this.getLocalStorageCart();
        }
    }

    // Add item to cart
    async addItem(itemData) {
        await this.loginCheckPromise; // Wait for login check to complete
        const { id, name, price, quantity = 1, notes = null } = itemData;

        if (this.isLoggedIn) {
            return await this.addItemToDatabase(itemData);
        } else {
            return this.addItemToLocalStorage(itemData);
        }
    }

    // Add item to localStorage
    addItemToLocalStorage(itemData) {
        try {
            let cartItems = this.getLocalStorageCart();
            const existingItemIndex = cartItems.findIndex(item => item.id === itemData.id);

            if (existingItemIndex > -1) {
                // Update existing item quantity
                cartItems[existingItemIndex].quantity += itemData.quantity;
            } else {
                // Add new item
                cartItems.push({
                    id: itemData.id,
                    name: itemData.name,
                    price: itemData.price,
                    quantity: itemData.quantity,
                    notes: itemData.notes,
                    image: itemData.image
                });
            }

            localStorage.setItem(this.cartStorageKey, JSON.stringify(cartItems));
            return { success: true, cart_count: this.getTotalQuantity(cartItems) };
        } catch (error) {
            console.error('Error adding to localStorage cart:', error);
            return { success: false, error: error.message };
        }
    }

    // Add item to database
    async addItemToDatabase(itemData) {
        console.log('Debug: Adding to database:', itemData);
        
        try {
            const response = await fetch('/customer/cart/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    menu_item_id: itemData.id,
                    quantity: itemData.quantity,
                    special_notes: itemData.notes
                })
            });

            console.log('Debug: Database response status:', response.status);

            // Check if response is JSON before parsing
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.warn('Non-JSON response received, using localStorage only. Content-Type:', contentType);
                return this.addItemToLocalStorage(itemData);
            }

            const data = await response.json();
            console.log('Debug: Database response data:', data);

            if (response.ok) {
                // Also update localStorage as backup
                this.addItemToLocalStorage(itemData);
                return data;
            } else {
                console.warn('Failed to add to database, using localStorage only:', data);
                return this.addItemToLocalStorage(itemData);
            }
        } catch (error) {
            console.error('Error adding to database cart:', error);
            // Fall back to localStorage - cart will still work
            return this.addItemToLocalStorage(itemData);
        }
    }

    // Update item quantity
    async updateItem(itemId, quantity) {
        await this.loginCheckPromise; // Wait for login check to complete
        if (this.isLoggedIn) {
            return await this.updateItemInDatabase(itemId, quantity);
        } else {
            return this.updateItemInLocalStorage(itemId, quantity);
        }
    }

    // Update item in localStorage
    updateItemInLocalStorage(itemId, quantity) {
        try {
            let cartItems = this.getLocalStorageCart();
            const itemIndex = cartItems.findIndex(item => item.id === itemId);

            if (itemIndex > -1) {
                if (quantity <= 0) {
                    cartItems.splice(itemIndex, 1);
                } else {
                    cartItems[itemIndex].quantity = quantity;
                }
                localStorage.setItem(this.cartStorageKey, JSON.stringify(cartItems));
                return { success: true, cart_count: this.getTotalQuantity(cartItems) };
            }
            return { success: false, error: 'Item not found' };
        } catch (error) {
            console.error('Error updating localStorage cart:', error);
            return { success: false, error: error.message };
        }
    }

    // Update item in database
    async updateItemInDatabase(itemId, quantity) {
        try {
            const response = await fetch(`/customer/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quantity })
            });

            // Check if response is JSON before parsing
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.warn('Non-JSON response received for update, using localStorage only');
                return this.updateItemInLocalStorage(itemId, quantity);
            }

            const data = await response.json();

            if (response.ok) {
                // Also update localStorage as backup
                this.updateItemInLocalStorage(itemId, quantity);
                return data;
            } else {
                console.warn('Failed to update database, using localStorage only');
                return this.updateItemInLocalStorage(itemId, quantity);
            }
        } catch (error) {
            console.error('Error updating database cart:', error);
            return this.updateItemInLocalStorage(itemId, quantity);
        }
    }

    // Remove item from cart
    async removeItem(itemId) {
        return await this.updateItem(itemId, 0);
    }

    // Clear entire cart
    async clearCart() {
        await this.loginCheckPromise; // Wait for login check to complete
        if (this.isLoggedIn) {
            await this.clearDatabaseCart();
        }
        this.clearLocalStorageCart();
    }

    // Clear localStorage cart
    clearLocalStorageCart() {
        localStorage.removeItem(this.cartStorageKey);
    }

    // Clear database cart
    async clearDatabaseCart() {
        try {
            await fetch('/customer/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.error('Error clearing database cart:', error);
        }
    }

    // Merge localStorage cart with database (called on login)
    async mergeCartOnLogin() {
        await this.loginCheckPromise; // Wait for login check to complete
        if (!this.isLoggedIn) return;

        const localCart = this.getLocalStorageCart();
        if (localCart.length === 0) return;

        try {
            const response = await fetch('/customer/cart/merge', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_items: localCart.map(item => ({
                        id: item.id,
                        quantity: item.quantity,
                        notes: item.notes
                    }))
                })
            });

            if (response.ok) {
                console.log('Cart merged successfully on login');
                // Optionally clear localStorage after successful merge
                // this.clearLocalStorageCart();
            }
        } catch (error) {
            console.error('Error merging cart on login:', error);
        }
    }

    // Get total quantity of items in cart
    getTotalQuantity(cartItems = null) {
        if (!cartItems) {
            cartItems = this.getLocalStorageCart();
        }
        return cartItems.reduce((total, item) => total + item.quantity, 0);
    }

    // Get total price of items in cart
    getTotalPrice(cartItems = null) {
        if (!cartItems) {
            cartItems = this.getLocalStorageCart();
        }
        return cartItems.reduce((total, item) => {
            const price = parseFloat(item.price.replace(/[^\d.]/g, '')) || 0;
            return total + (price * item.quantity);
        }, 0);
    }

    // Update cart count display in UI
    updateCartCountDisplay(count = null) {
        if (count === null) {
            const cartItems = this.getLocalStorageCart();
            count = this.getTotalQuantity(cartItems);
        }

        // Update all cart count elements
        const cartCountElements = document.querySelectorAll('.cart-count, .cart-counter, #cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline-block' : 'none';
        });
    }
}

// Create global cart manager instance
window.cartManager = new CartManager();

// Auto-merge cart on page load if user is logged in
document.addEventListener('DOMContentLoaded', () => {
    if (window.cartManager.isLoggedIn) {
        window.cartManager.mergeCartOnLogin();
    }
    // Update cart count display on page load
    window.cartManager.updateCartCountDisplay();
});