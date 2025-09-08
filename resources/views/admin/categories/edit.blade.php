<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Category') }} - {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Edit Category Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Update the category details below.") }}
                    </p>
                </header>

                <form method="post" action="{{ route('admin.categories.update', $category->id) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Category Name -->
                    <div>
                        <x-input-label for="name" :value="__('Category Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                            :value="old('name', $category->name)" placeholder="Enter category name" required autofocus/>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category Type -->
                    <div>
                        <x-input-label for="type" :value="__('Category Type')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select Category Type</option>
                            <option value="main" @if(old('type', $category->type) == 'main') selected @endif>Main Category</option>
                            <option value="sub" @if(old('type', $category->type) == 'sub') selected @endif>Sub Category</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <!-- Parent Category (for sub categories) -->
                    <div id="parent-category-section" class="{{ old('type', $category->type) != 'sub' ? 'hidden' : '' }}">
                        <x-input-label for="parent_id" :value="__('Parent Category')" />
                        <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Parent Category</option>
                            @foreach($mainCategories as $mainCategory)
                                <option value="{{ $mainCategory->id }}" @if(old('parent_id', $category->parent_id) == $mainCategory->id) selected @endif>
                                    {{ $mainCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
                        <p class="mt-1 text-sm text-gray-500">Required for sub categories</p>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <x-input-label for="sort_order" :value="__('Sort Order')" />
                        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" 
                            :value="old('sort_order', $category->sort_order)" placeholder="0"/>
                        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                    </div>

                    <!-- Current Category Information -->
                    <div class="border-t pt-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Current Category Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Category ID:</span>
                                    <span class="font-medium">#{{ $category->id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Current Type:</span>
                                    <span class="font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </div>
                                @if($category->menuItems->count() > 0)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-orange-800">Information</h3>
                                <div class="mt-2 text-sm text-orange-700">
                                    <p>This category has {{ $category->menuItems->count() }} menu items associated with it.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Update Category') }}</x-primary-button>

                        <a href="{{ route('admin.categories.show', $category->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            View Category
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Back to List
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const parentSection = document.getElementById('parent-category-section');
            const parentSelect = document.getElementById('parent_id');
            const nameInput = document.getElementById('name');
            const sortOrderInput = document.getElementById('sort_order');

            // Handle category type change
            typeSelect.addEventListener('change', function() {
                if (this.value === 'sub') {
                    parentSection.classList.remove('hidden');
                    parentSelect.required = true;
                } else {
                    parentSection.classList.add('hidden');
                    parentSelect.required = false;
                    parentSelect.value = '';
                }
                updatePreview();
            });

            // Handle input changes for preview
            [nameInput, typeSelect, parentSelect, sortOrderInput].forEach(input => {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            function updatePreview() {
                const name = nameInput.value.trim();
                const type = typeSelect.value;
                const parentId = parentSelect.value;
                const sortOrder = sortOrderInput.value;

                document.getElementById('preview-name').textContent = name || '{{ $category->name }}';
                document.getElementById('preview-type').textContent = (type || '{{ $category->type }}') === 'main' ? 'Main Category' : 'Sub Category';
                
                const parentDiv = document.getElementById('preview-parent');
                if (type === 'sub' && parentId) {
                    const parentOption = parentSelect.querySelector(`option[value="${parentId}"]`);
                    parentDiv.textContent = `Parent: ${parentOption ? parentOption.textContent : 'Unknown'}`;
                } else if (type === 'sub') {
                    parentDiv.textContent = 'Parent: (Select a parent category)';
                } else {
                    parentDiv.textContent = '';
                }
                
                document.getElementById('preview-order').textContent = `Sort Order: ${sortOrder || '{{ $category->sort_order }}'}`;
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const type = typeSelect.value;
                const parentId = parentSelect.value;

                if (type === 'sub' && !parentId) {
                    e.preventDefault();
                    alert('Please select a parent category for sub category');
                    parentSelect.focus();
                    return false;
                }
            });

            // Initialize preview on page load
            updatePreview();
        });
    </script>
</x-app-layout>category->parent)
                                <div>
                                    <span class="text-gray-600">Current Parent:</span>
                                    <span class="font-medium">{{ $category->parent->name }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600">Sub Categories:</span>
                                    <span class="font-medium">{{ $category->subCategories->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Menu Items:</span>
                                    <span class="font-medium">{{ $category->menuItems->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <span class="font-medium">{{ $category->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Preview -->
                    <div id="category-preview">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-medium text-gray-900 mb-2">Updated Category Preview</h4>
                            <div class="text-sm text-gray-600">
                                <div id="preview-name" class="font-medium">{{ old('name', $category->name) }}</div>
                                <div id="preview-type" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded inline-block mt-1">
                                    {{ ucfirst(old('type', $category->type)) }} Category
                                </div>
                                <div id="preview-parent" class="mt-1">
                                    @if(old('type', $category->type) == 'sub' && $category->parent)
                                        Parent: {{ $category->parent->name }}
                                    @endif
                                </div>
                                <div id="preview-order" class="mt-1">Sort Order: {{ old('sort_order', $category->sort_order) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Messages -->
                    @if($category->subCategories->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Warning</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This category has {{ $category->subCategories->count() }} sub-categories. Changing this to a sub-category may affect the structure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($