<x-mail::message>
# You've been invited!

Hello {{ $invite->first_name }},

**{{ $invite->invitedBy->full_name }}** from **{{ $invite->company->name }}** has invited you to collaborate as a guest.

@if ($invite->personal_message)
> {{ $invite->personal_message }}
@endif

As a guest, you'll be able to:
- View and comment on tasks you're assigned to
- Participate in discussions you're invited to
- Collaborate with the team on specific projects

<x-mail::button :url="$signupUrl">
Complete Your Registration
</x-mail::button>

This invitation will expire on {{ $invite->token_expires_at->format('F j, Y \a\t g:i A') }}.

If you have any questions, please contact {{ $invite->invitedBy->full_name }} at {{ $invite->invitedBy->email }}.

Thanks,<br>
{{ config('app.name') }}

---

<small>If you did not expect this invitation, you can safely ignore this email.</small>
</x-mail::message>
