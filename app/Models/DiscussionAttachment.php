<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'discussion_comment_id',
        'filename',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Get the discussion that owns the attachment.
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Get the comment that owns the attachment.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(DiscussionComment::class, 'discussion_comment_id');
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
