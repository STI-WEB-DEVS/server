<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Payload shape:
     * {
     *   "customer_uuid": "550e8400-e29b-41d4-a716-446655440000",
     *   "items": [
     *     { "product_uuid": "661f9511-f30c-52e5-b827-557766551111", "quantity": 2 }
     *   ]
     * }
     */
    public function rules(): array
    {
        return [
            'customer_uuid'        => ['required', 'uuid', 'exists:customers,uuid'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_uuid' => ['required', 'uuid', 'exists:products,uuid'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_uuid.required'        => 'A customer UUID is required.',
            'customer_uuid.uuid'            => 'The customer UUID format is invalid.',
            'customer_uuid.exists'          => 'No customer found with that UUID.',
            'items.required'                => 'At least one order item is required.',
            'items.min'                     => 'At least one order item is required.',
            'items.*.product_uuid.required' => 'Each item must have a product UUID.',
            'items.*.product_uuid.uuid'     => 'A product UUID format is invalid.',
            'items.*.product_uuid.exists'   => 'One or more products were not found.',
            'items.*.quantity.required'     => 'Each item must have a quantity.',
            'items.*.quantity.integer'      => 'Quantity must be a whole number.',
            'items.*.quantity.min'          => 'Quantity must be at least 1.',
        ];
    }
}