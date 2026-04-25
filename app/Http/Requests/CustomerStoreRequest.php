<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Customer name is required.',
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.unique'   => 'This email address is already registered.',
        ];
    }
}