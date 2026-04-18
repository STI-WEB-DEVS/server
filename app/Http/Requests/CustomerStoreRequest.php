<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGE THIS FROM false TO true
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
{
    return [
        // Change 'first_name' and 'last_name' to just 'name'
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
    ];
}
}