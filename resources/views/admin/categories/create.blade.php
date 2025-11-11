@extends('layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New {{ ucfirst($type) }} Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    @if($parentCategory)
    <div class="info-box" style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <p style="margin: 0; color: #1e40af;">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> You are creating a subcategory under <strong>{{ $parentCategory->name }}</strong>.
            This category will be used to organize menu items within the {{ $type }} section.
        </p>
    </div>
    @else
    <div class="info-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <p style="margin: 0; color: #856404;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Warning:</strong> No parent category found for {{ $type }}. Please create a parent category first.
        </p>
    </div>
    @endif

    <form method="post" action="{{ route('admin.categories.store') }}" class="category-form">
        @csrf

        <!-- Hidden fields for type and parent_id -->
        <input type="hidden" name="type" value="{{ $type }}">
        @if($parentCategory)
        <input type="hidden" name="parent_id" value="{{ $parentCategory->id }}">
        @endif

        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Asian Cuisine, Smoothies, Family Meals" required autofocus>
                @if($errors->get('name'))
                <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <small class="form-help">Enter a descriptive name for this category (e.g., "Asian Cuisine" for food, "Smoothies" for drinks)</small>
            </div>

            <div class="form-group">
                <label>Default Kitchen Station</label>
                <select name="default_station_id" class="form-control">
                    <option value="">-- Inherit from parent --</option>
                    @foreach(\App\Models\KitchenStation::where('is_active', true)->orderBy('name')->get() as $station)
                        <option value="{{ $station->id }}" {{ old('default_station_id') == $station->id ? 'selected' : '' }}>
                            {!! $station->icon ?? '🍴' !!}
                            {{ $station->name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-help">Select which kitchen station will handle items in this category</small>
            </div>

            <div class="form-group">
                <label>Kitchen Load Factor</label>
                <select name="default_load_factor" class="form-control">
                    <option value="0.3" {{ old('default_load_factor', $category->default_load_factor ?? '') == '0.3' ? 'selected' : '' }}>
                        0.3 - Very Fast (drinks, pour & serve)
                    </option>
                    <option value="0.5" {{ old('default_load_factor', $category->default_load_factor ?? '') == '0.5' ? 'selected' : '' }}>
                        0.5 - Simple (toast, blend)
                    </option>
                    <option value="1.0" {{ old('default_load_factor', $category->default_load_factor ?? '') == '1.0' ? 'selected' : '' }}>
                        1.0 - Normal (stir-fry, standard cook)
                    </option>
                    <option value="1.5" {{ old('default_load_factor', $category->default_load_factor ?? '') == '1.5' ? 'selected' : '' }}>
                        1.5 - Complex (grilling, multi-step)
                    </option>
                    <option value="2.0" {{ old('default_load_factor', $category->default_load_factor ?? '') == '2.0' ? 'selected' : '' }}>
                        2.0 - Very Complex (multiple components)
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">Sort Order (Optional)</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0" value="{{ old('sort_order') }}" placeholder="Leave empty for auto sort">
                @if($errors->get('sort_order'))
                <div class="form-error">{{ implode(', ', $errors->get('sort_order')) }}</div>
                @endif
                <small class="form-help">Controls the display order. Lower numbers appear first. Leave empty to add at the end.</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-plus"></i> Create Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection