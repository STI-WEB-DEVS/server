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
            'name'  => ['required', 'string', 'max:255', 'regex:/[a-zA-Z]/', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'unique:customers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'    => 'Name must contain letters only, no numbers or special characters.',
            'email.unique'  => 'This email is already registered.',
            'email.email'   => 'Please provide a valid email address.',
        ];
    }
}