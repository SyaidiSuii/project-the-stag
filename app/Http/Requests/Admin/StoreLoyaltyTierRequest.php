<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PHASE 4: Form Request for Loyalty Tier Creation
 */
class StoreLoyaltyTierRequest extends FormRequest
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

            // PHASE 7: Updated field names
            'order' => 'required|integer|min:1',
            'points_threshold' => 'required|integer|min:0',
            'points_multiplier' => 'required|numeric|min:1|max:10',

            // Legacy/optional fields
            'minimum_spending' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'benefits' => 'nullable|string|max:2000',
            'color' => 'nullable|string|max:7', // Hex color code
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tier name is required',
            'order.required' => 'Tier order/hierarchy is required',
            'order.min' => 'Tier order must be at least 1',
            'points_threshold.required' => 'Points threshold is mandatory',
            'points_threshold.integer' => 'Points threshold must be a whole number',
            'points_threshold.min' => 'Points threshold cannot be negative',
            'points_multiplier.required' => 'Points multiplier is required',
            'points_multiplier.min' => 'Points multiplier must be at least 1.0',
            'points_multiplier.max' => 'Points multiplier cannot exceed 10.0',
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
    }
}
