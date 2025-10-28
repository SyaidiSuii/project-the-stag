<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - The Stag</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .confirmation-card {
            background: white;
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .confirmation-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 20px;
        }

        .confirmation-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--text);
        }

        .confirmation-message {
            color: var(--text-2);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .order-details {
            background: #f8fafc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--muted);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-2);
        }

        .detail-value {
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: white;
        }

        .btn-secondary {
            background: var(--muted);
            color: var(--text);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            background: white;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--muted);
            z-index: 1;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-3);
            transition: all 0.3s ease;
        }

        .progress-step.completed .step-circle {
            background: var(--brand);
            color: white;
        }

        .progress-step.completed .step-circle i {
            display: block;
        }

        .progress-step.active .step-circle {
            background: var(--brand-2);
            color: white;
            transform: scale(1.1);
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-2);
        }

        .progress-step.completed .step-label {
            color: var(--brand);
        }

        .progress-step.active .step-label {
            color: var(--brand-2);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Order</div>
                </div>
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="progress-step active">
                    <div class="step-circle">3</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
        </div>

        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="confirmation-title">Order Confirmed!</h1>
            <p class="confirmation-message">
                Thank you for your order. Your payment has been processed successfully.
            </p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">{{ $order->confirmation_code }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Table:</span>
                    <span class="detail-value">{{ $order->table_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" style="color: #10b981; font-weight: 600;">{{ ucfirst($order->order_status) }}</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('qr.track') }}?order={{ $order->confirmation_code }}&token={{ $order->session_token }}" class="btn btn-primary">
                    <i class="fas fa-box"></i> Track My Order
                </a>
                <a href="{{ route('qr.menu', ['session' => $session->session_code]) }}" class="btn btn-secondary">
                    <i class="fas fa-utensils"></i> Order More
                </a>
            </div>
        </div>
    </div>
</body>

</html>