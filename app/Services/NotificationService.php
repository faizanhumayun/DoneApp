<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a mention notification.
     */
    public function createMentionNotification(User $mentionedUser, User $mentioner, string $context, string $link): void
    {
        $company = $mentionedUser->companies->first();

        if (!$company) {
            return;
        }

        Notification::create([
            'user_id' => $mentionedUser->id,
            'company_id' => $company->id,
            'type' => 'mention',
            'title' => "{$mentioner->full_name} mentioned you",
            'message' => "You were mentioned in {$context}",
            'data' => [
                'link' => $link,
                'mentioner_id' => $mentioner->id,
            ],
        ]);
    }

    /**
     * Create an invite notification.
     */
    public function createInviteNotification(User $invitedUser, User $inviter, string $role): void
    {
        $company = $invitedUser->companies->first();

        if (!$company) {
            return;
        }

        Notification::create([
            'user_id' => $invitedUser->id,
            'company_id' => $company->id,
            'type' => 'invite',
            'title' => 'New Team Invitation',
            'message' => "{$inviter->full_name} invited you to join as {$role}",
            'data' => [
                'link' => route('users.index'),
                'inviter_id' => $inviter->id,
                'role' => $role,
            ],
        ]);
    }

    /**
     * Create a task assignment notification.
     */
    public function createTaskAssignmentNotification(User $assignee, User $assigner, $task, $project): void
    {
        $company = $assignee->companies->first();

        if (!$company) {
            return;
        }

        Notification::create([
            'user_id' => $assignee->id,
            'company_id' => $company->id,
            'type' => 'workspace',
            'title' => 'New Task Assignment',
            'message' => "{$assigner->full_name} assigned you to: {$task->title}",
            'data' => [
                'link' => route('tasks.show', [$project, $task]),
                'task_id' => $task->id,
                'assigner_id' => $assigner->id,
            ],
        ]);
    }

    /**
     * Create a comment notification.
     */
    public function createCommentNotification(User $recipient, User $commenter, string $context, string $link): void
    {
        $company = $recipient->companies->first();

        if (!$company) {
            return;
        }

        // Don't notify if commenter is the recipient
        if ($recipient->id === $commenter->id) {
            return;
        }

        Notification::create([
            'user_id' => $recipient->id,
            'company_id' => $company->id,
            'type' => 'conversation',
            'title' => 'New Comment',
            'message' => "{$commenter->full_name} commented on {$context}",
            'data' => [
                'link' => $link,
                'commenter_id' => $commenter->id,
            ],
        ]);
    }

    /**
     * Extract mentioned user IDs from HTML content.
     */
    public function extractMentions(string $content): array
    {
        $mentionedUserIds = [];

        // Parse HTML to find mention spans
        // Quill mention plugin creates: <span class="mention" data-id="USER_ID">@Name</span>
        if (preg_match_all('/<span[^>]*class="mention"[^>]*data-id="(\d+)"[^>]*>/', $content, $matches)) {
            $mentionedUserIds = array_unique($matches[1]);
        }

        return $mentionedUserIds;
    }

    /**
     * Create notifications for all mentioned users.
     */
    public function notifyMentionedUsers(string $content, User $mentioner, string $context, string $link): void
    {
        $mentionedUserIds = $this->extractMentions($content);

        foreach ($mentionedUserIds as $userId) {
            $mentionedUser = User::find($userId);
            if ($mentionedUser && $mentionedUser->id !== $mentioner->id) {
                $this->createMentionNotification($mentionedUser, $mentioner, $context, $link);
            }
        }
    }
}
