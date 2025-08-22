<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preferred_contact' => 'nullable|in:email,sms,push',
            'dietary_preferences' => 'nullable|array',
            'dietary_preferences.*' => 'string|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be before today.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'The photo must be a file of type: jpeg, png, jpg, gif.',
            'photo.max' => 'The photo size must not exceed 2MB.',
            'preferred_contact.in' => 'Preferred contact must be email, sms, or push.',
            'dietary_preferences.*.string' => 'Each dietary preference must be text.',
            'dietary_preferences.*.max' => 'Each dietary preference must not exceed 100 characters.',
        ];
    }
}