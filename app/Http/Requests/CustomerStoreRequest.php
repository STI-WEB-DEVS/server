<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $emailRule = Rule::unique('customers', 'email');

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $emailRule->ignore($this->route('customer'), 'uuid');

            return [
                'name' => ['sometimes', 'string', 'required_without:email'],
                'email' => ['sometimes', 'email', $emailRule, 'required_without:name'],
            ];
        }

        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', $emailRule],
        ];
    }
}