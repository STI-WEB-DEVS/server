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
        // CHANGE THIS TO TRUE 
        // This allows the request to proceed to the validation rules.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Requirement 2 & 4: Payload Design and Validation
            'customer_uuid' => 'required|uuid|exists:customers,uuid',
            'product_uuid'  => 'required|uuid|exists:products,uuid',
            'quantity'      => 'required|integer|min:1',
        ];
    }
}