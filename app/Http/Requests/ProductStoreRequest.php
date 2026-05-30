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
        $stockRule = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'name'           => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-]+$/'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'price'          => ['required', 'numeric', 'min:0'],
            'stock_quantity' => [$stockRule, 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Product name must only contain letters and spaces. Numbers and special characters are not allowed.',
        ];
    }
}