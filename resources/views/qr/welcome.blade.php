<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - The Stag SmartDine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-container {
            max-width: 500px;
            width: 100%;
        }

        .welcome-card {
            background: white;
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: gradientSlide 3s ease infinite;
        }

        @keyframes gradientSlide {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .logo {
            font-size: 64px;
            margin-bottom: 24px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .table-info {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            margin: 24px 0 32px 0;
            font-weight: 700;
            color: #0369a1;
            border: 2px solid #bae6fd;
        }

        .table-info i {
            font-size: 20px;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .option-btn {
            padding: 20px;
            border-radius: 16px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .option-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .option-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .option-btn span, .option-btn i {
            position: relative;
            z-index: 1;
        }

        .option-btn.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .option-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(102, 126, 234, 0.5);
        }

        .option-btn.secondary {
            background: white;
            color: #667eea;
            border: 3px solid #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .option-btn.secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
            background: #f8f9ff;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 600;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }

        .divider span {
            padding: 0 16px;
        }

        .feature-list {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 2px dashed #e2e8f0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            color: #64748b;
            font-size: 14px;
        }

        .feature-item i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
            color: #6366f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .info-text {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 24px;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .welcome-card {
                padding: 36px 24px;
            }

            .welcome-title {
                font-size: 28px;
            }

            .logo {
                font-size: 56px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="logo">🦌</div>
            <h1 class="welcome-title">Welcome to The Stag!</h1>
            <p class="welcome-subtitle">SmartDine Experience</p>

            <div class="table-info">
                <i class="fas fa-chair"></i>
                <span>Table {{ $session->table->table_number }}</span>
            </div>

            <div class="options-container">
                <a href="{{ route('qr.guest.menu', ['session' => $session->session_code]) }}" class="option-btn primary">
                    <i class="fas fa-utensils"></i>
                    <span>View Menu & Order</span>
                </a>

                <div class="divider">
                    <span>OR</span>
                </div>

                <a href="{{ route('login') }}?redirect={{ urlencode(route('customer.menu.index', ['table' => $session->table->table_number, 'order_type' => 'dine_in', 'session' => $session->session_code])) }}" class="option-btn secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login for Better Experience</span>
                </a>
            </div>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-star"></i>
                    <span>Earn loyalty points when you login</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-history"></i>
                    <span>Track your orders easily</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-gift"></i>
                    <span>Access exclusive rewards & promotions</span>
                </div>
            </div>

            <p class="info-text">
                <i class="fas fa-info-circle"></i>
                You can continue as a guest or login to enjoy rewards and save your order history
            </p>
        </div>
    </div>
</body>
</html>
