@extends('layouts.admin')

@section('title', $menuItem->id ? 'Edit Menu Item' : 'Create Menu Item')
@section('page-title', $menuItem->id ? 'Edit Menu Item' : 'Create Menu Item')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $menuItem->id ? 'Edit Menu Item' : 'Create New Menu Item' }}</h2>
        <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Menu Items
        </a>
    </div>

    <form method="POST" action="{{ $menuItem->id ? route('admin.menu-items.update', $menuItem->id) : route('admin.menu-items.store') }}" class="menu-item-form" enctype="multipart/form-data">
        @csrf
        @if($menuItem->id)
        @method('PUT')
        @endif

        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Menu Item Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $menuItem->name) }}"
                    placeholder="Enter menu item name"
                    required>
                @error('name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Category *</label>
                <select
                    id="category_id"
                    name="category_id"
                    class="form-control @error('category_id') is-invalid @enderror"
                    required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    @php
                    // Determine icon based on category name
                    $icon = '🍽️'; // Default food icon
                    $lowerName = strtolower($category->name);

                    // Drink keywords
                    if (str_contains($lowerName, 'drink') ||
                        str_contains($lowerName, 'beverage') ||
                        str_contains($lowerName, 'juice') ||
                        str_contains($lowerName, 'coffee') ||
                        str_contains($lowerName, 'tea') ||
                        str_contains($lowerName, 'soda') ||
                        str_contains($lowerName, 'water') ||
                        str_contains($lowerName, 'cocktail') ||
                        str_contains($lowerName, 'beer') ||
                        str_contains($lowerName, 'wine') ||
                        str_contains($lowerName, 'alcohol') ||
                        str_contains($lowerName, 'minuman')) {
                        $icon = '🍹';
                    }
                    // Specific drink types
                    elseif (str_contains($lowerName, 'smoothie') ||
                        str_contains($lowerName, 'shake') ||
                        str_contains($lowerName, 'milkshake')) {
                        $icon = '🥤';
                    }
                    // Hot drinks
                    elseif (str_contains($lowerName, 'hot') &&
                        (str_contains($lowerName, 'drink') || str_contains($lowerName, 'tea') || str_contains($lowerName, 'coffee'))) {
                        $icon = '☕';
                    }
                    @endphp
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $selectedCategoryId) == $category->id ? 'selected' : '' }}>
                        {{ $icon }} {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="3"
                placeholder="Enter menu item description">{{ old('description', $menuItem->description) }}</textarea>
            @error('description')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="price" class="form-label">Price (RM) *</label>
                <input
                    type="number"
                    id="price"
                    name="price"
                    class="form-control @error('price') is-invalid @enderror"
                    value="{{ old('price', $menuItem->price) }}"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    required>
                @error('price')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="preparation_time" class="form-label">Preparation Time (minutes)</label>
                <input
                    type="number"
                    id="preparation_time"
                    name="preparation_time"
                    class="form-control @error('preparation_time') is-invalid @enderror"
                    value="{{ old('preparation_time', $menuItem->preparation_time ?? 15) }}"
                    min="1"
                    max="180"
                    placeholder="15">
                @error('preparation_time')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Kitchen Station Override (Optional) --}}
        <div class="form-row">
            <div class="form-group">
                <label for="station_type" class="form-label">Kitchen Station Override (Optional)</label>
                <select id="station_type" name="station_type" class="form-control @error('station_type') is-invalid @enderror">
                    <option value="">-- Inherit from Category --</option>
                    <option value="hot_kitchen" {{ old('station_type', $menuItem->station_type ?? '') == 'hot_kitchen' ? 'selected' : '' }}>
                        🔥 Hot Cooking
                    </option>
                    <option value="cold_kitchen" {{ old('station_type', $menuItem->station_type ?? '') == 'cold_kitchen' ? 'selected' : '' }}>
                        🥗 Cold Prep & Salads
                    </option>
                    <option value="drinks" {{ old('station_type', $menuItem->station_type ?? '') == 'drinks' ? 'selected' : '' }}>
                        🍹 Beverages & Drinks
                    </option>
                    <option value="desserts" {{ old('station_type', $menuItem->station_type ?? '') == 'desserts' ? 'selected' : '' }}>
                        🍰 Desserts
                    </option>
                </select>
                <small class="form-help">Leave empty to use category default. Override only for special items.</small>
                @error('station_type')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="kitchen_load_factor" class="form-label">Kitchen Load Factor (Optional)</label>
                <select id="kitchen_load_factor" name="kitchen_load_factor" class="form-control @error('kitchen_load_factor') is-invalid @enderror">
                    <option value="">-- Inherit from Category --</option>
                    <option value="0.3" {{ old('kitchen_load_factor', $menuItem->kitchen_load_factor ?? '') == '0.3' ? 'selected' : '' }}>
                        0.3 - Very Fast (drinks, pour & serve)
                    </option>
                    <option value="0.5" {{ old('kitchen_load_factor', $menuItem->kitchen_load_factor ?? '') == '0.5' ? 'selected' : '' }}>
                        0.5 - Simple (toast, blend)
                    </option>
                    <option value="1.0" {{ old('kitchen_load_factor', $menuItem->kitchen_load_factor ?? '') == '1.0' ? 'selected' : '' }}>
                        1.0 - Normal (stir-fry, standard cook)
                    </option>
                    <option value="1.5" {{ old('kitchen_load_factor', $menuItem->kitchen_load_factor ?? '') == '1.5' ? 'selected' : '' }}>
                        1.5 - Complex (grilling, multi-step)
                    </option>
                    <option value="2.0" {{ old('kitchen_load_factor', $menuItem->kitchen_load_factor ?? '') == '2.0' ? 'selected' : '' }}>
                        2.0 - Very Complex (multiple components)
                    </option>
                </select>
                <small class="form-help">Override complexity for this specific item if needed.</small>
                @error('kitchen_load_factor')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Image Photo</label>
            <input
                type="file"
                id="image"
                name="image"
                class="form-control @error('image') is-invalid @enderror"
                accept="image/*"
                onchange="previewImage(event)">
            <div class="image-preview-container mt-2">
                @if($menuItem->image)
                <div class="current-image">
                    <p class="text-sm text-gray-600">Current image:</p>
                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="Current image" class="img-thumbnail" id="currentImage" style="max-width: 200px; max-height: 200px;">
                </div>
                @endif
                <div class="preview-image mt-2" style="display: none;">
                    <p class="text-sm text-gray-600">New image preview:</p>
                    <img id="imagePreview" src="#" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                </div>
            </div>
            @error('image')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Allergens section hidden per requirements --}}
        <div class="form-group" style="display: none;">
            <label class="form-label">Allergens</label>
            <div class="allergens-container">
                @php
                $availableAllergens = ['Gluten', 'Dairy', 'Nuts', 'Eggs', 'Soy', 'Fish', 'Shellfish', 'Sesame'];
                $selectedAllergens = old('allergens', $menuItem->allergens ?? []);
                @endphp
                @foreach($availableAllergens as $allergen)
                <div class="allergen-checkbox">
                    <input
                        type="checkbox"
                        id="allergen_{{ strtolower($allergen) }}"
                        name="allergens[]"
                        value="{{ $allergen }}"
                        {{ in_array($allergen, $selectedAllergens) ? 'checked' : '' }}>
                    <label for="allergen_{{ strtolower($allergen) }}">{{ $allergen }}</label>
                </div>
                @endforeach
            </div>
            @error('allergens')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <div class="checkbox-group">
                    <input
                        type="checkbox"
                        id="availability"
                        name="availability"
                        value="1"
                        {{ old('availability', $menuItem->availability ?? true) ? 'checked' : '' }}>
                    <label for="availability" class="checkbox-label">
                        <i class="fas fa-check-circle"></i>
                        Available for Order
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input
                        type="checkbox"
                        id="is_featured"
                        name="is_featured"
                        value="1"
                        {{ old('is_featured', $menuItem->is_featured ?? false) ? 'checked' : '' }}>
                    <label for="is_featured" class="checkbox-label">
                        <i class="fas fa-star"></i>
                        Featured Item
                    </label>
                </div>
            </div>
        </div>

        @if(false && $menuItem->id)
        <div class="form-row" style="display: none;">
            <div class="form-group">
                <label class="form-label">Current Rating</label>
                <div class="rating-display">
                    @if($menuItem->rating_count > 0)
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $menuItem->rating_average ? 'active' : '' }}"></i>
                            @endfor
                    </div>
                    <span class="rating-text">
                        {{ number_format($menuItem->rating_average, 1) }}
                        ({{ $menuItem->rating_count }} {{ $menuItem->rating_count == 1 ? 'review' : 'reviews' }})
                    </span>
                    @else
                    <span class="no-rating">No ratings yet</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                {{ $menuItem->id ? 'Update Menu Item' : 'Create Menu Item' }}
            </button>
            <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            document.querySelector('.preview-image').style.display = 'block';

            var currentImage = document.getElementById('currentImage');
            if (currentImage) {
                document.querySelector('.current-image').style.display = 'none';
            }
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection