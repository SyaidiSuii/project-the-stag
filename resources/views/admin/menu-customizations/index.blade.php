@extends('layouts.admin')

@section('title', 'Menu Customizations Management')
@section('page-title', 'Menu Customizations Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Customizations</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-sliders-h"></i></div>
        </div>
        <div class="admin-card-value">{{ $customizations->total() ?? 0 }}</div>
        <div class="admin-card-desc">All customizations</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Customization Types</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-list"></i></div>
        </div>
        <div class="admin-card-value">{{ $customizationTypes->count() ?? 0 }}</div>
        <div class="admin-card-desc">Different types</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Avg Additional Price</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-calculator"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($customizations->avg('additional_price') ?? 0, 2) }}</div>
        <div class="admin-card-desc">Average extra cost</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Extra Revenue</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($customizations->sum('additional_price') ?? 0, 2) }}</div>
        <div class="admin-card-desc">From customizations</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Menu Customizations</h2>
        <div class="section-controls">
            <a href="{{ route('admin.menu-customizations.create') }}" class="admin-btn btn-secondary">
                <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
                Add Customization
            </a>
        </div>
    </div>
    

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search by customization value..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="customizationTypeFilter">
                <option value="">All Types</option>
                @foreach($customizationTypes as $type)
                    <option value="{{ $type }}" {{ request('customization_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <input type="number" class="filter-select" id="minPriceFilter" placeholder="Min Price" value="{{ request('min_price') }}" step="0.01">
            <input type="number" class="filter-select" id="maxPriceFilter" placeholder="Max Price" value="{{ request('max_price') }}" step="0.01">
            <select class="filter-select" id="sortByFilter">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="additional_price" {{ request('sort_by') == 'additional_price' ? 'selected' : '' }}>Sort by Price</option>
                <option value="customization_type" {{ request('sort_by') == 'customization_type' ? 'selected' : '' }}>Sort by Type</option>
                <option value="customization_value" {{ request('sort_by') == 'customization_value' ? 'selected' : '' }}>Sort by Value</option>
            </select>
            <select class="filter-select" id="sortOrderFilter">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>
        <div class="bulk-actions" style="display: none;">
            <button onclick="bulkDeleteCustomizations()" class="admin-btn btn-danger" id="bulkDeleteBtn">
                <div class="admin-nav-icon"><i class="fas fa-trash"></i></div>
                Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>
        <a href="{{ route('admin.menu-customizations.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create Customization
        </a>
    </div>

    <!-- Customizations Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-checkbox" style="width: 50px;">
                        <input type="checkbox" id="selectAll" title="Select All">
                    </th>
                    <th class="th-order">Order Item</th>
                    <th class="th-customer">Customization Type</th>
                    <th class="th-type">Customization Value</th>
                    <th class="th-amount">Additional Price</th>
                    <th class="th-status">Created</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customizations as $customization)
                <tr>
                    <td class="cell-center">
                        <input type="checkbox" class="customization-checkbox" value="{{ $customization->id }}">
                    </td>
                    <td>
                        <div class="order-info">
                            <div class="customization-id">#{{ $customization->id }}</div>
                            @if($customization->orderItem && $customization->orderItem->menuItem)
                                <div class="menu-item-name">{{ $customization->orderItem->menuItem->name }}</div>
                                @if($customization->orderItem->order)
                                    <div class="order-reference">Order #{{ $customization->orderItem->order->id }}</div>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="customization-type">
                            <span class="status status-type status-{{ str_replace('_', '-', $customization->customization_type) }}">
                                {{ str_replace('_', ' ', ucfirst($customization->customization_type)) }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="customization-value">
                            <strong>{{ $customization->customization_value }}</strong>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="amount">RM {{ number_format($customization->additional_price, 2) }}</div>
                    </td>
                    <td class="cell-center">
                        <div class="time-info">
                            <div class="order-date">{{ $customization->created_at->format('M d') }}</div>
                            <div class="order-time">{{ $customization->created_at->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <!-- Action Buttons -->
                            <a href="{{ route('admin.menu-customizations.show', $customization->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.menu-customizations.edit', $customization->id) }}" 
                               class="action-btn edit-btn" title="Edit Customization">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.menu-customizations.destroy', $customization->id) }}" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this customization?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete Customization">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="empty-state-title">No customizations found</div>
                        <div class="empty-state-text">
                            @if(request()->hasAny(['search', 'customization_type', 'min_price', 'max_price']))
                                No customizations match your current filters. Try adjusting your search criteria.
                            @else
                                No customizations have been created yet.
                            @endif
                        </div>
                        @if(!request()->hasAny(['search', 'customization_type', 'min_price', 'max_price']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('admin.menu-customizations.create') }}" class="admin-btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Customization
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($customizations->hasPages())
        <div class="pagination">
            <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
                <span style="font-size: 14px; color: var(--text-2);">
                    Showing {{ $customizations->firstItem() }} to {{ $customizations->lastItem() }} of {{ $customizations->total() }} results
                </span>
            </div>
            
            @if($customizations->onFirstPage())
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $customizations->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($customizations->getUrlRange(1, $customizations->lastPage()) as $page => $url)
                @if($page == $customizations->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($customizations->hasMorePages())
                <a href="{{ $customizations->nextPageUrl() }}" class="pagination-btn">
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
@endsection

@section('scripts')
<script src="{{ asset('js/admin/menu-customizations.js') }}"></script>
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

// Check for session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    // Add filter event listeners
    const searchInput = document.getElementById('searchInput');
    const customizationTypeFilter = document.getElementById('customizationTypeFilter');
    const minPriceFilter = document.getElementById('minPriceFilter');
    const maxPriceFilter = document.getElementById('maxPriceFilter');
    const sortByFilter = document.getElementById('sortByFilter');
    const sortOrderFilter = document.getElementById('sortOrderFilter');

    function applyFilters() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.set('search', searchInput.value);
        if (customizationTypeFilter.value) params.set('customization_type', customizationTypeFilter.value);
        if (minPriceFilter.value) params.set('min_price', minPriceFilter.value);
        if (maxPriceFilter.value) params.set('max_price', maxPriceFilter.value);
        if (sortByFilter.value) params.set('sort_by', sortByFilter.value);
        if (sortOrderFilter.value) params.set('sort_order', sortOrderFilter.value);
        
        window.location.href = '{{ route("admin.menu-customizations.index") }}?' + params.toString();
    }

    // Add event listeners with debounce for search
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });

    customizationTypeFilter?.addEventListener('change', applyFilters);
    minPriceFilter?.addEventListener('change', applyFilters);
    maxPriceFilter?.addEventListener('change', applyFilters);
    sortByFilter?.addEventListener('change', applyFilters);
    sortOrderFilter?.addEventListener('change', applyFilters);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const customizationCheckboxes = document.querySelectorAll('.customization-checkbox');
    const bulkActions = document.querySelector('.bulk-actions');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Select all functionality
    selectAllCheckbox?.addEventListener('change', function() {
        customizationCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox functionality
    customizationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            const checkedBoxes = document.querySelectorAll('.customization-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === customizationCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < customizationCheckboxes.length;
            
            updateBulkActions();
        });
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.customization-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCountSpan.textContent = count;
        } else {
            bulkActions.style.display = 'none';
        }
    }
});

// Function to bulk delete customizations
function bulkDeleteCustomizations() {
    const checkedBoxes = document.querySelectorAll('.customization-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select customizations to delete.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} selected customizations?`)) {
        return;
    }

    const customizationIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    fetch('{{ route("admin.menu-customizations.bulkDelete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            customization_ids: customizationIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            location.reload();
        } else {
            alert('Error deleting customizations: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting customizations. Please try again.');
    });
}
</script>
@endsection