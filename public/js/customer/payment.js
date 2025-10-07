document.addEventListener('DOMContentLoaded', () => {
    // Refresh CSRF token on page load
    fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        credentials: 'same-origin'
    }).catch(err => console.log('CSRF refresh failed:', err));

    // --- ORDER SUMMARY LOGIC ---
    const orderItemsContainer = document.getElementById('order-items');
    const subtotalEl = document.getElementById('subtotal');
    const grandTotalEl = document.getElementById('grand-total');

    let cart = [];
    const checkoutCartData = sessionStorage.getItem('checkoutCart');
    const currentOrderData = sessionStorage.getItem('currentOrder');

    console.log('Debug: checkoutCartData:', checkoutCartData);
    console.log('Debug: currentOrderData:', currentOrderData);

    // Get selected payment method and order type from cart checkout or Order Now
    let selectedPaymentMethod = sessionStorage.getItem('selectedPaymentMethod') || 'online';
    let selectedOrderType = sessionStorage.getItem('selectedOrderType') || 'dine_in'; // Get from sessionStorage

    if (checkoutCartData) {
        cart = JSON.parse(checkoutCartData);
        console.log('Debug: Cart loaded from checkoutCart:', cart);
        console.log('Debug: Order type from cart checkout:', selectedOrderType);
    } else if (currentOrderData) {
        const singleOrder = JSON.parse(currentOrderData);
        cart = [{
            id: singleOrder.item_id,
            name: singleOrder.item_name,
            price: singleOrder.item_price,
            quantity: singleOrder.quantity,
            notes: singleOrder.notes,
            payment_method: singleOrder.payment_method
        }];
        selectedPaymentMethod = singleOrder.payment_method || 'online';
        selectedOrderType = singleOrder.order_type || 'dine_in';
        console.log('Debug: Cart loaded from currentOrder:', cart);
        console.log('Debug: Payment method from order:', selectedPaymentMethod);
        console.log('Debug: Order type from order:', selectedOrderType);
    }

    console.log('Debug: Final cart:', cart);

    if (!orderItemsContainer || !subtotalEl || !grandTotalEl) {
        console.error('Payment summary elements not found!');
        return;
    }

    if (cart.length === 0) {
        orderItemsContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty. <a href="/customer/food">Go back to menu</a> to add items.</p>';
        subtotalEl.textContent = 'RM 0.00';
        grandTotalEl.textContent = 'RM 0.00';
    } else {
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
                <div class="item-details">
                    <div class="item-name">${item.quantity}x ${item.name}</div>
                    ${item.notes ? `<div class="item-notes">Notes: ${item.notes}</div>` : ''}
                </div>
                <div class="item-price">RM ${itemTotal.toFixed(2)}</div>
            `;
            orderItemsContainer.appendChild(itemElement);
        });
        subtotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
        grandTotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
    }

    // --- PAYMENT METHOD SWITCHING LOGIC ---
    const paymentMethods = document.querySelectorAll('.payment-method');
    const onlineSection = document.getElementById('online-payment-section');
    const counterSection = document.getElementById('counter-payment-section');
    const billingSection = document.querySelector('.form-section:has(#email)') ||
                          document.querySelector('.form-section h3.section-title i.fa-map-marker-alt')?.closest('.form-section');

    // Function to toggle sections based on payment method
    function updatePaymentSections(method) {
        if (onlineSection && counterSection) {
            onlineSection.style.display = (method === 'online') ? 'block' : 'none';
            counterSection.style.display = (method === 'counter') ? 'block' : 'none';
        }

        // Hide billing address for counter payment
        if (billingSection) {
            billingSection.style.display = (method === 'online') ? 'block' : 'none';
        }
    }

    // Pre-select payment method from cart checkout or Order Now modal
    if (selectedPaymentMethod) {
        paymentMethods.forEach(m => m.classList.remove('selected'));
        const methodToSelect = document.querySelector(`.payment-method[data-method="${selectedPaymentMethod}"]`);
        if (methodToSelect) {
            methodToSelect.classList.add('selected');
            updatePaymentSections(selectedPaymentMethod);
        }
    }

    // Handle payment method selection clicks
    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            paymentMethods.forEach(m => m.classList.remove('selected'));
            method.classList.add('selected');
            const selectedMethod = method.dataset.method;

            updatePaymentSections(selectedMethod);

            // Update the selected payment method for form submission
            selectedPaymentMethod = selectedMethod;
        });
    });

    // --- FORM SUBMISSION LOGIC ---
    const paymentForm = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-payment');
    const buttonText = document.getElementById('submit-text');
    const loadingSpinner = document.getElementById('loading-spinner');
    const successModal = document.getElementById('success-modal');
    const orderIdEl = document.getElementById('order-id');

    if (paymentForm) {
        paymentForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent actual form submission

            console.log('Debug: Form submitted');
            console.log('Debug: Cart at submission:', cart);

            if (cart.length === 0) {
                Toast.warning('Cart Empty', 'Please add items before checkout.');
                return;
            }

            // Show loading state
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            submitButton.disabled = true;

            // Gather form data
            const email = document.getElementById('email')?.value || '';

            // Determine if this is from cart checkout or Order Now
            const isFromCart = !!checkoutCartData;

            const payload = {
                cart: cart,
                is_from_cart: isFromCart,
                payment_details: {
                    method: selectedPaymentMethod,
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

                // Handle CSRF token mismatch (419)
                if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page and try again.');
                }

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Invalid response from server. Please try again.');
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
                        window.location.href = data.redirect_url;
                    } else {
                        // Manual payment - clear cart and show success
                        console.log('Debug: Manual payment! Clearing cart...');
                        
                        // Clear cart from sessionStorage
                        sessionStorage.removeItem('checkoutCart');
                        sessionStorage.removeItem('currentOrder');
                        
                        // Clear cart from localStorage (main cart storage)
                        localStorage.removeItem('cartItems');
                        
                        console.log('Debug: Cart cleared. Showing success modal...');
                        
                        // Show success modal
                        if (successModal && orderIdEl) {
                            orderIdEl.textContent = data.order_id;
                            successModal.style.display = 'flex';
                            console.log('Debug: Success modal displayed');
                        }
                    }
                } else {
                    // Handle failure
                    console.error('Payment failed:', data);
                    if (typeof Toast !== 'undefined') {
                        Toast.error('Payment Failed', data.message || 'Payment failed. Please try again.');
                    } else {
                        alert('Payment Failed: ' + (data.message || 'Payment failed. Please try again.'));
                    }
                    // Re-enable the button
                    buttonText.style.display = 'inline-block';
                    loadingSpinner.style.display = 'none';
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Toast !== 'undefined') {
                    Toast.error('Error Occurred', error.message || 'A critical error occurred. Please try again.');
                } else {
                    alert('Error: ' + (error.message || 'A critical error occurred. Please try again.'));
                }
                // Re-enable the button
                buttonText.style.display = 'inline-block';
                loadingSpinner.style.display = 'none';
                submitButton.disabled = false;
            });
        });
    }

    // --- SUCCESS MODAL BUTTONS LOGIC ---
    const viewOrderBtn = document.getElementById('view-order-btn');
    const newOrderBtn = document.getElementById('new-order-btn');

    if(viewOrderBtn) {
        viewOrderBtn.addEventListener('click', () => {
            // Redirect to customer orders page
            window.location.href = '/customer/orders'; 
        });
    }

    if(newOrderBtn) {
        newOrderBtn.addEventListener('click', () => {
            // Redirect back to the main food menu
            window.location.href = '/customer/food';
        });
    }
});
