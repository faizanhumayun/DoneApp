<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'size',
        'industry',
    ];

    /**
     * Get the users that belong to the company.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the invitations for the company.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the workflows for the company.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    /**
     * Get the owner of the company.
     */
    public function owner(): ?User
    {
        return $this->users()->wherePivot('role', 'owner')->first();
    }

    /**
     * Create default built-in workflows for the company.
     */
    public function createDefaultWorkflows(): void
    {
        $defaultWorkflows = [
            [
                'name' => 'Basic Task Tracking',
                'description' => 'A simple workflow to manage basic tasks.',
                'statuses' => [
                    ['name' => 'Open', 'color' => '#3B82F6', 'is_active' => true],
                    ['name' => 'In Progress', 'color' => '#F59E0B', 'is_active' => true],
                    ['name' => 'Ready for Review', 'color' => '#8B5CF6', 'is_active' => true],
                    ['name' => 'Closed', 'color' => '#6B7280', 'is_active' => true],
                ],
            ],
            [
                'name' => 'Bug Tracking',
                'description' => 'A workflow to identify, track, and resolve bugs.',
                'statuses' => [
                    ['name' => 'Open', 'color' => '#EF4444', 'is_active' => true],
                    ['name' => 'In Progress', 'color' => '#F59E0B', 'is_active' => true],
                    ['name' => 'Not a Bug', 'color' => '#6B7280', 'is_active' => true],
                    ['name' => 'Not Reproducible', 'color' => '#71717A', 'is_active' => true],
                    ['name' => 'Missing Information', 'color' => '#F97316', 'is_active' => true],
                    ['name' => 'Pushed Back', 'color' => '#EC4899', 'is_active' => true],
                    ['name' => 'Ready for Next Release', 'color' => '#8B5CF6', 'is_active' => true],
                    ['name' => 'Ready for Retest', 'color' => '#3B82F6', 'is_active' => true],
                    ['name' => 'Fix not Confirmed', 'color' => '#06B6D4', 'is_active' => true],
                    ['name' => 'Fixed', 'color' => '#10B981', 'is_active' => false],
                    ['name' => 'On Hold', 'color' => '#64748B', 'is_active' => false],
                    ['name' => 'Duplicate Bug', 'color' => '#78716C', 'is_active' => false],
                ],
            ],
        ];

        foreach ($defaultWorkflows as $workflowData) {
            $workflow = $this->workflows()->create([
                'name' => $workflowData['name'],
                'description' => $workflowData['description'],
                'is_builtin' => true,
                'created_by' => $this->owner()?->id,
            ]);

            foreach ($workflowData['statuses'] as $index => $statusData) {
                $workflow->statuses()->create([
                    'name' => $statusData['name'],
                    'color' => $statusData['color'],
                    'is_active' => $statusData['is_active'],
                    'position' => $index,
                ]);
            }
        }
    }
}
