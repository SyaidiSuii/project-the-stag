@extends('layouts.admin')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories_managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $category->name }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Category
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="form-group">
        <label class="form-label">Basic Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CATEGORY NAME</span>
                    <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">{{ $category->name }}</p>
                </div>
                
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TYPE</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0; text-transform: capitalize;">
                        {{ $category->parent_id ? 'Sub Category' : 'Main Category' }}
                    </p>
                </div>

                @if($category->parent_id)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PARENT CATEGORY</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $category->parent->name ?? 'N/A' }}</p>
                </div>
                @endif

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">SORT ORDER</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $category->sort_order }}</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $category->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($category->subCategories->count() > 0)
    <!-- Sub Categories -->
    <div class="form-group">
        <label class="form-label">Sub Categories ({{ $category->subCategories->count() }})</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
                @foreach($category->subCategories as $subCategory)
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-weight: 600; margin: 0; font-size: 14px;">{{ $subCategory->name }}</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 12px;">Order: {{ $subCategory->sort_order }}</p>
                        </div>
                        <a href="{{ route('admin.categories.show', $subCategory->id) }}" style="color: #3b82f6; text-decoration: none;">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($category->menuItems && $category->menuItems->count() > 0)
    <!-- Menu Items -->
    <div class="form-group">
        <label class="form-label">Menu Items ({{ $category->menuItems->count() }})</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px;">
                @foreach($category->menuItems as $menuItem)
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 600;">{{ $menuItem->name }}</h4>
                            <span style="background: #f3f4f6; color: #374151; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                RM {{ number_format($menuItem->price, 2) }}
                            </span>
                        </div>
                        @if($menuItem->description)
                            <p style="color: #6b7280; margin: 0; font-size: 12px; line-height: 1.4;">{{ Str::limit($menuItem->description, 100) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Category Statistics -->
    <div class="form-group">
        <label class="form-label">Category Statistics</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; text-align: center;">
                <div>
                    <i class="fas fa-layer-group" style="font-size: 24px; color: #3b82f6; margin-bottom: 8px;"></i>
                    <p style="font-size: 24px; font-weight: 700; margin: 0; color: #1f2937;">{{ $category->subCategories->count() }}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0; font-weight: 600;">Sub Categories</p>
                </div>
                <div>
                    <i class="fas fa-utensils" style="font-size: 24px; color: #10b981; margin-bottom: 8px;"></i>
                    <p style="font-size: 24px; font-weight: 700; margin: 0; color: #1f2937;">{{ $category->menuItems ? $category->menuItems->count() : 0 }}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0; font-weight: 600;">Menu Items</p>
                </div>
                <div>
                    <i class="fas fa-calendar-alt" style="font-size: 24px; color: #f59e0b; margin-bottom: 8px;"></i>
                    <p style="font-size: 14px; font-weight: 700; margin: 0; color: #1f2937;">{{ $category->updated_at->diffForHumans() }}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0; font-weight: 600;">Last Updated</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Category
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Category management scripts can be added here
</script>
@endsection