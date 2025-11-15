<?php

namespace App\Notifications;

use App\Models\SignupPending;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailSignup extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SignupPending $signupPending,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = route('signup.verify', ['token' => $this->signupPending->token]);

        return (new MailMessage())
            ->subject('Verify your email to get started')
            ->greeting('Hello!')
            ->line('Thanks for signing up! Please verify your email address to continue setting up your account.')
            ->action('Verify Your Email', $verificationUrl)
            ->line('This verification link will expire in ' . config('signup.verification_token_expiry_hours') . ' hours.')
            ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'signup_pending_id' => $this->signupPending->id,
            'email' => $this->signupPending->email,
        ];
    }
}
