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
     */
    public function rules(): array
    {
        return [
            'customer_uuid'        => 'required|uuid|exists:customers,uuid',
            'total_price'          => 'required|numeric|min:0', // Validates overall order total sum
            'items'                => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:products,uuid',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric|min:0', // Validates the unit price coming from Nuxt
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('customer_uuid')) {
            $this->merge([
                'customer_uuid' => $this->route('customer_uuid'),
            ]);
        }
    }
}