<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - The Stag - SmartDine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/payment.css') }}">
</head>
<body>
  <!-- Header -->
  <header class="checkout-header">
    <div class="logo-container">
      <div class="logo">🦌</div>
      <h1 class="header-title">The Stag - SmartDine</h1>
    </div>
    <a href="{{ secure_url(route('qr.guest.menu', ['session' => $session->session_code], false)) }}" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Menu
    </a>
  </header>

  <!-- Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Menu</div>
      </div>
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Cart</div>
      </div>
      <div class="progress-step active">
        <div class="step-circle">3</div>
        <div class="step-label">Payment</div>
      </div>
      <div class="progress-step">
        <div class="step-circle">4</div>
        <div class="step-label">Confirmation</div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <main class="checkout-container">
    <!-- Order Summary -->
    <section class="order-summary">
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Table Order
      </h2>

      <!-- Table Information -->
      <div class="order-item" style="background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: white; margin-bottom: 1rem; border-radius: var(--radius);">
        <div class="item-details" style="margin-left: 1rem;">
          <h4 style="color: white; margin-bottom: 0.5rem; margin-left: 0.5rem;">Table {{ $session->table->table_number }}</h4>
          <p style="color: rgba(255,255,255,0.9); margin: 0.25rem 0 0.25rem 0.5rem; font-size: 0.9rem;"><strong>Order Type:</strong> Dine In 🍽️</p>
        </div>
      </div>

      <!-- Order Items -->
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Order Summary
      </h2>
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
          <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="fas fa-shopping-cart"></i></div>
          <p>No items in your order.</p>
        </div>
        @endif
      </div>

      <!-- Order Totals -->
      <div class="order-totals">
        <div class="total-line grand-total">
          <span>Total</span>
          <span id="grand-total">RM {{ number_format($orderData['total_amount'], 2) }}</span>
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

        <!-- Email Receipt -->
        <div class="form-section" id="receipt-section">
          <h3 class="section-title">
            <i class="fas fa-envelope"></i> Receipt Options
          </h3>
          <div class="form-group">
            <label for="receipt-email" class="form-label">Email Receipt (Optional)</label>
            <input type="email" id="receipt-email" class="form-input" placeholder="your@email.com" value="">
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-section">
          <button type="submit" class="submit-btn" id="submit-payment">
            <i class="fas fa-lock"></i>
            <span id="submit-text">Pay RM {{ number_format($orderData['total_amount'], 2) }}</span>
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
      <p>Your order has been placed successfully.</p>
      <div id="success-details"></div>
      <div class="modal-actions">
        <button class="modal-btn modal-btn-primary" id="success-ok-btn">
          <i class="fas fa-box"></i> Track My Order
        </button>
      </div>
    </div>
  </div>

  <!-- Back Confirmation Modal -->
  <div id="back-confirm-modal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-icon" style="color: #f59e0b;">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h2>Leave Payment Page?</h2>
      <p>Are you sure you want to go back? Your order will not be placed.</p>
      <div class="modal-actions">
        <button class="modal-btn modal-btn-secondary" id="back-cancel-btn">
          Stay on Page
        </button>
        <button class="modal-btn modal-btn-primary" id="back-confirm-btn" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
          Yes, Go Back
        </button>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/toast.js') }}"></script>
  <script>
    // Show flash messages
    @if(session('success'))
        Toast.success('Success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        Toast.error('Error', '{{ session('error') }}');
    @endif

    // QR Payment Page JavaScript
    document.addEventListener('DOMContentLoaded', () => {
      console.log('QR Payment page loaded');

      // Show welcome message when page loads (only if not from a flash message)
      @if(!session('success') && !session('error'))
        setTimeout(() => {
          Toast.info('Payment', 'Complete your payment to place order');
        }, 300);
      @endif

      const sessionCode = '{{ $session->session_code }}';
      const totalAmount = {{ $orderData['total_amount'] }};

      // Payment method switching
      const paymentMethods = document.querySelectorAll('.payment-method');
      const cardDetails = document.getElementById('card-payment-section');
      const receiptSection = document.getElementById('receipt-section');
      const submitButton = document.getElementById('submit-payment');
      const submitTextEl = document.getElementById('submit-text');

      let selectedMethod = 'card'; // Default to online banking

      paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
          // Remove selected class from all methods
          paymentMethods.forEach(m => m.classList.remove('selected'));

          // Add selected class to clicked method
          this.classList.add('selected');

          // Get the payment method
          selectedMethod = this.getAttribute('data-method');

          // Show/hide payment sections based on payment method
          if (selectedMethod === 'card') {
            cardDetails.style.display = 'block';
            receiptSection.style.display = 'block';
            submitButton.innerHTML = `<i class="fas fa-university"></i> <span>Pay via FPX - RM ${totalAmount.toFixed(2)}</span>`;
          } else if (selectedMethod === 'cash') {
            cardDetails.style.display = 'none';
            receiptSection.style.display = 'none';
            submitButton.innerHTML = `<i class="fas fa-check"></i> <span>Order Now</span>`;
          }
        });
      });

      // Payment form submission
      const paymentForm = document.getElementById('payment-form');
      const successModal = document.getElementById('success-modal');
      const successOkBtn = document.getElementById('success-ok-btn');

      if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
          e.preventDefault();

          // Show loading state
          submitButton.disabled = true;
          submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

          // Get form data
          const receiptEmail = document.getElementById('receipt-email')?.value || '';

          // Map payment method
          let backendPaymentMethod = selectedMethod === 'card' ? 'card' : 'cash';

          const paymentData = {
            session_code: sessionCode,
            payment_method: backendPaymentMethod,
            receipt_email: receiptEmail,
            _token: '{{ csrf_token() }}'
          };

          console.log('Submitting payment:', paymentData);

          // Send payment request
          fetch('{{ secure_url(route("qr.payment.process", [], false)) }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(paymentData)
          })
          .then(response => {
            // Check if response is ok and is JSON
            if (!response.ok) {
              throw new Error('Payment request failed with status: ' + response.status);
            }
            return response.json();
          })
          .then(data => {
            console.log('Payment response:', data);

            if (data.success) {
              if (data.redirect_url) {
                // Redirect to payment gateway or confirmation
                window.location.href = data.redirect_url;
              } else {
                // Show success modal
                const successDetails = document.getElementById('success-details');
                if (successDetails) {
                  successDetails.innerHTML = `
                    <p><strong>Order ID:</strong> ${data.order_id}</p>
                    <p><strong>Amount:</strong> RM ${parseFloat(data.amount).toFixed(2)}</p>
                  `;
                }
                successModal.style.display = 'flex';
              }
            } else {
              // Show error
              Toast.error('Payment Failed', data.message || 'Payment failed. Please try again.');
              submitButton.disabled = false;
              submitButton.innerHTML = `<i class="fas fa-lock"></i> <span>Pay RM ${totalAmount.toFixed(2)}</span>`;
            }
          })
          .catch(error => {
            console.error('Payment error:', error);
            Toast.error('Error', 'An error occurred while processing your payment. Please try again.');
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-lock"></i> <span>Pay RM ${totalAmount.toFixed(2)}</span>`;
          });
        });
      }

      // Success modal button
      if (successOkBtn) {
        successOkBtn.addEventListener('click', function() {
          window.location.href = '{{ secure_url(route("qr.guest.menu", ["session" => $session->session_code], false)) }}';
        });
      }

      // Back button confirmation with modern modal
      const backLink = document.querySelector('.back-link');
      const backConfirmModal = document.getElementById('back-confirm-modal');
      const backCancelBtn = document.getElementById('back-cancel-btn');
      const backConfirmBtn = document.getElementById('back-confirm-btn');
      let backLinkHref = '';

      if (backLink) {
        backLink.addEventListener('click', function(e) {
          e.preventDefault();
          backLinkHref = this.href;
          backConfirmModal.style.display = 'flex';
        });
      }

      if (backCancelBtn) {
        backCancelBtn.addEventListener('click', function() {
          backConfirmModal.style.display = 'none';
        });
      }

      if (backConfirmBtn) {
        backConfirmBtn.addEventListener('click', function() {
          window.location.href = backLinkHref;
        });
      }

      // Close modal when clicking outside
      if (backConfirmModal) {
        backConfirmModal.addEventListener('click', function(e) {
          if (e.target === this) {
            this.style.display = 'none';
          }
        });
      }

      // Intercept browser back button
      let isLeavingPage = false;

      // Add a dummy state to history so we can intercept back button
      history.pushState({page: 'payment'}, '', '');

      window.addEventListener('popstate', function(e) {
        if (!isLeavingPage) {
          // User pressed back button, show confirmation
          history.pushState({page: 'payment'}, '', '');
          backLinkHref = '{{ route("qr.guest.menu", ["session" => $session->session_code]) }}';
          backConfirmModal.style.display = 'flex';
        }
      });

      // Update confirm button to set flag
      const originalConfirmHandler = backConfirmBtn.onclick;
      backConfirmBtn.addEventListener('click', function() {
        isLeavingPage = true;
        if (backLinkHref) {
          window.location.href = backLinkHref;
        }
      }, true);
    });
  </script>
</body>
</html>
