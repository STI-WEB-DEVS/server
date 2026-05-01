<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGE THIS TO TRUE so you are allowed to send the data
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // This is your Payload Design
            'customer_uuid' => 'required|uuid|exists:customers,uuid',
            'product_uuid'  => 'required|uuid|exists:products,uuid',
            'quantity'      => 'required|integer|min:1',
        ];
    }
}