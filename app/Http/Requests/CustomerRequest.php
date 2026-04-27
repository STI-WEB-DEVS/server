<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        // Use the parameter name defined in your routes (usually 'customer' or 'uuid')
        $customerUuid = $this->route('customer') ?? $this->route('uuid');

        return [
            'name'  => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'email',
                // This replaces that complex sub-query string
                \Illuminate\Validation\Rule::unique('customers', 'email')
                    ->ignore($customerUuid, 'uuid'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Customer name is required.',
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.unique'   => 'This email is already registered.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}