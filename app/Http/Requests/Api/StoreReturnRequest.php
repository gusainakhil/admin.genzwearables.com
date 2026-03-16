<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_item_id' => 'required|exists:order_items,id',
            'request_type' => 'required|in:return,replacement',
            'requested_by' => 'nullable|in:self,user',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'tracking_id' => 'nullable|string|max:100',
            'reason' => 'nullable|string|max:2000',
            'product_images' => 'nullable|array|max:6',
            'product_images.*' => 'required|image|max:5120',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_item_id.required' => 'Please select an order item.',
            'request_type.required' => 'Please select return or replacement.',
            'request_type.in' => 'Request type must be return or replacement.',
            'requested_by.in' => 'Requested by must be self or user.',
            'product_images.array' => 'Product images must be sent as an array.',
            'product_images.max' => 'You can upload up to 6 images.',
            'product_images.*.image' => 'Each file must be an image.',
            'product_images.*.max' => 'Each image must not exceed 5 MB.',
        ];
    }
}
