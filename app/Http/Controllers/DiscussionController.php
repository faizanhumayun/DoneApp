<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionComment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiscussionController extends Controller
{
    /**
     * Display a listing of discussions.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Build query for discussions visible to this user
        $query = Discussion::visibleTo($user, $company->id)
            ->with(['creator', 'project', 'participants', 'comments'])
            ->withCount('comments');

        // Filter by project
        if ($request->filled('project')) {
            if ($request->get('project') === 'standalone') {
                $query->whereNull('project_id');
            } else {
                $query->where('project_id', $request->get('project'));
            }
        }

        // Filter by type
        if ($request->filled('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }

        // Filter by privacy
        if ($request->filled('privacy')) {
            if ($request->get('privacy') === 'public') {
                $query->where('is_private', false);
            } elseif ($request->get('privacy') === 'private') {
                $query->where('is_private', true);
            }
        }

        // Filter "Only discussions I'm in"
        if ($request->filled('my_discussions') && $request->get('my_discussions') === 'true') {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('participants', function ($partQ) use ($user) {
                      $partQ->where('user_id', $user->id);
                  });
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Sort by most recently updated
        $discussions = $query->orderBy('updated_at', 'desc')->paginate(20);

        // Get filter options
        $projects = $company->projects()->orderBy('name')->get();

        return view('discussions.index', compact('discussions', 'projects'));
    }

    /**
     * Show the form for creating a new discussion.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Get all projects the user has access to
        $projects = $company->projects()->orderBy('name')->get();

        // Get context from request (if coming from a task)
        $fromTask = null;
        if ($request->filled('task_id')) {
            $fromTask = Task::find($request->get('task_id'));
        }

        // Get all company members (including guests) for invite list
        $companyMembers = $company->users()
            ->orderBy('first_name')
            ->get();

        return view('discussions.create', compact('projects', 'fromTask', 'companyMembers'));
    }

    /**
     * Store a newly created discussion.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->companies->first();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'type' => ['nullable', 'string', 'in:General,Design,Engineering,Support,Announcement'],
            'is_private' => ['boolean'],
            'participants' => ['nullable', 'array'],
            'participants.*' => ['exists:users,id'],
            'related_tasks' => ['nullable', 'array'],
            'related_tasks.*' => ['exists:tasks,id'],
        ]);

        // If private, participants are required
        if ($validated['is_private'] ?? false) {
            if (empty($validated['participants'])) {
                return back()->withErrors(['participants' => 'Private discussions must have at least one invited member.'])->withInput();
            }
        }

        $discussion = DB::transaction(function () use ($validated, $user, $company) {
            // Create discussion
            $discussion = Discussion::create([
                'title' => $validated['title'],
                'body' => $validated['body'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'company_id' => $company->id,
                'created_by' => $user->id,
                'is_private' => $validated['is_private'] ?? false,
                'type' => $validated['type'] ?? null,
            ]);

            // Add participants (if any)
            if (!empty($validated['participants'])) {
                $discussion->participants()->attach($validated['participants']);
            }

            // Always add creator as participant for private discussions
            if ($discussion->is_private && !in_array($user->id, $validated['participants'] ?? [])) {
                $discussion->participants()->attach($user->id);
            }

            // Link related tasks
            if (!empty($validated['related_tasks'])) {
                $discussion->tasks()->attach($validated['related_tasks']);
            }

            return $discussion;
        });

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Discussion created successfully.');
    }

    /**
     * Display the specified discussion.
     */
    public function show(Discussion $discussion): View
    {
        $user = Auth::user();

        // Check if user can view this discussion
        if (!$discussion->canView($user)) {
            abort(403, 'You don\'t have permission to view this discussion.');
        }

        $discussion->load([
            'creator',
            'project',
            'participants',
            'comments.user',
            'comments.attachments',
            'attachments',
            'tasks.project',
        ]);

        // Get available members to invite
        $availableMembers = collect();
        if ($discussion->project_id) {
            $availableMembers = $discussion->project->users()
                ->whereNotIn('users.id', $discussion->participants->pluck('id'))
                ->get();
        } else {
            $availableMembers = $discussion->company->users()
                ->whereNotIn('users.id', $discussion->participants->pluck('id'))
                ->get();
        }

        // Get all team members for mentions (project or company based)
        $allMembers = $discussion->project_id
            ? $discussion->project->users
            : $discussion->company->users;

        // Transform team members for mentions
        $teamMembers = $allMembers->map(function($member) {
            return [
                'id' => $member->id,
                'value' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
            ];
        })->values();

        return view('discussions.show', compact('discussion', 'availableMembers', 'teamMembers'));
    }

    /**
     * Show the form for editing the discussion.
     */
    public function edit(Discussion $discussion): View
    {
        $user = Auth::user();

        // Only creator, owner, or admin can edit
        $userRole = $user->getCompanyRole();
        if ($discussion->created_by !== $user->id && !in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You don\'t have permission to edit this discussion.');
        }

        $company = $user->companies->first();
        $projects = $company->projects()->orderBy('name')->get();

        // Get project members if project is set
        $projectMembers = collect();
        if ($discussion->project_id) {
            $projectMembers = $discussion->project->users;
        }

        return view('discussions.edit', compact('discussion', 'projects', 'projectMembers'));
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, Discussion $discussion): RedirectResponse
    {
        $user = Auth::user();

        // Only creator, owner, or admin can update
        $userRole = $user->getCompanyRole();
        if ($discussion->created_by !== $user->id && !in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You don\'t have permission to edit this discussion.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'type' => ['nullable', 'string', 'in:General,Design,Engineering,Support,Announcement'],
            'is_private' => ['boolean'],
            'participants' => ['nullable', 'array'],
            'participants.*' => ['exists:users,id'],
        ]);

        DB::transaction(function () use ($validated, $discussion, $user) {
            $discussion->update([
                'title' => $validated['title'],
                'body' => $validated['body'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'is_private' => $validated['is_private'] ?? false,
                'type' => $validated['type'] ?? null,
            ]);

            // Update participants
            if (isset($validated['participants'])) {
                $participants = $validated['participants'];
                // Always include creator for private discussions
                if ($discussion->is_private && !in_array($user->id, $participants)) {
                    $participants[] = $user->id;
                }
                $discussion->participants()->sync($participants);
            }
        });

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Discussion updated successfully.');
    }

    /**
     * Remove the specified discussion.
     */
    public function destroy(Discussion $discussion): RedirectResponse
    {
        $user = Auth::user();

        // Only creator, owner, or admin can delete
        $userRole = $user->getCompanyRole();
        if ($discussion->created_by !== $user->id && !in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You don\'t have permission to delete this discussion.');
        }

        $discussion->delete();

        return redirect()->route('discussions.index')
            ->with('success', 'Discussion deleted successfully.');
    }

    /**
     * Store a new comment on the discussion.
     */
    public function storeComment(Request $request, Discussion $discussion): RedirectResponse
    {
        $user = Auth::user();

        // Check if user can view this discussion
        if (!$discussion->canView($user)) {
            abort(403, 'You don\'t have permission to comment on this discussion.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        DiscussionComment::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // Update discussion's updated_at timestamp
        $discussion->touch();

        // Create notifications for mentions
        $notificationService = app(\App\Services\NotificationService::class);
        $link = route('discussions.show', $discussion) . '#comments';
        $notificationService->notifyMentionedUsers(
            $validated['body'],
            $user,
            "a comment on discussion: {$discussion->title}",
            $link
        );

        // Notify discussion participants about new comment
        foreach ($discussion->participants as $participant) {
            if ($participant->id !== $user->id) {
                $notificationService->createCommentNotification(
                    $participant,
                    $user,
                    "discussion: {$discussion->title}",
                    $link
                );
            }
        }

        // Notify discussion creator if not already notified
        if ($discussion->creator && $discussion->creator->id !== $user->id) {
            $alreadyNotified = $discussion->participants->contains('id', $discussion->creator->id);
            if (!$alreadyNotified) {
                $notificationService->createCommentNotification(
                    $discussion->creator,
                    $user,
                    "discussion: {$discussion->title}",
                    $link
                );
            }
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Delete a comment.
     */
    public function destroyComment(Discussion $discussion, DiscussionComment $comment): RedirectResponse
    {
        $user = Auth::user();

        // Only comment author, discussion creator, owner, or admin can delete
        $userRole = $user->getCompanyRole();
        if ($comment->user_id !== $user->id &&
            $discussion->created_by !== $user->id &&
            !in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You don\'t have permission to delete this comment.');
        }

        $comment->delete();

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Comment deleted successfully.');
    }
}
