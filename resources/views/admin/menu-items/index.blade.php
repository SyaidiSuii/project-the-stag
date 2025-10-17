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
    <a href="{{ route('admin.menu-items.index', ['tab' => 'food']) }}" class="admin-tab {{ $activeTab === 'food' ? 'active' : '' }}" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-decoration: none; {{ $activeTab === 'food' ? 'background: var(--brand, #6366f1); color: white;' : 'background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);' }}">Food Menu</a>
    <a href="{{ route('admin.menu-items.index', ['tab' => 'drinks']) }}" class="admin-tab {{ $activeTab === 'drinks' ? 'active' : '' }}" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-decoration: none; {{ $activeTab === 'drinks' ? 'background: var(--brand, #6366f1); color: white;' : 'background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);' }}">Drinks Menu</a>
    <a href="{{ route('admin.menu-items.index', ['tab' => 'set-meals']) }}" class="admin-tab {{ $activeTab === 'set-meals' ? 'active' : '' }}" style="padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-decoration: none; {{ $activeTab === 'set-meals' ? 'background: var(--brand, #6366f1); color: white;' : 'background: var(--muted, #e2e8f0); color: var(--text-2, #64748b);' }}">Set Meals</a>
</div>

<!-- Food Menu Section -->
<div class="admin-section menu-section" id="food-section" style="{{ $activeTab === 'food' ? 'display: block;' : 'display: none;' }}">
    <div class="section-header">
        <h2 class="section-title">Food Menu Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search food..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="foodCategoryFilter">
                <option value="">All Food Categories</option>
                @foreach($categories->where('type', 'food')->whereNotNull('parent_id') as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="foodAvailabilityFilter">
                <option value="">All Items</option>
                <option value="1">Available Only</option>
                <option value="0">Unavailable Only</option>
            </select>
        </div>
        <a href="{{ route('admin.menu-items.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add Food Item
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
            <div class="empty-state-title">No food items found</div>
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
                        <i class="fas fa-plus"></i> Add Your First Food Item
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
<!-- End Food Section -->

<!-- Drinks Menu Section -->
<div class="admin-section menu-section" id="drinks-section" style="{{ $activeTab === 'drinks' ? 'display: block;' : 'display: none;' }}">
    <div class="section-header">
        <h2 class="section-title">Drinks Menu Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search drinks..." id="searchInputDrinks" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="drinkCategoryFilter">
                <option value="">All Drink Categories</option>
                @foreach($categories->where('type', 'drink')->whereNotNull('parent_id') as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                <tbody id="drinksTableBody">
                    @foreach($menuItems as $item)
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
            <div class="empty-state-icon"><i class="fas fa-cocktail"></i></div>
            <div class="empty-state-title">No drinks found</div>
            <div class="empty-state-text">Start adding drinks to your menu.</div>
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
<!-- End Drinks Section -->

<!-- Set Meals Section -->
<div class="admin-section menu-section" id="set-meals-section" style="{{ $activeTab === 'set-meals' ? 'display: block;' : 'display: none;' }}">
    <div class="section-header">
        <h2 class="section-title">Set Meal Items</h2>
        <div class="section-controls">
            {{--  --}}
        </div>
    </div>

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search set meals..." id="searchInputSetMeals" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="setMealCategoryFilter">
                <option value="">All Set Meal Categories</option>
                @foreach($categories->where('type', 'set-meal')->whereNotNull('parent_id') as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="setMealAvailabilityFilter">
                <option value="">All Items</option>
                <option value="1">Available Only</option>
                <option value="0">Unavailable Only</option>
            </select>
        </div>
        <a href="{{ route('admin.menu-items.create-set-meal') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Add Set Meal
        </a>
    </div>

    <!-- Set Meals Table -->
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
                <tbody id="setMealsTableBody">
                    @foreach($menuItems as $item)
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
            <div class="empty-state-icon"><i class="fas fa-pizza-slice"></i></div>
            <div class="empty-state-title">No set meals found</div>
            <div class="empty-state-text">Start adding set meals to your menu.</div>
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
<!-- End Set Meals Section -->

@endsection

@section('scripts')
<script src="{{ asset('js/admin/menu-management.js') }}"></script>
<script>
// Notification function
function showNotification(message, type) {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Handle form submission with loading state and notifications
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.querySelector('.user-form');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-save');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            // Let the form submit normally - don't prevent default
        });
    }
    
    // Check for success/error messages from session
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    // === FILTER FUNCTIONALITY ===

    // Get current tab
    const currentTab = '{{ $activeTab }}';
    const currentUrl = new URL(window.location.href);

    // Helper function to apply filters
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        // Keep the tab parameter
        params.set('tab', currentTab);

        // Get filter values based on current tab
        let searchValue, categoryValue, availabilityValue;

        if (currentTab === 'food') {
            searchValue = document.getElementById('searchInput')?.value || '';
            categoryValue = document.getElementById('foodCategoryFilter')?.value || '';
            availabilityValue = document.getElementById('foodAvailabilityFilter')?.value || '';
        } else if (currentTab === 'drinks') {
            searchValue = document.getElementById('searchInputDrinks')?.value || '';
            categoryValue = document.getElementById('drinkCategoryFilter')?.value || '';
            availabilityValue = document.getElementById('drinkAvailabilityFilter')?.value || '';
        } else if (currentTab === 'set-meals') {
            searchValue = document.getElementById('searchInputSetMeals')?.value || '';
            categoryValue = document.getElementById('setMealCategoryFilter')?.value || '';
            availabilityValue = document.getElementById('setMealAvailabilityFilter')?.value || '';
        }

        // Apply search parameter
        if (searchValue && searchValue.trim() !== '') {
            params.set('search', searchValue.trim());
        } else {
            params.delete('search');
        }

        // Apply category filter
        if (categoryValue && categoryValue !== '') {
            params.set('category_id', categoryValue);
        } else {
            params.delete('category_id');
        }

        // Apply availability filter
        if (availabilityValue && availabilityValue !== '') {
            params.set('availability', availabilityValue);
        } else {
            params.delete('availability');
        }

        // Redirect with new parameters
        window.location.href = '{{ route("admin.menu-items.index") }}?' + params.toString();
    }

    // === FOOD TAB FILTERS ===
    const searchInput = document.getElementById('searchInput');
    const foodCategoryFilter = document.getElementById('foodCategoryFilter');
    const foodAvailabilityFilter = document.getElementById('foodAvailabilityFilter');

    if (searchInput) {
        // Search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Search on input (debounced)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }

    if (foodCategoryFilter) {
        foodCategoryFilter.value = '{{ request("category_id") }}';
        foodCategoryFilter.addEventListener('change', applyFilters);
    }

    if (foodAvailabilityFilter) {
        foodAvailabilityFilter.value = '{{ request("availability") }}';
        foodAvailabilityFilter.addEventListener('change', applyFilters);
    }

    // === DRINKS TAB FILTERS ===
    const searchInputDrinks = document.getElementById('searchInputDrinks');
    const drinkCategoryFilter = document.getElementById('drinkCategoryFilter');
    const drinkAvailabilityFilter = document.getElementById('drinkAvailabilityFilter');

    if (searchInputDrinks) {
        searchInputDrinks.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        let searchTimeoutDrinks;
        searchInputDrinks.addEventListener('input', function() {
            clearTimeout(searchTimeoutDrinks);
            searchTimeoutDrinks = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }

    if (drinkCategoryFilter) {
        drinkCategoryFilter.value = '{{ request("category_id") }}';
        drinkCategoryFilter.addEventListener('change', applyFilters);
    }

    if (drinkAvailabilityFilter) {
        drinkAvailabilityFilter.value = '{{ request("availability") }}';
        drinkAvailabilityFilter.addEventListener('change', applyFilters);
    }

    // === SET MEALS TAB FILTERS ===
    const searchInputSetMeals = document.getElementById('searchInputSetMeals');
    const setMealCategoryFilter = document.getElementById('setMealCategoryFilter');
    const setMealAvailabilityFilter = document.getElementById('setMealAvailabilityFilter');

    if (searchInputSetMeals) {
        searchInputSetMeals.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        let searchTimeoutSetMeals;
        searchInputSetMeals.addEventListener('input', function() {
            clearTimeout(searchTimeoutSetMeals);
            searchTimeoutSetMeals = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }

    if (setMealCategoryFilter) {
        setMealCategoryFilter.value = '{{ request("category_id") }}';
        setMealCategoryFilter.addEventListener('change', applyFilters);
    }

    if (setMealAvailabilityFilter) {
        setMealAvailabilityFilter.value = '{{ request("availability") }}';
        setMealAvailabilityFilter.addEventListener('change', applyFilters);
    }
});
</script>
@endsection