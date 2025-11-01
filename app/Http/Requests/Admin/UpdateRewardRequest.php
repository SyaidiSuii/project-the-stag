<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PHASE 4: Form Request for Reward Update
 *
 * Same validation as StoreRewardRequest but allows partial updates.
 */
class UpdateRewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reward_type' => 'sometimes|required|in:voucher,product,points',
            'reward_value' => 'nullable|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'points_required' => 'sometimes|required|integer|min:0',
            'voucher_template_id' => 'nullable|exists:voucher_templates,id',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'required_tier_id' => 'nullable|exists:loyalty_tiers,id',
            'expiry_days' => 'nullable|integer|min:1|max:365',
            'usage_limit' => 'nullable|integer|min:1',
            'max_redemptions' => 'nullable|integer|min:1',
            'redemption_method' => 'nullable|in:counter,qr_code,auto',
            'terms_conditions' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Reward title is required',
            'title.max' => 'Reward title cannot exceed 255 characters',
            'reward_type.required' => 'Please select a reward type',
            'reward_type.in' => 'Invalid reward type selected. Valid types: voucher, product, points',
            'points_required.required' => 'Points required field is mandatory',
            'points_required.integer' => 'Points required must be a whole number',
            'points_required.min' => 'Points required cannot be negative',
            'voucher_template_id.exists' => 'Selected voucher template does not exist',
            'required_tier_id.exists' => 'Selected loyalty tier does not exist',
            'expiry_days.max' => 'Expiry days cannot exceed 365 days (1 year)',
            'redemption_method.in' => 'The selected redemption method is invalid. Valid methods: counter, qr_code, auto',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox to boolean if present
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->has('is_active') ? true : false,
            ]);
        }
    }
}
