<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    /**
     * Display the workspace page.
     */
    public function index(): View
    {
        $company = Auth::user()->companies->first();

        if (!$company) {
            abort(403, 'You must be part of a company to access the workspace.');
        }

        // Get user's role for permission checks
        $userRole = $company->users()->where('user_id', Auth::id())->first()->pivot->role;

        // Get recent active projects (limit to 6 for workspace overview)
        $projects = $company->projects()
            ->with(['workflow', 'users'])
            ->active()
            ->latest()
            ->limit(6)
            ->get();

        // Get company statistics
        $stats = [
            'total_members' => $company->users()->count(),
            'active_workflows' => $company->workflows()->count(),
            'active_projects' => $company->projects()->active()->count(),
        ];

        return view('workspace', compact('company', 'projects', 'stats', 'userRole'));
    }
}
