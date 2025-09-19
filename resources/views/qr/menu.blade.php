<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - The Stag</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">Our Menu</h1>
            <p class="text-center text-gray-600 mb-8">Welcome to Table {{ $session->table->table_number }}</p>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Cart Summary</h2>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Items in cart:</span>
                    <span class="font-bold text-gray-800">{{ count($cart) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($cartTotal, 2) }}</span>
                </div>
                <a href="{{ route('qr.cart', ['session' => $session->session_code]) }}" class="text-blue-500 hover:underline mt-4 inline-block">View Cart &rarr;</a>
            </div>

            @foreach ($menuItems as $category => $items)
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 border-b-2 border-gray-200 pb-2 mb-4">{{ $category }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($items as $item)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No Image</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $item->name }}</h3>
                                    <p class="text-gray-600 mt-2">{{ $item->description }}</p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-lg font-bold text-gray-800">Rp {{ number_format($item->price, 2) }}</span>
                                        <form action="{{ route('qr.cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="session_code" value="{{ $session->session_code }}">
                                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                            <input type="number" name="quantity" value="1" min="1" class="w-16 text-center border border-gray-300 rounded-md">
                                            <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
