<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - The Stag</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- FontAwesome Icons -->
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
            max-width: 100%;
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

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
            animation: float 15s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateX(0) translateY(0) rotate(0deg);
            }

            100% {
                transform: translateX(-50px) translateY(-50px) rotate(360deg);
            }
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

        .category-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .tab {
            padding: 12px 20px;
            background: white;
            border: 2px solid var(--muted);
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tab.active {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            box-shadow: var(--shadow);
            border-color: var(--brand);
        }

        .tab:hover {
            background: var(--bg);
            transform: translateY(-2px);
        }

        .tab.active:hover {
            background: linear-gradient(135deg, var(--brand-2), var(--brand));
        }

        .category-section {
            display: none;
        }

        .category-section.active {
            display: block;
        }

        .subcategory-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text);
            margin: 30px 0 20px;
            padding-left: 15px;
            border-left: 5px solid var(--accent);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 20px;
        }

        .menu-item {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--muted);
        }

        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .item-image {
            height: 180px;
            background: linear-gradient(135deg, #f0f4ff, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-content {
            padding: 20px;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .item-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            flex: 1;
        }

        .item-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--accent);
            white-space: nowrap;
            margin-left: 10px;
        }

        .item-description {
            color: var(--text-2);
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .add-to-cart {
            display: flex;
            gap: 10px;
        }

        .quantity-control {
            display: flex;
            border: 2px solid var(--muted);
            border-radius: 12px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 36px;
            height: 36px;
            background: var(--bg);
            border: none;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background: var(--brand);
            color: white;
        }

        .quantity-input {
            width: 40px;
            height: 36px;
            text-align: center;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }

        .add-btn {
            flex: 1;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .alert {
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* ===== MODERN FLOATING ACTION BUTTON (FAB) CART ===== */
        .cart-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            color: white;
            font-size: 30px;
            overflow: visible;
        }

        .cart-fab:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6);
        }

        .cart-fab:active {
            transform: scale(0.95);
        }

        .cart-fab.bounce {
            animation: bounce 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes bounce {
            0% {
                transform: scale(1);
            }

            40% {
                transform: scale(1.3);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
            }
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
            z-index: 2;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            50% {
                transform: scale(1.1);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            }
        }

        .cart-fab::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            z-index: -1;
            opacity: 0.8;
            filter: blur(15px);
            transform: scale(0.9);
            transition: all 0.3s ease;
        }

        .cart-fab:hover::before {
            transform: scale(1.2);
            opacity: 0.6;
        }

        /* ===== MODERN CENTERED CART MODAL ===== */
        .cart-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .cart-modal.open {
            opacity: 1;
            visibility: visible;
        }

        .cart-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .cart-modal-container {
            position: relative;
            width: 90%;
            max-width: 380px;
            max-height: 95vh;
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            transform: scale(0.8) translateY(20px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
        }

        .cart-modal.open .cart-modal-container {
            transform: scale(1) translateY(0);
        }

        .cart-modal-header {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            padding: 1.3rem 1.5rem;
            position: relative;
        }

        .cart-modal-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
            animation: cartFloat 15s linear infinite;
        }

        @keyframes cartFloat {
            0% {
                transform: translateX(0) translateY(0) rotate(0deg);
            }

            100% {
                transform: translateX(-50px) translateY(-50px) rotate(360deg);
            }
        }

        .cart-modal-title {
            font-size: 1.4rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            z-index: 3;
            transition: all 0.2s ease;
        }

        .cart-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .cart-modal-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 1rem 1rem 0;
            background: #f9fafb;
        }

        .cart-modal-items {
            flex: 1;
            overflow-y: auto;
            padding: 0 0.5rem 1.5rem 0;
            scrollbar-width: none;
            -webkit-mask-image: linear-gradient(to bottom, black 95%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 95%, transparent 100%);
        }

        .cart-modal-items::-webkit-scrollbar {
            display: none;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: var(--text-2);
        }

        .empty-cart-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .empty-cart-text {
            font-size: 1.0rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .empty-cart-subtext {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            border-radius: 18px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.7);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cart-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--brand), var(--accent));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
        }

        .cart-item:hover::before {
            transform: scaleY(1);
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f0f4ff, #e0e7ff);
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.8rem;
            border: 1px solid rgba(226, 232, 240, 0.7);
            flex-shrink: 0;
            overflow: hidden;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }

        .cart-item-details {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .cart-item-price {
            color: var(--text-2);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .quantity-controls {
            display: grid;
            grid-auto-flow: column;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: 2px solid var(--muted);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 0.8rem;
            font-weight: 900;
            transition: all 0.2s ease;
            color: var(--text);
        }

        .qty-btn:hover {
            background: var(--brand);
            border-color: var(--brand);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
        }

        .qty-btn:active {
            transform: translateY(0);
        }

        .quantity {
            font-size: 1.0rem;
            font-weight: 900;
            min-width: 28px;
            text-align: center;
            color: var(--text);
        }

        .cart-modal-footer {
            padding: 1rem;
            background: white;
            border-top: 1px solid var(--muted);
        }

        .cart-modal-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            padding: 0.8rem;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 18px;
            border: 1px solid var(--muted);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }

        .cart-total-label {
            color: var(--text-2);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cart-total-amount {
            color: var(--text);
            font-weight: 900;
            font-size: 1.2rem;
        }

        .cart-modal-checkout {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 1.0rem;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cart-modal-checkout::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .cart-modal-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px rgba(99, 102, 241, 0.5);
        }

        .cart-modal-checkout:hover::before {
            transform: translateX(100%);
        }

        .cart-modal-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.7rem;
        }

        .cart-modal-count {
            background: var(--bg);
            color: var(--text-2);
            border-radius: 12px;
            padding: 0.3rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1px solid var(--muted);
        }

        .cart-modal-clear {
            background: transparent;
            color: var(--text-2);
            border: 1px solid var(--muted);
            cursor: pointer;
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-weight: 700;
        }

        .cart-modal-clear:hover {
            background: var(--muted);
            color: var(--text);
        }

        /* Responsive grid for larger screens */
        @media (min-width: 768px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .menu-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .menu-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .header {
                padding: 15px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .item-image {
                height: 150px;
            }

            .tab {
                padding: 10px 15px;
                font-size: 0.8rem;
            }

            .cart-fab {
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                font-size: 24px;
            }

            .cart-modal-container {
                max-width: 90%;
                max-height: 85vh;
                border-radius: 20px;
            }
        }

        @media (max-width: 480px) {
            .item-header {
                flex-direction: column;
                gap: 10px;
            }

            .item-price {
                margin-left: 0;
            }

            .add-to-cart {
                flex-direction: column;
            }

            .cart-item {
                flex-wrap: wrap;
            }

            .cart-item-details {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .quantity-controls {
                margin-left: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Our Menu</h1>
            <p>Welcome to Table {{ $session->table->table_number }}</p>
        </div>

        @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
        @endif

        <!-- Category Tabs -->
        <div class="category-tabs">
            @php $first = true; @endphp
            @foreach ($menuData as $mainCategory => $subCategories)
            <button class="tab {{ $first ? 'active' : '' }}" data-category="{{ strtolower(str_replace(' ', '-', $mainCategory)) }}">
                {{ $mainCategory }}
            </button>
            @php $first = false; @endphp
            @endforeach
        </div>

        <!-- Menu Items by Category -->
        @php $first = true; @endphp
        @foreach ($menuData as $mainCategory => $subCategories)
        <div id="{{ strtolower(str_replace(' ', '-', $mainCategory)) }}" class="category-section {{ $first ? 'active' : '' }}">
            @foreach ($subCategories as $subCategoryName => $items)
            <h2 class="subcategory-title">{{ $subCategoryName }}</h2>
            <div class="menu-grid">
                @foreach ($items as $item)
                <div class="menu-item">
                    <div class="item-image">
                        @if($item->image_url)
                        <img src="{{ $item->image_url }}"
                            alt="{{ $item->name }}">
                        @else
                        <div>🍽️</div>
                        @endif
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <div class="item-name">{{ $item->name }}</div>
                            <div class="item-price">RM {{ number_format($item->price, 2) }}</div>
                        </div>
                        <p class="item-description">{{ $item->description }}</p>
                        <form action="{{ route('qr.cart.add') }}" method="POST" class="add-to-cart">
                            @csrf
                            <input type="hidden" name="session_code" value="{{ $session->session_code }}">
                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">

                            <div class="quantity-control">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(this, -1)">−</button>
                                <input type="number" name="quantity" value="1" min="1" class="quantity-input" readonly>
                                <button type="button" class="quantity-btn" onclick="changeQuantity(this, 1)">+</button>
                            </div>

                            <button type="submit" class="add-btn">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @php $first = false; @endphp
        @endforeach
    </div>

    <!-- Modern Floating Action Button (FAB) for Cart -->
    <button class="cart-fab" id="cartFab">
        🛒
        <span class="cart-badge" id="cartBadge">{{ array_sum(array_column($cart, 'quantity')) }}</span>
    </button>

    <!-- Modern Centered Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-modal-backdrop" id="cartModalBackdrop"></div>
        <div class="cart-modal-container">
            <div class="cart-modal-header">
                <button class="cart-modal-close" id="cartModalClose">×</button>
                <h2 class="cart-modal-title">🛒 My Cart</h2>
            </div>
            <div class="cart-modal-content">
                <div class="cart-modal-toolbar">
                    <div class="cart-modal-count">Items: <span id="cart-count">0</span></div>
                    <button class="cart-modal-clear" id="clearAllBtn">Clear All</button>
                </div>
                <div class="cart-modal-items" id="cart-items">
                    <!-- Cart items will be displayed here -->
                    <div class="empty-cart" id="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <div class="empty-cart-text">Your cart is empty</div>
                        <div class="empty-cart-subtext">Add some delicious items to get started!</div>
                    </div>
                </div>
            </div>
            <div class="cart-modal-footer">
                <div class="cart-modal-total">
                    <div>
                        <div class="cart-total-label">Total Items</div>
                        <div class="cart-total-label">Total Amount</div>
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 1.2rem;">x<span id="total-items">0</span></div>
                        <div class="cart-total-amount" id="total-amount">Rp 0.00</div>
                    </div>
                </div>
                <button class="cart-modal-checkout" id="checkoutBtn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and sections
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.category-section').forEach(s => s.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Show corresponding section
                const categoryId = this.getAttribute('data-category');
                document.getElementById(categoryId).classList.add('active');
            });
        });

        function changeQuantity(button, change) {
            const container = button.closest('.quantity-control');
            const input = container.querySelector('.quantity-input');
            let value = parseInt(input.value) || 1;
            value += change;

            if (value < 1) value = 1;
            if (value > 10) value = 10; // Limit max quantity

            input.value = value;
        }

        // Handle form submission with AJAX
        document.querySelectorAll('.add-to-cart').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('.add-btn');
                const originalText = submitButton.innerHTML;

                // Show loading state
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                submitButton.disabled = true;

                fetch("{{ route('qr.cart.add') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart badge
                            document.getElementById('cartBadge').textContent = data.cart_count;
                            document.getElementById('cartBadge').style.display = 'flex';

                            // Bounce animation for FAB
                            const fab = document.getElementById('cartFab');
                            fab.classList.remove('bounce');
                            void fab.offsetWidth; // Force reflow
                            fab.classList.add('bounce');

                            // Show success feedback
                            submitButton.innerHTML = '<i class="fas fa-check"></i> Added!';
                            submitButton.style.backgroundColor = '#10b981';

                            // Reset button after delay
                            setTimeout(() => {
                                submitButton.innerHTML = originalText;
                                submitButton.style.backgroundColor = '';
                                submitButton.disabled = false;
                            }, 1500);
                        } else {
                            // Show error
                            alert(data.error || 'Failed to add item to cart');
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to add item to cart. Please try again.');
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    });
            });
        });

        // Cart Modal Functions
        const cartFab = document.getElementById('cartFab');
        const cartModal = document.getElementById('cartModal');
        const cartModalBackdrop = document.getElementById('cartModalBackdrop');
        const cartModalClose = document.getElementById('cartModalClose');
        const clearAllBtn = document.getElementById('clearAllBtn');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const sessionCode = "{{ $session->session_code }}";
        const storageUrl = "{{ asset('storage') }}/";

        // Open cart modal
        cartFab.addEventListener('click', function() {
            updateCartDisplay();
            cartModal.classList.add('open');
        });

        // Close cart modal
        function closeCartModal() {
            cartModal.classList.remove('open');
        }

        // Close modal when clicking backdrop or close button
        cartModalBackdrop.addEventListener('click', closeCartModal);
        cartModalClose.addEventListener('click', closeCartModal);

        // Update cart display
        function updateCartDisplay() {
            // Show loading state
            const cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = '<div class="empty-cart"><div class="empty-cart-icon">🔄</div><div class="empty-cart-text">Loading cart...</div></div>';

            // Fetch cart data from server
            fetch("{{ route('qr.cart', ['session' => '__SESSION__']) }}".replace('__SESSION__', sessionCode), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update cart count
                    const totalItems = data.cart.reduce((sum, item) => sum + item.quantity, 0);
                    document.getElementById('cart-count').textContent = totalItems;
                    document.getElementById('total-items').textContent = totalItems;

                    // Calculate total amount
                    const totalAmount = data.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    document.getElementById('total-amount').textContent = 'RM ' + totalAmount.toFixed(2);

                    // Update cart items display
                    const cartItemsContainer = document.getElementById('cart-items');

                    if (data.cart.length === 0) {
                        cartItemsContainer.innerHTML = `
                        <div class="empty-cart" id="empty-cart">
                            <div class="empty-cart-icon">🛒</div>
                            <div class="empty-cart-text">Your cart is empty</div>
                            <div class="empty-cart-subtext">Add some delicious items to get started!</div>
                        </div>
                    `;
                    } else {
                        cartItemsContainer.innerHTML = '';

                        data.cart.forEach((item, index) => {
                            const cartItemElement = document.createElement('div');
                            cartItemElement.className = 'cart-item';
                            cartItemElement.innerHTML = `
                            <div class="cart-item-image">
                                ${(item.image && item.image_url) ?
                                `<img src="${item.image_url}" alt="${item.name}">` :`🍽️`
                                }
                            </div>
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">RM ${parseFloat(item.price).toFixed(2)}</div>
                            </div>
                            <div class="quantity-controls">
                                <button class="qty-btn" onclick="updateCartItemQuantity('${item.id}', -1)">−</button>
                                <span class="quantity">${item.quantity}</span>
                                <button class="qty-btn" onclick="updateCartItemQuantity('${item.id}', 1)">+</button>
                            </div>
                        `;
                            cartItemsContainer.appendChild(cartItemElement);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching cart data:', error);
                    cartItemsContainer.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">❌</div>
                        <div class="empty-cart-text">Failed to load cart</div>
                        <div class="empty-cart-subtext">Please try again later</div>
                        <div class="empty-cart-subtext" style="font-size: 0.7rem; margin-top: 10px;">Error: ${error.message}</div>
                    </div>
                `;
                });
        }

        // Update cart item quantity
        function updateCartItemQuantity(itemId, change) {
            // Show loading state
            const cartItemsContainer = document.getElementById('cart-items');
            const originalContent = cartItemsContainer.innerHTML;
            cartItemsContainer.innerHTML = '<div class="empty-cart"><div class="empty-cart-icon">🔄</div><div class="empty-cart-text">Updating cart...</div></div>';

            // Send update request to server
            fetch("{{ route('qr.cart.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        session_code: sessionCode,
                        menu_item_id: itemId,
                        quantity: change
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update display
                        updateCartDisplay();

                        // Update FAB badge
                        document.getElementById('cartBadge').textContent = data.cart_count;
                        document.getElementById('cartBadge').style.display = data.cart_count > 0 ? 'flex' : 'none';
                    } else {
                        // Restore original content and show error
                        cartItemsContainer.innerHTML = originalContent;
                        alert('Failed to update cart item. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error updating cart item:', error);
                    cartItemsContainer.innerHTML = originalContent;
                    alert('Failed to update cart item. Please try again.');
                });
        }

        // Clear all items from cart
        clearAllBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all items from your cart?')) {
                // Show loading state
                const cartItemsContainer = document.getElementById('cart-items');
                const originalContent = cartItemsContainer.innerHTML;
                cartItemsContainer.innerHTML = '<div class="empty-cart"><div class="empty-cart-icon">🔄</div><div class="empty-cart-text">Clearing cart...</div></div>';

                // Send clear request to server (special case: itemId=0, quantity=0 to clear all)
                fetch("{{ route('qr.cart.update') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            session_code: sessionCode,
                            menu_item_id: 0, // Special value to clear all
                            quantity: 0
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update display
                            updateCartDisplay();

                            // Update FAB badge
                            document.getElementById('cartBadge').textContent = '0';
                            document.getElementById('cartBadge').style.display = 'none';
                        } else {
                            // Restore original content and show error
                            cartItemsContainer.innerHTML = originalContent;
                            alert('Failed to clear cart. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error clearing cart:', error);
                        cartItemsContainer.innerHTML = originalContent;
                        alert('Failed to clear cart. Please try again.');
                    });
            }
        });

        // Checkout button
        checkoutBtn.addEventListener('click', function() {
            window.location.href = "{{ route('qr.cart', ['session' => '__SESSION__']) }}".replace('__SESSION__', sessionCode);
        });
    </script>
</body>

</html>