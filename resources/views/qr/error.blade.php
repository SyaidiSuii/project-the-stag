<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Error - The Stag</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ===== Responsive Design ===== */
        
        /* Large Desktop (1600px+) - 30-40% increase */
        @media (min-width: 1600px) {
            .max-w-md {
                max-width: 38rem !important;
            }
            
            .p-8 {
                padding: 2.6rem !important;
            }
            
            .w-20 {
                width: 6.5rem !important;
                height: 6.5rem !important;
            }
            
            .w-10 {
                width: 3.2rem !important;
                height: 3.2rem !important;
            }
            
            .text-2xl {
                font-size: 2rem !important;
            }
            
            .text-sm {
                font-size: 1.05rem !important;
            }
            
            .py-2 {
                padding-top: 0.65rem !important;
                padding-bottom: 0.65rem !important;
            }
            
            .px-4 {
                padding-left: 1.3rem !important;
                padding-right: 1.3rem !important;
            }
        }
        
        /* Tablet (769px - 1199px) - 20-25% reduction */
        @media (max-width: 1199px) and (min-width: 769px) {
            .max-w-md {
                max-width: 26rem !important;
            }
            
            .p-8 {
                padding: 1.8rem !important;
            }
            
            .w-20 {
                width: 4.5rem !important;
                height: 4.5rem !important;
            }
            
            .mb-4 {
                margin-bottom: 0.9rem !important;
            }
            
            .w-10 {
                width: 2.2rem !important;
                height: 2.2rem !important;
            }
            
            .text-2xl {
                font-size: 1.4rem !important;
            }
            
            .mb-2 {
                margin-bottom: 0.45rem !important;
            }
            
            .text-base {
                font-size: 0.9rem !important;
            }
            
            .mb-6 {
                margin-bottom: 1.3rem !important;
            }
            
            .p-4 {
                padding: 0.9rem !important;
            }
            
            .text-sm {
                font-size: 0.8rem !important;
            }
            
            .mb-8, .mt-8 {
                margin-bottom: 1.8rem !important;
                margin-top: 1.8rem !important;
            }
            
            .pt-6 {
                padding-top: 1.3rem !important;
            }
        }
        
        /* Mobile (max-width: 768px) - 35-40% reduction */
        @media (max-width: 768px) {
            .min-h-screen {
                padding: 15px !important;
            }
            
            .max-w-md {
                max-width: 100% !important;
            }
            
            .rounded-lg {
                border-radius: 14px !important;
            }
            
            .p-8 {
                padding: 1.5rem !important;
            }
            
            .w-20 {
                width: 4rem !important;
                height: 4rem !important;
            }
            
            .mb-4 {
                margin-bottom: 0.8rem !important;
            }
            
            .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            
            .text-2xl {
                font-size: 1.3rem !important;
            }
            
            .mb-2 {
                margin-bottom: 0.4rem !important;
            }
            
            .text-base {
                font-size: 0.85rem !important;
            }
            
            .mb-6 {
                margin-bottom: 1.2rem !important;
            }
            
            .p-4 {
                padding: 0.85rem !important;
            }
            
            .text-sm {
                font-size: 0.75rem !important;
            }
            
            .space-y-1 > * + * {
                margin-top: 0.2rem !important;
            }
            
            .space-y-3 > * + * {
                margin-top: 0.7rem !important;
            }
            
            .space-y-4 > * + * {
                margin-top: 0.9rem !important;
            }
            
            .py-2 {
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }
            
            .px-4 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            .mb-8, .mt-8 {
                margin-bottom: 1.6rem !important;
                margin-top: 1.6rem !important;
            }
            
            .pt-6 {
                padding-top: 1.2rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">QR Code Error</h1>
            
            @if(session('error'))
                <p class="text-gray-600 mb-6">{{ session('error') }}</p>
            @else
                <p class="text-gray-600 mb-6">The QR code you scanned is invalid or has expired.</p>
            @endif
        </div>
        
        <div class="space-y-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left">
                <h3 class="font-medium text-yellow-800 mb-2">What to do:</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Ask your waiter for a new QR code</li>
                    <li>• Make sure you're scanning the correct table's QR code</li>
                    <li>• Check if the QR code has expired</li>
                </ul>
            </div>
            
            <div class="flex flex-col space-y-3">
                <button onclick="goBack()"
                        class="w-full bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200">
                    Go Back
                </button>

                <a href="{{ route('customer.menu.index') }}"
                   class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 text-center block">
                    Browse Menu
                </a>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">Need help?</p>
            <p class="text-sm text-blue-600">Please contact our staff for assistance</p>
        </div>
    </div>

    <script>
        function goBack() {
            // Check if there's history to go back to
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // If no history, redirect to customer menu
                window.location.href = "{{ route('customer.menu.index') }}";
            }
        }
    </script>
</body>
</html>
