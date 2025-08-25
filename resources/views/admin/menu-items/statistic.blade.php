<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu Items Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">Menu Statistics</h3>
                    <p class="text-sm text-gray-600">Overview of your menu items</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.menu-items.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                        Add New Item
                    </a>
                    <a href="{{ route('admin.menu-items.index') }}" class="items-center px-4 py-2 bg-gray-500 rounded font-semibold text-white hover:bg-gray-600">
                        All Items
                    </a>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_items'] ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Available</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['available_items'] ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Featured</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['featured_items'] ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">RM</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Price</dt>
                                    <dd class="text-lg font-medium text-gray-900">RM {{ number_format($stats['average_price'] ?? 0, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Category Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Items by Category</h4>
                    </div>
                    <div class="p-6">
                        @if($stats['by_category'] ?? false)
                            <div class="space-y-4">
                                @php
                                    $categoryColors = [
                                        'western' => 'bg-blue-500',
                                        'local' => 'bg-green-500',
                                        'drink' => 'bg-cyan-500',
                                        'dessert' => 'bg-pink-500',
                                        'appetizer' => 'bg-orange-500'
                                    ];
                                    $totalItems = collect($stats['by_category'])->sum();
                                @endphp
                                
                                @foreach($stats['by_category'] as $category => $count)
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="flex justify-between text-sm">
                                            <span class="font-medium capitalize">{{ $category }}</span>
                                            <span class="text-gray-500">{{ $count }} items</span>
                                        </div>
                                        <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $categoryColors[$category] ?? 'bg-gray-500' }}" 
                                                 style="width: {{ $totalItems > 0 ? ($count / $totalItems) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <span class="text-sm font-medium">{{ $totalItems > 0 ? round(($count / $totalItems) * 100, 1) : 0 }}%</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No category data available</p>
                        @endif
                    </div>
                </div>

                <!-- Price and Rating Info -->
                <div class="space-y-6">
                    <!-- Price Range -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Price Information</h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">RM {{ number_format($stats['price_range']['min'] ?? 0, 2) }}</div>
                                    <div class="text-sm text-gray-500">Lowest Price</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">RM {{ number_format($stats['price_range']['max'] ?? 0, 2) }}</div>
                                    <div class="text-sm text-gray-500">Highest Price</div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="text-lg font-semibold text-gray-700">RM {{ number_format($stats['average_price'] ?? 0, 2) }}</div>
                                <div class="text-sm text-gray-500">Average Price</div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Rating Information</h4>
                        </div>
                        <div class="p-6">
                            @if($stats['average_rating'])
                                <div class="text-center">
                                    <div class="flex justify-center text-yellow-400 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($stats['average_rating']))
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @elseif($i - 0.5 <= $stats['average_rating'])
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor" fill-opacity="0.5"/>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</div>
                                    <div class="text-sm text-gray-500">Overall Average Rating</div>
                                </div>
                            @else
                                <div class="text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    <p class="font-medium">No ratings yet</p>
                                    <p class="text-sm">Items haven't been rated</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Quick Actions</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('admin.menu-items.index', ['availability' => '0']) }}" 
                               class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-red-900">Unavailable Items</div>
                                        <div class="text-sm text-red-600">{{ ($stats['total_items'] ?? 0) - ($stats['available_items'] ?? 0) }} items</div>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.menu-items.featured') }}" 
                               class="block p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-yellow-900">Featured Items</div>
                                        <div class="text-sm text-yellow-600">{{ $stats['featured_items'] ?? 0 }} items</div>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.menu-items.index', ['sort_by' => 'rating_average', 'sort_order' => 'desc']) }}" 
                               class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-green-900">Top Rated</div>
                                        <div class="text-sm text-green-600">View by rating</div>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.menu-items.index', ['sort_by' => 'price', 'sort_order' => 'desc']) }}" 
                               class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-blue-900">Premium Items</div>
                                        <div class="text-sm text-blue-600">Highest priced</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Category Details -->
                @if($stats['by_category'] ?? false)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Category Overview</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            @foreach($stats['by_category'] as $category => $count)
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center
                                    @if($category == 'western') bg-blue-100 text-blue-600
                                    @elseif($category == 'local') bg-green-100 text-green-600
                                    @elseif($category == 'drink') bg-cyan-100 text-cyan-600
                                    @elseif($category == 'dessert') bg-pink-100 text-pink-600
                                    @elseif($category == 'appetizer') bg-orange-100 text-orange-600
                                    @else bg-gray-100 text-gray-600 @endif">
                                    @if($category == 'western')
                                        🍕
                                    @elseif($category == 'local')
                                        🍜
                                    @elseif($category == 'drink')
                                        🥤
                                    @elseif($category == 'dessert')
                                        🍰
                                    @elseif($category == 'appetizer')
                                        🥗
                                    @else
                                        📋
                                    @endif
                                </div>
                                <div class="text-lg font-bold text-gray-900">{{ $count }}</div>
                                <div class="text-sm text-gray-600 capitalize">{{ $category }}</div>
                                <a href="{{ route('admin.menu-items.index', ['category' => $category]) }}" 
                                   class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 inline-block">
                                    View Items
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Recent Activity (if applicable) -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Menu Management Tips</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-gray-900">Balance Your Menu</h5>
                                <p class="text-sm text-gray-600">Ensure you have a good mix of categories to appeal to different preferences.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-gray-900">Price Strategy</h5>
                                <p class="text-sm text-gray-600">Review your pricing regularly to ensure competitiveness and profitability.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-gray-900">Feature Popular Items</h5>
                                <p class="text-sm text-gray-600">Highlight your best-rated and most popular items to drive sales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>