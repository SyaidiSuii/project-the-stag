@extends('layouts.admin')

@section('title', $user->id ? 'Edit User' : 'Create User')
@section('page-title', $user->id ? 'Edit User' : 'Create User')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/customer-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $user->id ? 'Edit User' : 'Create New User' }}</h2>
    </div>

    @if($user->id)
        @php($route = route('admin.user.update', $user->id))
        @php($method = 'PUT')
    @else
        @php($route = route('admin.user.store'))
        @php($method = 'POST')
    @endif

    <form method="post" action="{{ $route }}" class="user-form">
        <input type="hidden" name="_method" value="{{ $method }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @if($errors->get('name'))
                    <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @if($errors->get('email'))
                    <div class="form-error">{{ implode(', ', $errors->get('email')) }}</div>
                @endif
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="012345678910" value="{{ old('phone_number', $user->phone_number) }}">
                @if($errors->get('phone_number'))
                    <div class="form-error">{{ implode(', ', $errors->get('phone_number')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Account Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Roles</label>
            <div class="roles-container">
                @foreach($roles as $role)
                    <div class="role-checkbox">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                            {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <label for="role_{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            @if($errors->get('roles'))
                <div class="form-error">{{ implode(', ', $errors->get('roles')) }}</div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                {{ $user->id ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('admin.user.index', ['cancel' => 'true']) }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
/* Form Specific Styles */
.user-form {
    background: white;
    padding: 24px;
    border-radius: var(--radius);
    border: 1px solid var(--muted);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border-radius: var(--radius);
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: all 0.2s ease;
    box-sizing: border-box;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
}

.roles-container {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: var(--radius);
    background: #f9fafb;
}

.role-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
}

.role-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 1px solid #d1d5db;
    accent-color: var(--brand);
}

.role-checkbox label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    margin-top: 20px;
}

.btn-save {
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--brand), #5856eb);
    border: 1px solid var(--brand);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-save:hover {
    background: linear-gradient(135deg, #5856eb, #4f46e5);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-cancel {
    padding: 12px 24px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .roles-container {
        flex-direction: column;
        gap: 12px;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .btn-save, .btn-cancel {
        width: 100%;
    }
}
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/customer-management.js') }}"></script>
@endsection