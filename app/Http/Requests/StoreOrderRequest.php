<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_uuid' => 'required|string|exists:customers,uuid',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|string|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_uuid.required' => 'Customer UUID is required',
            'customer_uuid.exists' => 'Customer not found',
            'items.required' => 'At least one item is required',
            'items.min' => 'Order must contain at least one item',
            'items.*.product_uuid.required' => 'Product UUID is required for each item',
            'items.*.product_uuid.exists' => 'One or more products not found',
            'items.*.quantity.required' => 'Quantity is required for each item',
            'items.*.quantity.min' => 'Quantity must be at least 1',
        ];
    }
}
