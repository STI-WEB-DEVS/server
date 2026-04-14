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
        // Set this to true to allow the request to proceed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * These cover the "Complex Validation Rules" in your workflow.
     */
    public function rules(): bool
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email,' . $this->route('customer'),
            'phone'      => 'nullable|string|min:10',
            'address'    => 'nullable|string',
        ];
    }

    /**
     * Optional: Custom error messages
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered in our system.',
            'first_name.required' => 'Please provide a first name.',
        ];
    }
}