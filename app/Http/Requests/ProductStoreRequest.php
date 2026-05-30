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
            'name'        => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (is_numeric(trim($value))) {
                        $fail('The product name cannot consist entirely of numbers.');
                    }
                },
            ],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'gt:0'],
            'stock'       => ['required', 'integer', 'min:0'],
        ];
    }
}
