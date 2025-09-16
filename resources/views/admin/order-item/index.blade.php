@extends('layouts.admin')

@section('title', 'Orders Items Management')
@section('page-title', 'Orders Items Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Items</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">All orders</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Today's Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM 0</div>
        <div class="admin-card-desc">Today's earnings</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Pending Items</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Still waiting</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Servedd Items</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Successfully served</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Order Items</h2>
    </div>
    
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search order items by ID, menu item..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="orderStatusFilter">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ request('order_status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="ready" {{ request('order_status') == 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="served" {{ request('order_status') == 'served' ? 'selected' : '' }}>Served</option>
            </select>
            <input type="date" class="filter-select" id="dateFilter" value="{{ request('date') }}">
        </div>
        <a href="{{ route('admin.order-item.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Order Items
        </a>
    </div>

    <!-- Order items Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Order Details</th>
                    <th class="th-customer">Menu Item</th>
                    <th class="th-type">Quantity</th>
                    <th class="th-amount">Total Price</th>
                    <th class="th-status">Status</th>
                    <th class="th-time">Special Note</th>
                    <th class="th-time">Created</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderItems as $item)
                <tr>
                    <!-- Order Details -->
                    <td>
                        <div class="order-info">
                            @if($item->order && $item->order->confirmation_code)
                                <div class="confirmation-code">{{ $item->order->confirmation_code }}</div>
                            @endif
                            @if($item->order && $item->order->user)
                                <div class="text-sm text-gray-600">{{ $item->order->user->name }}</div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Menu Item -->
                    <td>
                        <div class="menu-item-info">
                            <div class="font-medium">{{ $item->menuItem->name ?? 'Unknown Item' }}</div>
                            @if($item->menuItem && $item->menuItem->category)
                                <div class="text-sm text-gray-600">{{ $item->menuItem->category->name ?? 'No Category' }}</div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Quantity -->
                    <td class="cell-center">
                        <div class="quantity">{{ $item->quantity }}</div>
                    </td>
                    
                    <!-- Total Price -->
                    <td class="cell-center">
                        <div class="amount">RM {{ number_format($item->total_price, 2) }}</div>
                    </td>
                    
                    <!-- Status -->
                    <td class="cell-center">
                        <span class="status status-item status-{{ str_replace('_', '-', $item->item_status) }}">
                            {{ str_replace('_', ' ', ucfirst($item->item_status)) }}
                        </span>
                    </td>
                    
                    <!-- Special Note -->
                    <td>
                        <div class="special-note">
                            {{ $item->special_note ? Str::limit($item->special_note, 30) : '-' }}
                        </div>
                    </td>
                    
                    <!-- Created -->
                    <td class="cell-center">
                        <div class="time-info">
                            <div class="order-date">{{ $item->created_at->format('M d, Y') }}</div>
                            <div class="order-time">{{ $item->created_at->format('h:i A') }}</div>
                        </div>
                    </td>
                    
                    <!-- Actions -->
                    <td class="cell-center">
                        <div class="table-actions">
                            <a href="{{ route('admin.order-item.show', $item->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.order-item.edit', $item->id) }}" 
                               class="action-btn edit-btn" title="Edit Item">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($item->item_status, ['served', 'cancelled']))
                                <form method="POST" action="{{ route('admin.order-item.destroy', $item->id) }}" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this order item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="empty-state-title">No order items found</div>
                        <div class="empty-state-text">
                            @if(request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                                No order items match your current filters. Try adjusting your search criteria.
                            @else
                                No order items have been placed yet.
                            @endif
                        </div>
                        @if(!request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('admin.order-item.create') }}" class="admin-btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Order Item
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orderItems->hasPages())
    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info">
            <span>
                Showing {{ $orderItems->firstItem() }} to {{ $orderItems->lastItem() }} 
                of {{ $orderItems->total() }} results
            </span>
        </div>
        
        <div class="pagination-links">
            @if($orderItems->onFirstPage())
                <span class="pagination-btn disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $orderItems->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($orderItems->getUrlRange(1, $orderItems->lastPage()) as $page => $url)
                @if($page == $orderItems->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($orderItems->hasMorePages())
                <a href="{{ $orderItems->nextPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/order-management.js') }}"></script>
@endsection