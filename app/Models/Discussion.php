<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'project_id',
        'company_id',
        'created_by',
        'is_private',
        'type',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Get the company that owns the discussion.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project that owns the discussion (optional).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the discussion.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the participants of the discussion.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discussion_participants')
            ->withTimestamps();
    }

    /**
     * Get the comments for the discussion.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DiscussionComment::class);
    }

    /**
     * Get the attachments for the discussion.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DiscussionAttachment::class)
            ->whereNull('discussion_comment_id');
    }

    /**
     * Get the tasks linked to this discussion.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'discussion_task_links')
            ->withTimestamps();
    }

    /**
     * Check if user can view this discussion.
     */
    public function canView(User $user): bool
    {
        // If public, check if user is in the company or project
        if (!$this->is_private) {
            if ($this->project_id) {
                // Public discussion in a project - check project membership
                return $this->project->users()->where('user_id', $user->id)->exists();
            }
            // Public standalone discussion - check company membership
            return $this->company->users()->where('user_id', $user->id)->exists();
        }

        // If private, user must be creator or participant
        if ($this->created_by === $user->id) {
            return true;
        }

        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Optional: Owners and Admins can view private discussions
        $userRole = $user->companies()
            ->where('companies.id', $this->company_id)
            ->first()
            ?->pivot
            ?->role;

        return in_array($userRole, ['owner', 'admin']);
    }

    /**
     * Scope to filter discussions visible to a specific user.
     */
    public function scopeVisibleTo($query, User $user, $companyId)
    {
        return $query->where(function ($q) use ($user, $companyId) {
            // Public discussions in the company
            $q->where(function ($subQ) use ($companyId) {
                $subQ->where('company_id', $companyId)
                     ->where('is_private', false);
            })
            // Or private discussions where user is creator or participant
            ->orWhere(function ($subQ) use ($user) {
                $subQ->where('is_private', true)
                     ->where(function ($privateQ) use ($user) {
                         $privateQ->where('created_by', $user->id)
                                  ->orWhereHas('participants', function ($partQ) use ($user) {
                                      $partQ->where('user_id', $user->id);
                                  });
                     });
            });
        });
    }
}
