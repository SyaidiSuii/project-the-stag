{{-- Seasonal Type Fields --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">Seasonal Promotion Settings</h3>

    <div class="form-group">
        <label for="seasonal_promo_code" class="form-label">Promo Code (Optional)</label>
        <input type="text" id="seasonal_promo_code" name="promo_code" class="form-control" value="{{ old('promo_code', $promotion->promo_code) }}" placeholder="e.g., SUMMER2025" style="text-transform: uppercase;">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="seasonal_discount_type" class="form-label">Discount Type *</label>
            <select id="seasonal_discount_type" name="discount_type" class="form-control" required>
                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="seasonal_discount_value" class="form-label">Discount Value *</label>
            <input type="number" id="seasonal_discount_value" name="discount_value" class="form-control" value="{{ old('discount_value', $promotion->discount_value) }}" step="0.01" min="0" required>
        </div>
    </div>

    <div class="form-group">
        <label for="seasonal_minimum_order" class="form-label">Minimum Order Value (RM)</label>
        <input type="number" id="seasonal_minimum_order" name="minimum_order_value" class="form-control" value="{{ old('minimum_order_value', $promotion->minimum_order_value) }}" step="0.01" min="0">
    </div>
</div>
