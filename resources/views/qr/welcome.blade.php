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
            width: 80px;
            height: 80px;
            margin: 0 auto 24px auto;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 8px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
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

        /* Tablet View (769px - 1199px) - 20-25% reduction */
        @media (max-width: 1199px) and (min-width: 769px) {
            .welcome-card {
                padding: 40px 28px;
            }

            .logo {
                width: 70px;
                height: 70px;
                margin-bottom: 20px;
            }

            .welcome-title {
                font-size: 28px;
                margin-bottom: 10px;
            }

            .welcome-subtitle {
                font-size: 14px;
                margin-bottom: 6px;
            }

            .table-info {
                padding: 10px 20px;
                margin: 20px 0 28px 0;
                border-radius: 14px;
            }

            .table-info i {
                font-size: 18px;
            }

            .options-container {
                gap: 14px;
            }

            .option-btn {
                padding: 18px;
                border-radius: 14px;
                font-size: 15px;
                gap: 10px;
            }

            .divider {
                margin: 20px 0;
                font-size: 13px;
            }

            .divider span {
                padding: 0 14px;
            }

            .feature-list {
                margin-top: 28px;
                padding-top: 28px;
            }

            .feature-item {
                gap: 10px;
                padding: 10px 0;
                font-size: 13px;
            }

            .feature-item i {
                width: 28px;
                height: 28px;
                font-size: 13px;
            }

            .info-text {
                font-size: 12px;
                margin-top: 20px;
            }
        }

        /* Mobile View (max-width: 768px) - 35-40% reduction */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .welcome-card {
                padding: 32px 20px;
                border-radius: 20px;
            }

            .welcome-card::before {
                height: 5px;
            }

            .logo {
                width: 60px;
                height: 60px;
                margin-bottom: 18px;
            }

            .welcome-title {
                font-size: 24px;
                margin-bottom: 8px;
            }

            .welcome-subtitle {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .table-info {
                padding: 10px 18px;
                margin: 18px 0 24px 0;
                border-radius: 12px;
                font-size: 15px;
            }

            .table-info i {
                font-size: 16px;
            }

            .options-container {
                gap: 12px;
            }

            .option-btn {
                padding: 16px;
                border-radius: 12px;
                font-size: 14px;
                gap: 10px;
            }

            .option-btn.secondary {
                border: 2px solid #667eea;
            }

            .divider {
                margin: 18px 0;
                font-size: 12px;
            }

            .divider span {
                padding: 0 12px;
            }

            .feature-list {
                margin-top: 24px;
                padding-top: 24px;
            }

            .feature-item {
                gap: 10px;
                padding: 8px 0;
                font-size: 12px;
            }

            .feature-item i {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                font-size: 12px;
            }

            .info-text {
                font-size: 11px;
                margin-top: 18px;
            }
        }

        /* Small Mobile View (max-width: 480px) - Additional optimization */
        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .welcome-card {
                padding: 28px 18px;
                border-radius: 18px;
            }

            .logo {
                width: 54px;
                height: 54px;
                margin-bottom: 16px;
            }

            .welcome-title {
                font-size: 22px;
                margin-bottom: 8px;
            }

            .welcome-subtitle {
                font-size: 12px;
            }

            .table-info {
                padding: 8px 16px;
                margin: 16px 0 20px 0;
                font-size: 14px;
            }

            .table-info i {
                font-size: 14px;
            }

            .option-btn {
                padding: 14px;
                font-size: 13px;
                gap: 8px;
            }

            .divider {
                margin: 16px 0;
            }

            .feature-list {
                margin-top: 20px;
                padding-top: 20px;
            }

            .feature-item {
                font-size: 11px;
            }

            .feature-item i {
                width: 26px;
                height: 26px;
                font-size: 11px;
            }

            .info-text {
                font-size: 10px;
                margin-top: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="The Stag Logo">
            </div>
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
