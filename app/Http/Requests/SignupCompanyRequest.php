<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupCompanyRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'company_size' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(config('signup.company_sizes'))),
            ],
            'industry_type' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(config('signup.industries'))),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Please enter your company name.',
            'company_name.max' => 'Company name cannot exceed 255 characters.',
            'company_size.required' => 'Please select your company size.',
            'company_size.in' => 'Please select a valid company size.',
            'industry_type.required' => 'Please select your industry type.',
            'industry_type.in' => 'Please select a valid industry type.',
        ];
    }
}
