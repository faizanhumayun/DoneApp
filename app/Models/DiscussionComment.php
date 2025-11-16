<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscussionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'body',
    ];

    /**
     * Get the discussion that owns the comment.
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Get the user who created the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachments for the comment.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DiscussionAttachment::class);
    }
}
