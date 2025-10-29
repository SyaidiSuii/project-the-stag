<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - The Stag</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <style>
        :root {
            --brand: #6366f1;
            --brand-2: #5856eb;
            --accent: #ff6b35;
            --bg: #f8fafc;
            --card: #ffffff;
            --muted: #e2e8f0;
            --text: #1e293b;
            --text-2: #64748b;
            --text-3: #94a3b8;
            --radius: 20px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }

        .header p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--brand);
            text-decoration: none;
            font-weight: 600;
        }

        .back-button i {
            margin-right: 5px;
        }

        .cart-items {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--muted);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        /* Cart item animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOutDown {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateX(-30px) scale(0.9);
            }
        }

        .cart-item {
            animation: slideInUp 0.4s ease-out;
            transition: all 0.3s ease;
        }

        .cart-item:nth-child(1) { animation-delay: 0.05s; }
        .cart-item:nth-child(2) { animation-delay: 0.1s; }
        .cart-item:nth-child(3) { animation-delay: 0.15s; }
        .cart-item:nth-child(4) { animation-delay: 0.2s; }
        .cart-item:nth-child(5) { animation-delay: 0.25s; }

        .cart-item.removing {
            animation: slideOutDown 0.4s ease forwards;
        }

        .cart-item.updating {
            opacity: 0.5;
            transform: scale(0.98);
        }

        .item-image {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f0f4ff, #e0e7ff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .item-price {
            color: var(--text-2);
            font-size: 0.9rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            background: var(--bg);
            border: 1px solid var(--muted);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .remove-btn {
            background: none;
            border: none;
            color: var(--accent);
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: 15px;
        }

        .cart-summary {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid var(--muted);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            text-align: center;
            border-radius: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            font-size: 1.1rem;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-2);
        }

        .empty-cart-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-cart-text {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-wrap: wrap;
            }

            .item-image {
                margin-bottom: 10px;
            }

            .quantity-controls {
                width: 100%;
                margin-top: 10px;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Your Cart</h1>
            <p>Table {{ $session->table->table_number }}</p>
        </div>

        <a href="{{ secure_url(route('qr.guest.menu', ['session' => $session->session_code], false)) }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Menu
        </a>

        @if (count($cart) > 0)
        <div class="cart-items">
            @foreach ($cart as $item)
            <div class="cart-item">
                <div class="item-image">
                    @if(isset($item['image']) && $item['image'])
                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                    @else
                    🍽️
                    @endif
                </div>
                <div class="item-details">
                    <div class="item-name">{{ $item['name'] }}</div>
                    <div class="item-price">RM {{ number_format($item['price'], 2) }}</div>
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="updateQuantity('{{ $item['id'] }}', -1)">−</button>
                    <span class="quantity">{{ $item['quantity'] }}</span>
                    <button class="quantity-btn" onclick="updateQuantity('{{ $item['id'] }}', 1)">+</button>
                    <button class="remove-btn" onclick="removeItem('{{ $item['id'] }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="cart-summary">
            <div class="summary-total">
                <span>Total:</span>
                <span>RM {{ number_format($cartTotal, 2) }}</span>
            </div>
        </div>

        <a href="{{ secure_url(route('qr.payment', ['session' => $session->session_code], false)) }}" class="checkout-btn">
            <i class="fas fa-credit-card"></i> Proceed to Payment
        </a>
        @else
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <div class="empty-cart-text">Your cart is empty</div>
            <p>Add some delicious items from the menu to get started!</p>
            <a href="{{ secure_url(route('qr.guest.menu', ['session' => $session->session_code], false)) }}" class="checkout-btn" style="margin-top: 20px; display: inline-block;">
                <i class="fas fa-utensils"></i> Browse Menu
            </a>
        </div>
        @endif
    </div>

    <!-- Remove Item Confirmation Modal -->
    <div id="remove-confirm-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div style="background: white; border-radius: 20px; padding: 2.5rem; max-width: 500px; width: 100%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); animation: modalAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <div style="font-size: 4rem; margin-bottom: 1.5rem; color: #f59e0b;">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem; color: #1e293b;">Remove Item?</h2>
            <p style="color: #64748b; margin-bottom: 1.5rem; line-height: 1.6;">Are you sure you want to remove this item from your cart?</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="remove-cancel-btn" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem; background: white; color: #1e293b; border: 2px solid #e2e8f0;">
                    Cancel
                </button>
                <button id="remove-confirm-btn" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    Yes, Remove
                </button>
            </div>
        </div>
    </div>

    <!-- Back Confirmation Modal -->
    <div id="back-confirm-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div style="background: white; border-radius: 20px; padding: 2.5rem; max-width: 500px; width: 100%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); animation: modalAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <div style="font-size: 4rem; margin-bottom: 1.5rem; color: #3b82f6;">
                <i class="fas fa-arrow-left"></i>
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem; color: #1e293b;">Leave Cart?</h2>
            <p style="color: #64748b; margin-bottom: 1.5rem; line-height: 1.6;">Go back to menu? Your cart will be saved.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="back-cancel-btn" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem; background: white; color: #1e293b; border: 2px solid #e2e8f0;">
                    Stay in Cart
                </button>
                <button id="back-confirm-btn" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    Yes, Go Back
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>

    <!-- Toast JavaScript -->
    <script src="{{ asset('js/toast.js') }}"></script>

    <script>
        const sessionCode = '{{ $session->session_code }}';

        function updateQuantity(itemId, change) {
            // Add loading state
            const cartItem = event.target.closest('.cart-item');
            cartItem.classList.add('updating');

            fetch('{{ secure_url(route("qr.cart.update", [], false)) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_code: sessionCode,
                    menu_item_id: itemId,
                    change: change
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add slide out animation before reload
                    cartItem.classList.remove('updating');
                    cartItem.classList.add('removing');

                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    cartItem.classList.remove('updating');
                    Toast.error('Error', data.error || 'Failed to update quantity');
                }
            })
            .catch(error => {
                cartItem.classList.remove('updating');
                console.error('Error:', error);
                Toast.error('Error', 'An error occurred');
            });
        }

        // Remove item with modern modal confirmation
        let pendingRemoveItemId = null;
        const removeModal = document.getElementById('remove-confirm-modal');
        const removeCancelBtn = document.getElementById('remove-cancel-btn');
        const removeConfirmBtn = document.getElementById('remove-confirm-btn');

        function removeItem(itemId) {
            // Store the item ID and show modal
            pendingRemoveItemId = itemId;
            removeModal.style.display = 'flex';
        }

        // Cancel button
        if (removeCancelBtn) {
            removeCancelBtn.addEventListener('click', function() {
                removeModal.style.display = 'none';
                pendingRemoveItemId = null;
            });
        }

        // Confirm button
        if (removeConfirmBtn) {
            removeConfirmBtn.addEventListener('click', function() {
                if (!pendingRemoveItemId) return;

                // Hide modal
                removeModal.style.display = 'none';

                // Find the cart item and add removing animation
                const cartItem = document.querySelector(`[onclick="removeItem('${pendingRemoveItemId}')"]`).closest('.cart-item');
                cartItem.classList.add('removing');

                // Show toast immediately
                Toast.success('Success', 'Item removed from cart');

                fetch('{{ secure_url(route("qr.cart.update", [], false)) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_code: sessionCode,
                        menu_item_id: pendingRemoveItemId,
                        remove: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Wait for animation to complete before reload
                        setTimeout(() => {
                            window.location.reload();
                        }, 400);
                    } else {
                        cartItem.classList.remove('removing');
                        Toast.error('Error', data.error || 'Failed to remove item');
                    }
                })
                .catch(error => {
                    cartItem.classList.remove('removing');
                    console.error('Error:', error);
                    Toast.error('Error', 'An error occurred');
                });

                pendingRemoveItemId = null;
            });
        }

        // Close modal when clicking outside
        if (removeModal) {
            removeModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    pendingRemoveItemId = null;
                }
            });
        }

        // Show flash messages
        @if(session('success'))
            Toast.success('Success', '{{ session('success') }}');
        @endif

        @if(session('error'))
            Toast.error('Error', '{{ session('error') }}');
        @endif

        // Show welcome message when page loads (only if not from a flash message)
        @if(!session('success') && !session('error') && count($cart) > 0)
            setTimeout(() => {
                Toast.success('Cart', 'Review your items before checkout');
            }, 300);
        @elseif(!session('success') && !session('error') && count($cart) == 0)
            setTimeout(() => {
                Toast.info('Cart', 'Your cart is empty');
            }, 300);
        @endif

        // Back button confirmation modal
        const backButton = document.querySelector('.back-button');
        const backConfirmModal = document.getElementById('back-confirm-modal');
        const backCancelBtn = document.getElementById('back-cancel-btn');
        const backConfirmBtn = document.getElementById('back-confirm-btn');
        let backLinkHref = '';
        let isLeavingPage = false;

        // Intercept "Back to Menu" button
        if (backButton) {
            backButton.addEventListener('click', function(e) {
                e.preventDefault();
                backLinkHref = this.href;
                backConfirmModal.style.display = 'flex';
            });
        }

        // Cancel button
        if (backCancelBtn) {
            backCancelBtn.addEventListener('click', function() {
                backConfirmModal.style.display = 'none';
            });
        }

        // Confirm button
        if (backConfirmBtn) {
            backConfirmBtn.addEventListener('click', function() {
                isLeavingPage = true;
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
        history.pushState({page: 'cart'}, '', '');

        window.addEventListener('popstate', function(e) {
            if (!isLeavingPage) {
                // User pressed back button, show confirmation
                history.pushState({page: 'cart'}, '', '');
                backLinkHref = '{{ route("qr.guest.menu", ["session" => $session->session_code]) }}';
                backConfirmModal.style.display = 'flex';
            }
        });
    </script>
</body>

</html>