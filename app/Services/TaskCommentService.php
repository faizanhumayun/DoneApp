<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskCommentService
{
    /**
     * Add a comment to a task.
     */
    public function addComment(Task $task, array $data, User $user): TaskComment
    {
        return DB::transaction(function () use ($task, $data, $user) {
            $comment = TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'comment' => $data['comment'],
                'attachments' => $data['attachments'] ?? null,
            ]);

            // Log activity
            $task->logActivity(
                'comment_added',
                "{$user->full_name} added a comment."
            );

            return $comment->load('user');
        });
    }

    /**
     * Update a comment.
     */
    public function updateComment(TaskComment $comment, array $data, User $user): TaskComment
    {
        $comment->update([
            'comment' => $data['comment'],
            'attachments' => $data['attachments'] ?? $comment->attachments,
        ]);

        // Log activity
        $comment->task->logActivity(
            'comment_updated',
            "{$user->full_name} updated a comment."
        );

        return $comment->load('user');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(TaskComment $comment, User $user): bool
    {
        // Log activity
        $comment->task->logActivity(
            'comment_deleted',
            "{$user->full_name} deleted a comment."
        );

        return $comment->delete();
    }

    /**
     * Get all comments for a task.
     */
    public function getComments(Task $task)
    {
        return $task->comments()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
