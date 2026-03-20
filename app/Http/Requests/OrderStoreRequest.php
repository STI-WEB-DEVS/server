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
            'customer_uuid'         => ['required', 'string', 'exists:customers,uuid'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_uuid'  => ['required', 'string', 'exists:products,uuid'],
            'items.*.quantity'      => ['required', 'integer', 'min:1'],
        ];
    }
}