document.addEventListener('DOMContentLoaded', () => {
    console.log('Debug: Payment page loaded');

    // Add browser back button confirmation
    let isPaymentProcessing = false;

    // Note: We don't use beforeunload as it shows ugly browser alerts
    // and would interfere with legitimate redirects to payment gateway
    // Instead, we only handle the back button with a custom modal

    // --- CUSTOM CONFIRM MODAL (defined first so it's available) ---
    function showConfirmModal(title, message, onConfirm, onCancel) {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay confirm-modal-overlay';
        overlay.style.display = 'flex';
        overlay.style.animation = 'fadeIn 0.2s ease-out';

        // Create modal content
        overlay.innerHTML = `
            <div class="modal-content confirm-modal-content" style="max-width: 450px; animation: slideUp 0.3s ease-out;">
                <div class="modal-icon" style="width: 60px; height: 60px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; background: var(--warning, #f59e0b); color: white; border-radius: 50%; font-size: 28px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 style="margin-bottom: 1rem; font-size: 1.5rem;">${title}</h2>
                <p style="color: var(--text-secondary, #666); margin-bottom: 2rem; font-size: 1rem;">${message}</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="modal-btn modal-btn-secondary" id="confirm-cancel-btn" style="flex: 1; padding: 0.75rem 1.5rem; border: 2px solid #e5e7eb; background: white; color: #374151; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                        Stay Here
                    </button>
                    <button class="modal-btn modal-btn-primary" id="confirm-ok-btn" style="flex: 1; padding: 0.75rem 1.5rem; background: var(--danger, #ef4444); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                        Yes, Go Back
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Add inline styles for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .modal-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .modal-btn-secondary:hover {
                background: #f9fafb !important;
            }
            .modal-btn-primary:hover {
                background: #dc2626 !important;
            }
        `;
        document.head.appendChild(style);

        // Handle buttons
        const cancelBtn = overlay.querySelector('#confirm-cancel-btn');
        const okBtn = overlay.querySelector('#confirm-ok-btn');

        cancelBtn.addEventListener('click', () => {
            document.body.removeChild(overlay);
            document.head.removeChild(style);
            if (onCancel) onCancel();
        });

        okBtn.addEventListener('click', () => {
            document.body.removeChild(overlay);
            document.head.removeChild(style);
            if (onConfirm) onConfirm();
        });

        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                document.body.removeChild(overlay);
                document.head.removeChild(style);
                if (onCancel) onCancel();
            }
        });
    }

    // Detect back button and show confirmation
    // Note: This is defined inside DOMContentLoaded, so showConfirmModal needs to be accessible
    window.history.pushState(null, '', window.location.href);
    window.addEventListener('popstate', (e) => {
        console.log('Debug: Back button pressed, isPaymentProcessing:', isPaymentProcessing);

        if (!isPaymentProcessing) {
            // Push state back so modal stays on payment page
            window.history.pushState(null, '', window.location.href);

            showConfirmModal(
                'Go Back?',
                'Are you sure you want to go back? Your order will be cancelled.',
                () => {
                    // User confirmed - allow back
                    console.log('Debug: User confirmed going back');
                    isPaymentProcessing = true; // Prevent further prompts
                    // Go back 2 times because we pushed state when modal showed
                    window.history.go(-2);
                },
                () => {
                    // User cancelled - stay on page
                    console.log('Debug: User cancelled going back, staying on page');
                    // State already pushed, just stay here
                }
            );
        } else {
            // If payment is processing, allow navigation
            console.log('Debug: Payment processing, allowing back navigation');
        }
    });

    // Intercept "Back to Menu" link clicks
    const backLink = document.querySelector('.back-link');
    if (backLink) {
        backLink.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default navigation
            console.log('Debug: Back to Menu link clicked');

            if (!isPaymentProcessing) {
                showConfirmModal(
                    'Leave Payment Page?',
                    'Are you sure you want to go back to menu? Your order will be cancelled.',
                    () => {
                        // User confirmed - navigate to menu
                        console.log('Debug: User confirmed leaving payment page');
                        isPaymentProcessing = true; // Prevent further prompts
                        window.location.href = backLink.href;
                    },
                    () => {
                        // User cancelled - stay on payment page
                        console.log('Debug: User cancelled, staying on payment page');
                    }
                );
            } else {
                // If payment is processing, allow navigation
                window.location.href = backLink.href;
            }
        });
    }

    // --- ORDER SUMMARY LOGIC ---
    const orderItemsContainer = document.getElementById('order-items');
    const grandTotalEl = document.getElementById('grand-total');

    console.log('Debug: Looking for elements...');
    console.log('Debug: orderItemsContainer:', orderItemsContainer);
    console.log('Debug: grandTotalEl:', grandTotalEl);

    // Don't return early - we still need to attach form handlers even if summary elements are missing
    if (!orderItemsContainer || !grandTotalEl) {
        console.warn('Payment summary elements not found - cart display may not work');
        console.warn('Order items container:', orderItemsContainer);
        console.warn('Grand total element:', grandTotalEl);
    }

    let cart = [];
    const checkoutCartData = sessionStorage.getItem('checkoutCart');
    const currentOrderData = sessionStorage.getItem('currentOrder');

    console.log('Debug: checkoutCartData:', checkoutCartData);
    console.log('Debug: currentOrderData:', currentOrderData);

    // Get selected payment method and order type from cart checkout or Order Now
    // Default to 'card' (online banking) if not set - will be selected in UI below
    let selectedPaymentMethod = sessionStorage.getItem('selectedPaymentMethod') || 'card';
    let selectedOrderType = sessionStorage.getItem('selectedOrderType') || 'dine_in'; // Get from sessionStorage

    // IMPORTANT: Check currentOrder FIRST (single item "Order Now")
    // Then check checkoutCart (cart checkout)
    // This ensures the most recent action takes priority
    if (currentOrderData) {
        const singleOrder = JSON.parse(currentOrderData);

        // Validate that the order has an item_id
        if (!singleOrder.item_id) {
            console.error('Error: Order data is missing item_id!', singleOrder);
            if (orderItemsContainer) {
                orderItemsContainer.innerHTML = `
                    <div class="error-message" style="padding: 20px; text-align: center; background: #fee; border-radius: 10px; margin: 20px 0;">
                        <p style="color: #c00; font-weight: bold; margin-bottom: 10px;">⚠️ Error Loading Order</p>
                        <p style="margin-bottom: 15px;">There was a problem loading your order. The item information is incomplete.</p>
                        <a href="/customer/menu" class="btn" style="display: inline-block; padding: 10px 20px; background: #6366f1; color: white; text-decoration: none; border-radius: 8px;">Return to Menu</a>
                    </div>
                `;
            }
            return;
        }

        cart = [{
            id: singleOrder.item_id,
            name: singleOrder.item_name,
            price: singleOrder.item_price,
            quantity: singleOrder.quantity,
            notes: singleOrder.notes,
            // Remove payment_method from order data - user will select on this page
        }];
        // Don't override payment method from order data - user selects on this page
        selectedOrderType = singleOrder.order_type || 'dine_in';
        console.log('Debug: Cart loaded from currentOrder (single item):', cart);
        console.log('Debug: Order type from order:', selectedOrderType);
    } else if (checkoutCartData) {
        cart = JSON.parse(checkoutCartData);
        console.log('Debug: Cart loaded from checkoutCart (multiple items):', cart);
        console.log('Debug: Order type from cart checkout:', selectedOrderType);
    }

    console.log('Debug: Final cart:', cart);

    // Update order type display
    const orderTypeTextEl = document.getElementById('order-type-text');
    if (orderTypeTextEl) {
        const orderTypeLabel = selectedOrderType === 'dine_in' ? 'Dine In 🍽️' : 'Takeaway 🥡';
        orderTypeTextEl.textContent = orderTypeLabel;
    }

    if (cart.length === 0) {
        if (orderItemsContainer) orderItemsContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty. <a href="/customer/menu">Go back to menu</a> to add items.</p>';
        if (grandTotalEl) grandTotalEl.textContent = 'RM 0.00';
    } else if (orderItemsContainer && grandTotalEl) {
        // --- REVISED TOTALS CALCULATION ---

        // 1. Get totals from sessionStorage (passed from cart modal)
        const promoDiscount = parseFloat(sessionStorage.getItem('checkoutPromoDiscount')) || 0;
        const voucherDiscount = parseFloat(sessionStorage.getItem('checkoutVoucherDiscount')) || 0;
        const finalTotalFromSession = parseFloat(sessionStorage.getItem('checkoutFinalTotal'));

        // 2. Calculate subtotal from cart items (for display and validation)
        let subtotal = 0;
        orderItemsContainer.innerHTML = '';
        cart.forEach(item => {
            const priceString = item.price || '0';
            const priceMatch = priceString.match(/[\d.]+/);
            const unitPrice = priceMatch ? parseFloat(priceMatch[0]) : 0;
            const itemTotal = unitPrice * item.quantity;
            subtotal += itemTotal;

            const itemElement = document.createElement('div');
            itemElement.classList.add('order-item');
            itemElement.innerHTML = `
                ${item.image ? `
                    <div class="item-image" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <img src="${item.image}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                    </div>
                ` : `
                    <div class="item-image placeholder" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="fas fa-utensils"></i>
                    </div>
                `}
                <div class="item-details">
                    <h4 class="item-name">${item.name}</h4>
                    ${item.notes ? `<p class="item-notes">${item.notes}</p>` : ''}
                    <div class="item-price-qty">
                        <span class="item-price">RM ${unitPrice.toFixed(2)}</span>
                        <span class="item-quantity">x${item.quantity}</span>
                    </div>
                </div>
                <div class="item-total">
                    RM ${itemTotal.toFixed(2)}
                </div>
            `;
            orderItemsContainer.appendChild(itemElement);
        });

        // 3. Determine the final total to use
        // Use the value from session storage if it exists, otherwise calculate it.
        const finalTotal = !isNaN(finalTotalFromSession) ? finalTotalFromSession : (subtotal - promoDiscount - voucherDiscount);

        // 4. Update the UI elements
        const subtotalEl = document.getElementById('subtotal');
        const subtotalRow = document.getElementById('subtotal-row');
        const promoDiscountRow = document.getElementById('promo-discount-row');
        const promoDiscountEl = document.getElementById('promo-discount');
        const voucherDiscountRow = document.getElementById('voucher-discount-row');
        const voucherDiscountEl = document.getElementById('voucher-discount');

        // Show subtotal row if we have any discounts
        if (promoDiscount > 0 || voucherDiscount > 0) {
            if (subtotalRow && subtotalEl) {
                subtotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
                subtotalRow.style.display = 'flex';
            }
        }

        if (promoDiscount > 0 && promoDiscountRow && promoDiscountEl) {
            promoDiscountEl.textContent = `-RM ${promoDiscount.toFixed(2)}`;
            promoDiscountRow.style.display = 'flex';
        }

        if (voucherDiscount > 0 && voucherDiscountRow && voucherDiscountEl) {
            voucherDiscountEl.textContent = `-RM ${voucherDiscount.toFixed(2)}`;
            voucherDiscountRow.style.display = 'flex';
        }

        grandTotalEl.textContent = `RM ${finalTotal.toFixed(2)}`;

        // Update button text to show amount like in booking payment
        const submitTextEl = document.getElementById('submit-text');
        if (submitTextEl) {
            submitTextEl.textContent = `Pay RM ${finalTotal.toFixed(2)}`;
        }
    }

    // --- FORM SUBMISSION LOGIC ---
    const paymentForm = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-payment');
    const successModal = document.getElementById('success-modal');
    const successOkBtn = document.getElementById('success-ok-btn');

    // --- PAYMENT METHOD SWITCHING LOGIC ---
    const paymentMethods = document.querySelectorAll('.payment-method');
    const cardDetails = document.getElementById('card-payment-section');
    const cashDetails = document.getElementById('cash-payment-section');
    const receiptSection = document.getElementById('receipt-section');

    // Use selectedPaymentMethod from above (defaults to 'card' if not set)
    let selectedMethod = selectedPaymentMethod;

    // Get final total for button text (already calculated above)
    function getFinalTotal() {
        const promoDiscount = parseFloat(sessionStorage.getItem('checkoutPromoDiscount')) || 0;
        const voucherDiscount = parseFloat(sessionStorage.getItem('checkoutVoucherDiscount')) || 0;
        const finalTotalFromSession = parseFloat(sessionStorage.getItem('checkoutFinalTotal'));

        if (!isNaN(finalTotalFromSession)) {
            return finalTotalFromSession;
        }
        // Fallback calculation if session data is missing
        const subtotal = cart.reduce((acc, item) => acc + (parseFloat(item.price.match(/[\d.]+/)[0] || 0) * item.quantity), 0);
        return subtotal - promoDiscount - voucherDiscount;
    }

    // Set initial UI state based on selected method
    if (selectedMethod === 'card') {
        paymentMethods.forEach(m => {
            if (m.getAttribute('data-method') === 'card') {
                m.classList.add('selected');
            } else {
                m.classList.remove('selected');
            }
        });
        if (cardDetails) cardDetails.style.display = 'block';
        if (cashDetails) cashDetails.style.display = 'none';
        if (receiptSection) receiptSection.style.display = 'block';
    } else if (selectedMethod === 'cash') {
        paymentMethods.forEach(m => {
            if (m.getAttribute('data-method') === 'cash') {
                m.classList.add('selected');
            } else {
                m.classList.remove('selected');
            }
        });
        if (cardDetails) cardDetails.style.display = 'none';
        if (cashDetails) cashDetails.style.display = 'block';
        if (receiptSection) receiptSection.style.display = 'block';
    }

    // Handle payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            paymentMethods.forEach(m => m.classList.remove('selected'));

            // Add selected class to clicked method
            this.classList.add('selected');

            // Get the payment method
            selectedMethod = this.getAttribute('data-method');

            const finalTotal = getFinalTotal();

            // Show/hide payment sections based on payment method
            if (selectedMethod === 'card') {
                cardDetails.style.display = 'block';
                cashDetails.style.display = 'none';
                receiptSection.style.display = 'block';
                submitButton.innerHTML = `<i class="fas fa-university"></i> <span id="submit-text">Pay via FPX - RM ${finalTotal.toFixed(2)}</span>`;
            } else if (selectedMethod === 'cash') {
                cardDetails.style.display = 'none';
                cashDetails.style.display = 'block';
                receiptSection.style.display = 'block';
                submitButton.innerHTML = `<i class="fas fa-check"></i> <span id="submit-text">Order Now</span>`;
            }

            // Update the selected payment method for form submission
            selectedPaymentMethod = selectedMethod;
        });
    });

    // Function to reset button state
    function resetPayButton() {
        if (submitButton) {
            submitButton.disabled = false;

            // Get total from cart
            let total = 0;
            cart.forEach(item => {
                const priceString = item.price || '0';
                const priceMatch = priceString.match(/[\d.]+/);
                const unitPrice = priceMatch ? parseFloat(priceMatch[0]) : 0;
                total += unitPrice * item.quantity;
            });

            submitButton.innerHTML = `<i class="fas fa-lock"></i> <span>Pay RM ${total.toFixed(2)}</span>`;
        }
    }

    console.log('Debug: Payment form element:', paymentForm);
    console.log('Debug: Attaching form submit handler...');

    if (paymentForm) {
        console.log('Debug: Form found! Attaching event listener...');
        paymentForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent actual form submission

            console.log('Debug: Form submitted via event listener');
            console.log('Debug: Cart at submission:', cart);
            console.log('Debug: Cart[0] contents:', cart[0]);
            console.log('Debug: Cart[0].id:', cart[0]?.id);

            if (cart.length === 0) {
                Toast.warning('Cart Empty', 'Please add items before checkout.');
                return;
            }

            // Show loading state
            isPaymentProcessing = true; // Disable back button confirmation during payment
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Gather form data
            const email = document.getElementById('receipt-email')?.value || '';

            // Determine if this is from cart checkout or Order Now
            const isFromCart = !!checkoutCartData;

            // Get promo discount, voucher discount and final total from sessionStorage
            const promoDiscount = parseFloat(sessionStorage.getItem('checkoutPromoDiscount')) || 0;
            const voucherDiscount = parseFloat(sessionStorage.getItem('checkoutVoucherDiscount')) || 0;
            const finalTotal = parseFloat(sessionStorage.getItem('checkoutFinalTotal')) || 0;
            const appliedPromoCode = sessionStorage.getItem('checkoutAppliedPromoCode') || null; // Get the applied promo code

            // Map payment method from UI to backend values
            let backendPaymentMethod = 'online'; // default
            if (selectedMethod === 'card') {
                backendPaymentMethod = 'online'; // Online banking uses online payment gateway
            } else if (selectedMethod === 'cash') {
                backendPaymentMethod = 'counter'; // Pay at counter
            }

            const payload = {
                cart: cart,
                is_from_cart: isFromCart,
                promo_discount: promoDiscount,
                promo_code: appliedPromoCode,
                voucher_discount: voucherDiscount, // Add voucher discount
                final_total: finalTotal,
                payment_details: {
                    method: backendPaymentMethod,
                    order_type: selectedOrderType,
                    email: email,
                    // In a real scenario, you would collect more details here
                    // For card: card number, expiry, cvv from a secure payment gateway integration
                    // For wallet: phone number
                }
            };

            console.log('Debug: Payload to send:', payload);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send data to the server
            fetch('/customer/payment/place-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                console.log('Debug: Response status:', response.status);
                console.log('Debug: Response headers:', response.headers);

                // Handle CSRF token mismatch (419)
                if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page and try again.');
                }

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Debug: Content-Type:', contentType);

                if (!contentType || !contentType.includes('application/json')) {
                    // Log the response text for debugging
                    return response.text().then(text => {
                        console.error('Debug: Non-JSON response:', text.substring(0, 500));
                        throw new Error('Invalid response from server. Please check console for details.');
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('Debug: Server response:', data);
                if (data.success) {
                    console.log('Debug: Payment successful!');

                    if (data.payment_method === 'gateway') {
                        // Redirect to payment gateway
                        console.log('Debug: Redirecting to payment gateway:', data.redirect_url);

                        // Set flag for order type modal to appear after payment
                        sessionStorage.setItem('justPlacedOrder', 'true');

                        window.location.href = data.redirect_url;
                    } else {
                        // Manual payment - clear cart and show success
                        console.log('Debug: Manual payment! Clearing cart...');

                        // Set flag for order type modal to appear when user goes back to menu
                        sessionStorage.setItem('justPlacedOrder', 'true');

                        // Clear cart from sessionStorage
                        sessionStorage.removeItem('checkoutCart');
                        sessionStorage.removeItem('currentOrder');

                        // Clear cart from localStorage (main cart storage)
                        localStorage.removeItem('cartItems');

                        console.log('Debug: Cart cleared. Showing success modal...');

                        // Show success modal
                        if (successModal) {
                            const successDetails = document.getElementById('success-details');
                            if (successDetails && data.order_id) {
                                successDetails.innerHTML = `<p><strong>Order ID:</strong> ${data.order_id}</p>`;
                            }
                            successModal.style.display = 'flex';
                            console.log('Debug: Success modal displayed');
                        }
                    }
                } else {
                    // Handle failure
                    console.error('Payment failed:', data);
                    Toast.error('Payment Failed', data.message || 'Payment failed. Please try again.');
                    // Re-enable the button
                    isPaymentProcessing = false; // Re-enable back button confirmation
                    resetPayButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toast.error('Error', error.message || 'A critical error occurred. Please try again.');
                // Re-enable the button
                isPaymentProcessing = false; // Re-enable back button confirmation
                resetPayButton();
            });
        });
    }

    // --- SUCCESS MODAL BUTTON LOGIC ---
    if (successOkBtn) {
        successOkBtn.addEventListener('click', () => {
            // Redirect to customer orders page
            window.location.href = '/customer/orders';
        });
    }
});
