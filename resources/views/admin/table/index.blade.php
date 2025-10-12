@extends('layouts.admin')

@section('title', 'Table Management')
@section('page-title', 'Table Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Table</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-calendar-day"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalTables ?? 0 }}</div>
        <div class="admin-card-desc">-</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Available Table</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">{{ $availableTables ?? 0 }}</div>
        <div class="admin-card-desc">-</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Maintenance Table</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value">{{ $maintenanceTables ?? 0 }}</div>
        <div class="admin-card-desc">-</div>
    </div>
    {{-- <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Tables</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-chair"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Available for booking</div>
    </div> --}}
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Tables</h2>
    </div>

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search tables by number, type, location..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="tableStatusFilter">
                <option value="">All Statuses</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <select class="filter-select" id="tableTypeFilter">
                <option value="">All Types</option>
                <option value="indoor" {{ request('table_type') == 'indoor' ? 'selected' : '' }}>Indoor</option>
                <option value="outdoor" {{ request('table_type') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                <option value="vip" {{ request('table_type') == 'vip' ? 'selected' : '' }}>VIP</option>
            </select>
            <select class="filter-select" id="tableActiveFilter">
                <option value="">All Tables</option>
                <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Active Only</option>
                <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactive Only</option>
            </select>
            @if(request()->hasAny(['search', 'status', 'table_type', 'active']))
                <button type="button" class="admin-btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear Filters
                </button>
            @endif
        </div>
        <a href="{{ route('admin.table.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Table
        </a>
    </div>

    <!-- Table Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Table</th>
                    <th class="th-customer">Capacity</th>
                    <th class="th-type">Type</th>
                    <th class="th-amount">Status</th>
                    <th class="th-status">Location</th>
                    <th class="th-eta">Active</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tables as $table)
                <tr>
                    <td>
                        <div class="table-info">
                            <div class="table-number">{{ $table->table_number }}</div>
                            @if($table->name)
                                <div class="table-name">{{ $table->name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="capacity-info">
                            <strong>{{ $table->capacity }}</strong> seats
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-type">
                            {{ ucfirst($table->table_type ?? 'Standard') }}
                        </div>
                    </td>
                    <td class="cell-center">
                        <span class="status status-table status-{{ str_replace('_', '-', $table->status) }}">
                            {{ str_replace('_', ' ', ucfirst($table->status)) }}
                        </span>
                    </td>
                    <td class="cell-center">
                        <div class="location-info">
                            {{ $table->location ?? 'Main Floor' }}
                        </div>
                    </td>
                    <td class="cell-center">
                        <span class="status {{ $table->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $table->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <!-- Status Update Buttons -->
                            @if($table->status === 'available')
                                <button class="action-btn reserve-btn" title="Reserve Table" onclick="updateTableStatus({{ $table->id }}, 'reserved')">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                                <button class="action-btn maintenance-btn" title="Set Maintenance" onclick="updateTableStatus({{ $table->id }}, 'maintenance')">
                                    <i class="fas fa-tools"></i>
                                </button>
                            @elseif($table->status === 'occupied')
                                <button class="action-btn available-btn" title="Mark Available" onclick="updateTableStatus({{ $table->id }}, 'available')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            @elseif($table->status === 'reserved')
                                <button class="action-btn occupy-btn" title="Mark Occupied" onclick="updateTableStatus({{ $table->id }}, 'occupied')">
                                    <i class="fas fa-user-friends"></i>
                                </button>
                                <button class="action-btn available-btn" title="Cancel Reservation" onclick="updateTableStatus({{ $table->id }}, 'available')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($table->status === 'maintenance')
                                <button class="action-btn available-btn" title="Mark Available" onclick="updateTableStatus({{ $table->id }}, 'available')">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                            <!-- Default Action Buttons -->
                            <a href="{{ route('admin.table.show', $table->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.table.edit', $table->id) }}" 
                               class="action-btn edit-btn" title="Edit Table">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($table->status === 'available')
                                <button type="button" class="action-btn delete-btn" title="Delete Table" onclick="deleteTable({{ $table->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <i class="fas fa-chair" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                            <p style="font-weight: 600; margin-bottom: 8px; font-size: 16px;">No tables found</p>
                            <p style="font-size: 14px; margin-bottom: 0;">
                                @if(request()->hasAny(['search', 'status', 'table_type', 'active']))
                                    No tables match your current filters. Try adjusting your search criteria.
                                @else
                                    No tables have been created yet.
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'status', 'table_type', 'active']))
                                <div style="margin-top: 20px;">
                                    <a href="{{ route('admin.table.create') }}" class="admin-btn btn-primary">
                                        <i class="fas fa-plus"></i> Create First Table
                                    </a>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tables->hasPages())
        <div class="pagination">
            <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
                <span style="font-size: 14px; color: var(--text-2);">
                    Showing {{ $tables->firstItem() }} to {{ $tables->lastItem() }} of {{ $tables->total() }} results
                </span>
            </div>
            
            @if($tables->onFirstPage())
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $tables->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($tables->getUrlRange(1, $tables->lastPage()) as $page => $url)
                @if($page == $tables->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($tables->hasMorePages())
                <a href="{{ $tables->nextPageUrl() }}" class="pagination-btn">
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

<!-- Confirmation Modal -->
<div id="confirm-modal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="confirm-title">Confirm Action</h3>
        </div>
        <div class="modal-body">
            <p id="confirm-message"></p>
        </div>
        <div class="modal-footer">
            <button class="admin-btn btn-secondary" id="confirm-cancel">Cancel</button>
            <button class="admin-btn btn-primary" id="confirm-ok">Confirm</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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
});
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('tableStatusFilter');
    const typeFilter = document.getElementById('tableTypeFilter');
    const activeFilter = document.getElementById('tableActiveFilter');

    // Function to apply filters
    function applyFilters() {
        const params = new URLSearchParams();
        
        // Add search parameter
        if (searchInput.value.trim()) {
            params.append('search', searchInput.value.trim());
        }
        
        // Add status filter
        if (statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        
        // Add table type filter
        if (typeFilter.value) {
            params.append('table_type', typeFilter.value);
        }
        
        // Add active filter
        if (activeFilter.value) {
            params.append('active', activeFilter.value);
        }
        
        // Redirect with parameters
        const url = '{{ route("admin.table.index") }}' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    }

    // Search input handler with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500); // 500ms delay
    });

    // Filter change handlers
    statusFilter.addEventListener('change', applyFilters);
    typeFilter.addEventListener('change', applyFilters);
    activeFilter.addEventListener('change', applyFilters);

    // Clear filters function
    window.clearFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        activeFilter.value = '';
        window.location.href = '{{ route("admin.table.index") }}';
    };
});

// Function to update table status
function updateTableStatus(tableId, status) {
    const statusLabels = {
        'available': 'Available',
        'occupied': 'Occupied',
        'reserved': 'Reserved',
        'maintenance': 'Maintenance'
    };

    showConfirm(
        'Update Table Status',
        `Are you sure you want to change this table status to ${statusLabels[status] || status}?`,
        function() {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/table/${tableId}/status`;

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add method override for PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// Function to delete table
function deleteTable(tableId) {
    showConfirm(
        'Delete Table',
        'Are you sure you want to delete this table? This action cannot be undone.',
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/table/${tableId}`;

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add method override for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// Modern confirmation modal
function showConfirm(title, message, onConfirm) {
    const modal = document.getElementById('confirm-modal');
    const titleEl = document.getElementById('confirm-title');
    const messageEl = document.getElementById('confirm-message');
    const confirmBtn = document.getElementById('confirm-ok');
    const cancelBtn = document.getElementById('confirm-cancel');

    titleEl.textContent = title;
    messageEl.textContent = message;
    modal.style.display = 'flex';

    // Remove old listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    const newCancelBtn = cancelBtn.cloneNode(true);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

    // Add new listeners
    newConfirmBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        onConfirm();
    });

    newCancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}
</script>
@endsection