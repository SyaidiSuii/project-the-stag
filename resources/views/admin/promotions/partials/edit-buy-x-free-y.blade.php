{{-- Buy X Free Y Type Fields --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">Buy X Free Y Settings</h3>

    @php
        $config = $promotion->getBuyXGetYConfig() ?? [];
        // Get the actual buy and free items from the promotion
        $buyItem = $promotion->menuItems()->wherePivot('is_free', false)->first();
        $freeItem = $promotion->menuItems()->wherePivot('is_free', true)->first();
        // If no free item found, check if there's a buy item that is also free (same item promotion)
        if (!$freeItem) {
            $freeItem = $buyItem;
        }
    @endphp

    <div class="form-row">
        <div class="form-group">
            <label for="buy_quantity" class="form-label">Buy Quantity *</label>
            <input type="number" id="buy_quantity" name="promotion_data[buy_quantity]" class="form-control" value="{{ old('promotion_data.buy_quantity', $config['buy_quantity'] ?? 1) }}" min="1" required>
        </div>
        <div class="form-group">
            <label for="free_quantity" class="form-label">Free Quantity *</label>
            <input type="number" id="free_quantity" name="promotion_data[get_quantity]" class="form-control" value="{{ old('promotion_data.get_quantity', $config['get_quantity'] ?? 1) }}" min="1" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="buy_item_id" class="form-label">Buy Item *</label>
            <select id="buy_item_id" name="promotion_data[buy_item_id]" class="form-control" required>
                <option value="">Select item to buy...</option>
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}" {{ old('promotion_data.buy_item_id', $buyItem->id ?? '') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }} (RM {{ number_format($item->price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="free_item_id" class="form-label">Free Item *</label>
            <select id="free_item_id" name="promotion_data[get_item_id]" class="form-control" required>
                <option value="">Select free item...</option>
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}" {{ old('promotion_data.get_item_id', $freeItem->id ?? '') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }} (RM {{ number_format($item->price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <small style="color: #6b7280; font-size: 0.85rem;">Example: Buy 2 Burgers, Get 1 Fries Free</small>
</div>