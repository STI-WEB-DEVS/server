<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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