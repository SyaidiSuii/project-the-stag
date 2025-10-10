@extends('layouts.admin')

@section('title', 'Menu Management')
@section('page-title', 'Menu Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Items</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-utensils"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalItems ?? 0 }}</div>
        <div class="admin-card-desc">All menu items</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Available Items</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $availableItems ?? 0 }}</div>
        <div class="admin-card-desc">Ready to order</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Out of Stock</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-exclamation-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $unavailableItems ?? 0 }}</div>
        <div class="admin-card-desc">Needs attention</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Categories</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-tags"></i></div>
        </div>
        <div class="admin-card-value">{{ $categoriesCount ?? 0 }}</div>
        <div class="admin-card-desc">Menu categories</div>
    </div>
</div>

<!-- Tabs -->
<div class="admin-tabs" style="display: flex; gap: 8px; background: white; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
    <div class="admin-tab active" data-tab="food" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--brand, #6366f1); color: white;">Food Menu</div>
    <div class="admin-tab" data-tab="drinks" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);">Drinks Menu</div>
    <div class="admin-tab" data-tab="set-meals" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);">Set Meals</div>
</div>

<!-- Food Menu Section -->
<div class="admin-section menu-section" id="food-section">
    <div class="section-header">
        <h2 class="section-title">Food Menu Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search menu items..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="mainCategoryFilter">
                <option value="">All Categories</option>
                @foreach($categories->where('type', 'food') as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select class="filter-select" id="availabilityFilter">
                <option value="">All Items</option>
                <option value="1" {{ request('availability') === '1' ? 'selected' : '' }}>Available Only</option>
                <option value="0" {{ request('availability') === '0' ? 'selected' : '' }}>Unavailable Only</option>
            </select>
            <select class="filter-select" id="featuredFilter">
                <option value="">All Items</option>
                <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Not Featured</option>
            </select>
            <select class="filter-select" id="sortFilter">
                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Sort by Price</option>
                <option value="category" {{ request('sort_by') === 'category' ? 'selected' : '' }}>Sort by Category</option>
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="rating_average" {{ request('sort_by') === 'rating_average' ? 'selected' : '' }}>Sort by Rating</option>
            </select>
        </div>
        <a href="{{ route('admin.menu-items.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add Menu Item
        </a>
    </div>

    <!-- Menu Items Table -->
    @if($menuItems->count() > 0)
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="th-item">Item Details</th>
                        <th class="th-category">Category</th>
                        <th class="th-price">Price</th>
                        <th class="th-rating">Rating</th>
                        <th class="th-status">Status</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuItems as $item)
                    <tr>
                        <td>
                            <div class="item-info">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
                                @else
                                    <div class="item-image-placeholder">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                @endif
                                <div class="item-details">
                                    <div class="item-name">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="item-description">{{ Str::limit($item->description, 50) }}</div>
                                    @endif
                                    <div class="item-meta">
                                        <span class="prep-time">
                                            <i class="fas fa-clock"></i> {{ $item->preparation_time }}min
                                        </span>
                                        @if($item->allergens && count($item->allergens) > 0)
                                            <span class="allergens-count">
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                                {{ count($item->allergens) }} allergen{{ count($item->allergens) > 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->category)
                                <span class="status status-active">{{ $item->category->name }}</span>
                            @else
                                <span class="status status-inactive">No Category</span>
                            @endif
                        </td>
                        <td class="cell-center">
                            <div class="price">RM {{ number_format($item->price, 2) }}</div>
                        </td>
                        <td class="cell-center">
                            @if($item->rating_count > 0)
                                <div class="rating">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($item->rating_average))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i - 0.5 <= $item->rating_average)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="rating-info">
                                        {{ number_format($item->rating_average, 1) }} ({{ $item->rating_count }})
                                    </div>
                                </div>
                            @else
                                <div class="no-rating">No ratings yet</div>
                            @endif
                        </td>
                        <td class="cell-center">
                            <div class="status-group">
                                @if($item->availability)
                                    <span class="status status-active">Available</span>
                                @else
                                    <span class="status status-inactive">Unavailable</span>
                                @endif
                                @if($item->is_featured)
                                    <span class="status status-featured">
                                        <i class="fas fa-star"></i> Featured
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="cell-center">
                            <div class="table-actions">
                                <a href="{{ route('admin.menu-items.show', $item->id) }}" 
                                class="action-btn view-btn" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.menu-items.edit', $item->id) }}" 
                                class="action-btn edit-btn" title="Edit Item">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Quick Toggle Buttons -->
                                <form method="POST" action="{{ route('admin.menu-items.toggle-availability', $item->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn toggle-btn {{ $item->availability ? 'disable-action' : '' }}" 
                                            title="{{ $item->availability ? 'Mark Unavailable' : 'Mark Available' }}">
                                        <i class="fas fa-{{ $item->availability ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.menu-items.toggle-featured', $item->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn star-btn {{ $item->is_featured ? 'featured' : '' }}" 
                                            title="{{ $item->is_featured ? 'Remove from Featured' : 'Add to Featured' }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.menu-items.destroy', $item->id) }}" style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this menu item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="empty-state-title">No menu items found</div>
            <div class="empty-state-text">
                @if(request()->hasAny(['search', 'category_id', 'availability', 'is_featured']))
                    No items match your current filters. Try adjusting your search criteria.
                @else
                    Start building your menu by adding your first item.
                @endif
            </div>
            @if(!request()->hasAny(['search', 'category_id', 'availability', 'is_featured']))
                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.menu-items.create') }}" class="admin-btn btn-primary">
                        <i class="fas fa-plus"></i> Add Your First Menu Item
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Pagination -->
    @if($menuItems->hasPages())
        <div class="pagination">
            <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
                <span style="font-size: 14px; color: var(--text-2);">
                    Showing {{ $menuItems->firstItem() }} to {{ $menuItems->lastItem() }} of {{ $menuItems->total() }} results
                </span>
            </div>
            
            @if($menuItems->onFirstPage())
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $menuItems->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($menuItems->getUrlRange(1, $menuItems->lastPage()) as $page => $url)
                @if($page == $menuItems->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($menuItems->hasMorePages())
                <a href="{{ $menuItems->nextPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif
</div>

<!-- Drinks Menu Section -->
<div class="admin-section menu-section" id="drinks-section" style="display: none;">
    <div class="section-header">
        <h2 class="section-title">Drinks Menu Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search drinks..." id="searchInputDrinks" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="drinkCategoryFilter">
                <option value="">All Drink Categories</option>
                @foreach($categories->where('type', 'drink') as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @if($category->subCategories)
                        @foreach($category->subCategories as $subCategory)
                            <option value="{{ $subCategory->id }}">&nbsp;&nbsp;{{ $subCategory->name }}</option>
                        @endforeach
                    @endif
                @endforeach
            </select>
            <select class="filter-select" id="drinkAvailabilityFilter">
                <option value="">All Items</option>
                <option value="1">Available Only</option>
                <option value="0">Unavailable Only</option>
            </select>
        </div>
        <a href="{{ route('admin.menu-items.create') }}?type=drink" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add Drink Item
        </a>
    </div>

    <!-- Drinks Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-item">Item Details</th>
                    <th class="th-category">Category</th>
                    <th class="th-price">Price</th>
                    <th class="th-rating">Rating</th>
                    <th class="th-status">Status</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody id="drinksTableBody">
                @forelse($menuItems->where('category.type', 'drink') as $item)
                <tr>
                    <td>
                        <div class="item-info">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
                            @else
                                <div class="item-image-placeholder">
                                    <i class="fas fa-cocktail"></i>
                                </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="item-description">{{ Str::limit($item->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($item->category)
                            <span class="status status-active">{{ $item->category->name }}</span>
                        @else
                            <span class="status status-inactive">No Category</span>
                        @endif
                    </td>
                    <td class="cell-center">
                        <div class="price">RM {{ number_format($item->price, 2) }}</div>
                    </td>
                    <td class="cell-center">
                        @if($item->rating_count > 0)
                            <div class="rating">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->rating_average))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $item->rating_average)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="rating-info">
                                    {{ number_format($item->rating_average, 1) }} ({{ $item->rating_count }})
                                </div>
                            </div>
                        @else
                            <div class="no-rating">No ratings yet</div>
                        @endif
                    </td>
                    <td class="cell-center">
                        <div class="status-group">
                            @if($item->availability)
                                <span class="status status-active">Available</span>
                            @else
                                <span class="status status-inactive">Unavailable</span>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <a href="{{ route('admin.menu-items.show', $item->id) }}" class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.menu-items.edit', $item->id) }}" class="action-btn edit-btn" title="Edit Item">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.menu-items.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete Item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-cocktail"></i></div>
                        <div class="empty-state-title">No drinks found</div>
                        <div class="empty-state-text">Start adding drinks to your menu.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Set Meals Section -->
<div class="admin-section menu-section" id="set-meals-section" style="display: none;">
    <div class="section-header">
        <h2 class="section-title">Set Meal Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search set meals..." id="searchInputSetMeals" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="setMealCategoryFilter">
                <option value="">All Set Meal Categories</option>
                @foreach($categories->where('type', 'set-meal') as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @if($category->subCategories)
                        @foreach($category->subCategories as $subCategory)
                            <option value="{{ $subCategory->id }}">&nbsp;&nbsp;{{ $subCategory->name }}</option>
                        @endforeach
                    @endif
                @endforeach
            </select>
            <select class="filter-select" id="setMealAvailabilityFilter">
                <option value="">All Items</option>
                <option value="1">Available Only</option>
                <option value="0">Unavailable Only</option>
            </select>
        </div>
        <a href="{{ route('admin.menu-items.create') }}?type=set-meal" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add Set Meal
        </a>
    </div>

    <!-- Set Meals Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-item">Item Details</th>
                    <th class="th-category">Category</th>
                    <th class="th-price">Price</th>
                    <th class="th-rating">Rating</th>
                    <th class="th-status">Status</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody id="setMealsTableBody">
                @forelse($menuItems->where('category.type', 'set-meal') as $item)
                <tr>
                    <td>
                        <div class="item-info">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
                            @else
                                <div class="item-image-placeholder">
                                    <i class="fas fa-pizza-slice"></i>
                                </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="item-description">{{ Str::limit($item->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($item->category)
                            <span class="status status-active">{{ $item->category->name }}</span>
                        @else
                            <span class="status status-inactive">No Category</span>
                        @endif
                    </td>
                    <td class="cell-center">
                        <div class="price">RM {{ number_format($item->price, 2) }}</div>
                    </td>
                    <td class="cell-center">
                        @if($item->rating_count > 0)
                            <div class="rating">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->rating_average))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $item->rating_average)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="rating-info">
                                    {{ number_format($item->rating_average, 1) }} ({{ $item->rating_count }})
                                </div>
                            </div>
                        @else
                            <div class="no-rating">No ratings yet</div>
                        @endif
                    </td>
                    <td class="cell-center">
                        <div class="status-group">
                            @if($item->availability)
                                <span class="status status-active">Available</span>
                            @else
                                <span class="status status-inactive">Unavailable</span>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <a href="{{ route('admin.menu-items.show', $item->id) }}" class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.menu-items.edit', $item->id) }}" class="action-btn edit-btn" title="Edit Item">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.menu-items.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete Item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-pizza-slice"></i></div>
                        <div class="empty-state-title">No set meals found</div>
                        <div class="empty-state-text">Start adding set meals to your menu.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/menu-management.js') }}"></script>
<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.admin-tab');
    const sections = document.querySelectorAll('.menu-section');

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
</script>
@endsection