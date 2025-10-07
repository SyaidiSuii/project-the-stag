@extends('layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <form method="post" action="{{ route('admin.categories.store') }}" class="category-form">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @if($errors->get('name'))
                    <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="type" class="form-label">Category Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="main" {{ old('type', 'main') == 'main' ? 'selected' : '' }}>Main Category</option>
                    <option value="sub" {{ old('type') == 'sub' ? 'selected' : '' }}>Sub Category</option>
                </select>
                @if($errors->get('type'))
                    <div class="form-error">{{ implode(', ', $errors->get('type')) }}</div>
                @endif
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" id="parent-category-group" style="{{ old('type', 'main') == 'main' ? 'display: none;' : '' }}">
                <label for="parent_id" class="form-label">Parent Category</label>
                <select id="parent_id" name="parent_id" class="form-control">
                    <option value="">Select Parent Category</option>
                    @foreach($mainCategories as $mainCategory)
                        <option value="{{ $mainCategory->id }}" {{ old('parent_id') == $mainCategory->id ? 'selected' : '' }}>
                            {{ $mainCategory->name }}
                        </option>
                    @endforeach
                </select>
                @if($errors->get('parent_id'))
                    <div class="form-error">{{ implode(', ', $errors->get('parent_id')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0" value="{{ old('sort_order') }}" placeholder="Leave empty for auto sort">
                @if($errors->get('sort_order'))
                    <div class="form-error">{{ implode(', ', $errors->get('sort_order')) }}</div>
                @endif
            </div>
        </div>


        <div class="form-actions">
            <button type="submit" class="btn-save">
                Create Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const parentCategoryGroup = document.getElementById('parent-category-group');
    const parentIdSelect = document.getElementById('parent_id');

    function toggleParentCategory() {
        if (typeSelect.value === 'sub') {
            parentCategoryGroup.style.display = 'block';
            parentIdSelect.required = true;
        } else {
            parentCategoryGroup.style.display = 'none';
            parentIdSelect.required = false;
            parentIdSelect.value = '';
        }
    }

    typeSelect.addEventListener('change', toggleParentCategory);
    toggleParentCategory();
});
</script>
@endsection