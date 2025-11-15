<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'description',
        'is_builtin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_builtin' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the workflow.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the workflow.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the statuses for the workflow.
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(WorkflowStatus::class)->orderBy('position');
    }

    /**
     * Get only active statuses for the workflow.
     */
    public function activeStatuses(): HasMany
    {
        return $this->hasMany(WorkflowStatus::class)
            ->where('is_active', true)
            ->orderBy('position');
    }

    /**
     * Check if the workflow can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Add logic to check if workflow is used by tasks
        // For now, allow deletion unless it's a built-in workflow
        return !$this->is_builtin;
    }

    /**
     * Duplicate the workflow with all its statuses.
     */
    public function duplicate(): self
    {
        $newWorkflow = $this->replicate();
        $newWorkflow->name = 'Copy of ' . $this->name;
        $newWorkflow->is_builtin = false;
        $newWorkflow->created_by = auth()->id(); // Set current user as creator of duplicate
        $newWorkflow->save();

        // Duplicate all statuses
        foreach ($this->statuses as $status) {
            $newStatus = $status->replicate();
            $newStatus->workflow_id = $newWorkflow->id;
            $newStatus->save();
        }

        return $newWorkflow;
    }
}
