<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<int, ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'brand_name' => 'required|string|max:150',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico,webp,jpg,jpeg|max:1024',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:30',
            'phone_number1' => 'nullable|string|max:20',
            'phone_number2' => 'nullable|string|max:20',
            'website_name' => 'nullable|string|max:150',
            'support_email' => 'nullable|email|max:150',
            'email_primary' => 'nullable|email|max:150',
            'email_secondary' => 'nullable|email|max:150',
            'additional_info' => 'nullable|string|max:2000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_name.required' => 'Brand name is required.',
            'logo.image' => 'Logo must be a valid image file.',
            'favicon.image' => 'Favicon must be a valid image file.',
            'support_email.email' => 'Support email must be a valid email address.',
            'email_primary.email' => 'Primary email must be a valid email address.',
            'email_secondary.email' => 'Secondary email must be a valid email address.',
        ];
    }
}
