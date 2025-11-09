<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Authentication')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #5856eb;
            --accent: #ff6b35;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, var(--accent) 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .app-container {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .app-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
            border-radius: 16px 16px 0 0;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .logo i {
            font-size: 32px;
        }

        .app-content {
            padding: 25px;
        }

        .form-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .form-subtitle {
            color: var(--gray);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: var(--radius);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-input.error {
            border-color: var(--danger);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: auto;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            transform: none;
        }

        .btn-secondary {
            background: white;
            color: var(--dark);
            border: 2px solid var(--light-gray);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
        }

        .switch-form {
            text-align: center;
            margin-top: 25px;
            color: var(--gray);
        }

        .switch-form a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .switch-form a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: var(--danger);
            font-size: 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .success-message {
            color: var(--success);
            font-size: 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* ===== Responsive Design - 4-Tier Breakpoint System ===== */
        
        /* Tablet (769px - 1199px) - 20-25% reduction */
        @media (min-width: 769px) and (max-width: 1199px) {
            body {
                padding: 16px;
            }
            
            .app-container {
                max-width: 360px;
                border-radius: 14px;
            }
            
            .app-header {
                padding: 16px;
                border-radius: 14px 14px 0 0;
            }
            
            .logo {
                gap: 10px;
                font-size: 21px;
                margin-bottom: 8px;
            }
            
            .logo i {
                font-size: 28px;
            }
            
            .app-header p {
                font-size: 0.9rem;
            }
            
            .app-content {
                padding: 20px;
            }
            
            .form-title {
                font-size: 19px;
                margin-bottom: 4px;
            }
            
            .form-subtitle {
                font-size: 0.9rem;
                margin-bottom: 20px;
            }
            
            .form-group {
                margin-bottom: 16px;
            }
            
            .form-label {
                margin-bottom: 7px;
                font-size: 13px;
            }
            
            .form-input {
                padding: 12px 14px;
                font-size: 15px;
                border-radius: 10px;
            }
            
            .input-icon {
                right: 13px;
                font-size: 0.9rem;
            }
            
            .remember-forgot {
                margin-bottom: 20px;
                font-size: 13px;
            }
            
            .remember {
                gap: 7px;
            }
            
            .btn {
                padding: 14px;
                font-size: 15px;
                gap: 7px;
                border-radius: 10px;
            }
            
            .switch-form {
                margin-top: 20px;
                font-size: 0.9rem;
            }
            
            .error-message,
            .success-message {
                font-size: 13px;
                margin-top: 7px;
                gap: 5px;
            }
            
            .alert {
                padding: 10px 14px;
                margin-bottom: 16px;
                gap: 7px;
                border-radius: 10px;
            }
            
            /* Register page password requirements */
            [style*="background: #f0f9ff"] {
                margin-top: 7px !important;
                padding: 9px !important;
                border-radius: 3px !important;
            }
            
            [style*="background: #f0f9ff"] div {
                font-size: 12px !important;
                margin-bottom: 5px !important;
            }
            
            [style*="background: #f0f9ff"] ul {
                font-size: 11px !important;
            }
        }
        
        /* Mobile (max-width: 768px) - 35-40% reduction */
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            
            .app-container {
                max-width: 100%;
                border-radius: 12px;
            }
            
            .app-header {
                padding: 16px;
                border-radius: 12px 12px 0 0;
            }
            
            .logo {
                gap: 8px;
                font-size: 18px;
                margin-bottom: 6px;
            }
            
            .logo i {
                font-size: 24px;
            }
            
            .app-header p {
                font-size: 0.85rem;
            }
            
            .app-content {
                padding: 16px;
            }
            
            .form-title {
                font-size: 17px;
                margin-bottom: 4px;
            }
            
            .form-subtitle {
                font-size: 0.85rem;
                margin-bottom: 18px;
            }
            
            .form-group {
                margin-bottom: 14px;
            }
            
            .form-label {
                margin-bottom: 6px;
                font-size: 12px;
            }
            
            .form-input {
                padding: 11px 12px;
                font-size: 14px;
                border-radius: 9px;
            }
            
            .input-icon {
                right: 12px;
                font-size: 0.85rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 18px;
                font-size: 12px;
            }
            
            .remember {
                gap: 6px;
            }
            
            .remember label {
                font-size: 12px;
            }
            
            .btn {
                padding: 12px;
                font-size: 14px;
                gap: 6px;
                border-radius: 9px;
            }
            
            .switch-form {
                margin-top: 18px;
                font-size: 0.85rem;
            }
            
            .error-message,
            .success-message {
                font-size: 12px;
                margin-top: 6px;
                gap: 4px;
            }
            
            .alert {
                padding: 10px 12px;
                margin-bottom: 14px;
                gap: 6px;
                border-radius: 9px;
                font-size: 0.85rem;
            }
            
            .alert i {
                font-size: 0.9rem;
            }
            
            /* Register page password requirements */
            [style*="background: #f0f9ff"] {
                margin-top: 6px !important;
                padding: 8px !important;
                border-radius: 3px !important;
            }
            
            [style*="background: #f0f9ff"] div {
                font-size: 11px !important;
                margin-bottom: 4px !important;
            }
            
            [style*="background: #f0f9ff"] ul {
                font-size: 10px !important;
                padding-left: 16px !important;
            }
            
            /* Terms checkbox label */
            .remember[style*="margin-bottom: 20px"] label {
                font-size: 11px !important;
                line-height: 1.4;
            }
        }

        /* Loading spinner */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spin {
            animation: spin 1s linear infinite;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-container fade-in">
        <div class="app-header">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <span>{{ config('app.name', 'The Stag') }}</span>
            </div>
            <p>@yield('header-subtitle', 'Welcome Back')</p>
        </div>

        <div class="app-content">
            {{ $slot }}
        </div>
    </div>

    <!-- Password Toggle Functionality -->
    <script src="{{ asset('js/password-toggle.js') }}"></script>

    @stack('scripts')
</body>
</html>