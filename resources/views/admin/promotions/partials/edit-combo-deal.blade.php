{{-- Combo Deal Type Fields --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">Combo Deal Settings</h3>

    <div class="form-group">
        <label for="combo_price" class="form-label">Combo Price (RM) *</label>
        <input type="number" id="combo_price" name="promotion_data[combo_price]" class="form-control" value="{{ old('promotion_data.combo_price', $promotion->getComboPrice()) }}" step="0.01" min="0" required>
    </div>

    <div class="form-group">
        <label class="form-label">Select Menu Items for Combo * (at least 2 items)</label>
        <div id="comboItemsContainer">
            @php
                $comboItems = old('promotion_data.combo_items', $promotion->getComboItems() ?? []);
                $itemIndex = 0;
            @endphp
            @if(is_array($comboItems) && count($comboItems) > 0)
                @foreach($comboItems as $index => $item)
                    <div class="combo-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                        <select name="promotion_data[combo_items][{{ $index }}][item_id]" class="form-control" style="flex: 1;" required>
                            <option value="">Select item...</option>
                            @foreach($menuItems as $menuItem)
                                <option value="{{ $menuItem->id }}" {{ (isset($item['item_id']) && $item['item_id'] == $menuItem->id) ? 'selected' : '' }}>
                                    {{ $menuItem->name }} (RM {{ number_format($menuItem->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="promotion_data[combo_items][{{ $index }}][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="{{ $item['quantity'] ?? 1 }}" required>
                        @if($index > 1)
                        <button type="button" onclick="this.parentElement.remove()" class="btn-cancel" style="width: auto; padding: 8px 12px;">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif
                    </div>
                    @php $itemIndex = $index + 1; @endphp
                @endforeach
            @else
                <div class="combo-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <select name="promotion_data[combo_items][0][item_id]" class="form-control" style="flex: 1;" required>
                        <option value="">Select item...</option>
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="promotion_data[combo_items][0][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1" required>
                </div>
                <div class="combo-item-row" style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <select name="promotion_data[combo_items][1][item_id]" class="form-control" style="flex: 1;" required>
                        <option value="">Select item...</option>
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (RM {{ number_format($item->price, 2) }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="promotion_data[combo_items][1][quantity]" class="form-control" style="width: 100px;" placeholder="Qty" min="1" value="1" required>
                </div>
                @php $itemIndex = 2; @endphp
            @endif
        </div>
        <button type="button" onclick="addComboItem({{ $itemIndex }})" class="btn-cancel" style="margin-top: 8px;">
            <i class="fas fa-plus"></i> Add Another Item
        </button>
    </div>
</div>
