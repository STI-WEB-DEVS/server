<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'  => $isUpdate
                        ? 'sometimes|required|string|max:255'
                        : 'required|string|max:255',
            'price' => $isUpdate
                        ? 'sometimes|required|numeric|min:0'
                        : 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'price.numeric'  => 'Price must be a valid number.',
            'price.min'      => 'Price cannot be negative.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}