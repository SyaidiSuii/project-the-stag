<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Email Notification' }}</title>
    <style>
        body {
            margin: 0; padding: 0; 
            color: #333; background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px; margin: 20px auto;
            background: #ffffff; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #ffcc00; text-align: center; padding: 20px;
        }
        .header h1 {
            margin: 0; color: #333333; font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px; line-height: 1.5;
        }
        .cta-button {
            display: inline-block; border-radius: 5px;
            margin-top: 20px; padding: 12px 24px;
            background-color: #ffcc00; color: #333333;
            text-decoration: none; font-weight: bold;
        }
        .footer {
            text-align: center; padding: 10px; font-size: 12px;
            color: #777; background-color: #f9f9f9;
        }
        @media screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
            }
            .header h1 {
                font-size: 20px;
            }
            .content p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        @if($title)
        <!-- Header -->
        <div class="header">
            <h1>{{$title}}</h1>
        </div>
        @endif


        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>


        <!-- Footer -->
        <div class="footer">
            <p>You are receiving this email because you are a valued member of our community.</p>
            <p>&copy; {{ date('Y') }} Taming Tech Sdn Bhd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>