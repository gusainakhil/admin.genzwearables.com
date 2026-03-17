<?php

namespace App\Http\Requests\Api;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class DeleteReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        $review = $this->route('review');

        if (! $product instanceof Product || ! $review instanceof Review) {
            return false;
        }

        return $this->user()?->id === $review->user_id
            && $review->product_id === $product->id;
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
