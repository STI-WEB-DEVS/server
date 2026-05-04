<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_uuid' => 'required|exists:customers,uuid',
            'product_uuid'  => 'required|exists:products,uuid',
            'quantity'      => 'required|integer|min:1',
        ];
    }
}