<!-- index.blade.php for Table Layout Config -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Table Layout Configurations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('table-layout-config.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Layout
                </a>
                <div class="text-sm text-gray-600">
                    Total Layouts: {{ $layouts->total() }}
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('table-layout-config.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Layout name...">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Status</option>
                                <option value="1" @if(request('status') === '1') selected @endif>Active</option>
                                <option value="0" @if(request('status') === '0') selected @endif>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label for="canvas_size" class="block text-sm font-medium text-gray-700">Canvas Size</label>
                            <select name="canvas_size" id="canvas_size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Sizes</option>
                                <option value="800x600" @if(request('canvas_size') === '800x600') selected @endif>800x600</option>
                                <option value="1024x768" @if(request('canvas_size') === '1024x768') selected @endif>1024x768</option>
                                <option value="1200x800" @if(request('canvas_size') === '1200x800') selected @endif>1200x800</option>
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

                    @if(request()->hasAny(['search', 'status', 'canvas_size']))
                        <div class="mt-3">
                            <a href="{{ route('table-layout-config.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Layouts Grid/Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    <!-- Grid View for Layouts -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($layouts as $layout)
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Layout Preview -->
                            <div class="bg-gray-100 h-48 flex items-center justify-center relative">
                                @if($layout->floor_plan_image)
                                    <img src="{{ Storage::url($layout->floor_plan_image) }}" 
                                         alt="{{ $layout->layout_name }}" 
                                         class="max-w-full max-h-full object-contain">
                                @else
                                    <div class="text-center text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm">No image</p>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $layout->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $layout->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Layout Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2">{{ $layout->layout_name }}</h3>
                                
                                <div class="space-y-1 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                        {{ $layout->canvas_width }} x {{ $layout->canvas_height }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Created {{ $layout->created_at->diffForHumans() }}
                                    </div>
                                    @if($layout->updated_at != $layout->created_at)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Updated {{ $layout->updated_at->diffForHumans() }}
                                    </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('table-layout-config.show', $layout->id) }}" 
                                       class="relative z-10 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-black shadow">
                                        View
                                    </a>
                                    <a href="{{ route('table-layout-config.edit', $layout->id) }}" 
                                       class="flex-1 text-center px-3 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('table-layout-config.destroy', $layout->id) }}" 
                                          onsubmit="return confirm('Are you sure to delete this layout configuration?');" class="inline">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No layout configurations found</h3>
                                <p class="text-gray-500 mb-6">Create your first table layout configuration to get started.</p>
                                <a href="{{ route('table-layout-config.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Create Layout
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $layouts->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>