<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'workflow_id',
        'created_by',
        'name',
        'description',
        'estimated_cost',
        'billable_resource',
        'non_billable_resource',
        'total_estimated_hours',
        'status',
        'archived_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:2',
            'billable_resource' => 'decimal:2',
            'non_billable_resource' => 'decimal:2',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Get the company that owns the project.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the workflow assigned to the project.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the user who created the project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users (team members) assigned to the project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Check if the project can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Add logic to check if project has tasks
        // For now, allow deletion for all projects
        return true;
    }

    /**
     * Archive the project.
     */
    public function archive(): void
    {
        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);
    }

    /**
     * Restore the project from archive.
     */
    public function restore(): void
    {
        $this->update([
            'status' => 'active',
            'archived_at' => null,
        ]);
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include archived projects.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}
