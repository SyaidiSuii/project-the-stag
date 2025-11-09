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

/* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */

/* Large Desktop (≥1600px) - 30-40% larger */
@media (min-width: 1600px) {
    .admin-section {
        padding: 32px;
    }
    .section-title {
        font-size: 24px;
    }
    .type-selector {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }
    .type-card {
        padding: 20px;
    }
    .type-icon {
        font-size: 40px;
    }
    .type-name {
        font-size: 16px;
    }
    .form-label {
        font-size: 16px;
    }
    .form-control {
        padding: 14px 18px;
        font-size: 15px;
    }
}

/* Tablet (769px-1199px) - 20-25% smaller */
@media (max-width: 1199px) and (min-width: 769px) {
    .admin-section {
        padding: 20px;
    }
    .section-title {
        font-size: 16px;
    }
    .type-selector {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .type-card {
        padding: 12px;
    }
    .type-icon {
        font-size: 28px;
    }
    .type-name {
        font-size: 13px;
    }
    .type-description {
        font-size: 11px;
    }
    .form-row {
        gap: 12px;
    }
    .form-label {
        font-size: 13px;
    }
    .form-control {
        padding: 10px 14px;
        font-size: 13px;
    }
    .btn-save,
    .btn-cancel {
        padding: 10px 20px;
        font-size: 13px;
    }
}

/* Mobile (≤768px) - 35-40% smaller, single column */
@media (max-width: 768px) {
    .admin-section {
        padding: 16px;
    }
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .section-title {
        font-size: 15px;
    }
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
    /* Type selector - 2 columns on mobile */
    .type-selector {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 20px;
    }
    .type-card {
        padding: 12px;
    }
    .type-icon {
        font-size: 24px;
        margin-bottom: 6px;
    }
    .type-name {
        font-size: 12px;
    }
    .type-description {
        font-size: 10px;
    }
    /* Form sections */
    .form-section h3 {
        font-size: 14px;
        margin-bottom: 12px;
    }
    .form-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }
    .form-control {
        padding: 10px 14px;
        font-size: 13px;
    }
    .form-control small {
        font-size: 11px;
    }
    /* Item selector */
    .item-selector {
        max-height: 250px;
        padding: 10px;
    }
    .item-checkbox {
        padding: 6px;
        font-size: 12px;
    }
    /* Combo/Bundle item rows */
    .combo-item-row,
    .bundle-item-row {
        flex-direction: column !important;
        gap: 8px !important;
    }
    .combo-item-row select,
    .bundle-item-row select {
        width: 100% !important;
    }
    .combo-item-row input[type="number"],
    .bundle-item-row input[type="number"] {
        width: 100% !important;
    }
    /* Form actions */
    .form-actions {
        flex-direction: column;
        gap: 10px;
    }
    .btn-save,
    .btn-cancel {
        width: 100%;
        padding: 12px 16px;
        font-size: 13px;
        justify-content: center;
    }
    /* Error messages */
    .admin-section > div[style*="background: #fee2e2"] {
        padding: 12px !important;
        font-size: 13px !important;
    }
}

