<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Categories Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('admin.categories.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Create New Category
                </a>
                <div class="flex space-x-2">
                    <button onclick="toggleSortable()" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Toggle Sort Mode
                    </button>
                    <a href="{{ route('admin.categories.hierarchical') }}" class="px-4 py-2 bg-green-600 text-white rounded font-semibold hover:bg-green-700">
                        View API
                    </a>
                </div>
            </div>

            <!-- Categories List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div id="categories-container" class="space-y-4">
                        @forelse($categories as $category)
                        <div class="category-item border rounded-lg" data-id="{{ $category->id }}">
                            <!-- Main Category -->
                            <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <div class="sort-handle cursor-move hidden">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $category->name }}</h3>
                                        <div class="flex items-center space-x-3 text-sm text-gray-600">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                {{ ucfirst($category->type) }}
                                            </span>
                                            <span>Sort Order: {{ $category->sort_order }}</span>
                                            <span>Sub Categories: {{ $category->subCategories->count() }}</span>
                                            <span>Menu Items: {{ $category->menuItems->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.show', $category->id) }}" 
                                       class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                        View
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="px-3 py-1 bg-gray-800 text-white rounded text-sm hover:bg-gray-700">
                                        Edit
                                    </a>
                                    @if($category->subCategories->count() == 0 && $category->menuItems->count() == 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" 
                                              onsubmit="return confirm('Are you sure to delete this category?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Sub Categories -->
                            @if($category->subCategories->count() > 0)
                            <div class="p-4">
                                <h4 class="font-medium text-gray-700 mb-3">Sub Categories:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($category->subCategories as $subCategory)
                                    <div class="sub-category-item bg-gray-50 border rounded-lg p-3" data-id="{{ $subCategory->id }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h5 class="font-medium">{{ $subCategory->name }}</h5>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                                        {{ ucfirst($subCategory->type) }}
                                                    </span>
                                                    <span class="ml-2">Order: {{ $subCategory->sort_order }}</span>
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Menu Items: {{ $subCategory->menuItems->count() }}
                                                </div>
                                            </div>
                                            <div class="flex space-x-1 ml-2">
                                                <a href="{{ route('admin.categories.show', $subCategory->id) }}" 
                                                   class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">
                                                    View
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $subCategory->id) }}" 
                                                   class="px-2 py-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700">
                                                    Edit
                                                </a>
                                                @if($subCategory->menuItems->count() == 0)
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $subCategory->id) }}" 
                                                          onsubmit="return confirm('Are you sure to delete this sub category?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No categories found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.categories.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    New Category
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Include SortableJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    <script>
        let sortableInstance = null;
        let sortMode = false;

        function toggleSortable() {
            if (sortMode) {
                disableSortMode();
            } else {
                enableSortMode();
            }
        }

        function enableSortMode() {
            sortMode = true;
            document.querySelectorAll('.sort-handle').forEach(handle => {
                handle.classList.remove('hidden');
            });
            
            const container = document.getElementById('categories-container');
            sortableInstance = Sortable.create(container, {
                handle: '.sort-handle',
                animation: 150,
                onEnd: function(evt) {
                    updateSortOrder();
                }
            });
            
            // Change button text
            document.querySelector('button[onclick="toggleSortable()"]').textContent = 'Save Sort Order';
        }

        function disableSortMode() {
            sortMode = false;
            document.querySelectorAll('.sort-handle').forEach(handle => {
                handle.classList.add('hidden');
            });
            
            if (sortableInstance) {
                sortableInstance.destroy();
                sortableInstance = null;
            }
            
            // Change button text
            document.querySelector('button[onclick="toggleSortable()"]').textContent = 'Toggle Sort Mode';
        }

        function updateSortOrder() {
            const categories = [];
            document.querySelectorAll('.category-item').forEach((item, index) => {
                categories.push({
                    id: item.dataset.id,
                    sort_order: index + 1
                });
            });

            fetch('{{ route("admin.categories.sort-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    categories: categories
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                    successDiv.textContent = 'Sort order updated successfully!';
                    document.body.appendChild(successDiv);
                    
                    setTimeout(() => {
                        successDiv.remove();
                    }, 3000);
                } else {
                    alert('Error updating sort order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating sort order');
            });
        }
    </script>
</x-app-layout>