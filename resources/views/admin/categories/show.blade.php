<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Category Details') }} - {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ ucfirst($category->type) }} Category
                        @if($category->parent)
                            - Under {{ $category->parent->name }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Category
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Category Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Category Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Category ID:</span>
                                <p class="font-bold text-lg">#{{ $category->id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Category Name:</span>
                                <p class="font-bold text-lg">{{ $category->name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Type:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($category->type == 'main') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($category->type) }} Category
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Sort Order:</span>
                                <p class="font-medium">{{ $category->sort_order }}</p>
                            </div>
                        </div>

                        @if($category->parent)
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Parent Category:</span>
                            <p class="font-medium">
                                <a href="{{ route('admin.categories.show', $category->parent->id) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $category->parent->name }}
                                </a>
                            </p>
                        </div>
                        @endif

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $category->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Last Updated:</span>
                                    <p class="font-medium">{{ $category->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Statistics</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $category->subCategories->count() }}</div>
                                <div class="text-sm text-gray-600">Sub Categories</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $category->menuItems->count() }}</div>
                                <div class="text-sm text-gray-600">Menu Items</div>
                            </div>
                        </div>

                        @if($category->type == 'main')
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">Total Menu Items (including sub-categories):</span>
                            @php
                                $totalMenuItems = $category->menuItems->count();
                                foreach($category->subCategories as $subCategory) {
                                    $totalMenuItems += $subCategory->menuItems->count();
                                }
                            @endphp
                            <p class="font-bold text-xl text-purple-600">{{ $totalMenuItems }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sub Categories -->
                @if($category->subCategories->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">Sub Categories ({{ $category->subCategories->count() }})</h4>
                        <a href="{{ route('admin.categories.create') }}?parent_id={{ $category->id }}" 
                           class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            Add Sub Category
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($category->subCategories as $subCategory)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                                <div>
                                    <h5 class="font-medium">
                                        <a href="{{ route('admin.categories.show', $subCategory->id) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $subCategory->name }}
                                        </a>
                                    </h5>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <span>Sort Order: {{ $subCategory->sort_order }}</span>
                                        <span class="ml-3">Menu Items: {{ $subCategory->menuItems->count() }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.show', $subCategory->id) }}" 
                                       class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">
                                        View
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $subCategory->id) }}" 
                                       class="px-2 py-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700">
                                        Edit
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Menu Items -->
                @if($category->menuItems->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">Menu Items ({{ $category->menuItems->count() }})</h4>
                        <a href="{{ route('admin.menu-items.create') }}?category_id={{ $category->id }}" 
                           class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                            Add Menu Item
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($category->menuItems as $menuItem)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                                <div>
                                    <h5 class="font-medium">
                                        <a href="{{ route('admin.menu-items.show', $menuItem->id) }}" 
                                           class="text-green-600 hover:text-green-800">
                                            {{ $menuItem->name ?? 'Menu Item #' . $menuItem->id }}
                                        </a>
                                    </h5>
                                    <div class="text-sm text-gray-600 mt-1">
                                        @if(isset($menuItem->price))
                                            <span>Price: RM {{ number_format($menuItem->price, 2) }}</span>
                                        @endif
                                        @if(isset($menuItem->status))
                                            <span class="ml-3 px-2 py-1 rounded text-xs
                                                @if($menuItem->status == 'available') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($menuItem->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.menu-items.show', $menuItem->id) }}" 
                                       class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                        View
                                    </a>
                                    <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" 
                                       class="px-2 py-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700">
                                        Edit
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Menu Items</h4>
                    </div>
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No menu items</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a menu item to this category.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.menu-items.create') }}?category_id={{ $category->id }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Add Menu Item
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Actions</h4>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Edit Category
                        </a>
                        
                        @if($category->type == 'main')
                        <a href="{{ route('admin.categories.create') }}?parent_id={{ $category->id }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Add Sub Category
                        </a>
                        @endif

                        <a href="{{ route('admin.menu-items.create') }}?category_id={{ $category->id }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Add Menu Item
                        </a>

                        @if($category->subCategories->count() == 0 && $category->menuItems->count() == 0)
                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');" 
                              class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                Delete Category
                            </button>
                        </form>
                        @else
                        <div class="text-center text-sm text-gray-500">
                            Cannot delete category with sub-categories or menu items
                        </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>