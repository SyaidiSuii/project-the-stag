<!-- form.blade.php for Table Layout Config -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($layout->id)
                {{ __('Edit Layout Configuration') }} - {{ $layout->layout_name }}
            @else
                {{ __('Create New Layout Configuration') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($layout->id)
                            {{ __('Edit Layout Configuration') }}
                        @else
                            {{ __('Layout Configuration Details') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Configure your table layout settings and upload floor plan image.") }}
                    </p>
                </header>

                @if($layout->id)
                    <form method="post" action="{{ route('admin.table-layout-config.update', $layout->id) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('admin.table-layout-config.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <!-- Basic Layout Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="layout_name" :value="__('Layout Name')" />
                            <x-text-input id="layout_name" name="layout_name" type="text" class="mt-1 block w-full" 
                                :value="old('layout_name', $layout->layout_name)" 
                                placeholder="e.g. Main Dining Area, Outdoor Terrace" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('layout_name')" />
                        </div>

                        <div>
                            <x-input-label for="is_active" :value="__('Status')" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="1" @if(old('is_active', $layout->is_active ?? 1) == 1) selected @endif>Active</option>
                                <option value="0" @if(old('is_active', $layout->is_active ?? 1) == 0) selected @endif>Inactive</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>
                    </div>

                    <!-- Canvas Dimensions -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Canvas Dimensions</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="canvas_width" :value="__('Canvas Width (px)')" />
                                <x-text-input id="canvas_width" name="canvas_width" type="number" min="400" max="2000" class="mt-1 block w-full" 
                                    :value="old('canvas_width', $layout->canvas_width ?? 800)" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('canvas_width')" />
                                <p class="mt-1 text-sm text-gray-500">Recommended: 800-1200px</p>
                            </div>

                            <div>
                                <x-input-label for="canvas_height" :value="__('Canvas Height (px)')" />
                                <x-text-input id="canvas_height" name="canvas_height" type="number" min="300" max="1500" class="mt-1 block w-full" 
                                    :value="old('canvas_height', $layout->canvas_height ?? 600)" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('canvas_height')" />
                                <p class="mt-1 text-sm text-gray-500">Recommended: 600-800px</p>
                            </div>
                        </div>

                        <!-- Preset Dimensions -->
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Quick Presets:</p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="setDimensions(800, 600)" 
                                        class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                    800x600 (Standard)
                                </button>
                                <button type="button" onclick="setDimensions(1024, 768)" 
                                        class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                    1024x768 (Large)
                                </button>
                                <button type="button" onclick="setDimensions(1200, 800)" 
                                        class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                    1200x800 (Extra Large)
                                </button>
                                <button type="button" onclick="setDimensions(600, 800)" 
                                        class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                    600x800 (Portrait)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Floor Plan Image -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Floor Plan Image</h3>
                        
                        @if($layout->floor_plan_image)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Current Image:</p>
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <img src="{{ Storage::url($layout->floor_plan_image) }}" 
                                     alt="Current floor plan" 
                                     class="max-w-md max-h-64 object-contain mx-auto">
                                <p class="text-xs text-gray-500 text-center mt-2">{{ $layout->floor_plan_image }}</p>
                            </div>
                        </div>
                        @endif

                        <div>
                            <x-input-label for="floor_plan_image" :value="__('Upload New Floor Plan Image (Optional)')" />
                            <input type="file" id="floor_plan_image" name="floor_plan_image" 
                                   accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-50 file:text-indigo-700
                                          hover:file:bg-indigo-100"/>
                            <x-input-error class="mt-2" :messages="$errors->get('floor_plan_image')" />
                            <p class="mt-1 text-sm text-gray-500">
                                Supported formats: JPG, PNG, GIF. Max size: 2MB. 
                                @if($layout->floor_plan_image)
                                    Leave empty to keep current image.
                                @endif
                            </p>
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-4 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <img id="preview-img" src="" alt="Preview" class="max-w-md max-h-64 object-contain mx-auto">
                            </div>
                        </div>
                    </div>

                    <!-- Canvas Preview -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Canvas Preview</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">This shows how your canvas will appear:</p>
                            <div id="canvas-preview" class="border-2 border-dashed border-gray-300 bg-white mx-auto flex items-center justify-center" 
                                 style="width: 400px; height: 300px;">
                                <span class="text-gray-500 text-sm">Canvas Preview</span>
                            </div>
                            <p id="canvas-info" class="text-xs text-gray-500 text-center mt-2">
                                Actual size: 800 x 600 pixels (shown at 50% scale)
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Layout Configuration') }}</x-primary-button>

                        <a href="{{ route('admin.table-layout-config.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        // Set canvas dimensions from presets
        function setDimensions(width, height) {
            document.getElementById('canvas_width').value = width;
            document.getElementById('canvas_height').value = height;
            updateCanvasPreview();
        }

        // Update canvas preview when dimensions change
        function updateCanvasPreview() {
            const width = parseInt(document.getElementById('canvas_width').value) || 800;
            const height = parseInt(document.getElementById('canvas_height').value) || 600;
            
            const preview = document.getElementById('canvas-preview');
            const info = document.getElementById('canvas-info');
            
            // Scale down for preview (max 400px width)
            const scale = Math.min(400 / width, 300 / height, 1);
            const previewWidth = width * scale;
            const previewHeight = height * scale;
            
            preview.style.width = previewWidth + 'px';
            preview.style.height = previewHeight + 'px';
            
            const scalePercent = Math.round(scale * 100);
            info.textContent = `Actual size: ${width} x ${height} pixels (shown at ${scalePercent}% scale)`;
        }

        // Image file preview
        document.getElementById('floor_plan_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });

        // Initialize canvas preview
        document.addEventListener('DOMContentLoaded', function() {
            updateCanvasPreview();
            
            // Update preview when dimensions change
            document.getElementById('canvas_width').addEventListener('input', updateCanvasPreview);
            document.getElementById('canvas_height').addEventListener('input', updateCanvasPreview);
        });

        // Validate dimensions
        function validateDimensions() {
            const width = parseInt(document.getElementById('canvas_width').value);
            const height = parseInt(document.getElementById('canvas_height').value);
            
            if (width < 400 || width > 2000) {
                alert('Canvas width must be between 400 and 2000 pixels');
                return false;
            }
            
            if (height < 300 || height > 1500) {
                alert('Canvas height must be between 300 and 1500 pixels');
                return false;
            }
            
            return true;
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateDimensions()) {
                e.preventDefault();
            }
        });
    </script>
</x-app-layout>