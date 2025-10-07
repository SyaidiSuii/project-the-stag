<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - The Stag - SmartDine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/customer/payment.css') }}">
</head>
<body>
  <!-- Header -->
  <header class="checkout-header">
    <div class="logo-container">
      <div class="logo">🦌</div>
      <h1 class="header-title">The Stag - SmartDine</h1>
    </div>
    <a href="{{ route('qr.cart', ['session' => $session->session_code]) }}" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Cart
    </a>
  </header>
  
  <!-- Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Order</div>
      </div>
      <div class="progress-step active">
        <div class="step-circle">2</div>
        <div class="step-label">Payment</div>
      </div>
      <div class="progress-step">
        <div class="step-circle">3</div>
        <div class="step-label">Confirmation</div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <main class="checkout-container">
    <!-- Order Summary -->
    <section class="order-summary">
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Order Summary
      </h2>
      
      <!-- Order Items -->
      <div class="order-items" id="order-items">
        @if(!empty($orderData['items']))
          @foreach($orderData['items'] as $item)
            <div class="order-item">
              @if($item['image'])
                <div class="item-image" style="width: 50px; height: 50px; flex-shrink: 0;">
                  <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                </div>
              @else
                <div class="item-image placeholder" style="width: 50px; height: 50px; flex-shrink: 0;">
                  <i class="fas fa-utensils"></i>
                </div>
              @endif
              <div class="item-details">
                <h4 class="item-name">{{ $item['name'] }}</h4>
                @if(!empty($item['notes']))
                  <p class="item-notes">{{ $item['notes'] }}</p>
                @endif
                <div class="item-price-qty">
                  <span class="item-price">RM {{ number_format($item['price'], 2) }}</span>
                  <span class="item-quantity">x{{ $item['quantity'] }}</span>
                </div>
              </div>
              <div class="item-total">
                RM {{ number_format($item['total'], 2) }}
              </div>
            </div>
          @endforeach
        @else
          <div style="text-align: center; padding: 2rem; color: var(--text-light);">
            <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="fas fa-calendar-check"></i></div>
            <p>No items in your order.</p>
          </div>
        @endif
      </div>

      <!-- Order Totals -->
      <div class="order-totals">
        <div class="total-line grand-total">
          <span>Total</span>
          <span>RM {{ number_format($orderData['total_amount'], 2) }}</span>
        </div>
      </div>
      
      <div class="security-badge">
        <i class="fas fa-shield-alt"></i>
        <span>Secure Payment</span>
      </div>
    </section>

    <!-- Payment Form -->
    <section class="payment-form-container">
      <h2 class="form-title">Payment Details</h2>
      
      <!-- Payment Method Selection -->
      <div class="form-section">
        <h3 class="section-title">
          <i class="fas fa-credit-card"></i> Payment Method
        </h3>
        <div class="payment-methods">
          <div class="payment-method selected" data-method="card">
            <div class="method-icon">
              <i class="fas fa-university"></i>
            </div>
            <div class="method-name">Online Banking</div>
          </div>
          <div class="payment-method" data-method="wallet">
            <div class="method-icon">
              <i class="fas fa-wallet"></i>
            </div>
            <div class="method-name">E-Wallet</div>
          </div>
          <div class="payment-method" data-method="cash">
            <div class="method-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="method-name">Pay at Restaurant</div>
          </div>
        </div>
      </div>
      
      <!-- Card Payment Form -->
      <form id="payment-form">
        <div class="form-section" id="card-payment-section">
          <h3 class="section-title">
            <i class="fas fa-lock"></i> Online Banking Payment
          </h3>
          <div class="banking-info-card">
            <div class="banking-icon">
              <i class="fas fa-university"></i>
            </div>
            <div class="banking-details">
              <h4>Secure Online Banking Payment</h4>
              <p>You will be redirected to ToyyibPay's secure payment page where you can:</p>
              <ul class="banking-features">
                <li><i class="fas fa-check"></i> Choose from all major Malaysian banks</li>
                <li><i class="fas fa-check"></i> Login securely through your bank's official page</li>
                <li><i class="fas fa-check"></i> Complete payment with your existing banking credentials</li>
                <li><i class="fas fa-check"></i> Get instant payment confirmation</li>
              </ul>
            </div>
          </div>
          <div class="supported-banks">
            <h5>Supported Banks Include:</h5>
            <div class="bank-logos">
              <span class="bank-name">Maybank2u</span>
              <span class="bank-name">CIMB Clicks</span>
              <span class="bank-name">Public Bank</span>
              <span class="bank-name">RHB Now</span>
              <span class="bank-name">Hong Leong</span>
              <span class="bank-name">AmBank</span>
              <span class="bank-name">Bank Islam</span>
              <span class="bank-name">BSN</span>
              <span class="bank-name">And more...</span>
            </div>
          </div>
        </div>

        <!-- E-Wallet Payment Section -->
        <div class="form-section" id="wallet-payment-section" style="display: none;">
          <h3 class="section-title">
            <i class="fas fa-mobile-alt"></i> E-Wallet Payment
          </h3>
          <div class="form-group">
            <label for="wallet-type" class="form-label">Select E-Wallet</label>
            <select id="wallet-type" class="form-input">
              <option value="">Select your e-wallet</option>
              <option value="touchngo">Touch 'n Go eWallet</option>
              <option value="grabpay">GrabPay</option>
              <option value="boost">Boost</option>
              <option value="maybank">Maybank QRPay</option>
            </select>
            <div class="error-message">Please select an e-wallet</div>
          </div>
          <div class="form-group">
            <label for="phone-number" class="form-label">Phone Number</label>
            <input type="tel" id="phone-number" class="form-input" placeholder="012-345 6789">
            <div class="error-message">Please enter a valid phone number</div>
          </div>
        </div>

        <!-- Email Receipt -->
        <div class="form-section" id="receipt-section">
          <h3 class="section-title">
            <i class="fas fa-envelope"></i> Receipt Options
          </h3>
          <div class="form-group">
            <label for="receipt-email" class="form-label">Email Receipt (Optional)</label>
            <input type="email" id="receipt-email" class="form-input" placeholder="your@email.com">
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-section">
          <button type="submit" class="submit-btn" id="submit-payment">
            <i class="fas fa-lock"></i>
            <span>Pay RM {{ number_format($orderData['total_amount'], 2) }}</span>
          </button>
          <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Your payment information is secure and encrypted</span>
          </div>
        </div>
      </form>
    </section>
  </main>

  <!-- Success Modal -->
  <div id="success-modal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h2>Payment Successful!</h2>
      <p>Your payment has been processed successfully.</p>
      <div id="success-details"></div>
      <button class="modal-btn modal-btn-primary" id="success-ok-btn">
        Continue to Orders
      </button>
    </div>
  </div>

  <script>
    // Payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
            
            // Add selected class to clicked method
            this.classList.add('selected');
            
            // Show/hide payment sections based on selection
            const selectedMethod = this.dataset.method;
            
            if (selectedMethod === 'card') {
                document.getElementById('card-payment-section').style.display = 'block';
                document.getElementById('wallet-payment-section').style.display = 'none';
            } else if (selectedMethod === 'wallet') {
                document.getElementById('card-payment-section').style.display = 'none';
                document.getElementById('wallet-payment-section').style.display = 'block';
            } else {
                document.getElementById('card-payment-section').style.display = 'none';
                document.getElementById('wallet-payment-section').style.display = 'none';
            }
        });
    });

    // Payment form submission
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-payment');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Processing Payment...';
        
        // Get selected payment method
        const selectedMethod = document.querySelector('.payment-method.selected').dataset.method;
        
        // Get form data
        const paymentData = {
            session_code: '{{ $session->session_code }}', // Include session_code in the request body
            payment_method: selectedMethod,
            receipt_email: document.getElementById('receipt-email').value,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        // Add wallet-specific data if needed
        if (selectedMethod === 'wallet') {
            paymentData.wallet_type = document.getElementById('wallet-type').value;
            paymentData.phone_number = document.getElementById('phone-number').value;
        }
        
        // Send payment request
        fetch('{{ route("qr.payment.process") }}', { // Use route without session parameter
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success modal
                document.getElementById('success-details').innerHTML = `
                    <p><strong>Order ID:</strong> ${data.order_id}</p>
                    <p><strong>Amount:</strong> RM ${parseFloat(data.amount).toFixed(2)}</p>
                `;
                document.getElementById('success-modal').classList.add('active');
                
                // Redirect to confirmation page after a delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 3000);
            } else {
                // Show error
                alert('Payment failed: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your payment. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Success modal button
    document.getElementById('success-ok-btn').addEventListener('click', function() {
        window.location.href = '{{ route("qr.menu", ["session" => $session->session_code]) }}';
    });

    // Initialize payment form with order data
    window.qrOrderData = @json($orderData);
    console.log('QR Order Data:', window.qrOrderData);
  </script>
</body>
</html>