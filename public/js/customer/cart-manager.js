/**
 * Session-Based Cart Manager
 * Handles cart operations for both guest users (session) and logged-in users (database)
 * All cart data is stored server-side for security and consistency
 */

class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('Debug: CSRF Token:', this.csrfToken);
    }

    // Get current cart items from server (session or database)
    async getCart() {
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
                console.log('Debug: Cart fetched from server:', data.cart);
                console.log('Debug: Cart items count:', data.cart ? data.cart.length : 0);
                console.log('Debug: Unavailable count:', data.unavailable_count);

                // Return the full response object INCLUDING promotion_groups and regular_items
                return {
                    cart: data.cart || [],
                    regular_items: data.regular_items || [],
                    promotion_groups: data.promotion_groups || [],
                    unavailable_count: data.unavailable_count || 0,
                    available_total: data.available_total || 0,
                    total: data.total || 0,
                    count: data.count || 0,
                    promo_discount: data.promo_discount || 0,
                    final_total: data.final_total,
                    applied_promo_code: data.applied_promo_code
                };
            } else {
                console.error('Failed to fetch cart from server');
                return {
                    cart: [],
                    regular_items: [],
                    promotion_groups: [],
                    unavailable_count: 0,
                    available_total: 0,
                    total: 0,
                    count: 0,
                    promo_discount: 0,
                    final_total: 0
                };
            }
        } catch (error) {
            console.error('Error fetching cart:', error);
            return {
                cart: [],
                regular_items: [],
                promotion_groups: [],
                unavailable_count: 0,
                available_total: 0,
                total: 0,
                count: 0,
                promo_discount: 0,
                final_total: 0
            };
        }
    }

    // Add item to cart (server-side storage)
    async addItem(itemData) {
        console.log('Debug: Adding item to cart:', itemData);

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

            console.log('Debug: Add item response status:', response.status);

            if (response.ok) {
                const data = await response.json();
                console.log('Debug: Item added successfully:', data);
                return data;
            } else {
                const error = await response.json();
                console.error('Failed to add item to cart:', error);
                return { success: false, error: error.message || 'Failed to add item' };
            }
        } catch (error) {
            console.error('Error adding item to cart:', error);
            return { success: false, error: error.message };
        }
    }

    // Update item quantity (server-side storage)
    async updateItem(itemId, quantity) {
        try {
            const response = await fetch(`/customer/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quantity })
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Debug: Item updated successfully:', data);
                return data;
            } else {
                const error = await response.json();
                console.error('Failed to update item:', error);
                // Return all error data including is_locked flag
                return {
                    success: false,
                    message: error.message || 'Failed to update item',
                    is_locked: error.is_locked || false,
                    promotion_name: error.promotion_name || null,
                    promotion_group_id: error.promotion_group_id || null
                };
            }
        } catch (error) {
            console.error('Error updating item:', error);
            return { success: false, error: error.message };
        }
    }

    // Remove item from cart
    async removeItem(itemId) {
        return await this.updateItem(itemId, 0);
    }

    // Clear entire cart (server-side storage)
    async clearCart() {
        try {
            const response = await fetch('/customer/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Debug: Cart cleared successfully:', data);
                return data;
            } else {
                console.error('Failed to clear cart');
                return { success: false };
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            return { success: false, error: error.message };
        }
    }

    // Merge session cart with database (called on login)
    async mergeCartOnLogin() {
        try {
            const response = await fetch('/customer/cart/merge', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Cart merged successfully on login:', data);
                return data;
            } else {
                console.error('Failed to merge cart on login');
                return { success: false };
            }
        } catch (error) {
            console.error('Error merging cart on login:', error);
            return { success: false, error: error.message };
        }
    }

    // Get total quantity of items in cart
    getTotalQuantity(cartItems) {
        // Handle both formats: array or object with cart property
        const items = Array.isArray(cartItems) ? cartItems : (cartItems.cart || []);
        return items.reduce((total, item) => total + item.quantity, 0);
    }

    // Get total price of items in cart
    getTotalPrice(cartItems) {
        // Handle both formats: array or object with cart property
        const items = Array.isArray(cartItems) ? cartItems : (cartItems.cart || []);
        return items.reduce((total, item) => {
            const price = parseFloat(item.price.replace(/[^\d.]/g, '')) || 0;
            return total + (price * item.quantity);
        }, 0);
    }

    // Update cart count display in UI
    async updateCartCountDisplay() {
        try {
            const cartData = await this.getCart();
            const count = cartData.count || 0;

            // Update all cart count elements
            const cartCountElements = document.querySelectorAll('.cart-count, .cart-counter, #cart-count');
            cartCountElements.forEach(element => {
                element.textContent = count;
                element.style.display = count > 0 ? 'inline-block' : 'none';
            });
        } catch (error) {
            console.error('Error updating cart count display:', error);
        }
    }

    // === PROMO CODE METHODS ===

    /**
     * Apply promo code to cart
     */
    async applyPromoCode(promoCode) {
        try {
            const response = await fetch('/customer/cart/promo/apply', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ promo_code: promoCode })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('Promo code applied successfully:', data);
                return {
                    success: true,
                    message: data.message,
                    discount: data.discount_amount,
                    promo_code: data.promo_code,
                    final_total: data.final_total
                };
            } else {
                console.error('Failed to apply promo code:', data.message);
                return {
                    success: false,
                    message: data.message || 'Failed to apply promo code'
                };
            }
        } catch (error) {
            console.error('Error applying promo code:', error);
            return {
                success: false,
                message: 'An error occurred. Please try again.'
            };
        }
    }

    /**
     * Remove promo code from cart
     */
    async removePromoCode() {
        try {
            const response = await fetch('/customer/cart/promo/remove', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('Promo code removed successfully');
                return { success: true, message: data.message };
            } else {
                console.error('Failed to remove promo code');
                return { success: false, message: 'Failed to remove promo code' };
            }
        } catch (error) {
            console.error('Error removing promo code:', error);
            return { success: false, message: 'An error occurred' };
        }
    }

    /**
     * Get current promo code details
     */
    async getPromoCodeDetails() {
        try {
            const response = await fetch('/customer/cart/promo/details', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                return data;
            }
            return { success: true, has_promo: false };
        } catch (error) {
            console.error('Error fetching promo details:', error);
            return { success: true, has_promo: false };
        }
    }

    // === PROMOTION GROUP METHODS ===

    /**
     * Remove entire promotion group from cart (locked items)
     */
    async removePromotionGroup(promotionGroupId) {
        try {
            const response = await fetch(`/customer/cart/promotion-group/${promotionGroupId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('Promotion group removed successfully:', data);
                return {
                    success: true,
                    message: data.message,
                    promotion_name: data.promotion_name,
                    items_removed: data.items_removed,
                    cart_count: data.cart_count
                };
            } else {
                console.error('Failed to remove promotion group:', data.message);
                return {
                    success: false,
                    message: data.message || 'Failed to remove promotion group'
                };
            }
        } catch (error) {
            console.error('Error removing promotion group:', error);
            return {
                success: false,
                message: 'An error occurred. Please try again.'
            };
        }
    }

    /**
     * Check if an item is part of a promotion (locked item)
     */
    isPromotionItem(item) {
        return item && (item.promotion_id || item.is_free_item || item.promotion);
    }

    /**
     * Check if item is locked (cannot be modified individually)
     */
    isItemLocked(item) {
        return this.isPromotionItem(item);
    }
}

// Create global cart manager instance
window.cartManager = new CartManager();

// Update cart count display on page load
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager.updateCartCountDisplay();
});