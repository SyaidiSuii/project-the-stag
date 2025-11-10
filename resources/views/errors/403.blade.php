<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden | The Stag</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(
                135deg,
                rgba(220, 38, 38, 0.95) 0%,
                rgba(239, 68, 68, 0.9) 50%,
                rgba(255, 107, 53, 0.95) 100%
            );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background pattern */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
            animation: backgroundMove 15s ease-in-out infinite alternate;
            z-index: 0;
        }
        
        /* Floating decorative elements */
        .decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
            z-index: 0;
        }
        
        .decoration:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation: float1 20s ease-in-out infinite;
        }
        
        .decoration:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -50px;
            right: -50px;
            animation: float2 15s ease-in-out infinite;
        }
        
        .decoration:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 10%;
            animation: float3 18s ease-in-out infinite;
        }
        
        .error-container {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 3rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            max-width: 700px;
            width: 90%;
            margin: 2rem;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .logo-container {
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            background: white;
            border-radius: 50%;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                0 0 0 8px rgba(255, 255, 255, 1);
            position: relative;
            animation: logoFloat 3s ease-in-out infinite;
            padding: 20px;
        }
        
        .logo-container::before {
            content: '';
            position: absolute;
            inset: -12px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.95), rgba(255, 107, 53, 0.95));
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.8;
        }
        
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .error-code {
            font-size: 7rem;
            font-weight: 800;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.95), rgba(239, 68, 68, 0.9), rgba(255, 107, 53, 0.95));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
            letter-spacing: -0.02em;
            animation: gradientShift 3s ease infinite;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 1.5rem 0 1rem;
            color: #1f2937;
            letter-spacing: -0.01em;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #6b7280;
            max-width: 500px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            font-weight: 400;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.95), rgba(255, 107, 53, 0.95));
            color: white;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(220, 38, 38, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #6b7280;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            color: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn i {
            font-size: 1.1em;
        }
        
        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 2rem auto;
        }
        
        .help-text {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-top: 2rem;
        }
        
        .help-text a {
            color: rgba(220, 38, 38, 0.95);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .help-text a:hover {
            color: rgba(255, 107, 53, 0.95);
            text-decoration: underline;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes logoFloat {
            0%, 100% { 
                transform: translateY(0); 
            }
            50% { 
                transform: translateY(-10px); 
            }
        }
        
        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        @keyframes backgroundMove {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }
        
        @keyframes float1 {
            0%, 100% { 
                transform: translate(0, 0) scale(1); 
            }
            50% { 
                transform: translate(50px, 50px) scale(1.1); 
            }
        }
        
        @keyframes float2 {
            0%, 100% { 
                transform: translate(0, 0) scale(1); 
            }
            50% { 
                transform: translate(-30px, -30px) scale(0.9); 
            }
        }
        
        @keyframes float3 {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg); 
            }
            50% { 
                transform: translate(20px, -20px) rotate(180deg); 
            }
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-container {
                padding: 3rem 2rem;
                margin: 1rem;
            }
            
            .logo-container {
                width: 120px;
                height: 120px;
            }
            
            .button-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 0.9rem 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .error-code {
                font-size: 4rem;
            }
            
            .error-container {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="decoration"></div>
    <div class="decoration"></div>
    <div class="decoration"></div>
    
    <div class="error-container">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="The Stag Logo">
        </div>
        
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Access Forbidden</h2>
        
        <p class="error-message">
            Sorry, you don't have permission to access this area. 
            This section requires special access rights. Please contact the administrator if you believe this is a mistake.
        </p>
        
        <div class="button-group">
            <a href="{{ route('customer.index') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <span>Back to Home</span>
            </a>
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Go Back</span>
            </button>
        </div>
        
        <div class="divider"></div>
        
    </div>
</body>
</html>