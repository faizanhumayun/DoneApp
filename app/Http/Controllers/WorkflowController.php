<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowRequest;
use App\Http\Requests\UpdateWorkflowRequest;
use App\Models\Workflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkflowController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index(): View
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to manage workflows.');
        }

        $workflows = $company->workflows()->with(['statuses', 'creator'])->get();
        return view('workflows.index', compact('workflows', 'company'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create(): View
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to create workflows.');
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to create workflows.');
        }

        return view('workflows.create', compact('company'));
    }

    /**
     * Store a newly created workflow.
     */
    public function store(StoreWorkflowRequest $request): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to create workflows.');
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to create workflows.');
        }

        DB::beginTransaction();

        try {
            // Create workflow
            $workflow = $company->workflows()->create([
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
                'is_builtin' => false,
                'created_by' => Auth::id(),
            ]);

            // Create statuses
            $statuses = $request->validated('statuses');
            foreach ($statuses as $index => $statusData) {
                $workflow->statuses()->create([
                    'name' => $statusData['name'],
                    'color' => $statusData['color'],
                    'is_active' => $statusData['is_active'] ?? true,
                    'position' => $index,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('workflows.index')
                ->with('message', 'Workflow created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to create workflow. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(Workflow $workflow): View
    {
        $company = Auth::user()->companies->first();

        if (!$company || $workflow->company_id !== $company->id) {
            abort(403, 'You do not have permission to edit this workflow.');
        }

        // Prevent editing built-in workflows
        if ($workflow->is_builtin) {
            return redirect()
                ->route('workflows.index')
                ->withErrors(['error' => 'Built-in workflows cannot be edited. Please duplicate this workflow to customize it.']);
        }

        // Load statuses ordered by position
        $workflow->load(['statuses' => function ($query) {
            $query->orderBy('position');
        }]);

        return view('workflows.edit', compact('workflow', 'company'));
    }

    /**
     * Update the specified workflow.
     */
    public function update(UpdateWorkflowRequest $request, Workflow $workflow): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $workflow->company_id !== $company->id) {
            abort(403, 'You do not have permission to update this workflow.');
        }

        // Prevent updating built-in workflows
        if ($workflow->is_builtin) {
            return redirect()
                ->route('workflows.index')
                ->withErrors(['error' => 'Built-in workflows cannot be modified.']);
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to update workflows.');
        }

        DB::beginTransaction();

        try {
            // Update workflow
            $workflow->update([
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
            ]);

            // Delete removed statuses
            $statusIds = collect($request->validated('statuses'))->pluck('id')->filter();
            $workflow->statuses()->whereNotIn('id', $statusIds)->delete();

            // Update or create statuses
            $statuses = $request->validated('statuses');
            foreach ($statuses as $index => $statusData) {
                if (isset($statusData['id'])) {
                    // Update existing status
                    $workflow->statuses()->where('id', $statusData['id'])->update([
                        'name' => $statusData['name'],
                        'color' => $statusData['color'],
                        'is_active' => $statusData['is_active'] ?? true,
                        'position' => $index,
                    ]);
                } else {
                    // Create new status
                    $workflow->statuses()->create([
                        'name' => $statusData['name'],
                        'color' => $statusData['color'],
                        'is_active' => $statusData['is_active'] ?? true,
                        'position' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('workflows.index')
                ->with('message', 'Workflow updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to update workflow. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(Workflow $workflow): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $workflow->company_id !== $company->id) {
            abort(403, 'You do not have permission to delete this workflow.');
        }

        // Check permissions
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to delete workflows.');
        }

        if (!$workflow->canBeDeleted()) {
            return back()->withErrors(['error' => 'This workflow cannot be deleted because it is a built-in workflow or is in use by tasks.']);
        }

        $workflow->delete();

        return redirect()
            ->route('workflows.index')
            ->with('message', 'Workflow deleted successfully.');
    }

    /**
     * Duplicate the specified workflow.
     */
    public function duplicate(Workflow $workflow): RedirectResponse
    {
        $company = Auth::user()->companies->first();

        if (!$company || $workflow->company_id !== $company->id) {
            abort(403, 'You do not have permission to duplicate this workflow.');
        }

        $newWorkflow = $workflow->duplicate();

        return redirect()
            ->route('workflows.edit', $newWorkflow)
            ->with('message', 'Workflow duplicated successfully. You can now customize it.');
    }
}
