<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentApiCredentialRequest extends FormRequest
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
            'api_email' => 'required|email|max:150',
            'api_password' => 'nullable|string|min:8|max:255',
            'api_token' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'api_email.required' => 'Shiprocket API email is required.',
            'api_email.email' => 'Shiprocket API email must be a valid email address.',
            'api_password.min' => 'Shiprocket API password must be at least 8 characters.',
        ];
    }
}
