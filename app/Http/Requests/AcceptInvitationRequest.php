<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AcceptInvitationRequest extends FormRequest
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
        $invitation = Invitation::where('invite_token', $this->token)->first();
        $existingUser = $invitation ? User::where('email', $invitation->invited_email)->first() : null;

        $passwordRules = Password::min(config('signup.password.min_length', 8));

        if (config('signup.password.require_letter')) {
            $passwordRules->letters();
        }

        if (config('signup.password.require_number')) {
            $passwordRules->numbers();
        }

        if (config('signup.password.require_special_char')) {
            $passwordRules->symbols();
        }

        $rules = [
            'token' => ['required', 'string'],
            'timezone' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(config('signup.timezones'))),
            ],
            'about_yourself' => ['nullable', 'string', 'max:1000'],
        ];

        // Only require name and password for new users
        if (!$existingUser) {
            $rules['first_name'] = ['required', 'string', 'min:1', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'min:1', 'max:100'];
            $rules['password'] = ['required', 'confirmed', $passwordRules];
            $rules['password_confirmation'] = ['required'];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Invitation token is required.',
            'first_name.required' => 'Please enter your first name.',
            'first_name.max' => 'First name cannot exceed 100 characters.',
            'last_name.required' => 'Please enter your last name.',
            'last_name.max' => 'Last name cannot exceed 100 characters.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'The passwords do not match.',
            'password_confirmation.required' => 'Please confirm your password.',
            'timezone.required' => 'Please select your timezone.',
            'timezone.in' => 'Please select a valid timezone.',
            'about_yourself.max' => 'About yourself cannot exceed 1000 characters.',
        ];
    }
}
