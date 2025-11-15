<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignupEmailRequest extends FormRequest
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
            'work_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
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
            'work_email.required' => 'Please enter your email address.',
            'work_email.email' => 'Please enter a valid email address.',
            'work_email.max' => 'Email address cannot exceed 255 characters.',
            'work_email.unique' => 'This email is already registered. Please log in instead.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'work_email' => 'email address',
        ];
    }
}
