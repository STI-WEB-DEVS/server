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
            'customer_uuid' => ['required', 'uuid'],
            'orders.*.product_uuid' => ['required', 'uuid'],
            'orders.*.product_quantity' => ['required', 'int']
        ];
    }
}
