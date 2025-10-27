{{-- Promo Code Type Fields --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">Promo Code Settings</h3>

    <div class="form-group">
        <label for="promo_code" class="form-label">Promo Code *</label>
        <input type="text" id="promo_code" name="promo_code" class="form-control" value="{{ old('promo_code', $promotion->promo_code) }}" placeholder="e.g., WELCOME10" style="text-transform: uppercase;" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="discount_type" class="form-label">Discount Type *</label>
            <select id="discount_type" name="discount_type" class="form-control" required>
                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="discount_value" class="form-label">Discount Value *</label>
            <input type="number" id="discount_value" name="discount_value" class="form-control" value="{{ old('discount_value', $promotion->discount_value) }}" step="0.01" min="0" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="minimum_order_value" class="form-label">Minimum Order Value (RM)</label>
            <input type="number" id="minimum_order_value" name="minimum_order_value" class="form-control" value="{{ old('minimum_order_value', $promotion->minimum_order_value) }}" step="0.01" min="0">
        </div>
        <div class="form-group">
            <label for="max_discount_amount" class="form-label">Maximum Discount Cap (RM)</label>
            <input type="number" id="max_discount_amount" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $promotion->max_discount_amount) }}" step="0.01" min="0">
        </div>
    </div>
</div>
