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
                walletDetails.style.display = 'none';
                receiptSection.style.display = 'block';
                payNowBtn.innerHTML = `<i class="fas fa-lock"></i> <span>Pay RM ${window.bookingOrderData.total_amount.toFixed(2)}</span>`;
            } else if (selectedPaymentMethod === 'wallet') {
                cardDetails.style.display = 'none';
                walletDetails.style.display = 'block';
                receiptSection.style.display = 'block';
                payNowBtn.innerHTML = `<i class="fas fa-lock"></i> <span>Pay RM ${window.bookingOrderData.total_amount.toFixed(2)}</span>`;
            } else if (selectedPaymentMethod === 'cash') {
                cardDetails.style.display = 'none';
                walletDetails.style.display = 'none';
                receiptSection.style.display = 'none';
                payNowBtn.innerHTML = `<i class="fas fa-check"></i> <span>Order Now</span>`;
            }
        });
    });

    // Format card number input
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') ?? value;
            this.value = formattedValue;
        });
    }

    // Format expiry date input
    const expiryInput = document.getElementById('expiry-date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });
    }

    // CVV input validation
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }

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

        // Basic validation for card payments
        if (selectedMethod === 'card') {
            const cardNumber = document.getElementById('card-number').value;
            const expiryDate = document.getElementById('expiry-date').value;
            const cvv = document.getElementById('cvv').value;
            const cardName = document.getElementById('card-name').value;

            if (!cardNumber || !expiryDate || !cvv || !cardName) {
                alert('Please fill in all card details.');
                resetPayButton();
                return;
            }

            // Basic card number validation (should be 16 digits)
            const cardDigits = cardNumber.replace(/\s/g, '');
            if (cardDigits.length < 13 || cardDigits.length > 19) {
                alert('Please enter a valid card number.');
                resetPayButton();
                return;
            }

            // Basic expiry validation
            if (expiryDate.length !== 5 || !expiryDate.includes('/')) {
                alert('Please enter a valid expiry date (MM/YY).');
                resetPayButton();
                return;
            }

            // Basic CVV validation
            if (cvv.length < 3 || cvv.length > 4) {
                alert('Please enter a valid CVV.');
                resetPayButton();
                return;
            }
        }

        // Validation for e-wallet payments
        if (selectedMethod === 'wallet') {
            const walletType = document.getElementById('wallet-type').value;
            const phoneNumber = document.getElementById('phone-number').value;

            if (!walletType) {
                alert('Please select an e-wallet.');
                resetPayButton();
                return;
            }

            if (!phoneNumber) {
                alert('Please enter your phone number.');
                resetPayButton();
                return;
            }

            // Basic phone number validation (Malaysian format)
            const phoneRegex = /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/;
            if (!phoneRegex.test(phoneNumber.replace(/[\s-]/g, ''))) {
                alert('Please enter a valid Malaysian phone number.');
                resetPayButton();
                return;
            }
        }

        // Prepare payment data
        const paymentData = {
            payment_details: {
                method: selectedMethod,
                email: document.getElementById('receipt-email').value || null
            }
        };

        // Add card details if card payment
        if (selectedMethod === 'card') {
            paymentData.payment_details.card = {
                number: document.getElementById('card-number').value,
                expiry: document.getElementById('expiry-date').value,
                cvv: document.getElementById('cvv').value,
                name: document.getElementById('card-name').value
            };
        }

        // Add wallet details if e-wallet payment
        if (selectedMethod === 'wallet') {
            paymentData.payment_details.wallet = {
                type: document.getElementById('wallet-type').value,
                phone: document.getElementById('phone-number').value
            };
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
                showSuccessModal(data);
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
        payNowBtn.innerHTML = `<i class="fas fa-lock"></i> Pay RM ${window.bookingOrderData.total_amount.toFixed(2)}`;
    }

    function showSuccessModal(data) {
        const successDetails = document.getElementById('success-details');
        successDetails.innerHTML = `
            <div class="success-details">
                <p><strong>Order ID:</strong> ${data.order_id}</p>
                <p><strong>Amount Paid:</strong> RM ${window.bookingOrderData.total_amount.toFixed(2)}</p>
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
    const initialMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (initialMethod !== 'card') {
        cardDetails.style.display = 'none';
    }
});