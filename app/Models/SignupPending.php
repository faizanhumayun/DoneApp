<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SignupPending extends Model
{
    /** @use HasFactory<\Database\Factories\SignupPendingFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'signup_pending';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token',
        'token_expires_at',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Check if the token is expired.
     */
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at->isPast();
    }

    /**
     * Check if the email is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Mark the email as verified.
     */
    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Generate a new verification token.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
