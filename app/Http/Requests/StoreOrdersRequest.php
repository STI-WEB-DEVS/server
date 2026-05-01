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
        // Keep this as true so your request isn't blocked
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Validate the customer exists
            'customer_uuid' => 'required|uuid|exists:customers,uuid',
            
            // Validate that "items" is sent as a list/array
            'items' => 'required|array|min:1',
            
            // The items.* syntax validates every object inside the items array
            'items.*.product_uuid' => 'required|uuid|exists:products,uuid',
            'items.*.quantity'     => 'required|integer|min:1',
        ];
    }
}