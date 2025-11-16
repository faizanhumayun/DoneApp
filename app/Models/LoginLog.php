<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'session_duration',
    ];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    /**
     * Get the user for this login log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company for this login log.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get device name from user agent.
     */
    public function getDeviceAttribute(): string
    {
        $ua = $this->user_agent ?? '';

        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) {
            return 'Mobile';
        } elseif (stripos($ua, 'iPad') !== false || stripos($ua, 'Tablet') !== false) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Get formatted duration.
     */
    public function getDurationAttribute(): string
    {
        if (!$this->session_duration) {
            return 'Active';
        }

        $hours = floor($this->session_duration / 3600);
        $minutes = floor(($this->session_duration % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
