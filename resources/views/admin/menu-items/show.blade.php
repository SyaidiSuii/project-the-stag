<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Item Details') }} - {{ $menuItem->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $menuItem->name }}</h3>
                    <p class="text-sm text-gray-600">{{ ucfirst($menuItem->category) }} - RM {{ number_format($menuItem->price, 2) }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Menu Item
                    </a>
                    <a href="{{ route('admin.menu-items.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Actions</h4>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.menu-items.toggle-availability', $menuItem->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium
                                @if($menuItem->availability) bg-red-500 text-white hover:bg-red-600
                                @else bg-green-500 text-white hover:bg-green-600 @endif">
                                {{ $menuItem->availability ? 'Mark Unavailable' : 'Mark Available' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.menu-items.toggle-featured', $menuItem->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium
                                @if($menuItem->is_featured) bg-gray-500 text-white hover:bg-gray-600
                                @else bg-yellow-500 text-white hover:bg-yellow-600 @endif">
                                {{ $menuItem->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                            </button>
                        </form>

                        <!-- Add Rating Form -->
                        <button onclick="document.getElementById('rating-modal').classList.remove('hidden')" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-medium">
                            Add Rating
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Item Image and Basic Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Item Information</h4>
                    </div>
                    <div class="p-6">
                        @if($menuItem->image_url)
                            <div class="mb-6">
                                <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}" 
                                     class="w-full h-64 object-cover rounded-lg shadow-lg">
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <span class="text-sm text-gray-600">Name:</span>
                                <p class="font-medium text-xl">{{ $menuItem->name }}</p>
                            </div>

                            @if($menuItem->description)
                            <div>
                                <span class="text-sm text-gray-600">Description:</span>
                                <p class="text-gray-700 mt-1">{{ $menuItem->description }}</p>
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Category:</span>
                                    <p class="font-medium">
                                        <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 capitalize">
                                            {{ $menuItem->category }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Price:</span>
                                    <p class="font-bold text-2xl text-green-600">RM {{ number_format($menuItem->price, 2) }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Preparation Time:</span>
                                    <p class="font-medium">{{ $menuItem->preparation_time }} minutes</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <div class="flex flex-col gap-1">
                                        <span class="px-2 py-1 text-xs rounded w-fit
                                            @if($menuItem->availability) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $menuItem->availability ? 'Available' : 'Unavailable' }}
                                        </span>
                                        @if($menuItem->is_featured)
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 w-fit">
                                                Featured Item
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rating and Allergens -->
                <div class="space-y-6">
                    <!-- Rating Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Customer Rating</h4>
                        </div>
                        <div class="p-6">
                            @if($menuItem->rating_count > 0)
                                <div class="text-center">
                                    <div class="flex justify-center text-yellow-400 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menuItem->rating_average))
                                                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @elseif($i - 0.5 <= $menuItem->rating_average)
                                                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor" fill-opacity="0.5"/>
                                                </svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-3xl font-bold text-gray-900">{{ number_format($menuItem->rating_average, 1) }}</p>
                                    <p class="text-sm text-gray-600">Based on {{ $menuItem->rating_count }} {{ Str::plural('review', $menuItem->rating_count) }}</p>
                                </div>
                            @else
                                <div class="text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    <p class="font-medium">No ratings yet</p>
                                    <p class="text-sm">Be the first to rate this item</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Allergen Information -->
                    @if($menuItem->allergens && count($menuItem->allergens) > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-red-50 border-b">
                            <h4 class="font-semibold text-red-800">Allergen Information</h4>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($menuItem->allergens as $allergen)
                                    <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800 border border-red-200">
                                        ⚠️ {{ $allergen }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-sm text-red-700 mt-3">
                                <strong>Warning:</strong> This item contains the allergens listed above. Please inform staff of any allergies before ordering.
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-green-50 border-b">
                            <h4 class="font-semibold text-green-800">Allergen Information</h4>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-green-700">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">No specific allergens listed</span>
                            </div>
                            <p class="text-sm text-green-600 mt-2">
                                However, please inform staff of any allergies as ingredients may vary.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- System Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">System Information</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <span class="text-sm text-gray-600">Created:</span>
                                <p class="font-medium">{{ $menuItem->created_at->format('M d, Y h:i A') }}</p>
                            </div>

                            @if($menuItem->updated_at != $menuItem->created_at)
                            <div>
                                <span class="text-sm text-gray-600">Last Updated:</span>
                                <p class="font-medium">{{ $menuItem->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif

                            <div>
                                <span class="text-sm text-gray-600">Item ID:</span>
                                <p class="font-mono text-sm">{{ $menuItem->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Rating Modal -->
    <div id="rating-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Add Rating for {{ $menuItem->name }}</h3>
            
            <form method="POST" action="{{ route('admin.menu-items.rating', $menuItem->id) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="rating-star text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="{{ $i }}">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value" required>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('rating-modal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Rating modal functionality
        const stars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating-value');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                // Update star display
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('text-yellow-400');
                    }
                });
            });
        });
        
        // Reset stars on mouse leave
        document.querySelector('.flex.space-x-1').addEventListener('mouseleave', function() {
            const currentRating = ratingInput.value;
            stars.forEach((s, i) => {
                if (i < currentRating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
        
        // Close modal when clicking outside
        document.getElementById('rating-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>