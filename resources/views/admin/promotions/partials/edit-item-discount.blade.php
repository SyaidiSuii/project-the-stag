{{-- Item Discount Type Fields --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">Item Discount Settings</h3>

    <div class="form-row">
        <div class="form-group">
            <label for="discount_type_item" class="form-label">Discount Type *</label>
            <select id="discount_type_item" name="discount_type" class="form-control" required>
                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="discount_value_item" class="form-label">Discount Value *</label>
            <input type="number" id="discount_value_item" name="discount_value" class="form-control" value="{{ old('discount_value', $promotion->discount_value) }}" step="0.01" min="0" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Select Items to Apply Discount * (at least 1 item)</label>
        @php
            $selectedItemIds = old('promotion_data.item_ids', $promotion->getDiscountedItemIds() ?? []);
        @endphp
        <div class="item-selector">
            @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                <div style="margin-bottom: 12px;">
                    <strong style="color: var(--text-2); font-size: 13px;">{{ $categoryName ?? 'Uncategorized' }}</strong>
                    @foreach($items as $item)
                        <div class="item-checkbox">
                            <input type="checkbox" name="promotion_data[item_ids][]" value="{{ $item->id }}" id="discount_item_{{ $item->id }}" {{ in_array($item->id, $selectedItemIds) ? 'checked' : '' }}>
                            <label for="discount_item_{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
