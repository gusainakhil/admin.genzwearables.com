<?php

namespace App\Http\Requests\Api;

use App\Models\UserAddress;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $address = $this->route('address');

        if (! $address instanceof UserAddress) {
            return false;
        }

        return $this->user()?->id === $address->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
