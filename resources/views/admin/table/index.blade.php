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
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">-</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Available Table</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">-</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Maintenance Table</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value">0</div>
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
            <select class="filter-select" id="tableActiveFilter">
                <option value="">All Tables</option>
                <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Active Only</option>
                <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactive Only</option>
            </select>
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
                                <form method="POST" action="{{ route('admin.table.destroy', $table->id) }}" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this table?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Table">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chair"></i>
                        </div>
                        <div class="empty-state-title">No tables found</div>
                        <div class="empty-state-text">
                            @if(request()->hasAny(['search', 'status']))
                                No tables match your current filters. Try adjusting your search criteria.
                            @else
                                No tables have been created yet.
                            @endif
                        </div>
                        @if(!request()->hasAny(['search', 'status']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('admin.table.create') }}" class="admin-btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Table
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin/.js') }}"></script>
@endsection