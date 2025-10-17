@extends('layouts.admin')

@section('title', 'Create Set Meal')
@section('page-title', 'Create Set Meal')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
{{-- CDN for Tom Select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    /* Tom Select input container */
    .ts-control {
        max-height: 140px; /* Limit height */
        overflow-y: auto;  /* Add scrollbar if needed */
        padding: 8px;
    }
    /* Style for each selected item (tag) */
    .ts-control .item {
        background-color: #6366f1;
        color: white;
        border-radius: 4px;
        padding: 4px 8px;
        margin: 2px;
        font-size: 0.875rem;
    }
    .ts-control .item .remove-button {
        color: white;
        text-decoration: none;
        margin-left: 6px;
        opacity: 0.7;
    }
    .ts-control .item .remove-button:hover {
        color: white;
        background: none;
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New Set Meal</h2>
        <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Menu Items
        </a>
    </div>

    <form method="POST" action="{{ route('admin.menu-items.store-set-meal') }}" class="menu-item-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="is_set_meal" value="1">

        {{-- Basic Set Meal Details --}}
        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Set Meal Name *</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Lunch Set A" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="category_id" class="form-label">Category *</label>
                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Describe the set meal">{{ old('description') }}</textarea>
            @error('description')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="price" class="form-label">Set Meal Price (RM) *</label>
                <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" placeholder="15.00" required>
                @error('price')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="preparation_time" class="form-label">Total Preparation Time (minutes)</label>
                <input type="number" id="preparation_time" name="preparation_time" class="form-control @error('preparation_time') is-invalid @enderror" value="{{ old('preparation_time', 20) }}" min="1" placeholder="20">
                @error('preparation_time')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Improved Component Selector --}}
        <div class="form-group">
            <label for="select-components" class="form-label">Select Components *</label>
            <p class="form-hint">Type to search and select one or more food/drink items for this set meal.</p>
            <select
                name="components[]"
                id="select-components"
                class="@error('components') is-invalid @enderror"
                multiple
                required
                placeholder="Search for menu items...">
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}" {{ in_array($item->id, old('components', [])) ? 'selected' : '' }}>
                        {{ $item->name }} ({{ $item->category->name ?? 'Uncategorized' }}) - RM{{ number_format($item->price, 2) }}
                    </option>
                @endforeach
            </select>
            @error('components')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Set Meal Image</label>
            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            @error('image')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="availability" name="availability" value="1" checked>
                <label for="availability" class="checkbox-label">
                    <i class="fas fa-check-circle"></i> Available for Order
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Create Set Meal
            </button>
            <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
{{-- CDN for Tom Select --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#select-components',{
            plugins: ['remove_button'],
            create: false,
            maxItems: 20, // Limit max components
            render: {
                item: function(data, escape) {
                    return `<div class="item">${escape(data.text)}</div>`;
                },
                option: function(data, escape) {
                    return `<div class="option">${escape(data.text)}</div>`;
                }
            }
        });
    });
</script>
@endsection