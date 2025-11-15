<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Verification Token Expiry
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in hours) the email verification token
    | remains valid before expiring. Default is 24 hours.
    |
    */

    'verification_token_expiry_hours' => env('SIGNUP_VERIFICATION_EXPIRY', 24),

    /*
    |--------------------------------------------------------------------------
    | Invitation Token Expiry
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in days) the team invitation token
    | remains valid before expiring. Default is 7 days.
    |
    */

    'invitation_token_expiry_days' => env('SIGNUP_INVITATION_EXPIRY', 7),

    /*
    |--------------------------------------------------------------------------
    | Maximum Team Invitations Per Signup
    |--------------------------------------------------------------------------
    |
    | The maximum number of team members that can be invited during the
    | signup process (Step 3).
    |
    */

    'max_invitations_per_signup' => 10,

    /*
    |--------------------------------------------------------------------------
    | Company Size Options
    |--------------------------------------------------------------------------
    |
    | Available company size options for the signup form.
    |
    */

    'company_sizes' => [
        '1-10' => '1-10 employees',
        '11-50' => '11-50 employees',
        '51-200' => '51-200 employees',
        '201-1000' => '201-1000 employees',
        '1000+' => '1000+ employees',
    ],

    /*
    |--------------------------------------------------------------------------
    | Industry Types
    |--------------------------------------------------------------------------
    |
    | Available industry types for the signup form.
    |
    */

    'industries' => [
        'Technology' => 'Technology',
        'Healthcare' => 'Healthcare',
        'Finance' => 'Finance',
        'Education' => 'Education',
        'Retail' => 'Retail',
        'Manufacturing' => 'Manufacturing',
        'Marketing' => 'Marketing',
        'Consulting' => 'Consulting',
        'Real Estate' => 'Real Estate',
        'Legal' => 'Legal',
        'Media' => 'Media',
        'Hospitality' => 'Hospitality',
        'Other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timezones
    |--------------------------------------------------------------------------
    |
    | Available timezone options for the signup form.
    | Using common timezones for better UX.
    |
    */

    'timezones' => [
        'America/New_York' => '(UTC-05:00) Eastern Time',
        'America/Chicago' => '(UTC-06:00) Central Time',
        'America/Denver' => '(UTC-07:00) Mountain Time',
        'America/Los_Angeles' => '(UTC-08:00) Pacific Time',
        'America/Anchorage' => '(UTC-09:00) Alaska',
        'Pacific/Honolulu' => '(UTC-10:00) Hawaii',
        'Europe/London' => '(UTC+00:00) London',
        'Europe/Paris' => '(UTC+01:00) Paris',
        'Europe/Athens' => '(UTC+02:00) Athens',
        'Europe/Moscow' => '(UTC+03:00) Moscow',
        'Asia/Dubai' => '(UTC+04:00) Dubai',
        'Asia/Kolkata' => '(UTC+05:30) Mumbai',
        'Asia/Shanghai' => '(UTC+08:00) Beijing',
        'Asia/Tokyo' => '(UTC+09:00) Tokyo',
        'Australia/Sydney' => '(UTC+10:00) Sydney',
        'Pacific/Auckland' => '(UTC+12:00) Auckland',
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Requirements
    |--------------------------------------------------------------------------
    |
    | Password validation requirements for user registration.
    |
    */

    'password' => [
        'min_length' => 8,
        'require_letter' => true,
        'require_number' => true,
        'require_special_char' => true,
    ],

];
