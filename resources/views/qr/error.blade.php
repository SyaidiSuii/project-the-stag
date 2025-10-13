<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Error - The Stag</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
