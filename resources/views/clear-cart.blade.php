<!DOCTYPE html>
<html>
<head>
    <title>Clear Cart</title>
</head>
<body>
    <h1>Clearing cart...</h1>
    <script>
        localStorage.removeItem('cart');
        sessionStorage.clear();
        alert('Cart cleared!');
        window.location.href = '{{ route("customer.menu.index") }}';
    </script>
</body>
</html>
