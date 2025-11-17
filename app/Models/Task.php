<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'priority',
        'workflow_status_id',
        'assignee_id',
        'created_by',
        'due_date',
        'task_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the workflow status of the task.
     */
    public function workflowStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowStatus::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get the user who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tags for the task.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tags')
            ->withTimestamps();
    }

    /**
     * Get the watchers for the task.
     */
    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_watchers')
            ->withTimestamps();
    }

    /**
     * Get the comments for the task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the discussions linked to this task.
     */
    public function discussions(): BelongsToMany
    {
        return $this->belongsToMany(Discussion::class, 'discussion_task_links')
            ->withTimestamps();
    }

    /**
     * Get the activity logs for the task.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class);
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => '#10B981',      // Green
            'medium' => '#F59E0B',   // Yellow
            'high' => '#EF4444',     // Orange/Red
            'critical' => '#DC2626', // Dark Red
            default => '#6B7280',    // Gray
        };
    }

    /**
     * Get the priority text color for contrast.
     */
    public function getPriorityTextColorAttribute(): string
    {
        return '#FFFFFF'; // White text for all priority badges
    }

    /**
     * Log an activity for this task.
     */
    public function logActivity(string $action, string $description, $oldValue = null, $newValue = null): void
    {
        $this->activityLogs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
