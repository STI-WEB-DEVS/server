<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => "sometimes|string",
            'items' => "sometimes|array|min:1",
            'items.*.product_id' => "required_with:items|string",
            'items.*.quantity' => "required_with:items|integer|min:1",
        ];
    }
}
