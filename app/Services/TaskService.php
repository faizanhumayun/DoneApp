<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected AttachmentService $attachmentService
    ) {}

    /**
     * Create a new task.
     */
    public function createTask(array $data, Project $project, User $user): Task
    {
        return DB::transaction(function () use ($data, $project, $user) {
            // Generate task number
            $taskNumber = $this->generateTaskNumber($project);

            // Create the task
            $task = Task::create([
                'project_id' => $project->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? 'low',
                'workflow_status_id' => $data['workflow_status_id'] ?? $this->getDefaultWorkflowStatus($project)->id,
                'assignee_id' => $data['assignee_id'] ?? null,
                'created_by' => $user->id,
                'due_date' => $data['due_date'] ?? null,
                'task_number' => $taskNumber,
            ]);

            // Attach tags if provided
            if (!empty($data['tags'])) {
                $this->syncTags($task, $data['tags']);
            }

            // Attach watchers (creator is always a watcher)
            $watchers = $data['watchers'] ?? [];
            if (!in_array($user->id, $watchers)) {
                $watchers[] = $user->id;
            }
            $task->watchers()->sync($watchers);

            // Log activity
            $task->logActivity(
                'created',
                "{$user->full_name} created this task."
            );

            // Handle file attachments if provided
            if (!empty($data['attachments'])) {
                \Log::info('Processing task attachments', [
                    'task_id' => $task->id,
                    'attachments_count' => count($data['attachments']),
                    'storage_disk' => $data['storage_disk'] ?? 'local'
                ]);
                $this->handleAttachments($task, $data['attachments'], $data['storage_disk'] ?? 'local', $user);
            }

            // Notify assignee if task is assigned to someone
            if ($task->assignee_id && $task->assignee_id !== $user->id) {
                $this->notificationService->createTaskAssignmentNotification(
                    $task->assignee,
                    $user,
                    $task,
                    $project
                );
            }

            return $task->load(['project', 'workflowStatus', 'assignee', 'creator', 'tags', 'watchers', 'attachments']);
        });
    }

    /**
     * Handle file attachments for a task.
     */
    protected function handleAttachments(Task $task, array $attachments, string $storageDisk, User $user): void
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($attachments as $index => $attachmentData) {
            // Skip empty attachments
            if (empty($attachmentData)) {
                continue;
            }

            \Log::info("Processing attachment #{$index}", [
                'data_length' => strlen($attachmentData),
                'is_base64' => strpos($attachmentData, 'data:') === 0,
            ]);

            // Convert base64 to file
            if (strpos($attachmentData, 'data:') === 0) {
                try {
                    // Decode base64 data
                    list($type, $data) = explode(';', $attachmentData);
                    list(, $data) = explode(',', $data);
                    $decodedData = base64_decode($data);

                    // Extract MIME type
                    $mimeType = str_replace('data:', '', $type);

                    \Log::info("Decoded attachment #{$index}", [
                        'mime_type' => $mimeType,
                        'decoded_size' => strlen($decodedData),
                    ]);

                    // Generate temp file
                    $tempFile = tmpfile();
                    $tempFilePath = stream_get_meta_data($tempFile)['uri'];
                    fwrite($tempFile, $decodedData);

                    // Create UploadedFile instance
                    $extension = $this->getExtensionFromMimeType($mimeType);
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempFilePath,
                        'file.' . $extension,
                        $mimeType,
                        null,
                        true
                    );

                    \Log::info("Uploading attachment #{$index}", [
                        'extension' => $extension,
                        'storage_disk' => $storageDisk,
                    ]);

                    // Upload file (pass MIME type to avoid fileinfo dependency)
                    $attachment = $this->attachmentService->uploadFile(
                        $uploadedFile,
                        Task::class,
                        $task->id,
                        $user,
                        $storageDisk,
                        $mimeType
                    );

                    \Log::info("Successfully uploaded attachment #{$index}", [
                        'attachment_id' => $attachment->id,
                        'file_path' => $attachment->file_path,
                    ]);

                    $successCount++;

                    // Close temp file
                    fclose($tempFile);
                } catch (\Exception $e) {
                    $failureCount++;
                    // Log detailed error information
                    \Log::error("Failed to upload attachment #{$index}: " . $e->getMessage(), [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the entire task creation, just skip this attachment
                }
            } else {
                \Log::warning("Skipping attachment #{$index} - not base64 encoded");
            }
        }

        \Log::info('Attachment processing complete', [
            'task_id' => $task->id,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
        ]);
    }

    /**
     * Get file extension from MIME type.
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/zip' => 'zip',
            'text/plain' => 'txt',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data, User $user): Task
    {
        return DB::transaction(function () use ($task, $data, $user) {
            $oldValues = $task->toArray();

            // Update basic fields
            $task->fill([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'workflow_status_id' => $data['workflow_status_id'] ?? $task->workflow_status_id,
                'assignee_id' => $data['assignee_id'] ?? $task->assignee_id,
                'due_date' => $data['due_date'] ?? $task->due_date,
            ]);

            // Log changes
            if ($task->isDirty('title')) {
                $task->logActivity('title_changed', "{$user->full_name} changed the title.", $oldValues['title'], $task->title);
            }

            if ($task->isDirty('description')) {
                $task->logActivity('description_changed', "{$user->full_name} updated the description.");
            }

            if ($task->isDirty('priority')) {
                $task->logActivity('priority_changed', "{$user->full_name} changed priority from {$oldValues['priority']} to {$task->priority}.", $oldValues['priority'], $task->priority);
            }

            if ($task->isDirty('workflow_status_id')) {
                $oldStatus = $task->workflowStatus->name ?? 'Unknown';
                $newStatus = \App\Models\WorkflowStatus::find($task->workflow_status_id)->name ?? 'Unknown';
                $task->logActivity('status_changed', "{$user->full_name} changed status from {$oldStatus} to {$newStatus}.", $oldStatus, $newStatus);
            }

            if ($task->isDirty('assignee_id')) {
                $oldAssignee = $oldValues['assignee_id'] ? User::find($oldValues['assignee_id'])?->full_name : 'Unassigned';
                $newAssignee = $task->assignee_id ? User::find($task->assignee_id)?->full_name : 'Unassigned';
                $task->logActivity('assignee_changed', "{$user->full_name} changed assignee from {$oldAssignee} to {$newAssignee}.", $oldAssignee, $newAssignee);

                // Notify new assignee if task is assigned to someone
                if ($task->assignee_id && $task->assignee_id !== $user->id) {
                    $this->notificationService->createTaskAssignmentNotification(
                        $task->assignee,
                        $user,
                        $task,
                        $task->project
                    );
                }
            }

            if ($task->isDirty('due_date')) {
                $task->logActivity('due_date_changed', "{$user->full_name} changed the due date.", $oldValues['due_date'], $task->due_date);
            }

            $task->save();

            // Update tags if provided
            if (isset($data['tags'])) {
                $this->syncTags($task, $data['tags']);
            }

            // Update watchers if provided
            if (isset($data['watchers'])) {
                $task->watchers()->sync($data['watchers']);
            }

            return $task->load(['project', 'workflowStatus', 'assignee', 'creator', 'tags', 'watchers']);
        });
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task, User $user): bool
    {
        $task->logActivity('deleted', "{$user->full_name} deleted this task.");

        return $task->delete();
    }

    /**
     * Duplicate a task.
     */
    public function duplicateTask(Task $task, User $user): Task
    {
        return DB::transaction(function () use ($task, $user) {
            $newTask = $task->replicate();
            $newTask->task_number = $this->generateTaskNumber($task->project);
            $newTask->created_by = $user->id;
            $newTask->title = $task->title . ' (Copy)';
            $newTask->save();

            // Copy tags
            $newTask->tags()->sync($task->tags->pluck('id'));

            // Copy watchers
            $newTask->watchers()->sync($task->watchers->pluck('id'));

            // Log activity
            $newTask->logActivity('created', "{$user->full_name} duplicated this task from {$task->task_number}.");

            return $newTask->load(['project', 'workflowStatus', 'assignee', 'creator', 'tags', 'watchers']);
        });
    }

    /**
     * Generate a unique task number for the project.
     */
    protected function generateTaskNumber(Project $project): string
    {
        // Get project prefix (first 3-4 letters of project name in uppercase)
        $prefix = strtoupper(Str::limit(str_replace(' ', '', $project->name), 4, ''));

        // Get the last task number for this project
        $lastTask = Task::where('project_id', $project->id)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTask ? (int) explode('-', $lastTask->task_number)[1] + 1 : 1;

        return "{$prefix}-{$number}";
    }

    /**
     * Get the default workflow status for a project.
     */
    protected function getDefaultWorkflowStatus(Project $project)
    {
        return $project->workflow->statuses()
            ->where('is_active', true)
            ->orderBy('order')
            ->first();
    }

    /**
     * Sync tags for a task (create new tags if they don't exist).
     */
    protected function syncTags(Task $task, array $tagNames): void
    {
        $companyId = $task->project->company_id;
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            if (empty($tagName)) {
                continue;
            }

            // Find or create tag
            $tag = \App\Models\Tag::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'name' => $tagName,
                ],
                [
                    'color' => $this->getRandomTagColor(),
                ]
            );

            $tagIds[] = $tag->id;
        }

        $task->tags()->sync($tagIds);
    }

    /**
     * Get a random color for tags.
     */
    protected function getRandomTagColor(): string
    {
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#14B8A6', // Teal
            '#F97316', // Orange
        ];

        return $colors[array_rand($colors)];
    }
}
