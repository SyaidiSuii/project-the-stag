<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - The Stag</title>
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

        .tracking-card {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            padding: 15px;
            background: var(--bg);
            border-radius: 12px;
            text-align: center;
        }

        .info-label {
            color: var(--text-2);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
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

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .order-items {
            margin-top: 30px;
        }

        .order-items h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--text);
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: var(--bg);
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .item-name {
            font-weight: 600;
        }

        .item-qty {
            color: var(--text-2);
            margin-left: 10px;
        }

        .item-price {
            font-weight: 600;
            color: var(--brand);
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .loading {
            text-align: center;
            padding: 40px;
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

        .refresh-btn {
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-receipt"></i> Track Your Order</h1>
            <p>Real-time order status updates</p>
        </div>

        <div class="tracking-card">
            <div id="order-content">
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p style="margin-top: 20px;">Loading order details...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toast.js') }}"></script>
    <script>
        // Get order details from URL
        const urlParams = new URLSearchParams(window.location.search);
        const orderCode = urlParams.get('order');
        const phone = urlParams.get('phone');

        if (!orderCode || !phone) {
            document.getElementById('order-content').innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ef4444; margin-bottom: 20px;"></i>
                    <h2>Missing Information</h2>
                    <p style="color: var(--text-2); margin: 20px 0;">Please provide order code and phone number.</p>
                </div>
            `;
        } else {
            loadOrderDetails();
        }

        function loadOrderDetails() {
            fetch('{{ secure_url(route("qr.api.track-order", [], false)) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_code: orderCode,
                    phone: phone
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('order-content').innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-search" style="font-size: 3rem; color: var(--text-3); margin-bottom: 20px;"></i>
                            <h2>Order Not Found</h2>
                            <p style="color: var(--text-2); margin: 20px 0;">${data.error}</p>
                        </div>
                    `;
                } else {
                    displayOrder(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toast.error('Error', 'Failed to load order details');
            });
        }

        function displayOrder(data) {
            const order = data.order;
            const statusClass = `status-${order.order_status.toLowerCase()}`;

            let itemsHtml = '';
            if (order.items && order.items.length > 0) {
                itemsHtml = order.items.map(item => `
                    <div class="item">
                        <div>
                            <span class="item-name">${item.menu_item ? item.menu_item.name : 'Item'}</span>
                            <span class="item-qty">x${item.quantity}</span>
                        </div>
                        <div class="item-price">RM ${parseFloat(item.subtotal).toFixed(2)}</div>
                    </div>
                `).join('');
            }

            document.getElementById('order-content').innerHTML = `
                <div class="order-info">
                    <div class="info-item">
                        <div class="info-label">Order ID</div>
                        <div class="info-value">${order.confirmation_code}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Table Number</div>
                        <div class="info-value">${data.table_number}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge ${statusClass}">${order.order_status.toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value">RM ${parseFloat(data.total_amount).toFixed(2)}</div>
                    </div>
                </div>

                ${data.estimated_time ? `
                <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-clock" style="color: #92400e; font-size: 2rem; margin-bottom: 10px;"></i>
                    <div style="font-weight: 600; color: #92400e;">Estimated Ready Time</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: #78350f; margin-top: 5px;">${new Date(data.estimated_time).toLocaleTimeString()}</div>
                </div>
                ` : ''}

                <div class="order-items">
                    <h3><i class="fas fa-utensils"></i> Order Items</h3>
                    ${itemsHtml}
                </div>

                <button onclick="loadOrderDetails()" class="btn btn-primary refresh-btn">
                    <i class="fas fa-sync-alt"></i> Refresh Status
                </button>
            `;
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (orderCode && phone) {
                loadOrderDetails();
            }
        }, 30000);
    </script>
</body>

</html>
