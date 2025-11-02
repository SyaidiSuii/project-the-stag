<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | The Stag</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(
                135deg,
                rgba(99, 102, 241, 0.95) 0%,
                rgba(88, 86, 235, 0.9) 50%,
                rgba(255, 107, 53, 0.95) 100%
            );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="food-pattern" x="0" y="0" width="200" height="200" patternUnits="userSpaceOnUse"><g fill="white" opacity="0.08"><text x="20" y="60" font-family="Arial" font-size="32">🥩🍝🥗</text><text x="20" y="120" font-family="Arial" font-size="32">🍔🍟🥘</text><text x="20" y="180" font-family="Arial" font-size="32">🍲🥪🍌</text></g></pattern></defs><rect width="1200" height="800" fill="url(%23food-pattern)"/></svg>') center/cover;
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        
        .error-container {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            margin: 2rem;
            animation: slideUp 0.6s ease-out;
        }
        
        .error-illustration {
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            background: rgb(255, 255, 255);
            border-radius: 50%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);s
            border: 3px solid rgba(255, 255, 255, 0.3);
            animation: float 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            padding: 2rem;
        }
        
        .error-illustration::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: white;
            margin: 0;
            line-height: 1;
            text-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            animation: pulse 2s infinite;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 1.5rem 0;
            color: white;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .error-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            margin: 1rem auto;
            border-radius: 2px;
        }
        
        .error-message {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-home {
            padding: 1.2rem 2.5rem;
            background: linear-gradient(135deg, #fff, #f8f9fa);
            color: #6366f1;
            border: none;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
            font-size: 1rem;
        }
        
        .btn-home::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.2), transparent);
            transition: left 0.6s ease;
        }
        
        .btn-home:hover::before {
            left: 100%;
        }
        
        .btn-home:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }
        
        .btn-home:active {
            transform: translateY(-2px);
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0) rotate(0deg); 
            }
            50% { 
                transform: translateY(-20px) rotate(5deg); 
            }
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1;
            }
            50% { 
                transform: scale(1.05); 
                opacity: 0.9;
            }
        }
        
        @keyframes shine {
            0% { 
                transform: translateX(-100%) translateY(-100%) rotate(45deg); 
            }
            100% { 
                transform: translateX(100%) translateY(100%) rotate(45deg); 
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-container {
                margin: 1rem;
                padding: 2rem 1rem;
            }
            
            .error-illustration {
                width: 150px;
                height: 150px;
                padding: 1.5rem;
            }
            
            .btn-home {
                padding: 1rem 2rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-illustration">
            <img src="{{ asset('images/logo.png') }}" alt="The Stag Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page Not Found</h2>
        <p class="error-message">
            Sorry, the page you're looking for seems to have wandered off into the forest. 
            Even our smart deer can't find it! Let's get you back on track.
        </p>
        <a href="{{ route('customer.index') }}" class="btn-home">
            <i class="fas fa-home"></i>
            <span>Return to Home</span>
        </a>
    </div>
</body>
</html>