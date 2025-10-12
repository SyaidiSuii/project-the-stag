@extends('layouts.admin')

@section('title', 'Create Promotion')
@section('page-title', 'Create New Promotion')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
.type-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}

.type-card {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s;
    background: white;
}

.type-card:hover {
    border-color: var(--brand);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.type-card.selected {
    border-color: var(--brand);
    background: #f0f9ff;
}

.type-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.type-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--text);
}

.type-description {
    font-size: 12px;
    color: var(--text-3);
    margin-top: 4px;
}

.field-group {
    display: none;
}

.field-group.active {
    display: block;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.item-selector {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    max-height: 300px;
    overflow-y: auto;
}

.item-checkbox {
    display: flex;
    align-items: center;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 4px;
}

.item-checkbox:hover {
    background: #f9fafb;
}

.item-checkbox input {
    margin-right: 8px;
}
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Create New Promotion</h2>
        <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Promotions
        </a>
    </div>

    {{-- Error Messages --}}
    @if(session('error'))
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #dc2626;">Error:</strong>
            <p style="color: #991b1b; margin-top: 4px;">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #dc2626;">Validation Errors:</strong>
            <ul style="margin-top: 8px; padding-left: 20px; color: #991b1b;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data" class="user-form" id="promotionForm">
        @csrf

        {{-- Step 1: Select Promotion Type --}}
        <div class="form-section">
            <h3 style="margin-bottom: 16px; color: var(--text);">Step 1: Select Promotion Type</h3>
            <div class="type-selector">
                <div class="type-card" data-type="promo_code">
                    <div class="type-icon"><i class="fas fa-ticket-alt" style="color: #3b82f6;"></i></div>
                    <div class="type-name">Promo Code</div>
                    <div class="type-description">Voucher discount codes</div>
                </div>
                <div class="type-card" data-type="combo_deal">
                    <div class="type-icon"><i class="fas fa-layer-group" style="color: #8b5cf6;"></i></div>
                    <div class="type-name">Combo Deal</div>
                    <div class="type-description">Set meal packages</div>
                </div>
                <div class="type-card" data-type="item_discount">
                    <div class="type-icon"><i class="fas fa-percent" style="color: #10b981;"></i></div>
                    <div class="type-name">Item Discount</div>
                    <div class="type-description">Category/item discounts</div>
                </div>
                <div class="type-card" data-type="buy_x_free_y">
                    <div class="type-icon"><i class="fas fa-gift" style="color: #f59e0b;"></i></div>
                    <div class="type-name">Buy X Free Y</div>
                    <div class="type-description">BOGO deals</div>
                </div>
                <div class="type-card" data-type="bundle">
                    <div class="type-icon"><i class="fas fa-box-open" style="color: #ef4444;"></i></div>
                    <div class="type-name">Bundle</div>
                    <div class="type-description">Value packs</div>
                </div>
                <div class="type-card" data-type="seasonal">
                    <div class="type-icon"><i class="fas fa-calendar-alt" style="color: #ec4899;"></i></div>
                    <div class="type-name">Seasonal</div>
                    <div class="type-description">Limited-time offers</div>
                </div>
            </div>
            <input type="hidden" name="promotion_type" id="promotion_type" required>
        </div>

        {{-- Step 2: Basic Information --}}
        <div class="form-section" id="basicFields" style="display: none;">
            <h3 style="margin-bottom: 16px; color: var(--text);">Step 2: Basic Information</h3>

            <!-- Promotion Name -->
            <div class="form-group">
                <label for="name" class="form-label">Promotion Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Weekend Special, Summer Sale" required>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Describe this promotion">{{ old('description') }}</textarea>
            </div>

            <!-- Promotion Image -->
            <div class="form-group">
                <label for="image" class="form-label">Promotion Banner/Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">Maximum file size: 2MB</small>
                <div id="imagePreview" style="display: none; margin-top: 10px;">
                    <img id="preview" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
                </div>
            </div>

            <!-- Valid Period -->
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date *</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
            </div>

            <!-- Active Status -->
            <div class="form-group">
                <div class="role-checkbox">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active">Activate this promotion immediately</label>
                </div>
            </div>
        </div>

        {{-- Type-Specific Fields: Promo Code --}}
        <div class="field-group" id="promoCodeFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Promo Code Settings</h3>

            <div class="form-group">
                <label for="promo_code" class="form-label">Promo Code *</label>
                <input type="text" id="promo_code" name="promo_code" class="form-control" value="{{ old('promo_code') }}" placeholder="e.g., WELCOME10" style="text-transform: uppercase;">
                <small style="color: #6b7280; font-size: 0.85rem;">Leave empty to auto-generate</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="discount_type" class="form-label">Discount Type *</label>
                    <select id="discount_type" name="discount_type" class="form-control">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (RM)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discount_value" class="form-label">Discount Value *</label>
                    <input type="number" id="discount_value" name="discount_value" class="form-control" value="{{ old('discount_value') }}" step="0.01" min="0" placeholder="e.g., 10 or 20.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="minimum_order_value" class="form-label">Minimum Order Value (RM)</label>
                    <input type="number" id="minimum_order_value" name="minimum_order_value" class="form-control" value="{{ old('minimum_order_value') }}" step="0.01" min="0" placeholder="e.g., 50.00">
                </div>
                <div class="form-group">
                    <label for="max_discount_amount" class="form-label">Maximum Discount Cap (RM)</label>
                    <input type="number" id="max_discount_amount" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount') }}" step="0.01" min="0" placeholder="e.g., 100.00">
                </div>
            </div>
        </div>

        {{-- Type-Specific Fields: Combo Deal / Bundle / Seasonal --}}
        <div class="field-group" id="comboFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Combo Deal Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="combo_price" class="form-label">Combo Price (RM) *</label>
                    <input type="number" id="combo_price" name="combo_price" class="form-control" value="{{ old('combo_price') }}" step="0.01" min="0" placeholder="e.g., 25.00">
                </div>
                <div class="form-group">
                    <label for="original_price" class="form-label">Original Price (RM)</label>
                    <input type="number" id="original_price" name="original_price" class="form-control" value="{{ old('original_price') }}" step="0.01" min="0" placeholder="e.g., 35.00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Select Menu Items for Combo *</label>
                <div class="item-selector">
                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                        <div style="margin-bottom: 12px;">
                            <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                            @foreach($items as $item)
                                <div class="item-checkbox">
                                    <input type="checkbox" name="menu_items[]" value="{{ $item->id }}" id="combo_item_{{ $item->id }}">
                                    <label for="combo_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Type-Specific Fields: Item Discount --}}
        <div class="field-group" id="itemDiscountFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Item Discount Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="discount_type_item" class="form-label">Discount Type *</label>
                    <select id="discount_type_item" name="discount_type" class="form-control">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (RM)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discount_value_item" class="form-label">Discount Value *</label>
                    <input type="number" id="discount_value_item" name="discount_value" class="form-control" value="{{ old('discount_value') }}" step="0.01" min="0" placeholder="e.g., 20">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Apply Discount To</label>
                <select id="apply_to" name="apply_to" class="form-control" onchange="toggleDiscountTargets()">
                    <option value="all">All Menu Items</option>
                    <option value="specific_items">Specific Items</option>
                    <option value="categories">Categories</option>
                </select>
            </div>

            <div class="form-group" id="specificItemsSelector" style="display: none;">
                <label class="form-label">Select Specific Items</label>
                <div class="item-selector">
                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                        <div style="margin-bottom: 12px;">
                            <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                            @foreach($items as $item)
                                <div class="item-checkbox">
                                    <input type="checkbox" name="discount_items[]" value="{{ $item->id }}" id="discount_item_{{ $item->id }}">
                                    <label for="discount_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group" id="categoriesSelector" style="display: none;">
                <label class="form-label">Select Categories</label>
                <div class="item-selector">
                    @foreach($categories as $category)
                        <div class="item-checkbox">
                            <input type="checkbox" name="discount_categories[]" value="{{ $category->id }}" id="discount_cat_{{ $category->id }}">
                            <label for="discount_cat_{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Type-Specific Fields: Buy X Free Y --}}
        <div class="field-group" id="buyXFreeYFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Buy X Free Y Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="buy_quantity" class="form-label">Buy Quantity *</label>
                    <input type="number" id="buy_quantity" name="buy_quantity" class="form-control" value="{{ old('buy_quantity', 1) }}" min="1" placeholder="e.g., 1">
                </div>
                <div class="form-group">
                    <label for="free_quantity" class="form-label">Free Quantity *</label>
                    <input type="number" id="free_quantity" name="free_quantity" class="form-control" value="{{ old('free_quantity', 1) }}" min="1" placeholder="e.g., 1">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Select Items to Buy *</label>
                <div class="item-selector">
                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                        <div style="margin-bottom: 12px;">
                            <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                            @foreach($items as $item)
                                <div class="item-checkbox">
                                    <input type="checkbox" name="buy_items[]" value="{{ $item->id }}" id="buy_item_{{ $item->id }}">
                                    <label for="buy_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <div class="role-checkbox">
                    <input type="checkbox" id="same_item" name="same_item" value="1" checked onchange="toggleFreeItems()">
                    <label for="same_item">Free item is same as buy item</label>
                </div>
            </div>

            <div class="form-group" id="freeItemsSelector" style="display: none;">
                <label class="form-label">Select Free Items</label>
                <div class="item-selector">
                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                        <div style="margin-bottom: 12px;">
                            <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                            @foreach($items as $item)
                                <div class="item-checkbox">
                                    <input type="checkbox" name="free_items[]" value="{{ $item->id }}" id="free_item_{{ $item->id }}">
                                    <label for="free_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Create Promotion
            </button>
            <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let selectedType = null;

// Type selection
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', function() {
        // Remove previous selection
        document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));

        // Select this card
        this.classList.add('selected');
        selectedType = this.dataset.type;
        document.getElementById('promotion_type').value = selectedType;

        // Show basic fields
        document.getElementById('basicFields').style.display = 'block';

        // Hide all type-specific fields
        document.querySelectorAll('.field-group').forEach(group => group.classList.remove('active'));

        // Show relevant fields
        if (selectedType === 'promo_code') {
            document.getElementById('promoCodeFields').classList.add('active');
        } else if (['combo_deal', 'bundle', 'seasonal'].includes(selectedType)) {
            document.getElementById('comboFields').classList.add('active');
        } else if (selectedType === 'item_discount') {
            document.getElementById('itemDiscountFields').classList.add('active');
        } else if (selectedType === 'buy_x_free_y') {
            document.getElementById('buyXFreeYFields').classList.add('active');
        }

        // Scroll to basic fields
        document.getElementById('basicFields').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Preview image
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Toggle discount targets
function toggleDiscountTargets() {
    const applyTo = document.getElementById('apply_to').value;
    document.getElementById('specificItemsSelector').style.display = applyTo === 'specific_items' ? 'block' : 'none';
    document.getElementById('categoriesSelector').style.display = applyTo === 'categories' ? 'block' : 'none';
}

// Toggle free items selector
function toggleFreeItems() {
    const sameItem = document.getElementById('same_item').checked;
    document.getElementById('freeItemsSelector').style.display = sameItem ? 'none' : 'block';
}

// Form validation and cleanup before submit
document.getElementById('promotionForm').addEventListener('submit', function(e) {
    if (!selectedType) {
        e.preventDefault();
        alert('Please select a promotion type');
        return false;
    }

    // Disable all hidden field inputs to prevent validation conflicts
    document.querySelectorAll('.field-group:not(.active)').forEach(group => {
        group.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = true;
        });
    });

    // Show loading state
    const submitBtn = this.querySelector('.btn-save');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    submitBtn.disabled = true;
});

// Auto-uppercase promo code
const promoCodeInput = document.getElementById('promo_code');
if (promoCodeInput) {
    promoCodeInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
}

// Set minimum end date
document.getElementById('start_date').addEventListener('change', function() {
    const endDateInput = document.getElementById('end_date');
    endDateInput.min = this.value;
    if (endDateInput.value && endDateInput.value < this.value) {
        endDateInput.value = this.value;
    }
});
</script>
@endsection
