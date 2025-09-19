@extends('layouts.admin')

@section('title', 'Menu Item Details')
@section('page-title', 'Menu Item Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $menuItem->name }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Menu Item
            </a>
            <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to Menu Items
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-section" style="margin-bottom: 20px;">
        <div class="section-header">
            <h3 class="section-title">Quick Actions</h3>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <form method="POST" action="{{ route('admin.menu-items.toggle-availability', $menuItem->id) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-save" style="background: {{ $menuItem->availability ? '#ef4444' : '#10b981' }};">
                    <i class="fas fa-{{ $menuItem->availability ? 'eye-slash' : 'eye' }}"></i>
                    {{ $menuItem->availability ? 'Mark Unavailable' : 'Mark Available' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.menu-items.toggle-featured', $menuItem->id) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-save" style="background: {{ $menuItem->is_featured ? '#6b7280' : '#f59e0b' }};">
                    <i class="fas fa-star"></i>
                    {{ $menuItem->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                </button>
            </form>

            <button onclick="document.getElementById('rating-modal').classList.remove('hidden')" class="btn-save" style="background: #3b82f6;">
                <i class="fas fa-star"></i> Add Rating
            </button>
        </div>
    </div>

    <!-- Item Information -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Item Image</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                @if($menuItem->image)
                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                @else
                    <div style="width: 100%; height: 200px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                        <i class="fas fa-utensils" style="font-size: 48px;"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Basic Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb; space-y: 12px;">
                <div style="margin-bottom: 12px;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NAME</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $menuItem->name }}</p>
                </div>
                
                <div style="margin-bottom: 12px;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CATEGORY</span>
                    <p style="margin: 4px 0 0 0;">
                        @if($menuItem->category)
                            <span class="status status-active">{{ $menuItem->category->name }}</span>
                            @if($menuItem->category->parent)
                                <br><small style="color: #6b7280;">Parent: {{ $menuItem->category->parent->name }}</small>
                            @endif
                        @else
                            <span class="status status-inactive">No Category</span>
                        @endif
                    </p>
                </div>

                <div style="margin-bottom: 12px;">
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PRICE</span>
                    <p style="font-size: 24px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">RM {{ number_format($menuItem->price, 2) }}</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PREPARATION TIME</span>
                    <p style="margin: 4px 0 0 0;">{{ $menuItem->preparation_time }} minutes</p>
                </div>
            </div>
        </div>
    </div>

    @if($menuItem->description)
    <div class="form-group">
        <label class="form-label">Description</label>
        <div class="form-control" style="background: #f9fafb; cursor: default;">
            {{ $menuItem->description }}
        </div>
    </div>
    @endif

    <!-- Status and Features -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Availability Status</label>
            <div class="checkbox-group" style="background: {{ $menuItem->availability ? '#d1fae5' : '#fee2e2' }};">
                <i class="fas fa-{{ $menuItem->availability ? 'check-circle' : 'times-circle' }}" 
                   style="color: {{ $menuItem->availability ? '#10b981' : '#ef4444' }};"></i>
                <span style="color: {{ $menuItem->availability ? '#065f46' : '#991b1b' }}; font-weight: 600;">
                    {{ $menuItem->availability ? 'Available for Order' : 'Currently Unavailable' }}
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Featured Status</label>
            <div class="checkbox-group" style="background: {{ $menuItem->is_featured ? '#fef3c7' : '#f3f4f6' }};">
                <i class="fas fa-star" style="color: {{ $menuItem->is_featured ? '#d97706' : '#6b7280' }};"></i>
                <span style="color: {{ $menuItem->is_featured ? '#92400e' : '#374151' }}; font-weight: 600;">
                    {{ $menuItem->is_featured ? 'Featured Item' : 'Regular Item' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Rating Information -->
    <div class="form-group">
        <label class="form-label">Customer Rating</label>
        <div class="rating-display" style="text-align: center; padding: 24px;">
            @if($menuItem->rating_count > 0)
                <div class="rating-stars" style="justify-content: center; margin-bottom: 12px;">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($menuItem->rating_average))
                            <i class="fas fa-star active" style="font-size: 24px; color: #fbbf24;"></i>
                        @elseif($i - 0.5 <= $menuItem->rating_average)
                            <i class="fas fa-star-half-alt" style="font-size: 24px; color: #fbbf24;"></i>
                        @else
                            <i class="far fa-star" style="font-size: 24px; color: #d1d5db;"></i>
                        @endif
                    @endfor
                </div>
                <p style="font-size: 32px; font-weight: 700; margin: 8px 0;">{{ number_format($menuItem->rating_average, 1) }}</p>
                <span class="rating-text">
                    Based on {{ $menuItem->rating_count }} {{ Str::plural('review', $menuItem->rating_count) }}
                </span>
            @else
                <div style="color: #6b7280;">
                    <i class="fas fa-star" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
                    <p style="font-weight: 600; margin: 8px 0;">No ratings yet</p>
                    <span class="no-rating">Be the first to rate this item</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Allergen Information -->
    <div class="form-group">
        <label class="form-label">Allergen Information</label>
        @if($menuItem->allergens && count($menuItem->allergens) > 0)
            <div style="border: 1px solid #f87171; border-radius: 12px; padding: 16px; background: #fef2f2;">
                <div class="allergens-container" style="border: none; background: transparent; padding: 0;">
                    @foreach($menuItem->allergens as $allergen)
                        <div class="allergen-checkbox" style="pointer-events: none;">
                            <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
                            <span style="color: #dc2626; font-weight: 600;">{{ $allergen }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 12px; padding: 8px; background: #fecaca; border-radius: 6px;">
                    <small style="color: #991b1b; font-weight: 600;">
                        <i class="fas fa-info-circle"></i>
                        Warning: This item contains the allergens listed above. Please inform staff of any allergies before ordering.
                    </small>
                </div>
            </div>
        @else
            <div style="border: 1px solid #10b981; border-radius: 12px; padding: 16px; background: #ecfdf5;">
                <div style="display: flex; align-items: center; color: #065f46;">
                    <i class="fas fa-check-circle" style="margin-right: 8px; color: #10b981;"></i>
                    <span style="font-weight: 600;">No specific allergens listed</span>
                </div>
                <small style="color: #047857; margin-top: 8px; display: block;">
                    However, please inform staff of any allergies as ingredients may vary.
                </small>
            </div>
        @endif
    </div>

    <!-- System Information -->
    <div class="form-group">
        <label class="form-label">System Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $menuItem->created_at->format('M d, Y h:i A') }}</p>
                </div>

                @if($menuItem->updated_at != $menuItem->created_at)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">LAST UPDATED</span>
                    <p style="margin: 4px 0 0 0;">{{ $menuItem->updated_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ITEM ID</span>
                    <p style="font-family: monospace; font-size: 12px; margin: 4px 0 0 0;">#{{ $menuItem->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Menu Item
        </a>
        <a href="{{ route('admin.menu-items.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

<!-- Rating Modal -->
<div id="rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3 class="modal-title">Add Rating for {{ $menuItem->name }}</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('rating-modal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.menu-items.rating', $menuItem->id) }}">
            <div class="modal-body">
                @csrf
                @method('PATCH')
                
                <div class="form-group">
                    <label class="form-label">Rating</label>
                    <div style="display: flex; justify-content: center; gap: 4px; margin: 12px 0;">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="rating-star" data-rating="{{ $i }}" 
                                    style="background: none; border: none; cursor: pointer; color: #d1d5db; font-size: 24px;">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('rating-modal').style.display='none'" class="btn-cancel">
                    Cancel
                </button>
                <button type="submit" class="btn-save">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Rating modal functionality
const stars = document.querySelectorAll('.rating-star');
const ratingInput = document.getElementById('rating-value');

stars.forEach((star, index) => {
    star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        ratingInput.value = rating;
        
        // Update star display
        stars.forEach((s, i) => {
            if (i < rating) {
                s.style.color = '#fbbf24';
            } else {
                s.style.color = '#d1d5db';
            }
        });
    });
    
    star.addEventListener('mouseenter', function() {
        const rating = this.dataset.rating;
        stars.forEach((s, i) => {
            if (i < rating) {
                s.style.color = '#fbbf24';
            }
        });
    });
});

// Reset stars on mouse leave
document.querySelector('#rating-modal .modal-body > div > div').addEventListener('mouseleave', function() {
    const currentRating = ratingInput.value;
    stars.forEach((s, i) => {
        if (i < currentRating) {
            s.style.color = '#fbbf24';
        } else {
            s.style.color = '#d1d5db';
        }
    });
});

// Close modal when clicking outside
document.getElementById('rating-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});
</script>
@endsection