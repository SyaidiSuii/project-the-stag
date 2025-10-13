document.addEventListener('DOMContentLoaded', function() {
    // Payment form elements
    const paymentForm = document.getElementById('payment-form');
    const paymentMethods = document.querySelectorAll('.payment-method');
    const cardDetails = document.getElementById('card-payment-section');
    const walletDetails = document.getElementById('wallet-payment-section');
    const receiptSection = document.getElementById('receipt-section');
    const payNowBtn = document.querySelector('#submit-payment');
    const successModal = document.getElementById('success-modal');
    const successOkBtn = document.getElementById('success-ok-btn');
    
    let selectedPaymentMethod = 'card'; // Default to card
    
    // Handle payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            paymentMethods.forEach(m => m.classList.remove('selected'));
            
            // Add selected class to clicked method
            this.classList.add('selected');
            
            // Get the payment method
            selectedPaymentMethod = this.getAttribute('data-method');
            
            // Show/hide payment sections based on payment method
            if (selectedPaymentMethod === 'card') {
                cardDetails.style.display = 'block';
                receiptSection.style.display = 'block';
                payNowBtn.innerHTML = `<i class="fas fa-university"></i> <span>Pay via FPX - RM ${parseFloat(window.bookingOrderData.total_amount || 0).toFixed(2)}</span>`;
            } else if (selectedPaymentMethod === 'cash') {
                cardDetails.style.display = 'none';
                receiptSection.style.display = 'none';
                payNowBtn.innerHTML = `<i class="fas fa-check"></i> <span>Order Now</span>`;
            }
        });
    });

    // No card input fields needed for FPX online banking
    // All payment processing handled by ToyyibPay gateway

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment();
    });

    function processPayment() {
        // Disable submit button to prevent double submission
        payNowBtn.disabled = true;
        payNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        // Get selected payment method
        const selectedMethod = selectedPaymentMethod;

        // For online banking (card method), no validation needed - will redirect to ToyyibPay
        if (selectedMethod === 'card') {
            // No card details needed - this will redirect to ToyyibPay gateway
            console.log('Processing online banking payment via ToyyibPay...');
        }

        // Prepare payment data
        const receiptEmailEl = document.getElementById('receipt-email');
        const paymentData = {
            payment_details: {
                method: selectedMethod,
                email: receiptEmailEl ? receiptEmailEl.value : null
            }
        };

        // For FPX online banking (card method), no card details needed - handled by ToyyibPay
        if (selectedMethod === 'card') {
            // No card details to send - ToyyibPay will handle FPX bank selection
            console.log('FPX online banking payment will be handled by ToyyibPay gateway');
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Submit payment
        fetch(`/customer/booking/${window.bookingOrderData.id}/payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Payment response:', data);
            
            if (data.success) {
                if (data.payment_method === 'gateway') {
                    // Redirect to payment gateway
                    window.location.href = data.redirect_url;
                } else {
                    // Show success modal for manual payment
                    showSuccessModal(data);
                }
            } else {
                alert('Payment failed: ' + (data.message || 'Unknown error occurred'));
                resetPayButton();
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            alert('Payment failed: Network error. Please try again.');
            resetPayButton();
        });
    }

    function resetPayButton() {
        payNowBtn.disabled = false;
        if (selectedPaymentMethod === 'card') {
            payNowBtn.innerHTML = `<i class="fas fa-university"></i> <span>Pay via FPX - RM ${parseFloat(window.bookingOrderData.total_amount || 0).toFixed(2)}</span>`;
        } else {
            payNowBtn.innerHTML = `<i class="fas fa-check"></i> <span>Order Now</span>`;
        }
    }

    function showSuccessModal(data) {
        const successDetails = document.getElementById('success-details');
        successDetails.innerHTML = `
            <div class="success-details">
                <p><strong>Order ID:</strong> ${data.order_id}</p>
                <p><strong>Amount Paid:</strong> RM ${parseFloat(window.bookingOrderData.total_amount || 0).toFixed(2)}</p>
                <p class="success-note">Your table reservation is confirmed! Please arrive on time.</p>
            </div>
        `;
        successModal.style.display = 'flex';
    }

    // Success modal OK button
    successOkBtn.addEventListener('click', function() {
        window.location.href = '/customer/orders';
    });

    // Close modal when clicking outside
    successModal.addEventListener('click', function(e) {
        if (e.target === successModal) {
            window.location.href = '/customer/orders';
        }
    });

    // Initialize card details visibility
    const initialMethodElement = document.querySelector('.payment-method.selected');
    const initialMethod = initialMethodElement ? initialMethodElement.getAttribute('data-method') : 'card';
    if (initialMethod !== 'card') {
        cardDetails.style.display = 'none';
    }
});