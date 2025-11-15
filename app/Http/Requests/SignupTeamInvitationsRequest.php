<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupTeamInvitationsRequest extends FormRequest
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
        $maxInvitations = config('signup.max_invitations_per_signup', 10);

        return [
            'team_member_emails' => [
                'nullable',
                'array',
                "max:{$maxInvitations}",
            ],
            'team_member_emails.*' => [
                'required',
                'email',
                'max:255',
                'different:' . auth()->user()?->email,
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
            'team_member_emails.max' => 'You can invite a maximum of ' . config('signup.max_invitations_per_signup', 10) . ' team members.',
            'team_member_emails.*.required' => 'Please enter an email address.',
            'team_member_emails.*.email' => 'Please enter a valid email address.',
            'team_member_emails.*.max' => 'Email address cannot exceed 255 characters.',
            'team_member_emails.*.different' => 'You cannot invite yourself.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Filter out empty email fields
        if ($this->has('team_member_emails')) {
            $this->merge([
                'team_member_emails' => array_filter(
                    $this->team_member_emails ?? [],
                    fn ($email) => !empty($email)
                ),
            ]);
        }
    }
}
