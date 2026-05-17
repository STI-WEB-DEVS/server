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
        $uuid = $this->route('uuid'); // get current customer uuid if present

        // For update (PUT/PATCH), make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name'  => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'email', 'unique:customers,email,' . $uuid . ',uuid'],
            ];
        }

        // For create (POST), require both fields
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
        ];
    }
}
