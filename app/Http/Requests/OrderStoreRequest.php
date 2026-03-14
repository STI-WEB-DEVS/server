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
            'customer_id' => "required|string",
            'items' => "required|array|min:1",
            'items.*.product_id' => "required|string",
            'items.*.quantity' => "required|integer|min:1",
        ];
    }
}
