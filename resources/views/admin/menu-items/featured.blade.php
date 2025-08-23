<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Featured Menu Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">Featured Items</h3>
                    <p class="text-sm text-gray-600">Items highlighted on your menu</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('menu-items.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                        Add New Item
                    </a>
                    <a href="{{ route('menu-items.index') }}" class="items-center px-4 py-2 bg-gray-500 rounded font-semibold text-white hover:bg-gray-600">
                        All Items
                    </a>
                </div>
            </div>

            @if(session('message'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if($featuredItems->count() > 0)
                <!-- Featured Items Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredItems as $item)
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
                        <!-- Featured Badge -->
                        <div class="relative">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Featured Star Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Featured
                                </span>
                            </div>

                            <!-- Availability Status -->
                            @if(!$item->availability)
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Unavailable
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
                                <span class="text-lg font-bold text-green-600">RM {{ number_format($item->price, 2) }}</span>
                            </div>

                            <div class="mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                    {{ $item->category }}
                                </span>
                            </div>

                            @if($item->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>
                            @endif

                            <!-- Rating -->
                            @if($item->rating_count > 0)
                            <div class="flex items-center mb-4">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->rating_average))
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @elseif($i - 0.5 <= $item->rating_average)
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor" fill-opacity="0.5"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-1 text-sm text-gray-600">
                                    {{ number_format($item->rating_average, 1) }} ({{ $item->rating_count }})
                                </span>
                            </div>
                            @endif

                            <!-- Allergens -->
                            @if($item->allergens && count($item->allergens) > 0)
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($item->allergens, 0, 3) as $allergen)
                                        <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-800 rounded">{{ $allergen }}</span>
                                    @endforeach
                                    @if(count($item->allergens) > 3)
                                        <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-800 rounded">+{{ count($item->allergens) - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Preparation Time -->
                            <div class="mb-4 flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $item->preparation_time }} min prep time
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col space-y-2">
                                <div class="flex space-x-2">
                                    <a href="{{ route('menu-items.show', $item->id) }}" 
                                       class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                        View Details
                                    </a>
                                    <a href="{{ route('menu-items.edit', $item->id) }}" 
                                       class="flex-1 text-center px-3 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                        Edit
                                    </a>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('menu-items.toggle-availability', $item->id) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full px-3 py-2 text-sm rounded
                                            @if($item->availability) bg-red-500 text-white hover:bg-red-600
                                            @else bg-green-500 text-white hover:bg-green-600 @endif">
                                            {{ $item->availability ? 'Hide' : 'Show' }}
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('menu-items.toggle-featured', $item->id) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full px-3 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                            Unfeature
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No featured items</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by featuring some menu items to highlight them.</p>
                        <div class="mt-6">
                            <a href="{{ route('menu-items.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                View All Menu Items
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>