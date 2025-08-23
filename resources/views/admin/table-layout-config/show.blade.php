<!-- show.blade.php for Table Layout Config -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Layout Configuration') }} - {{ $layout->layout_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $layout->layout_name }}</h3>
                    <p class="text-sm text-gray-600">
                        Canvas: {{ $layout->canvas_width }} x {{ $layout->canvas_height }} pixels
                        <span class="ml-2 px-2 py-1 text-xs rounded-full 
                            {{ $layout->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $layout->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('table-layout-config.edit', $layout->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Layout
                    </a>
                    <a href="{{ route('table-layout-config.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            @if(session('message'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Layout Details -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Layout Details</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <span class="text-sm text-gray-600">Layout Name:</span>
                                <p class="font-medium text-lg">{{ $layout->layout_name }}</p>
                            </div>

                            <div>
                                <span class="text-sm text-gray-600">Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full
                                        {{ $layout->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $layout->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Height:</span>
                                    <p class="font-medium">{{ $layout->canvas_height }}px</p>
                                </div>
                            </div>

                            <div>
                                <span class="text-sm text-gray-600">Aspect Ratio:</span>
                                <p class="font-medium">
                                    @php
                                        $gcd = function($a, $b) use (&$gcd) {
                                            return $b ? $gcd($b, $a % $b) : $a;
                                        };
                                        $divisor = $gcd($layout->canvas_width, $layout->canvas_height);
                                        $ratioW = $layout->canvas_width / $divisor;
                                        $ratioH = $layout->canvas_height / $divisor;
                                    @endphp
                                    {{ $ratioW }}:{{ $ratioH }}
                                    @if($ratioW == 4 && $ratioH == 3)
                                        <span class="text-gray-500 text-sm">(Standard)</span>
                                    @elseif($ratioW == 16 && $ratioH == 9)
                                        <span class="text-gray-500 text-sm">(Widescreen)</span>
                                    @elseif($ratioW == 3 && $ratioH == 4)
                                        <span class="text-gray-500 text-sm">(Portrait)</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <span class="text-sm text-gray-600">Total Canvas Area:</span>
                                <p class="font-medium">{{ number_format($layout->canvas_width * $layout->canvas_height) }} pixels²</p>
                            </div>

                            @if($layout->floor_plan_image)
                            <div>
                                <span class="text-sm text-gray-600">Floor Plan:</span>
                                <p class="font-medium text-green-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Image uploaded
                                </p>
                            </div>
                            @else
                            <div>
                                <span class="text-sm text-gray-600">Floor Plan:</span>
                                <p class="font-medium text-gray-500">No image uploaded</p>
                            </div>
                            @endif

                            <div>
                                <span class="text-sm text-gray-600">Created:</span>
                                <p class="font-medium">{{ $layout->created_at->format('M d, Y h:i A') }}</p>
                                <p class="text-xs text-gray-500">{{ $layout->created_at->diffForHumans() }}</p>
                            </div>

                            @if($layout->updated_at != $layout->created_at)
                            <div>
                                <span class="text-sm text-gray-600">Last Updated:</span>
                                <p class="font-medium">{{ $layout->updated_at->format('M d, Y h:i A') }}</p>
                                <p class="text-xs text-gray-500">{{ $layout->updated_at->diffForHumans() }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Quick Actions</h4>
                        </div>
                        <div class="p-4 space-y-3">
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('table-layout-config.toggle-status', $layout->id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to {{ $layout->is_active ? 'deactivate' : 'activate' }} this layout?')"
                                        class="w-full px-4 py-2 text-sm font-medium rounded-md
                                            {{ $layout->is_active 
                                                ? 'bg-red-100 text-red-800 hover:bg-red-200' 
                                                : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                    {{ $layout->is_active ? 'Deactivate Layout' : 'Activate Layout' }}
                                </button>
                            </form>

                            <!-- Duplicate Layout -->
                            <form method="POST" action="{{ route('table-layout-config.duplicate', $layout->id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('This will create a copy of this layout configuration. Continue?')"
                                        class="w-full px-4 py-2 bg-blue-100 text-blue-800 hover:bg-blue-200 text-sm font-medium rounded-md">
                                    Duplicate Layout
                                </button>
                            </form>

                            <!-- Delete Layout -->
                            <form method="POST" action="{{ route('table-layout-config.destroy', $layout->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this layout? This action cannot be undone.')"
                                        class="w-full px-4 py-2 bg-red-100 text-red-800 hover:bg-red-200 text-sm font-medium rounded-md">
                                    Delete Layout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Canvas Preview and Floor Plan -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Canvas Preview</h4>
                        </div>
                        <div class="p-6">
                            <!-- Canvas Size Info -->
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-blue-800">Canvas Dimensions</span>
                                    <span class="text-blue-600">{{ $layout->canvas_width }} × {{ $layout->canvas_height }} pixels</span>
                                </div>
                            </div>

                            <!-- Canvas Container -->
                            <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50 overflow-auto">
                                <div class="flex justify-center">
                                    <div id="canvas-container" class="relative border-2 border-dashed border-gray-400 bg-white" 
                                         style="width: {{ min($layout->canvas_width, 800) }}px; height: {{ min($layout->canvas_height, 600) }}px;">
                                        
                                        @if($layout->floor_plan_image)
                                            <!-- Floor Plan Image -->
                                            <img src="{{ Storage::url($layout->floor_plan_image) }}" 
                                                 alt="{{ $layout->layout_name }} floor plan"
                                                 class="absolute inset-0 w-full h-full object-cover opacity-30">
                                        @endif

                                        <!-- Grid Overlay -->
                                        <div class="absolute inset-0 opacity-20" 
                                             style="background-image: repeating-linear-gradient(0deg, #000 0px, #000 1px, transparent 1px, transparent 20px), repeating-linear-gradient(90deg, #000 0px, #000 1px, transparent 1px, transparent 20px);">
                                        </div>

                                        <!-- Canvas Info Overlay -->
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="bg-white bg-opacity-90 p-4 rounded-lg border text-center">
                                                <h5 class="font-semibold text-gray-800">{{ $layout->layout_name }}</h5>
                                                <p class="text-sm text-gray-600">{{ $layout->canvas_width }} × {{ $layout->canvas_height }}</p>
                                                @if($layout->canvas_width > 800 || $layout->canvas_height > 600)
                                                    <p class="text-xs text-orange-600 mt-1">
                                                        Shown at {{ round(min(800/$layout->canvas_width, 600/$layout->canvas_height) * 100) }}% scale
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Corner Coordinates -->
                                        <div class="absolute top-1 left-1 text-xs text-gray-500 bg-white px-1 rounded">(0,0)</div>
                                        <div class="absolute top-1 right-1 text-xs text-gray-500 bg-white px-1 rounded">({{ $layout->canvas_width }},0)</div>
                                        <div class="absolute bottom-1 left-1 text-xs text-gray-500 bg-white px-1 rounded">(0,{{ $layout->canvas_height }})</div>
                                        <div class="absolute bottom-1 right-1 text-xs text-gray-500 bg-white px-1 rounded">({{ $layout->canvas_width }},{{ $layout->canvas_height }})</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scale Info -->
                            @if($layout->canvas_width > 800 || $layout->canvas_height > 600)
                                <div class="mt-3 text-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Canvas preview is scaled down to fit the screen. Actual canvas size is {{ $layout->canvas_width }} × {{ $layout->canvas_height }} pixels.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Floor Plan Image (if exists) -->
                    @if($layout->floor_plan_image)
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Floor Plan Image</h4>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <img src="{{ Storage::url($layout->floor_plan_image) }}" 
                                     alt="{{ $layout->layout_name }} floor plan"
                                     class="max-w-full max-h-96 object-contain mx-auto border rounded-lg">
                                <p class="mt-2 text-sm text-gray-600">
                                    Original floor plan image
                                </p>
                                <div class="mt-3 flex justify-center space-x-2">
                                    <a href="{{ Storage::url($layout->floor_plan_image) }}" 
                                       target="_blank"
                                       class="px-3 py-1 bg-blue-100 text-blue-800 hover:bg-blue-200 text-sm rounded">
                                        View Full Size
                                    </a>
                                    <a href="{{ Storage::url($layout->floor_plan_image) }}" 
                                       download="{{ $layout->layout_name }}_floor_plan"
                                       class="px-3 py-1 bg-green-100 text-green-800 hover:bg-green-200 text-sm rounded">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Usage Guidelines -->
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Usage Guidelines</h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <h5 class="font-medium text-gray-900 mb-2">Canvas Recommendations</h5>
                                    <ul class="space-y-1 text-gray-600">
                                        <li>• Standard screens: 800×600 or 1024×768</li>
                                        <li>• Large displays: 1200×800 or wider</li>
                                        <li>• Mobile devices: Consider responsive design</li>
                                        <li>• Keep aspect ratios in mind for display</li>
                                    </ul>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900 mb-2">Floor Plan Tips</h5>
                                    <ul class="space-y-1 text-gray-600">
                                        <li>• Use high-contrast images for clarity</li>
                                        <li>• PNG format recommended for sharp lines</li>
                                        <li>• Keep file size under 2MB for performance</li>
                                        <li>• Consider table placement zones</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Add interactive features to canvas preview
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('canvas-container');
            
            if (canvas) {
                // Add mouse coordinate tracking
                canvas.addEventListener('mousemove', function(e) {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = {{ $layout->canvas_width }} / canvas.offsetWidth;
                    const scaleY = {{ $layout->canvas_height }} / canvas.offsetHeight;
                    const x = Math.round((e.clientX - rect.left) * scaleX);
                    const y = Math.round((e.clientY - rect.top) * scaleY);
                    
                    // Update cursor coordinates (you could show this in a tooltip)
                    canvas.title = `Position: (${x}, ${y})`;
                });
            }
        });
    </script>
</x-app-layout>