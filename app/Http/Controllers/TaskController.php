<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkflowStatus;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to view tasks.');
        }

        // Get all company tasks with relationships
        $query = Task::query()
            ->whereHas('project', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->with(['project', 'workflowStatus', 'assignee', 'creator', 'tags', 'watchers']);

        // Filter tasks for guests - they can only see tasks they're assigned to or watching
        if ($user->isGuest()) {
            $query->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhereHas('watchers', function ($watcherQuery) use ($user) {
                      $watcherQuery->where('users.id', $user->id);
                  });
            });
        }

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('task_number', 'like', "%{$search}%");
            });
        }

        // Filter by project
        if (request('project')) {
            $query->where('project_id', request('project'));
        }

        // Filter by assignee
        if (request('assignee')) {
            if (request('assignee') === 'unassigned') {
                $query->whereNull('assignee_id');
            } else {
                $query->where('assignee_id', request('assignee'));
            }
        }

        // Filter by creator
        if (request('creator')) {
            $query->where('created_by', request('creator'));
        }

        // Filter by status
        if (request('status')) {
            $query->where('workflow_status_id', request('status'));
        }

        // Filter by priority
        if (request('priority')) {
            $query->where('priority', request('priority'));
        }

        // Filter by due date
        if (request('due_date_from')) {
            $query->where('due_date', '>=', request('due_date_from'));
        }
        if (request('due_date_to')) {
            $query->where('due_date', '<=', request('due_date_to'));
        }

        // Filter by tags
        if (request('tags')) {
            $tagIds = is_array(request('tags')) ? request('tags') : [request('tags')];
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        // Filter by watching
        if (request('watching') === 'true') {
            $query->whereHas('watchers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // Filter by created within days
        if (request('created_within')) {
            $days = (int) request('created_within');
            $query->where('tasks.created_at', '>=', now()->subDays($days));
        }

        // Filter by updated within days
        if (request('updated_within')) {
            $days = (int) request('updated_within');
            $query->where('tasks.updated_at', '>=', now()->subDays($days));
        }

        // Sorting
        $sortBy = request('sort_by', 'updated_at');
        $sortOrder = request('sort_order', 'desc');

        switch ($sortBy) {
            case 'created':
                $query->orderBy('tasks.created_at', 'asc');
                break;
            case 'created_last':
                $query->orderBy('tasks.created_at', 'desc');
                break;
            case 'updated_last':
                $query->orderBy('tasks.updated_at', 'desc');
                break;
            case 'due_date':
                $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ' . $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'status':
                $query->join('workflow_statuses', 'tasks.workflow_status_id', '=', 'workflow_statuses.id')
                      ->orderBy('workflow_statuses.name', $sortOrder)
                      ->select('tasks.*');
                break;
            case 'assignee':
                $query->leftJoin('users', 'tasks.assignee_id', '=', 'users.id')
                      ->orderByRaw('CASE WHEN users.id IS NULL THEN 1 ELSE 0 END, users.first_name ' . $sortOrder)
                      ->select('tasks.*');
                break;
            case 'priority':
                $priorityOrder = "CASE
                    WHEN priority = 'critical' THEN 1
                    WHEN priority = 'high' THEN 2
                    WHEN priority = 'medium' THEN 3
                    WHEN priority = 'low' THEN 4
                    ELSE 5 END";
                $query->orderByRaw($priorityOrder . ' ' . $sortOrder);
                break;
            default:
                $query->orderBy('tasks.updated_at', 'desc');
        }

        // Paginate
        $tasks = $query->paginate(20)->withQueryString();

        // Get filter options
        $projects = $company->projects()->orderBy('name')->get();
        $users = $company->users()->orderBy('first_name')->get();
        $statuses = $company->workflows()
            ->with('statuses')
            ->get()
            ->pluck('statuses')
            ->flatten()
            ->unique('id');
        $tags = $company->tags()->orderBy('name')->get();

        // Handle AJAX requests
        if (request()->ajax()) {
            return view('tasks.partials.tasks-list', compact('tasks'))->render();
        }

        return view('tasks.index', compact('tasks', 'projects', 'users', 'statuses', 'tags'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(?Project $project = null): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to create tasks.');
        }

        // Get all company projects for the project selector
        $projects = $company->projects()->orderBy('name')->get();

        // Get company tags
        $tags = $company->tags()->get();

        // If a project is specified, load its related data
        if ($project) {
            $projectMembers = $project->users;
            $workflowStatuses = $project->workflow->statuses()
                ->where('is_active', true)
                ->orderBy('position')
                ->get();
        } else {
            // No project selected - use empty collections
            $projectMembers = collect();
            $workflowStatuses = collect();
        }

        // Transform team members for mentions
        $teamMembers = $projectMembers->map(function($member) {
            return [
                'id' => $member->id,
                'value' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
            ];
        })->values();

        return view('tasks.create', compact('project', 'projects', 'projectMembers', 'workflowStatuses', 'tags', 'teamMembers'));
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        // Debug: Log incoming request data
        \Log::info('TaskController::store - Request data', [
            'has_attachments' => $request->has('attachments'),
            'attachments_count' => $request->has('attachments') ? count($request->input('attachments', [])) : 0,
            'storage_disk' => $request->input('storage_disk'),
            'all_keys' => array_keys($request->all()),
        ]);

        // Get the project from the validated data
        $project = Project::findOrFail($request->validated()['project_id']);

        \Log::info('TaskController::store - Validated data', [
            'has_attachments' => isset($request->validated()['attachments']),
            'attachments_count' => isset($request->validated()['attachments']) ? count($request->validated()['attachments']) : 0,
            'storage_disk' => $request->validated()['storage_disk'] ?? 'not set',
        ]);

        $task = $this->taskService->createTask(
            $request->validated(),
            $project,
            Auth::user()
        );

        $redirectAction = $request->input('action', 'create');

        if ($redirectAction === 'create_and_add_more') {
            return redirect()
                ->route('tasks.create', $project)
                ->with('message', "Task {$task->task_number} created successfully.");
        }

        if ($redirectAction === 'create_and_copy') {
            return redirect()
                ->route('tasks.create', $project)
                ->with('message', "Task {$task->task_number} created successfully.")
                ->with('copy_data', $request->except(['title', 'description']));
        }

        // Default: redirect to task detail page
        return redirect()
            ->route('tasks.show', [$project, $task])
            ->with('message', "Task {$task->task_number} created successfully.");
    }

    /**
     * Display the specified task.
     */
    public function show(Project $project, Task $task): View
    {
        // Authorization for guests - they can only view tasks they're assigned to or watching
        $user = Auth::user();
        if ($user->isGuest()) {
            $canView = $task->assignee_id === $user->id ||
                       $task->watchers()->where('users.id', $user->id)->exists();

            if (!$canView) {
                abort(403, 'You do not have permission to view this task.');
            }
        }

        $task->load([
            'project',
            'workflowStatus',
            'assignee',
            'creator',
            'tags',
            'watchers',
            'comments.user',
            'activityLogs.user',
            'attachments.uploader'
        ]);

        // Get project team members for assignee dropdown
        $projectMembers = $project->users;

        // Get workflow statuses for status dropdown
        $workflowStatuses = $project->workflow->statuses()
            ->orderBy('position')
            ->get();

        // Get company tags
        $tags = $project->company->tags()->get();

        // Transform team members for mentions
        $teamMembers = $projectMembers->map(function($member) {
            return [
                'id' => $member->id,
                'value' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
            ];
        })->values();

        // Determine user role in project
        $userRole = $project->users()
            ->where('user_id', Auth::id())
            ->first()
            ?->pivot
            ->role ?? 'member';

        return view('tasks.show', compact(
            'task',
            'project',
            'projectMembers',
            'workflowStatuses',
            'tags',
            'userRole',
            'teamMembers'
        ));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Project $project, Task $task): View
    {
        $task->load(['tags', 'watchers']);

        // Get project team members for assignee dropdown
        $projectMembers = $project->users;

        // Get workflow statuses for status dropdown
        $workflowStatuses = $project->workflow->statuses()
            ->orderBy('position')
            ->get();

        // Get company tags
        $tags = $project->company->tags()->get();

        // Transform team members for mentions
        $teamMembers = $projectMembers->map(function($member) {
            return [
                'id' => $member->id,
                'value' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
            ];
        })->values();

        return view('tasks.edit', compact('task', 'project', 'projectMembers', 'workflowStatuses', 'tags', 'teamMembers'));
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->taskService->updateTask(
            $task,
            $request->validated(),
            Auth::user()
        );

        return redirect()
            ->route('tasks.show', [$project, $task])
            ->with('message', 'Task updated successfully.');
    }

    /**
     * Update the task workflow status.
     */
    public function updateStatus(Request $request, Project $project, Task $task): JsonResponse
    {
        // Check if current status is final (closed)
        if ($task->workflowStatus->is_final) {
            return response()->json([
                'error' => 'Cannot change status of a closed task. The task is already finished.'
            ], 403);
        }

        $validated = $request->validate([
            'workflow_status_id' => ['required', 'exists:workflow_statuses,id'],
        ]);

        // Verify the workflow status belongs to the project's workflow
        $workflowStatus = WorkflowStatus::find($validated['workflow_status_id']);
        if ($workflowStatus->workflow_id !== $project->workflow_id) {
            return response()->json(['error' => 'Invalid workflow status for this project.'], 422);
        }

        // Check if user is trying to set a restricted status (Open or Closed)
        $restrictedStatuses = ['Open', 'Closed'];
        if (in_array($workflowStatus->name, $restrictedStatuses)) {
            // Get user's role in the project
            $userRole = $project->users()
                ->where('user_id', Auth::id())
                ->first()
                ?->pivot
                ->role ?? 'member';

            // Only owner, admin, or task creator can use restricted statuses
            $canUseRestrictedStatus = in_array($userRole, ['owner', 'admin']) || $task->created_by === Auth::id();

            if (!$canUseRestrictedStatus) {
                return response()->json([
                    'error' => 'Only project owners, admins, or task creators can set status to ' . $workflowStatus->name . '.'
                ], 403);
            }
        }

        $this->taskService->updateTask(
            $task,
            ['workflow_status_id' => $validated['workflow_status_id']],
            Auth::user()
        );

        // Reload task with relationships
        $task->load('workflowStatus');

        return response()->json([
            'success' => true,
            'status' => [
                'id' => $task->workflowStatus->id,
                'name' => $task->workflowStatus->name,
                'color' => $task->workflowStatus->color,
                'text_color' => $task->workflowStatus->text_color,
                'is_final' => $task->workflowStatus->is_final ?? false,
            ],
        ]);
    }

    /**
     * Update the task assignee.
     */
    public function updateAssignee(Request $request, Project $project, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        // If assignee_id is provided, verify they are a member of the project
        if (isset($validated['assignee_id']) && $validated['assignee_id']) {
            $isMember = $project->users()->where('user_id', $validated['assignee_id'])->exists();
            if (!$isMember) {
                return response()->json(['error' => 'User is not a member of this project.'], 422);
            }
        }

        $this->taskService->updateTask(
            $task,
            ['assignee_id' => $validated['assignee_id']],
            Auth::user()
        );

        // Reload task with relationships
        $task->load('assignee');

        return response()->json([
            'success' => true,
            'assignee' => $task->assignee ? [
                'id' => $task->assignee->id,
                'full_name' => $task->assignee->full_name,
                'avatar_url' => $task->assignee->avatar_url,
            ] : null,
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Project $project, Task $task): RedirectResponse
    {
        // Check permissions - only owner, admin, or creator can delete
        $userRole = $project->users()
            ->where('user_id', Auth::id())
            ->first()
            ?->pivot
            ->role ?? 'member';

        if (!in_array($userRole, ['owner', 'admin']) && $task->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to delete this task.');
        }

        $this->taskService->deleteTask($task, Auth::user());

        return redirect()
            ->route('projects.show', $project)
            ->with('message', 'Task deleted successfully.');
    }

    /**
     * Duplicate the specified task.
     */
    public function duplicate(Project $project, Task $task): RedirectResponse
    {
        $newTask = $this->taskService->duplicateTask($task, Auth::user());

        return redirect()
            ->route('tasks.show', [$project, $newTask])
            ->with('message', "Task duplicated successfully as {$newTask->task_number}.");
    }
}
