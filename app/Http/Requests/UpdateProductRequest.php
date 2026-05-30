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
            'name'        => [
                'sometimes',
                'string',
                'max:255',
                'regex:/[a-zA-Z]/',   // ✅ same rule on update
            ],
            'description' => 'sometimes|nullable|string',
            'price' => 'required|numeric|min:0.01',  
            'restock'     => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Product name must contain at least one letter, not numbers only.',
        ];
    }
}
