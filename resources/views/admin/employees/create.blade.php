@extends('layouts.admin')

@section('title', 'Create Employee')
@section('page-title', 'Create New Employee')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Employee Details</h2>
    </div>

    <form method="POST" action="{{ route('admin.employees.store') }}" class="admin-form">
        @csrf
        @include('admin.employees._form')

        <div class="form-actions">
            <a href="{{ route('admin.employees.index') }}" class="admin-btn btn-secondary">Cancel</a>
            <button type="submit" class="admin-btn btn-primary">Save Employee</button>
        </div>
    </form>
</div>
@endsection
