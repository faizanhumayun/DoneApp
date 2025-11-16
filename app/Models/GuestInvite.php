<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GuestInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'token',
        'token_expires_at',
        'invited_by_user_id',
        'company_id',
        'personal_message',
        'invited_from_type',
        'invited_from_id',
        'is_accepted',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'is_accepted' => 'boolean',
        ];
    }

    /**
     * Get the user who sent the invitation.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    /**
     * Get the company this invitation belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Generate a unique invitation token.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if the invitation has expired.
     */
    public function isExpired(): bool
    {
        return $this->token_expires_at->isPast();
    }

    /**
     * Check if the invitation is still valid.
     */
    public function isValid(): bool
    {
        return !$this->is_accepted && !$this->isExpired();
    }

    /**
     * Mark the invitation as accepted.
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'is_accepted' => true,
            'accepted_at' => now(),
        ]);
    }
}
