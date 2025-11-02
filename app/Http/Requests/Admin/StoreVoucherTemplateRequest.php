<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PHASE 4: Form Request for Voucher Template Creation
 */
class StoreVoucherTemplateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'source_type' => 'required|in:collection,reward,promotion,manual',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'spending_requirement' => 'nullable|numeric|min:0',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'max_total_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'expiry_days' => 'nullable|integer|min:1|max:365',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Voucher template name is required',
            'source_type.required' => 'Please select a source type',
            'source_type.in' => 'Invalid source type selected',
            'discount_type.required' => 'Please select discount type (fixed or percentage)',
            'discount_value.required' => 'Discount value is required',
            'discount_value.min' => 'Discount value cannot be negative',
            'valid_until.after_or_equal' => 'Expiry date must be after or equal to start date',
            'expiry_days.max' => 'Expiry days cannot exceed 365 days',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);

        // Validate percentage discount
        if ($this->discount_type === 'percentage' && $this->discount_value > 100) {
            $this->merge([
                'discount_value' => 100
            ]);
        }
    }
}
