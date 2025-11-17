<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workflow_id',
        'name',
        'color',
        'is_active',
        'is_final',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_final' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * Get the workflow that owns the status.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the text color based on background color for better visibility.
     */
    public function getTextColorAttribute(): string
    {
        // Convert hex to RGB
        $hex = ltrim($this->color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate relative luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Return white for dark backgrounds, black for light backgrounds
        return $luminance > 0.5 ? '#1b1b18' : '#ffffff';
    }
}
