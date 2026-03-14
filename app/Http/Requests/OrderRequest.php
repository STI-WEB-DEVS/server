<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // customer_uuid — we look up the customer by uuid
            'customer_uuid'        => 'required|string|exists:customers,uuid',

            // items array — at least 1 item required
            'items'                => 'required|array|min:1',
            'items.*.product_uuid' => 'required|string|exists:products,uuid',
            'items.*.quantity'     => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_uuid.required'        => 'A customer UUID is required.',
            'customer_uuid.exists'          => 'The selected customer does not exist.',
            'items.required'                => 'At least one order item is required.',
            'items.array'                   => 'Items must be an array.',
            'items.min'                     => 'At least one item is required.',
            'items.*.product_uuid.required' => 'Each item must have a product UUID.',
            'items.*.product_uuid.exists'   => 'One or more products do not exist.',
            'items.*.quantity.required'     => 'Each item must have a quantity.',
            'items.*.quantity.integer'      => 'Quantity must be a whole number.',
            'items.*.quantity.min'          => 'Quantity must be at least 1.',
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