<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The Stag - Menu')</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }}">

    <!-- Toast CSS -->
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">

    <!-- Page Specific Styles -->
    @yield('styles')

    <style>
        /* Override customer layout for QR - no sidebar */
        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 20px;
            width: 100%;
            max-width: 100%;
        }

        /* Hide sidebar if it exists */
        .sidebar {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Main Content (No Sidebar) -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Toast JavaScript -->
    <script src="{{ asset('js/toast.js') }}"></script>

    <!-- Flash Messages Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Toast.success('Success', '{{ session('success') }}');
            @endif

            @if(session('error'))
                Toast.error('Error', '{{ session('error') }}');
            @endif
        });
    </script>

    <!-- Page Specific Scripts -->
    @yield('scripts')
</body>

</html>
