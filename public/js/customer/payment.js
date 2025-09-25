document.addEventListener('DOMContentLoaded', () => {
    // --- ORDER SUMMARY LOGIC ---
    const orderItemsContainer = document.getElementById('order-items');
    const subtotalEl = document.getElementById('subtotal');
    const grandTotalEl = document.getElementById('grand-total');

    let cart = [];
    const checkoutCartData = sessionStorage.getItem('checkoutCart');
    const currentOrderData = sessionStorage.getItem('currentOrder');

    console.log('Debug: checkoutCartData:', checkoutCartData);
    console.log('Debug: currentOrderData:', currentOrderData);

    if (checkoutCartData) {
        cart = JSON.parse(checkoutCartData);
        console.log('Debug: Cart loaded from checkoutCart:', cart);
    } else if (currentOrderData) {
        const singleOrder = JSON.parse(currentOrderData);
        cart = [{
            id: singleOrder.item_id,
            name: singleOrder.item_name,
            price: singleOrder.item_price,
            quantity: singleOrder.quantity,
            notes: singleOrder.notes
        }];
        console.log('Debug: Cart loaded from currentOrder:', cart);
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
    const cardSection = document.getElementById('card-payment-section');
    const walletSection = document.getElementById('wallet-payment-section');

    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            paymentMethods.forEach(m => m.classList.remove('selected'));
            method.classList.add('selected');
            const selectedMethod = method.dataset.method;
            
            cardSection.style.display = (selectedMethod === 'card') ? 'block' : 'none';
            walletSection.style.display = (selectedMethod === 'wallet') ? 'block' : 'none';
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
                alert('Your cart is empty. Please add items before checkout.');
                return;
            }

            // Show loading state
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            submitButton.disabled = true;

            // Gather form data
            const selectedMethod = document.querySelector('.payment-method.selected').dataset.method;
            const email = document.getElementById('email')?.value || '';

            const payload = {
                cart: cart,
                payment_details: {
                    method: selectedMethod,
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
                    alert(data.message || 'Payment failed. Please try again.');
                    // Re-enable the button
                    buttonText.style.display = 'inline-block';
                    loadingSpinner.style.display = 'none';
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A critical error occurred. Please check the console and try again.');
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