/* Small Mobile (≤480px) - Ultra compact */
@media (max-width: 480px) {
    .admin-section {
        padding: 12px;
    }
    .section-title {
        font-size: 14px;
    }
    /* Type selector - single column on very small screens */
    .type-selector {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .type-card {
        padding: 10px;
    }
    .type-icon {
        font-size: 20px;
        margin-bottom: 4px;
    }
    .type-name {
        font-size: 11px;
    }
    .type-description {
        font-size: 9px;
    }
    .form-section h3 {
        font-size: 13px;
    }
    .form-label {
        font-size: 12px;
    }
    .form-control {
        padding: 8px 12px;
        font-size: 12px;
    }
    .btn-save,
    .btn-cancel {
        padding: 10px 14px;
        font-size: 12px;
    }
    .item-checkbox {
        font-size: 11px;
    }
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

            <!-- Promotion Image (legacy for promo code type) -->
            <div class="form-group" id="legacyImageField" style="display: none;">
                <label for="image" class="form-label">Promotion Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(event, 'preview')">
                <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">Maximum file size: 2MB</small>
                <div id="imagePreview" style="display: none; margin-top: 10px;">
                    <img id="preview" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
                </div>
            </div>

            <!-- Banner Image (for combo, seasonal, bundle) -->
            <div class="form-group" id="bannerImageField" style="display: none;">
                <label for="banner_image" class="form-label">Banner Image</label>
                <input type="file" id="banner_image" name="banner_image" class="form-control" accept="image/*" onchange="previewImage(event, 'bannerPreview')">
                <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">Large banner for combo deals (Maximum: 2MB)</small>
                <div id="bannerImagePreview" style="display: none; margin-top: 10px;">
                    <img id="bannerPreview" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
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

        {{-- Type-Specific Fields: Combo Deal --}}
        <div class="field-group" id="comboFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Combo Deal Settings</h3>

            <div class="form-group">
                <label for="combo_price" class="form-label">Combo Price (RM) *</label>
                <input type="number" id="combo_price" name="promotion_data[combo_price]" class="form-control" value="{{ old('promotion_data.combo_price') }}" step="0.01" min="0" placeholder="e.g., 25.00">
            </div>

            <div class="form-group">
                <label class="form-label">Select Menu Items for Combo * (at least 2 items)</label>
                <div id="comboItemsContainer">
                    <div class="combo-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <select name="promotion_data[combo_items][0][item_id]" class="form-control" style="flex: 1;">
                            <option value="">Select item...</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="promotion_data[combo_items][0][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
                    </div>
                    <div class="combo-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <select name="promotion_data[combo_items][1][item_id]" class="form-control" style="flex: 1;">
                            <option value="">Select item...</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="promotion_data[combo_items][1][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
                    </div>
                </div>
                <button type="button" onclick="addComboItem()" class="btn-cancel" style="margin-top: 8px;">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>
            </div>
        </div>

        {{-- Type-Specific Fields: Bundle --}}
        <div class="field-group" id="bundleFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Bundle Settings</h3>

            <div class="form-group">
                <label for="bundle_price" class="form-label">Bundle Price (RM) *</label>
                <input type="number" id="bundle_price" name="promotion_data[bundle_price]" class="form-control" value="{{ old('promotion_data.bundle_price') }}" step="0.01" min="0" placeholder="e.g., 40.00">
            </div>

            <div class="form-group">
                <label class="form-label">Select Menu Items for Bundle * (at least 2 items)</label>
                <div id="bundleItemsContainer">
                    <div class="bundle-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <select name="promotion_data[bundle_items][0][item_id]" class="form-control" style="flex: 1;">
                            <option value="">Select item...</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="promotion_data[bundle_items][0][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
                    </div>
                    <div class="bundle-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <select name="promotion_data[bundle_items][1][item_id]" class="form-control" style="flex: 1;">
                            <option value="">Select item...</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="promotion_data[bundle_items][1][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
                    </div>
                </div>
                <button type="button" onclick="addBundleItem()" class="btn-cancel" style="margin-top: 8px;">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>
            </div>
        </div>

        {{-- Type-Specific Fields: Seasonal --}}
        <div class="field-group" id="seasonalFields">
            <h3 style="margin-bottom: 16px; color: var(--text);">Seasonal Promotion Settings</h3>

            <div class="form-group">
                <label for="seasonal_promo_code" class="form-label">Promo Code (Optional)</label>
                <input type="text" id="seasonal_promo_code" name="promo_code" class="form-control" value="{{ old('promo_code') }}" placeholder="e.g., SUMMER2025" style="text-transform: uppercase;">
                <small style="color: #6b7280; font-size: 0.85rem;">Leave empty if no code required</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="seasonal_discount_type" class="form-label">Discount Type *</label>
                    <select id="seasonal_discount_type" name="discount_type" class="form-control">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (RM)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="seasonal_discount_value" class="form-label">Discount Value *</label>
                    <input type="number" id="seasonal_discount_value" name="discount_value" class="form-control" value="{{ old('discount_value') }}" step="0.01" min="0" placeholder="e.g., 15">
                </div>
            </div>

            <div class="form-group">
                <label for="seasonal_minimum_order" class="form-label">Minimum Order Value (RM)</label>
                <input type="number" id="seasonal_minimum_order" name="minimum_order_value" class="form-control" value="{{ old('minimum_order_value') }}" step="0.01" min="0" placeholder="e.g., 30.00">
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
                <label class="form-label">Select Items to Apply Discount * (at least 1 item)</label>
                <div class="item-selector">
                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                        <div style="margin-bottom: 12px;">
                            <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                            @foreach($items as $item)
                                <div class="item-checkbox">
                                    <input type="checkbox" name="promotion_data[item_ids][]" value="{{ $item->id }}" id="discount_item_{{ $item->id }}">
                                    <label for="discount_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                                </div>
                            @endforeach
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
                    <input type="number" id="buy_quantity" name="promotion_data[buy_quantity]" class="form-control" value="{{ old('promotion_data.buy_quantity', 1) }}" min="1" placeholder="e.g., 2">
                </div>
                <div class="form-group">
                    <label for="free_quantity" class="form-label">Free Quantity *</label>
                    <input type="number" id="free_quantity" name="promotion_data[get_quantity]" class="form-control" value="{{ old('promotion_data.get_quantity', 1) }}" min="1" placeholder="e.g., 1">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="buy_item_id" class="form-label">Buy Item *</label>
                    <select id="buy_item_id" name="promotion_data[buy_item_id]" class="form-control">
                        <option value="">Select item to buy...</option>
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="free_item_id" class="form-label">Free Item *</label>
                    <select id="free_item_id" name="promotion_data[get_item_id]" class="form-control">
                        <option value="">Select free item...</option>
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <small style="color: #6b7280; font-size: 0.85rem;">Example: Buy 2 Burgers, Get 1 Fries Free</small>
        </div>

        {{-- Usage Limits & Restrictions --}}
        @include('admin.promotions.partials.usage-limits', ['promotion' => null])

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
            document.getElementById('legacyImageField').style.display = 'block';
            document.getElementById('bannerImageField').style.display = 'none';
        } else if (selectedType === 'combo_deal') {
            document.getElementById('comboFields').classList.add('active');
            document.getElementById('legacyImageField').style.display = 'none';
            document.getElementById('bannerImageField').style.display = 'block';
        } else if (selectedType === 'bundle') {
            document.getElementById('bundleFields').classList.add('active');
            document.getElementById('legacyImageField').style.display = 'none';
            document.getElementById('bannerImageField').style.display = 'block';
        } else if (selectedType === 'seasonal') {
            document.getElementById('seasonalFields').classList.add('active');
            document.getElementById('legacyImageField').style.display = 'none';
            document.getElementById('bannerImageField').style.display = 'block';
        } else if (selectedType === 'item_discount') {
            document.getElementById('itemDiscountFields').classList.add('active');
            document.getElementById('legacyImageField').style.display = 'block';
            document.getElementById('bannerImageField').style.display = 'none';
        } else if (selectedType === 'buy_x_free_y') {
            document.getElementById('buyXFreeYFields').classList.add('active');
            document.getElementById('legacyImageField').style.display = 'block';
            document.getElementById('bannerImageField').style.display = 'none';
        }

        // Scroll to basic fields
        document.getElementById('basicFields').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Preview image
function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).parentElement.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Add dynamic combo item row
let comboItemIndex = 2;
function addComboItem() {
    const container = document.getElementById('comboItemsContainer');
    const row = document.createElement('div');
    row.className = 'combo-item-row';
    row.style = 'display: flex; gap: 12px; margin-bottom: 8px;';
    row.innerHTML = `
        <select name="promotion_data[combo_items][${comboItemIndex}][item_id]" class="form-control" style="flex: 1;">
            <option value="">Select item...</option>
            @foreach($menuItems as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
            @endforeach
        </select>
        <input type="number" name="promotion_data[combo_items][${comboItemIndex}][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
        <button type="button" onclick="this.parentElement.remove()" class="btn-cancel" style="width: auto; padding: 8px 12px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
    comboItemIndex++;
}

// Add dynamic bundle item row
let bundleItemIndex = 2;
function addBundleItem() {
    const container = document.getElementById('bundleItemsContainer');
    const row = document.createElement('div');
    row.className = 'bundle-item-row';
    row.style = 'display: flex; gap: 12px; margin-bottom: 8px;';
    row.innerHTML = `
        <select name="promotion_data[bundle_items][${bundleItemIndex}][item_id]" class="form-control" style="flex: 1;">
            <option value="">Select item...</option>
            @foreach($menuItems as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
            @endforeach
        </select>
        <input type="number" name="promotion_data[bundle_items][${bundleItemIndex}][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1">
        <button type="button" onclick="this.parentElement.remove()" class="btn-cancel" style="width: auto; padding: 8px 12px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
    bundleItemIndex++;
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
