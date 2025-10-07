@extends('layouts.admin')

@section('title', 'Categories Management')
@section('page-title', 'Categories Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Categories Management</h2>
        <div class="header-actions">
            <a href="{{ route('admin.categories.create') }}" class="admin-btn btn-primary">
                <i class="fas fa-plus"></i> Create New Category
            </a>
            <button onclick="toggleSortable()" class="admin-btn btn-secondary" id="sortButton">
                <i class="fas fa-sort"></i> Toggle Sort Mode
            </button>
            {{-- <a href="{{ route('admin.categories.hierarchical') }}" class="admin-btn btn-info">
                <i class="fas fa-sitemap"></i> View Hierarchy
            </a> --}}
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

    <!-- Categories List -->
    <div class="admin-content">
        <div id="categories-container" class="categories-list">
            @forelse($categories as $category)
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
                                <span class="badge badge-{{ $category->type === 'food' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($category->type) }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-sort-numeric-down"></i>
                                    Sort: {{ $category->sort_order }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-layer-group"></i>
                                    Sub Categories: {{ $category->subCategories->count() }}
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
                        @if($category->subCategories->count() == 0 && $category->menuItems->count() == 0)
                            <button type="button" class="btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" title="Delete Category">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Sub Categories -->
                @if($category->subCategories->count() > 0)
                <div class="subcategories-section">
                    <h4 class="subcategories-title">
                        <i class="fas fa-layer-group"></i>
                        Sub Categories ({{ $category->subCategories->count() }})
                    </h4>
                    <div class="subcategories-grid">
                        @foreach($category->subCategories as $subCategory)
                        <div class="subcategory-item" data-id="{{ $subCategory->id }}">
                            <div class="subcategory-content">
                                <h5 class="subcategory-name">{{ $subCategory->name }}</h5>
                                <div class="subcategory-meta">
                                    <span class="badge badge-{{ $subCategory->type === 'food' ? 'success' : 'warning' }}">
                                        {{ ucfirst($subCategory->type) }}
                                    </span>
                                    <span class="meta-small">Order: {{ $subCategory->sort_order }}</span>
                                    <span class="meta-small">Items: {{ $subCategory->menuItems->count() }}</span>
                                </div>
                            </div>
                            <div class="subcategory-actions">
                                <a href="{{ route('admin.categories.show', $subCategory->id) }}" class="btn-view-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $subCategory->id) }}" class="btn-edit-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($subCategory->menuItems->count() == 0)
                                    <button type="button" class="btn-delete-sm" onclick="confirmDelete({{ $subCategory->id }}, '{{ $subCategory->name }}')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="empty-title">No categories found</h3>
                <p class="empty-description">Get started by creating your first category.</p>
                <a href="{{ route('admin.categories.create') }}" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Category
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Deletion</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the category "<span id="categoryName"></span>"?</p>
            <p class="warning-text">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete</button>
            </form>
            <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
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
});

function toggleSortable() {
    if (sortMode) {
        disableSortMode();
    } else {
        enableSortMode();
    }
}

function enableSortMode() {
    sortMode = true;
    document.querySelectorAll('.sort-handle').forEach(handle => {
        handle.style.display = 'flex';
    });
    
    const container = document.getElementById('categories-container');
    sortableInstance = Sortable.create(container, {
        handle: '.sort-handle',
        animation: 150,
        onEnd: function(evt) {
            updateSortOrder();
        }
    });
    
    // Change button text and icon
    const sortButton = document.getElementById('sortButton');
    sortButton.innerHTML = '<i class="fas fa-save"></i> Save Sort Order';
    sortButton.classList.remove('btn-secondary');
    sortButton.classList.add('btn-success');
}

function disableSortMode() {
    sortMode = false;
    document.querySelectorAll('.sort-handle').forEach(handle => {
        handle.style.display = 'none';
    });
    
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
    
    // Change button text and icon back
    const sortButton = document.getElementById('sortButton');
    sortButton.innerHTML = '<i class="fas fa-sort"></i> Toggle Sort Mode';
    sortButton.classList.remove('btn-success');
    sortButton.classList.add('btn-secondary');
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
function confirmDelete(categoryId, categoryName) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = '{{ route('admin.categories.destroy', '') }}/' + categoryId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        modal.style.display = 'none';
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