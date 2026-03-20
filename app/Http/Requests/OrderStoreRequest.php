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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|uuid|exists:customers,id',
            'product_id'  => 'required|uuid|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'nullable|string|in:pending,paid,shipped,cancelled',
        ];

    }
}
