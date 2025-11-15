<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
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
        $userId = auth()->id();

        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'timezone' => [
                'required',
                'string',
                Rule::in(array_keys(config('signup.timezones'))),
            ],
            'about_yourself' => [
                'nullable',
                'string',
                'max:500',
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(config('signup.password.min_length'))
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'current_password' => [
                'nullable',
                'required_with:password',
                'current_password',
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
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'timezone.required' => 'Please select your timezone.',
            'password.confirmed' => 'The password confirmation does not match.',
            'current_password.required_with' => 'Please enter your current password to change it.',
            'current_password.current_password' => 'The current password is incorrect.',
        ];
    }
}
