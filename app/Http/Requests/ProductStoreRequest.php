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
        $rules = [
            'name'        => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) {
                    if (is_numeric(str_replace(['.', ','], '', trim($value)))) {
                        $fail('The product name cannot be a number.');
                    }
                }
            ],
            'price'       => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stock_quantity'    => ['required', 'integer'],
        ];

        if ($this->isMethod('post')) {
            $rules['stock_quantity'][] = 'min:1';
        } else {
            $rules['stock_quantity'][] = 'min:0';
        }

        return $rules;
    }
}