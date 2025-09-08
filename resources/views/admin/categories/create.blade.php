<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Category Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Create a new category for organizing menu items.") }}
                    </p>
                </header>

                <form method="post" action="{{ route('admin.categories.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <!-- Category Name -->
                    <div>
                        <x-input-label for="name" :value="__('Category Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                            :value="old('name')" placeholder="Enter category name" required autofocus/>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category Type -->
                    <div>
                        <x-input-label for="type" :value="__('Category Type')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select Category Type</option>
                            <option value="main" @if(old('type') == 'main') selected @endif>Main Category</option>
                            <option value="sub" @if(old('type') == 'sub') selected @endif>Sub Category</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <!-- Parent Category (for sub categories) -->
                    <div id="parent-category-section" class="hidden">
                        <x-input-label for="parent_id" :value="__('Parent Category')" />
                        <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Parent Category</option>
                            @foreach($mainCategories as $mainCategory)
                                <option value="{{ $mainCategory->id }}" @if(old('parent_id') == $mainCategory->id) selected @endif>
                                    {{ $mainCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
                        <p class="mt-1 text-sm text-gray-500">Required for sub categories</p>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <x-input-label for="sort_order" :value="__('Sort Order (Optional)')" />
                        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" 
                            :value="old('sort_order')" placeholder="Auto-generated if empty"/>
                        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                        <p class="mt-1 text-sm text-gray-500">Leave empty to automatically set as the last order</p>
                    </div>

                    <!-- Category Preview -->
                    <div id="category-preview" class="hidden">
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-medium text-gray-900 mb-2">Category Preview</h4>
                            <div class="text-sm text-gray-600">
                                <div id="preview-name" class="font-medium"></div>
                                <div id="preview-type" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded inline-block mt-1"></div>
                                <div id="preview-parent" class="mt-1"></div>
                                <div id="preview-order" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Create Category') }}</x-primary-button>

                        <a href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
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
            const preview = document.getElementById('category-preview');

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

                if (name && type) {
                    preview.classList.remove('hidden');
                    
                    document.getElementById('preview-name').textContent = name;
                    document.getElementById('preview-type').textContent = type === 'main' ? 'Main Category' : 'Sub Category';
                    
                    const parentDiv = document.getElementById('preview-parent');
                    if (type === 'sub' && parentId) {
                        const parentOption = parentSelect.querySelector(`option[value="${parentId}"]`);
                        parentDiv.textContent = `Parent: ${parentOption ? parentOption.textContent : 'Unknown'}`;
                        parentDiv.classList.remove('hidden');
                    } else {
                        parentDiv.textContent = '';
                        parentDiv.classList.add('hidden');
                    }
                    
                    const orderDiv = document.getElementById('preview-order');
                    if (sortOrder) {
                        orderDiv.textContent = `Sort Order: ${sortOrder}`;
                    } else {
                        orderDiv.textContent = 'Sort Order: Auto-generated';
                    }
                } else {
                    preview.classList.add('hidden');
                }
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
        });
    </script>
</x-app-layout>