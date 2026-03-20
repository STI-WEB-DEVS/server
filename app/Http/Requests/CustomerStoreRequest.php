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
            'name'  => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z]+$/',
            ],
            'email' => ['required', 'email', 'unique:customers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters.',
        ];
    }
}