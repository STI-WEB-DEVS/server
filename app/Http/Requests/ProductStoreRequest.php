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
        // For update (PUT/PATCH), make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name'  => ['sometimes', 'string', 'max:255'],
                'price' => ['sometimes', 'numeric', 'min:0'],
            ];
        }

        // For create (POST), require both fields
        return [
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
