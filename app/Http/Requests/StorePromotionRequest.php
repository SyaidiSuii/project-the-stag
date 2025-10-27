<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Promotion;

class StorePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization handled by route middleware (role:admin|manager)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'promotion_type' => 'required|in:promo_code,combo_deal,item_discount,buy_x_free_y,bundle,seasonal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',

            // Usage Limits
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',

            // Display & Featured
            'is_featured' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'badge_text' => 'nullable|string|max:50',
            'terms_conditions' => 'nullable|string',

            // Time-based restrictions
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'applicable_start_time' => 'nullable|date_format:H:i',
            'applicable_end_time' => 'nullable|date_format:H:i|after:applicable_start_time',
        ];

        // Type-specific validation
        $promotionType = $this->input('promotion_type');

        switch ($promotionType) {
            case Promotion::TYPE_PROMO_CODE:
                $rules = array_merge($rules, [
                    'promo_code' => 'required|string|max:50|unique:promotions,promo_code',
                    'discount_type' => 'required|in:percentage,fixed',
                    'discount_value' => 'required|numeric|min:0',
                    'minimum_order_value' => 'nullable|numeric|min:0',
                ]);
                break;

            case Promotion::TYPE_COMBO_DEAL:
                $rules = array_merge($rules, [
                    'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'promotion_data.combo_price' => 'required|numeric|min:0',
                    'promotion_data.combo_items' => 'required|array|min:2',
                    'promotion_data.combo_items.*.item_id' => 'required|exists:menu_items,id',
                    'promotion_data.combo_items.*.quantity' => 'required|integer|min:1',
                ]);
                break;

            case Promotion::TYPE_ITEM_DISCOUNT:
                $rules = array_merge($rules, [
                    'discount_type' => 'required|in:percentage,fixed',
                    'discount_value' => 'required|numeric|min:0',
                    'promotion_data.item_ids' => 'required|array|min:1',
                    'promotion_data.item_ids.*' => 'required|exists:menu_items,id',
                ]);
                break;

            case Promotion::TYPE_BUY_X_FREE_Y:
                $rules = array_merge($rules, [
                    'promotion_data.buy_item_id' => 'required|exists:menu_items,id',
                    'promotion_data.buy_quantity' => 'required|integer|min:1',
                    'promotion_data.get_item_id' => 'required|exists:menu_items,id',
                    'promotion_data.get_quantity' => 'required|integer|min:1',
                ]);
                break;

            case Promotion::TYPE_BUNDLE:
                $rules = array_merge($rules, [
                    'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'promotion_data.bundle_price' => 'required|numeric|min:0',
                    'promotion_data.bundle_items' => 'required|array|min:2',
                    'promotion_data.bundle_items.*.item_id' => 'required|exists:menu_items,id',
                    'promotion_data.bundle_items.*.quantity' => 'required|integer|min:1',
                ]);
                break;

            case Promotion::TYPE_SEASONAL:
                $rules = array_merge($rules, [
                    'promo_code' => 'nullable|string|max:50|unique:promotions,promo_code',
                    'discount_type' => 'required|in:percentage,fixed',
                    'discount_value' => 'required|numeric|min:0',
                    'minimum_order_value' => 'nullable|numeric|min:0',
                    'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                break;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The promotion name is required.',
            'promotion_type.required' => 'Please select a promotion type.',
            'promotion_type.in' => 'Invalid promotion type selected.',
            'promo_code.required' => 'The promo code is required for this promotion type.',
            'promo_code.unique' => 'This promo code is already in use.',
            'discount_type.required' => 'Please select a discount type.',
            'discount_value.required' => 'The discount value is required.',
            'start_date.required' => 'The start date is required.',
            'end_date.required' => 'The end date is required.',
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
            'promotion_data.combo_items.required' => 'Please select at least 2 items for the combo deal.',
            'promotion_data.bundle_items.required' => 'Please select at least 2 items for the bundle.',
            'promotion_data.item_ids.required' => 'Please select at least 1 item for discount.',

            // Usage limits messages
            'usage_limit_per_customer.integer' => 'Per-customer limit must be a whole number.',
            'usage_limit_per_customer.min' => 'Per-customer limit must be at least 1.',
            'total_usage_limit.integer' => 'Total usage limit must be a whole number.',
            'total_usage_limit.min' => 'Total usage limit must be at least 1.',

            // Time restriction messages
            'applicable_days.*.in' => 'Invalid day selected.',
            'applicable_start_time.date_format' => 'Start time must be in HH:MM format (e.g., 15:00).',
            'applicable_end_time.date_format' => 'End time must be in HH:MM format (e.g., 17:00).',
            'applicable_end_time.after' => 'End time must be after start time.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation: per-customer limit should not exceed total limit
            if ($this->filled('usage_limit_per_customer') && $this->filled('total_usage_limit')) {
                if ($this->usage_limit_per_customer > $this->total_usage_limit) {
                    $validator->errors()->add(
                        'usage_limit_per_customer',
                        'Per-customer limit cannot exceed total usage limit.'
                    );
                }
            }

            // If time range is set, at least one day should be selected
            if (($this->filled('applicable_start_time') || $this->filled('applicable_end_time'))
                && empty($this->applicable_days)) {
                $validator->errors()->add(
                    'applicable_days',
                    'Please select at least one day when setting time restrictions.'
                );
            }
        });
    }
}
