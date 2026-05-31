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
            'name'        => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => [$this->isMethod('POST') ? 'required' : 'nullable', 'integer', 'min:0'],
            'restock'     => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'     => 'The product name must contain only letters and spaces.',
            'stock.required' => 'Stock quantity is required when creating a product.',
            'restock.min'    => 'Restock amount cannot be negative.',
        ];
    }
}
