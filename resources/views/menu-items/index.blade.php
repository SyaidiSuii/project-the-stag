<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('menu-items.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Menu Item
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('menu-items.featured') }}" class="items-center px-4 py-2 bg-yellow-600 rounded font-semibold text-white hover:bg-yellow-700">
                        Featured Items
                    </a>
                    <a href="{{ route('menu-items.stats') }}" class="items-center px-4 py-2 bg-blue-600 rounded font-semibold text-white hover:bg-blue-700">
                        Statistics
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('menu-items.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Name or description...">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Categories</option>
                                <option value="western" @if(request('category') == 'western') selected @endif>Western</option>
                                <option value="local" @if(request('category') == 'local') selected @endif>Local</option>
                                <option value="drink" @if(request('category') == 'drink') selected @endif>Drink</option>
                                <option value="dessert" @if(request('category') == 'dessert') selected @endif>Dessert</option>
                                <option value="appetizer" @if(request('category') == 'appetizer') selected @endif>Appetizer</option>
                            </select>
                        </div>

                        <div>
                            <label for="availability" class="block text-sm font-medium text-gray-700">Availability</label>
                            <select name="availability" id="availability" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Items</option>
                                <option value="1" @if(request('availability') == '1') selected @endif>Available</option>
                                <option value="0" @if(request('availability') == '0') selected @endif>Unavailable</option>
                            </select>
                        </div>

                        <div>
                            <label for="featured" class="block text-sm font-medium text-gray-700">Featured</label>
                            <select name="featured" id="featured" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Items</option>
                                <option value="1" @if(request('featured') == '1') selected @endif>Featured</option>
                                <option value="0" @if(request('featured') == '0') selected @endif>Not Featured</option>
                            </select>
                        </div>

                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700">Sort By</label>
                            <select name="sort_by" id="sort_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="name" @if(request('sort_by') == 'name') selected @endif>Name</option>
                                <option value="price" @if(request('sort_by') == 'price') selected @endif>Price</option>
                                <option value="category" @if(request('sort_by') == 'category') selected @endif>Category</option>
                                <option value="rating_average" @if(request('sort_by') == 'rating_average') selected @endif>Rating</option>
                                <option value="created_at" @if(request('sort_by') == 'created_at') selected @endif>Date Added</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 !text-white font-semibold 
                                    rounded-md hover:bg-indigo-700 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Filter
                            </button>
                        </div>
                    </form>

                    @if(request()->hasAny(['search', 'category', 'availability', 'featured', 'sort_by']))
                        <div class="mt-3">
                            <a href="{{ route('menu-items.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Menu Items Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Image</th>
                                    <th class="text-left py-2">Name</th>
                                    <th class="text-left py-2">Category</th>
                                    <th class="text-left py-2">Price</th>
                                    <th class="text-left py-2">Rating</th>
                                    <th class="text-left py-2">Prep Time</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menuItems as $menuItem)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($menuItems->currentPage() - 1) * $menuItems->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        @if($menuItem->image_url)
                                            <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}" class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">{{ $menuItem->name }}</div>
                                            @if($menuItem->description)
                                                <div class="text-sm text-gray-600">{{ Str::limit($menuItem->description, 50) }}</div>
                                            @endif
                                            @if($menuItem->allergens && count($menuItem->allergens) > 0)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($menuItem->allergens as $allergen)
                                                        <span class="px-1 py-0.5 text-xs bg-red-100 text-red-800 rounded">{{ $allergen }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded capitalize bg-gray-100 text-gray-800">
                                            {{ $menuItem->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-lg">RM {{ number_format($menuItem->price, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($menuItem->rating_average))
                                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                    @elseif($i - 0.5 <= $menuItem->rating_average)
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
                                                {{ number_format($menuItem->rating_average, 1) }} ({{ $menuItem->rating_count }})
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm">{{ $menuItem->preparation_time }} min</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 py-1 text-xs rounded
                                                @if($menuItem->availability) bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $menuItem->availability ? 'Available' : 'Unavailable' }}
                                            </span>
                                            @if($menuItem->is_featured)
                                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex space-x-1">
                                                <a href="{{ route('menu-items.show', $menuItem->id) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-black rounded-lg shadow hover:bg-blue-700">
                                                View
                                                </a>

                                                <a href="{{ route('menu-items.edit', $menuItem->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                    Edit
                                                </a>
                                            </div>
                                            <div class="flex space-x-1">
                                                <form method="POST" action="{{ route('menu-items.toggle-availability', $menuItem->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent rounded text-xs uppercase tracking-widest
                                                        @if($menuItem->availability) bg-red-500 text-white hover:bg-red-600
                                                        @else bg-green-500 text-white hover:bg-green-600 @endif">
                                                        {{ $menuItem->availability ? 'Hide' : 'Show' }}
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('menu-items.toggle-featured', $menuItem->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent rounded text-xs uppercase tracking-widest
                                                        @if($menuItem->is_featured) bg-gray-500 text-black hover:bg-gray-600
                                                        @else bg-yellow-500 text-black hover:bg-yellow-600 @endif">
                                                        {{ $menuItem->is_featured ? 'Unfeature' : 'Feature' }}
                                                    </button>
                                                </form>
                                            </div>
                                            <form method="POST" action="{{ route('menu-items.destroy', $menuItem->id) }}" 
                                                  onsubmit="return confirm('Are you sure to delete this menu item?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                               <x-danger-button class="text-xs w-full justify-center">
                                                    Delete
                                                </x-danger-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No menu items found</p>
                                            <p class="text-sm">Try adjusting your search criteria or add a new menu item</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $menuItems->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>