@extends('layouts.admin')

@section('title', 'Employee Management')
@section('page-title', 'Employee Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Employee Records</h2>
        <a href="{{ route('admin.employees.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Employee
        </a>
    </div>

    @if($employees->count() > 0)
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Contact</th>
                    <th>Salary</th>
                    <th>Hire Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td data-label="Name">
                        <div class="customer-info">
                            <div class="customer-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="customer-name">{{ $employee->user->name ?? 'User Not Found' }}</div>
                                <div class="customer-email">{{ $employee->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Position">
                        <span class="status status-active">{{ $employee->position }}</span>
                    </td>
                    <td data-label="Contact">
                        <div>{{ $employee->user->email ?? 'N/A' }}</div>
                        @if($employee->phone_number)
                            <div style="font-size: 13px; color: var(--text-3);">Work: {{ $employee->phone_number }}</div>
                        @else
                            <div style="font-size: 13px; color: var(--text-3); font-style: italic;">No work phone</div>
                        @endif
                    </td>
                    <td data-label="Salary">
                        RM {{ number_format($employee->salary, 2) }}
                    </td>
                    <td data-label="Hire Date">
                        <div style="font-size: 13px;">{{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: var(--text-3);">{{ \Carbon\Carbon::parse($employee->hire_date)->diffForHumans() }}</div>
                    </td>
                    <td data-label="Actions">
                        <div class="table-actions">
                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="action-btn edit-btn" title="Edit Employee">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.employees.destroy', $employee->id) }}"
                                  style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this employee record?');">
                                <input type="hidden" name="_method" value="DELETE">
                                @csrf
                                <button type="submit" class="action-btn delete-btn" title="Delete Employee">
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
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="empty-state-title">No Employees Found</div>
            <div class="empty-state-text">
                No employee records have been created yet.
            </div>
            <div style="margin-top: 20px;">
                <a href="{{ route('admin.employees.create') }}" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Employee
                </a>
            </div>
        </div>
    @endif

    @if($employees->hasPages())
    <div class="pagination">
        {{ $employees->links('vendor.pagination.custom-admin') }}
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
});

function showNotification(message, type) {
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
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
