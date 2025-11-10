@extends('layouts.admin')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee Record')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Update Employee Details</h2>
    </div>

    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" class="admin-form">
        @csrf
        @method('PUT')
        
        @include('admin.employees._form')

        <div class="form-actions">
            <a href="{{ route('admin.employees.index') }}" class="admin-btn btn-secondary">Cancel</a>
            <button type="submit" class="admin-btn btn-primary">Update Employee</button>
        </div>
    </form>
</div>
@endsection
