<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - The Stag</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        <a href="{{ route('qr.menu', ['session' => $session->session_code]) }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Menu
        </a>

        @if (count($cart) > 0)
            <div class="cart-items">
                @foreach ($cart as $item)
                    <div class="cart-item">
                        <div class="item-image">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
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

            <a href="{{ route('qr.payment', ['session' => $session->session_code]) }}" class="checkout-btn">
                <i class="fas fa-credit-card"></i> Proceed to Payment
            </a>
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <div class="empty-cart-text">Your cart is empty</div>
                <p>Add some delicious items from the menu to get started!</p>
                <a href="{{ route('qr.menu', ['session' => $session->session_code]) }}" class="checkout-btn" style="margin-top: 20px; display: inline-block;">
                    <i class="fas fa-utensils"></i> Browse Menu
                </a>
            </div>
        @endif
    </div>

    <script>
        function updateQuantity(itemId, change) {
            // In a real implementation, you would send an AJAX request to update the quantity
            alert('In a real implementation, this would update the item quantity via AJAX');
        }

        function removeItem(itemId) {
            // In a real implementation, you would send an AJAX request to remove the item
            alert('In a real implementation, this would remove the item via AJAX');
        }
    </script>
</body>
</html>