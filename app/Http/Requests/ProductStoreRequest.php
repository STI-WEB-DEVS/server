<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'price.numeric'  => 'Price must be a number.',
            'price.min'      => 'Price cannot be negative.',
        ];
    }
}