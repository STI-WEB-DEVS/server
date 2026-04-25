<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'customer_uuid' => 'required|exists:customers,uuid',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}