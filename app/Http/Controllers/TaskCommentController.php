<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCommentRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Services\TaskCommentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public function __construct(
        protected TaskCommentService $commentService
    ) {}

    /**
     * Store a newly created comment.
     */
    public function store(StoreTaskCommentRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->commentService->addComment(
            $task,
            $request->validated(),
            Auth::user()
        );

        return redirect()
            ->route('tasks.show', [$project, $task])
            ->with('message', 'Comment added successfully.')
            ->with('scroll_to', 'comments');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Project $project, Task $task, TaskComment $comment): RedirectResponse
    {
        // Check permissions - only owner, admin, or comment author can delete
        $userRole = $project->users()
            ->where('user_id', Auth::id())
            ->first()
            ?->pivot
            ->role ?? 'member';

        if (!in_array($userRole, ['owner', 'admin']) && $comment->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this comment.');
        }

        $this->commentService->deleteComment($comment, Auth::user());

        return redirect()
            ->route('tasks.show', [$project, $task])
            ->with('message', 'Comment deleted successfully.')
            ->with('scroll_to', 'comments');
    }
}
