@extends('layouts.admin')

@section('title', 'Categories Management')
@section('page-title', 'Categories Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')

<!-- Tabs -->
<div class="admin-tabs" style="display: flex; gap: 8px; background: white; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
    <div class="admin-tab active" data-tab="food-categories" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--brand, #6366f1); color: white;">Food Categories</div>
    <div class="admin-tab" data-tab="drink-categories" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);">Drink Categories</div>
    <div class="admin-tab" data-tab="set-meal-categories" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);">Set Meal Categories</div>
</div>

<!-- Food Categories Section -->
<div class="admin-section category-section" id="food-categories-section">
    <div class="section-header">
        <h2 class="section-title">Food Categories Management</h2>
        <div class="header-actions">
            <a href="{{ route('admin.categories.create') }}?type=food" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create Food Category
            </a>
            <button onclick="toggleSortable('food')" class="admin-btn btn-secondary" id="sortButtonFood">
                <i class="fas fa-sort"></i> Toggle Sort Mode
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Food Categories List -->
    <div class="admin-content">
        <div id="food-categories-container" class="categories-list">
            @forelse($categories->where('type', 'food')->where('parent_id', '!=', null) as $category)
            <div class="category-item" data-id="{{ $category->id }}">
                <!-- Subcategory -->
                <div class="category-header">
                    <div class="category-info">
                        <div class="sort-handle" style="display: none;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="category-details">
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <div class="category-meta">
                                <span class="badge badge-primary">
                                    Food Category
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-sort-numeric-down"></i>
                                    Sort: {{ $category->sort_order }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-utensils"></i>
                                    Menu Items: {{ $category->menuItems->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn-view" title="View Category">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit" title="Edit Category">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($category->menuItems->count() == 0)
                            <button type="button" class="btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" title="Delete Category">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="empty-title">No food categories found</h3>
                <p class="empty-description">Get started by creating your first food category.</p>
                <a href="{{ route('admin.categories.create') }}?type=food" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Category
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Drink Categories Section -->
<div class="admin-section category-section" id="drink-categories-section" style="display: none;">
    <div class="section-header">
        <h2 class="section-title">Drink Categories Management</h2>
        <div class="header-actions">
            <a href="{{ route('admin.categories.create') }}?type=drink" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create Drink Category
            </a>
            <button onclick="toggleSortable('drink')" class="admin-btn btn-secondary" id="sortButtonDrink">
                <i class="fas fa-sort"></i> Toggle Sort Mode
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Drink Categories List -->
    <div class="admin-content">
        <div id="drink-categories-container" class="categories-list">
            @forelse($categories->where('type', 'drink')->where('parent_id', '!=', null) as $category)
            <div class="category-item" data-id="{{ $category->id }}">
                <!-- Main Category -->
                <div class="category-header">
                    <div class="category-info">
                        <div class="sort-handle" style="display: none;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="category-details">
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <div class="category-meta">
                                <span class="badge badge-secondary">
                                    Drink Category
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-sort-numeric-down"></i>
                                    Sort: {{ $category->sort_order }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-cocktail"></i>
                                    Menu Items: {{ $category->menuItems->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn-view" title="View Category">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit" title="Edit Category">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($category->menuItems->count() == 0)
                            <button type="button" class="btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" title="Delete Category">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-cocktail"></i>
                </div>
                <h3 class="empty-title">No drink categories found</h3>
                <p class="empty-description">Get started by creating your first drink category.</p>
                <a href="{{ route('admin.categories.create') }}?type=drink" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create Drink Category
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Set Meal Categories Section -->
<div class="admin-section category-section" id="set-meal-categories-section" style="display: none;">
    <div class="section-header">
        <h2 class="section-title">Set Meal Categories Management</h2>
        <div class="header-actions">
            <a href="{{ route('admin.categories.create') }}?type=set-meal" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create Set Meal Category
            </a>
            <button onclick="toggleSortable('set-meal')" class="admin-btn btn-secondary" id="sortButtonSetMeal">
                <i class="fas fa-sort"></i> Toggle Sort Mode
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Set Meal Categories List -->
    <div class="admin-content">
        <div id="set-meal-categories-container" class="categories-list">
            @forelse($categories->where('type', 'set-meal')->where('parent_id', '!=', null) as $category)
            <div class="category-item" data-id="{{ $category->id }}">
                <!-- Main Category -->
                <div class="category-header">
                    <div class="category-info">
                        <div class="sort-handle" style="display: none;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="category-details">
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <div class="category-meta">
                                <span class="badge badge-primary">
                                    Set Meal Category
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-sort-numeric-down"></i>
                                    Sort: {{ $category->sort_order }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-utensils"></i>
                                    Menu Items: {{ $category->menuItems->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn-view" title="View Category">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit" title="Edit Category">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($category->menuItems->count() == 0)
                            <button type="button" class="btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" title="Delete Category">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-pizza-slice"></i>
                </div>
                <h3 class="empty-title">No set meal categories found</h3>
                <p class="empty-description">Get started by creating your first set meal category.</p>
                <a href="{{ route('admin.categories.create') }}?type=set-meal" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create Set Meal Category
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
let sortableInstance = null;
let sortMode = false;

document.addEventListener('DOMContentLoaded', function() {
    // Show success/error messages
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    // Tab switching functionality
    const tabs = document.querySelectorAll('.admin-tab');
    const sections = document.querySelectorAll('.category-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs
            tabs.forEach(t => {
                t.style.background = 'var(--muted, #e2e8f0)';
                t.style.color = 'var(--text-2, #64748b)';
                t.classList.remove('active');
            });

            // Add active class to clicked tab
            this.style.background = 'var(--brand, #6366f1)';
            this.style.color = 'white';
            this.classList.add('active');

            // Hide all sections
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show target section
            const targetSection = document.getElementById(targetTab + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        });
    });
});

function toggleSortable(type) {
    if (sortMode) {
        disableSortMode(type);
    } else {
        enableSortMode(type);
    }
}

function enableSortMode(type) {
    sortMode = true;
    document.querySelectorAll('.sort-handle').forEach(handle => {
        handle.style.display = 'flex';
    });

    const containerMap = {
        'food': 'food-categories-container',
        'drink': 'drink-categories-container',
        'set-meal': 'set-meal-categories-container'
    };

    const container = document.getElementById(containerMap[type] || 'food-categories-container');
    sortableInstance = Sortable.create(container, {
        handle: '.sort-handle',
        animation: 150,
        onEnd: function(evt) {
            updateSortOrder();
        }
    });

    // Change button text and icon
    const buttonMap = {
        'food': 'sortButtonFood',
        'drink': 'sortButtonDrink',
        'set-meal': 'sortButtonSetMeal'
    };

    const sortButton = document.getElementById(buttonMap[type] || 'sortButtonFood');
    sortButton.innerHTML = '<i class="fas fa-save"></i> Save Sort Order';
    sortButton.classList.remove('btn-secondary');
    sortButton.classList.add('btn-success');
}

function disableSortMode(type) {
    sortMode = false;
    document.querySelectorAll('.sort-handle').forEach(handle => {
        handle.style.display = 'none';
    });

    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }

    // Change button text and icon back
    const buttonMap = {
        'food': 'sortButtonFood',
        'drink': 'sortButtonDrink',
        'set-meal': 'sortButtonSetMeal'
    };

    const sortButton = document.getElementById(buttonMap[type] || 'sortButtonFood');
    if (sortButton) {
        sortButton.innerHTML = '<i class="fas fa-sort"></i> Toggle Sort Mode';
        sortButton.classList.remove('btn-success');
        sortButton.classList.add('btn-secondary');
    }
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
            showNotification('Sort order updated successfully!', 'success');
        } else {
            showNotification('Error updating sort order: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating sort order', 'error');
    });
}

// Delete confirmation
async function confirmDelete(categoryId, categoryName) {
    // Show modern confirmation modal
    const confirmed = await showConfirm(
        'Delete Category?',
        `Are you sure you want to delete "${categoryName}"? This action cannot be undone.`,
        'danger',
        'Delete',
        'Cancel'
    );

    if (confirmed) {
        // Submit the delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.categories.destroy', '') }}/' + categoryId;

        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 8px;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection