<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($menuItem->id)
                {{ __('Edit Menu Item') }} - {{ $menuItem->name }}
            @else
                {{ __('Create New Menu Item') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($menuItem->id)
                            {{ __('Edit Menu Item Information') }}
                        @else
                            {{ __('Menu Item Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Complete the menu item details below.") }}
                    </p>
                </header>

                @if($menuItem->id)
                    <form method="post" action="{{ route('admin.menu-items.update', $menuItem->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('admin.menu-items.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Basic Item Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Item Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                :value="old('name', $menuItem->name)" placeholder="e.g. Nasi Lemak Special" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <optgroup label="{{ $category->name }}">
                                        <option value="{{ $category->id }}" 
                                            @if(old('category_id', $selectedCategoryId ?? $menuItem->category_id) == $category->id) selected @endif>
                                            {{ $category->name }} (Main)
                                        </option>
                                        @foreach($category->subCategories as $subCategory)
                                            <option value="{{ $subCategory->id }}" 
                                                @if(old('category_id', $selectedCategoryId ?? $menuItem->category_id) == $subCategory->id) selected @endif>
                                                {{ $category->name }} > {{ $subCategory->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            <p class="mt-1 text-sm text-gray-500">Choose the main category or a sub-category</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                  placeholder="Describe the item, ingredients, or special features...">{{ old('description', $menuItem->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Price and Timing -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="price" :value="__('Price (RM)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                :value="old('price', $menuItem->price)" placeholder="e.g. 15.90" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <div>
                            <x-input-label for="preparation_time" :value="__('Preparation Time (minutes)')" />
                            <x-text-input id="preparation_time" name="preparation_time" type="number" min="1" max="180" class="mt-1 block w-full" 
                                :value="old('preparation_time', $menuItem->preparation_time ?: 15)" placeholder="e.g. 15"/>
                            <x-input-error class="mt-2" :messages="$errors->get('preparation_time')" />
                        </div>
                    </div>

                    <!-- Image URL -->
                    <div>
                        <x-input-label for="image_url" :value="__('Image URL (Optional)')" />
                        <x-text-input id="image_url" name="image_url" type="url" class="mt-1 block w-full" 
                            :value="old('image_url', $menuItem->image_url)" placeholder="https://example.com/image.jpg"/>
                        <x-input-error class="mt-2" :messages="$errors->get('image_url')" />
                        <p class="mt-1 text-sm text-gray-500">Add a URL to an image of this menu item</p>
                        
                        @if($menuItem->image_url || old('image_url'))
                        <div class="mt-2">
                            <img id="image_preview" src="{{ old('image_url', $menuItem->image_url) }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                        @else
                        <div id="image_preview" class="mt-2 hidden">
                            <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                        @endif
                    </div>

                    <!-- Allergens -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Allergen Information</h3>
                        
                        <div>
                            <x-input-label for="allergens" :value="__('Allergens (Optional)')" />
                            <div class="mt-2 space-y-2">
                                @php
                                    $commonAllergens = ['Dairy', 'Eggs', 'Fish', 'Shellfish', 'Tree Nuts', 'Peanuts', 'Wheat', 'Soy', 'Sesame'];
                                    $selectedAllergens = old('allergens', $menuItem->allergens ?: []);
                                @endphp
                                
                                <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                                    @foreach($commonAllergens as $allergen)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="allergens[]" value="{{ $allergen }}" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               @if(in_array($allergen, $selectedAllergens)) checked @endif>
                                        <span class="ml-2 text-sm text-gray-700">{{ $allergen }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3">
                                    <input type="text" id="custom_allergen" placeholder="Add custom allergen..." 
                                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <button type="button" id="add_allergen" class="mt-1 px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                        Add Allergen
                                    </button>
                                </div>
                                
                                <div id="custom_allergens" class="flex flex-wrap gap-1 mt-2">
                                    @if($selectedAllergens)
                                        @foreach($selectedAllergens as $allergen)
                                            @if(!in_array($allergen, $commonAllergens))
                                                <span class="inline-flex items-center px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                                    {{ $allergen }}
                                                    <input type="hidden" name="allergens[]" value="{{ $allergen }}">
                                                    <button type="button" class="ml-1 text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">×</button>
                                                </span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('allergens')" />
                        </div>
                    </div>

                    <!-- Status and Features -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Features</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <input type="hidden" name="availability" value="0">
                                <input type="checkbox" name="availability" value="1" id="availability" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       @if(old('availability', $menuItem->availability ?? true)) checked @endif>
                                <label for="availability" class="ml-2 text-sm text-gray-700">Available for ordering</label>
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" id="is_featured" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       @if(old('is_featured', $menuItem->is_featured)) checked @endif>
                                <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured item</label>
                            </div>
                        </div>
                    </div>

                    <!-- Current Category Information (for edit) -->
                    @if($menuItem->id && $menuItem->category)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Category Information</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Current Category:</span>
                                        <span class="font-medium">{{ $menuItem->category->name }}</span>
                                        @if($menuItem->category->parent)
                                            <span class="text-gray-500">({{ $menuItem->category->parent->name }} > {{ $menuItem->category->name }})</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Category Type:</span>
                                        <span class="font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                            {{ ucfirst($menuItem->category->type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Current Rating (for edit) -->
                    @if($menuItem->id && $menuItem->rating_count > 0)
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900">Current Rating</h4>
                                <div class="mt-2 flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menuItem->rating_average))
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @elseif($i - 0.5 <= $menuItem->rating_average)
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor" fill-opacity="0.5"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-lg font-medium text-gray-700">{{ number_format($menuItem->rating_average, 2) }}</span>
                                    <span class="ml-1 text-sm text-gray-500">({{ $menuItem->rating_count }} reviews)</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Menu Item') }}</x-primary-button>

                        <a href="{{ route('admin.menu-items.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image_url').addEventListener('input', function() {
            const imageUrl = this.value;
            const preview = document.getElementById('image_preview');
            const img = preview.querySelector('img');
            
            if (imageUrl) {
                img.src = imageUrl;
                preview.classList.remove('hidden');
                
                // Hide preview if image fails to load
                img.onerror = function() {
                    preview.classList.add('hidden');
                };
            } else {
                preview.classList.add('hidden');
            }
        });

        // Custom allergen functionality
        document.getElementById('add_allergen').addEventListener('click', function() {
            const input = document.getElementById('custom_allergen');
            const allergen = input.value.trim();
            
            if (allergen && allergen.length > 0) {
                const container = document.getElementById('custom_allergens');
                
                // Check if allergen already exists
                const existing = container.querySelector(`input[value="${allergen}"]`);
                if (existing) {
                    alert('This allergen is already added');
                    return;
                }
                
                const span = document.createElement('span');
                span.className = 'inline-flex items-center px-2 py-1 text-xs bg-red-100 text-red-800 rounded';
                span.innerHTML = `
                    ${allergen}
                    <input type="hidden" name="allergens[]" value="${allergen}">
                    <button type="button" class="ml-1 text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">×</button>
                `;
                
                container.appendChild(span);
                input.value = '';
            }
        });

        // Allow adding allergen with Enter key
        document.getElementById('custom_allergen').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('add_allergen').click();
            }
        });

        // Price validation
        document.getElementById('price').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = '';
            }
        });

        // Preparation time validation
        document.getElementById('preparation_time').addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value < 1) {
                this.value = 1;
            } else if (value > 180) {
                this.value = 180;
            }
        });
    </script>
</x-app-layout>