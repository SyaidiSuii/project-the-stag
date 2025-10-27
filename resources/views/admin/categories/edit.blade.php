@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Edit Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    @php
        $parentCategory = $category->parent;
        $typeLabel = ucfirst($category->type);
    @endphp

    <div class="info-box" style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <p style="margin: 0; color: #1e40af;">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> This is a subcategory under <strong>{{ $parentCategory->name }}</strong> ({{ $typeLabel }}).
            You can only edit the name and sort order.
        </p>
    </div>

    <form method="post" action="{{ route('admin.categories.update', $category->id) }}" class="category-form">
        <input type="hidden" name="_method" value="PUT">
        @csrf

        <!-- Hidden fields to maintain type and parent -->
        <input type="hidden" name="type" value="{{ $category->type }}">
        <input type="hidden" name="parent_id" value="{{ $category->parent_id }}">

        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $category->name) }}" placeholder="e.g., Asian Cuisine, Smoothies, Family Meals" required autofocus>
                @if($errors->get('name'))
                <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
                <small class="form-help">Enter a descriptive name for this category</small>
            </div>

            <div class="form-group">
                <label>Default Kitchen Station</label>
                <select name="default_station_type" class="form-control">
                    <option value="">-- Inherit from parent --</option>
                    <option value="hot_kitchen" {{ old('default_station_type', $category->default_station_type ?? '') == 'hot_kitchen' ? 'selected' : '' }}>
                        🔥 Hot Cooking
                    </option>
                    <option value="cold_kitchen" {{ old('default_station_type', $category->default_station_type ?? '') == 'cold_kitchen' ? 'selected' : '' }}>
                        🥗 Cold Prep & Salads
                    </option>
                    <option value="drinks" {{ old('default_station_type', $category->default_station_type ?? '') == 'drinks' ? 'selected' : '' }}>
                        🍹 Beverages & Drinks
                    </option>
                    <option value="desserts" {{ old('default_station_type', $category->default_station_type ?? '') == 'desserts' ? 'selected' : '' }}>
                        🍰 Desserts
                    </option>
                </select>
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
                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $category->sort_order) }}" placeholder="Leave empty for auto sort">
                @if($errors->get('sort_order'))
                <div class="form-error">{{ implode(', ', $errors->get('sort_order')) }}</div>
                @endif
                <small class="form-help">Controls the display order. Lower numbers appear first.</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection