<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'timezone',
        'about_yourself',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the companies that the user belongs to.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's avatar URL.
     * Returns uploaded image or auto-generated avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_image && file_exists(public_path('storage/' . $this->profile_image))) {
            return asset('storage/' . $this->profile_image);
        }

        // Generate avatar using UI Avatars with user's initials
        $name = urlencode($this->first_name . ' ' . $this->last_name);
        return "https://ui-avatars.com/api/?name={$name}&size=200&background=3B82F6&color=ffffff&bold=true";
    }

    /**
     * Get the user's initials for avatar.
     */
    public function getInitialsAttribute(): string
    {
        $firstInitial = strtoupper(substr($this->first_name, 0, 1));
        $lastInitial = strtoupper(substr($this->last_name, 0, 1));
        return $firstInitial . $lastInitial;
    }
}
