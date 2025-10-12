@extends('layouts.admin')

@section('title', $user->id ? 'Edit User' : 'Create User')
@section('page-title', $user->id ? 'Edit User' : 'Create User')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $user->id ? 'Edit User' : 'Create New User' }}</h2>
        <a href="{{ route('admin.user.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
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
                <label for="phone_number" class="form-label">Phone Number <small>(Optional)</small></label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="012345678910" value="{{ old('phone_number', $user->phone_number) }}">
                @if($errors->get('phone_number'))
                    <div class="form-error">{{ implode(', ', $errors->get('phone_number')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Account Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        @if(!$user->id)
        <div class="form-row">
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group" style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                @if($errors->get('password'))
                    <div class="form-error">{{ implode(', ', $errors->get('password')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group" style="position: relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                </div>
                @if($errors->get('password_confirmation'))
                    <div class="form-error">{{ implode(', ', $errors->get('password_confirmation')) }}</div>
                @endif
            </div>
        </div>
        @endif

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
@endsection

@section('scripts')
<script src="{{ asset('js/admin/customer-management.js') }}"></script>
@endsection