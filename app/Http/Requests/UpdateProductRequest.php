<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'price' => 'numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'quantity' => 'integer|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Product name must contain only letters, spaces, hyphens, and apostrophes.',
            'price.min' => 'Price must be greater than zero.',
            'price.regex' => 'Price must be a valid number with up to 2 decimal places.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
        ];
    }
}
