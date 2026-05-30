<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Restock quantity is required.',
            'quantity.integer' => 'Restock quantity must be a whole number.',
            'quantity.min' => 'Restock quantity must be at least 1.',
        ];
    }
}
