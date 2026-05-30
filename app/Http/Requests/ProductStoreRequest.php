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
            'name'           => [
                'required',
                'string',
                'max:255',
                'regex:/[a-zA-Z]/',   
            ],
            'description'    => 'nullable|string',
            'price' => 'required|numeric|min:0.01',  
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Product name must contain at least one letter, not numbers only.',
        ];
    }
}
