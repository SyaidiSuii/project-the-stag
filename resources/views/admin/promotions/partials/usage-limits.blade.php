{{-- Usage Limits & Restrictions Section --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">
        <i class="fas fa-shield-alt" style="margin-right: 8px; color: #f59e0b;"></i>
        Usage Limits & Restrictions
    </h3>
    <p style="color: var(--text-3); font-size: 0.9rem; margin-bottom: 20px;">
        Control how many times this promotion can be used to prevent abuse and manage inventory.
    </p>

    {{-- Usage Limits --}}
    <div class="form-row">
        <div class="form-group">
            <label for="usage_limit_per_customer" class="form-label">
                Max Uses Per Customer
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="How many times each customer can use this promotion"></i>
            </label>
            <input type="number"
                   id="usage_limit_per_customer"
                   name="usage_limit_per_customer"
                   class="form-control"
                   value="{{ old('usage_limit_per_customer', $promotion->usage_limit_per_customer ?? '') }}"
                   min="1"
                   placeholder="Leave empty for unlimited">
            <small style="color: #6b7280; font-size: 0.85rem;">Leave blank for unlimited uses per customer</small>
        </div>
        <div class="form-group">
            <label for="total_usage_limit" class="form-label">
                Total Usage Limit (All Customers)
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Maximum total uses across all customers"></i>
            </label>
            <input type="number"
                   id="total_usage_limit"
                   name="total_usage_limit"
                   class="form-control"
                   value="{{ old('total_usage_limit', $promotion->total_usage_limit ?? '') }}"
                   min="1"
                   placeholder="Leave empty for unlimited">
            <small style="color: #6b7280; font-size: 0.85rem;">Leave blank for unlimited total uses</small>
        </div>
    </div>

    @if(isset($promotion) && $promotion->exists)
    {{-- Show current usage count in edit mode --}}
    <div class="form-group">
        <div style="padding: 12px; background: #f3f4f6; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <strong style="color: #1f2937;">Current Usage:</strong>
            <span style="color: #4b5563; margin-left: 8px;">
                {{ $promotion->current_usage_count ?? 0 }}
                @if($promotion->total_usage_limit)
                    / {{ $promotion->total_usage_limit }} times used
                    ({{ number_format(($promotion->current_usage_count / $promotion->total_usage_limit) * 100, 1) }}%)
                @else
                    times used (unlimited)
                @endif
            </span>
        </div>
    </div>
    @endif

    {{-- Display & Featured Settings --}}
    <div class="form-row">
        <div class="form-group">
            <label for="badge_text" class="form-label">
                Badge Text
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Short text to display on promotion card (e.g., HOT!, NEW!, LIMITED!)"></i>
            </label>
            <input type="text"
                   id="badge_text"
                   name="badge_text"
                   class="form-control"
                   value="{{ old('badge_text', $promotion->badge_text ?? '') }}"
                   maxlength="50"
                   placeholder="e.g., HOT DEAL!, LIMITED!, NEW!">
            <small style="color: #6b7280; font-size: 0.85rem;">Short attention-grabbing text (max 50 characters)</small>
        </div>
        <div class="form-group">
            <label for="display_order" class="form-label">
                Display Priority
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Lower numbers appear first"></i>
            </label>
            <input type="number"
                   id="display_order"
                   name="display_order"
                   class="form-control"
                   value="{{ old('display_order', $promotion->display_order ?? 0) }}"
                   min="0"
                   placeholder="0">
            <small style="color: #6b7280; font-size: 0.85rem;">Lower numbers appear first (0 = highest priority)</small>
        </div>
    </div>

    <div class="form-group">
        <div class="role-checkbox">
            <input type="checkbox"
                   id="is_featured"
                   name="is_featured"
                   value="1"
                   {{ old('is_featured', $promotion->is_featured ?? false) ? 'checked' : '' }}>
            <label for="is_featured">
                <i class="fas fa-star" style="color: #f59e0b; margin-right: 4px;"></i>
                Feature this promotion (show in featured/hot deals section)
            </label>
        </div>
    </div>

    {{-- Terms & Conditions --}}
    <div class="form-group">
        <label for="terms_conditions" class="form-label">
            Terms & Conditions
            <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Important terms customers should know"></i>
        </label>
        <textarea id="terms_conditions"
                  name="terms_conditions"
                  class="form-control"
                  rows="4"
                  placeholder="e.g., Valid for dine-in only. Cannot be combined with other promotions. Not applicable on public holidays.">{{ old('terms_conditions', $promotion->terms_conditions ?? '') }}</textarea>
        <small style="color: #6b7280; font-size: 0.85rem;">Important conditions and restrictions (optional)</small>
    </div>
</div>

{{-- Time-Based Restrictions --}}
<div class="form-section">
    <h3 style="margin-bottom: 16px; color: var(--text);">
        <i class="fas fa-clock" style="margin-right: 8px; color: #8b5cf6;"></i>
        Time-Based Restrictions
    </h3>
    <p style="color: var(--text-3); font-size: 0.9rem; margin-bottom: 20px;">
        Limit when this promotion can be used (e.g., weekends only, happy hour).
    </p>

    {{-- Applicable Days --}}
    <div class="form-group">
        <label class="form-label">
            Applicable Days
            <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Select which days this promotion is valid"></i>
        </label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px; margin-top: 8px;">
            @php
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $selectedDays = old('applicable_days', isset($promotion) ? $promotion->applicable_days : []);
                if (is_string($selectedDays)) {
                    $selectedDays = json_decode($selectedDays, true) ?? [];
                }
            @endphp
            @foreach($days as $index => $day)
                <div class="role-checkbox">
                    <input type="checkbox"
                           id="day_{{ $day }}"
                           name="applicable_days[]"
                           value="{{ $day }}"
                           {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                    <label for="day_{{ $day }}">{{ $dayLabels[$index] }}</label>
                </div>
            @endforeach
        </div>
        <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 8px;">
            Leave all unchecked for 7-days-a-week availability
        </small>
    </div>

    {{-- Time Range --}}
    <div class="form-row">
        <div class="form-group">
            <label for="applicable_start_time" class="form-label">
                Available From (Time)
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="Start time for daily availability"></i>
            </label>
            <input type="time"
                   id="applicable_start_time"
                   name="applicable_start_time"
                   class="form-control"
                   value="{{ old('applicable_start_time', isset($promotion) && $promotion->applicable_start_time ? substr($promotion->applicable_start_time, 0, 5) : '') }}">
            <small style="color: #6b7280; font-size: 0.85rem;">e.g., 15:00 for 3:00 PM</small>
        </div>
        <div class="form-group">
            <label for="applicable_end_time" class="form-label">
                Available Until (Time)
                <i class="fas fa-info-circle" style="color: #6b7280; font-size: 0.85rem;" title="End time for daily availability"></i>
            </label>
            <input type="time"
                   id="applicable_end_time"
                   name="applicable_end_time"
                   class="form-control"
                   value="{{ old('applicable_end_time', isset($promotion) && $promotion->applicable_end_time ? substr($promotion->applicable_end_time, 0, 5) : '') }}">
            <small style="color: #6b7280; font-size: 0.85rem;">e.g., 17:00 for 5:00 PM</small>
        </div>
    </div>

    <div style="padding: 12px; background: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6; margin-top: 12px;">
        <div style="display: flex; align-items: start; gap: 10px;">
            <i class="fas fa-lightbulb" style="color: #3b82f6; margin-top: 2px;"></i>
            <div style="color: #1e40af; font-size: 0.9rem;">
                <strong>Example:</strong> For a "Weekend Happy Hour" promotion, select Saturday & Sunday and set time from 15:00 to 18:00.
                <br>Leave time fields empty for all-day availability.
            </div>
        </div>
    </div>
</div>

<style>
.role-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
}

.role-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.role-checkbox label {
    cursor: pointer;
    margin: 0;
    user-select: none;
}
</style>
