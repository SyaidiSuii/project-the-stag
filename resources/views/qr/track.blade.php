<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - The Stag SmartDine</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
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
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            border-radius: var(--radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .loading {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-2);
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            text-align: center;
            padding: 60px 20px;
        }

        .error-message i {
            font-size: 4rem;
            color: #ef4444;
            margin-bottom: 20px;
        }

        .error-message h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .error-message p {
            color: var(--text-2);
        }

        /* Use order card styles from customer orders */
        .order-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        .order-info h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
        }

        .order-date {
            font-size: 0.9rem;
            color: var(--text-2);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .order-statuses {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .order-status,
        .payment-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: capitalize;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-preparing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-ready {
            background: #d1fae5;
            color: #065f46;
        }

        .status-completed,
        .status-served {
            background: #dcfce7;
            color: #166534;
        }

        .status-payment-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-payment-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .order-items {
            margin: 20px 0;
        }

        .order-items h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }

        .item-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .item-name {
            font-weight: 600;
            color: var(--text);
        }

        .item-qty {
            font-size: 0.85rem;
            color: var(--text-2);
        }

        .item-price {
            font-weight: 700;
            color: var(--brand);
            font-size: 1.05rem;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 2px solid #f1f5f9;
            margin-top: 20px;
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .total-amount {
            font-size: 1.6rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .estimated-time-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .estimated-time-card i {
            font-size: 2.5rem;
            color: #92400e;
            margin-bottom: 12px;
        }

        .estimated-time-label {
            font-weight: 700;
            color: #92400e;
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .estimated-time-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #78350f;
        }

        .table-info-card {
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
            border: 2px solid var(--brand);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .table-info-card i {
            font-size: 2.5rem;
            color: var(--brand);
        }

        .table-info-text {
            text-align: left;
        }

        .table-info-label {
            font-size: 0.9rem;
            color: #4338ca;
            font-weight: 600;
        }

        .table-info-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3730a3;
        }

        .refresh-btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 2px solid var(--muted);
            background: white;
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .back-btn:hover {
            border-color: var(--brand);
            color: var(--brand);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .back-btn i {
            font-size: 1rem;
        }

        @media (max-width: 640px) {
            .order-header {
                flex-direction: column;
                gap: 12px;
            }

            .order-statuses {
                align-items: flex-start;
            }

            .item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-receipt"></i> Track Your Order</h1>
            <p>Real-time order status updates</p>
        </div>

        <div id="order-content">
            <div class="order-card">
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p style="margin-top: 20px; font-weight: 600;">Loading order details...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toast.js') }}"></script>
    <script>
        // Get order details from URL
        const urlParams = new URLSearchParams(window.location.search);
        const orderCode = urlParams.get('order');
        const phone = urlParams.get('phone') || null;
        const token = urlParams.get('token') || null;

        // Clean up empty string values
        const cleanPhone = phone && phone.trim() !== '' ? phone : null;
        const cleanToken = token && token.trim() !== '' ? token : null;

        if (!orderCode) {
            document.getElementById('order-content').innerHTML = `
                <div class="order-card">
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <h2>Missing Information</h2>
                        <p style="margin: 20px 0;">Please provide order code to track your order.</p>
                    </div>
                </div>
            `;
        } else {
            loadOrderDetails();
        }

        function loadOrderDetails() {
            fetch('{{ route("qr.api.track-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_code: orderCode,
                    phone: cleanPhone,
                    token: cleanToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('order-content').innerHTML = `
                        <div class="order-card">
                            <div class="error-message">
                                <i class="fas fa-search"></i>
                                <h2>Order Not Found</h2>
                                <p style="margin: 20px 0;">${data.error}</p>
                            </div>
                        </div>
                    `;
                } else {
                    displayOrder(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Toast !== 'undefined') {
                    Toast.error('Error', 'Failed to load order details');
                }
                document.getElementById('order-content').innerHTML = `
                    <div class="order-card">
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h2>Connection Error</h2>
                            <p style="margin: 20px 0;">Failed to load order details. Please check your connection and try again.</p>
                            <button onclick="loadOrderDetails()" class="refresh-btn" style="max-width: 400px; margin: 20px auto 0 auto;">
                                <i class="fas fa-sync-alt"></i> Try Again
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        function displayOrder(data) {
            const order = data.order;
            const statusClass = `status-${order.order_status.toLowerCase()}`;
            const paymentStatusClass = `status-payment-${order.payment_status ? order.payment_status.toLowerCase() : 'pending'}`;

            let itemsHtml = '';
            if (order.items && order.items.length > 0) {
                itemsHtml = order.items.map(item => `
                    <div class="item">
                        <div class="item-details">
                            <div class="item-name">${item.menu_item ? item.menu_item.name : 'Item'}</div>
                            <div class="item-qty">Quantity: ${item.quantity}</div>
                        </div>
                        <div class="item-price">RM ${parseFloat(item.total_price || item.subtotal || 0).toFixed(2)}</div>
                    </div>
                `).join('');
            }

            const orderDate = new Date(order.created_at || order.order_time);
            const formattedDate = orderDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const formattedTime = orderDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

            document.getElementById('order-content').innerHTML = `
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Order #${order.confirmation_code}</h3>
                            <div class="order-date">
                                <i class="fas fa-calendar-alt"></i>
                                ${formattedDate}, ${formattedTime}
                            </div>
                        </div>
                        <div class="order-statuses">
                            ${order.payment_status ? `
                            <div class="payment-status ${paymentStatusClass}">
                                <i class="fas fa-${order.payment_status === 'paid' ? 'check-circle' : 'clock'}"></i>
                                ${order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}
                            </div>
                            ` : ''}
                            <div class="order-status ${statusClass}">
                                ${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}
                            </div>
                        </div>
                    </div>

                    ${data.table_number ? `
                    <div class="table-info-card">
                        <i class="fas fa-chair"></i>
                        <div class="table-info-text">
                            <div class="table-info-label">Your Table</div>
                            <div class="table-info-value">Table ${data.table_number}</div>
                        </div>
                    </div>
                    ` : ''}

                    ${data.estimated_time ? `
                    <div class="estimated-time-card">
                        <i class="fas fa-clock"></i>
                        <div class="estimated-time-label">Estimated Ready Time</div>
                        <div class="estimated-time-value">${new Date(data.estimated_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
                    </div>
                    ` : ''}

                    <div class="order-items">
                        <h4><i class="fas fa-utensils"></i> Order Items</h4>
                        <div class="item-list">
                            ${itemsHtml}
                        </div>
                    </div>

                    <div class="order-total">
                        <span class="total-label">Total</span>
                        <span class="total-amount">RM ${parseFloat(data.total_amount).toFixed(2)}</span>
                    </div>

                    <button onclick="loadOrderDetails()" class="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Refresh Status
                    </button>
                </div>
            `;
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (orderCode) {
                loadOrderDetails();
            }
        }, 30000);
    </script>
</body>
</html>
