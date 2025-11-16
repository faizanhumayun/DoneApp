<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(): View
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to view projects.');
        }

        $projects = $company->projects()
            ->with(['workflow', 'creator', 'users'])
            ->active()
            ->get();

        return view('projects.index', compact('projects', 'company'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to create projects.');
        }

        // Check permissions - only owner and admin can create projects
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to create projects.');
        }

        // Get workflows for dropdown
        $workflows = $company->workflows()->get();

        // Get company users for member invitation
        $companyUsers = $company->users()->get();

        return view('projects.create', compact('company', 'workflows', 'companyUsers'));
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to create projects.');
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to create projects.');
        }

        DB::beginTransaction();

        try {
            // Create project
            $project = $company->projects()->create([
                'workflow_id' => $request->validated('workflow_id'),
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
                'estimated_cost' => $request->validated('estimated_cost'),
                'billable_resource' => $request->validated('billable_resource'),
                'non_billable_resource' => $request->validated('non_billable_resource'),
                'total_estimated_hours' => $request->validated('total_estimated_hours'),
                'created_by' => Auth::id(),
                'status' => 'active',
            ]);

            // Add creator as owner to project members
            $project->users()->attach(Auth::id(), ['role' => 'owner']);

            // Add invited members if any
            if ($request->has('members')) {
                $members = $request->validated('members');
                foreach ($members as $member) {
                    if ($member['user_id'] != Auth::id()) { // Don't add creator again
                        $project->users()->attach($member['user_id'], [
                            'role' => $member['role'] ?? 'member',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('projects.show', $project)
                ->with('message', 'Project created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to create project. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): View
    {
        $company = Auth::user()->companies->first();

        if (!$company || $project->company_id !== $company->id) {
            abort(403, 'You do not have permission to view this project.');
        }

        $project->load([
            'workflow.statuses',
            'creator',
            'users',
            'tasks.workflowStatus',
            'tasks.assignee',
            'tasks.comments'
        ]);

        // Get user's role in company for permission checks
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;

        return view('projects.show', compact('project', 'company', 'userRole'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        $company = Auth::user()->companies->first();

        if (!$company || $project->company_id !== $company->id) {
            abort(403, 'You do not have permission to edit this project.');
        }

        // Check permissions - only owner and admin can edit
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to edit projects.');
        }

        // Get workflows for dropdown
        $workflows = $company->workflows()->get();

        // Get company users for member invitation
        $companyUsers = $company->users()->get();

        $project->load('users');

        return view('projects.edit', compact('project', 'company', 'workflows', 'companyUsers'));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $project->company_id !== $company->id) {
            abort(403, 'You do not have permission to update this project.');
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to update projects.');
        }

        DB::beginTransaction();

        try {
            // Update project
            $project->update([
                'workflow_id' => $request->validated('workflow_id'),
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
                'estimated_cost' => $request->validated('estimated_cost'),
                'billable_resource' => $request->validated('billable_resource'),
                'non_billable_resource' => $request->validated('non_billable_resource'),
                'total_estimated_hours' => $request->validated('total_estimated_hours'),
            ]);

            // Update members if provided
            if ($request->has('members')) {
                $members = $request->validated('members');
                $syncData = [];

                foreach ($members as $member) {
                    $syncData[$member['user_id']] = ['role' => $member['role'] ?? 'member'];
                }

                $project->users()->sync($syncData);
            }

            DB::commit();

            return redirect()
                ->route('projects.show', $project)
                ->with('message', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to update project. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $project->company_id !== $company->id) {
            abort(403, 'You do not have permission to delete this project.');
        }

        // Check permissions - only owner can delete
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if ($userRole !== 'owner') {
            abort(403, 'Only the company owner can delete projects.');
        }

        if (!$project->canBeDeleted()) {
            return back()->withErrors(['error' => 'This project cannot be deleted because it has tasks.']);
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('message', 'Project deleted successfully.');
    }

    /**
     * Archive the specified project.
     */
    public function archive(Project $project): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $project->company_id !== $company->id) {
            abort(403, 'You do not have permission to archive this project.');
        }

        // Check permissions - only owner can archive
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if ($userRole !== 'owner') {
            abort(403, 'Only the company owner can archive projects.');
        }

        $project->archive();

        return redirect()
            ->route('projects.index')
            ->with('message', 'Project archived successfully.');
    }
}
