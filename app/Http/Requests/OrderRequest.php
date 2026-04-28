<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
}
