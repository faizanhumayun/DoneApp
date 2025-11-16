<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Check permissions - only owner and admin can access
        $userRole = $company->users()->where('user_id', $authUser->id)->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to view users.');
        }

        // Get tab from request, default to 'active'
        $tab = $request->get('tab', 'active');

        // Build query for users in this company
        $query = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        })->with(['companies' => function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        }]);

        // Filter by status based on tab
        switch ($tab) {
            case 'inactive':
                $query->where('status', 'inactive');
                break;
            case 'archived':
                $query->where('status', 'archived');
                break;
            default:
                $query->where('status', 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $role = $request->get('role');
            $query->whereHas('companies', function ($q) use ($company, $role) {
                $q->where('companies.id', $company->id)
                  ->where('company_user.role', $role);
            });
        }

        $users = $query->orderBy('first_name')->get();

        // Get counts for each tab
        $activeCount = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        })->where('status', 'active')->count();

        $inactiveCount = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        })->where('status', 'inactive')->count();

        $archivedCount = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        })->where('status', 'archived')->count();

        return view('users.index', compact(
            'users',
            'tab',
            'activeCount',
            'inactiveCount',
            'archivedCount',
            'userRole'
        ));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Check permissions
        $authUserRole = $company->users()->where('user_id', $authUser->id)->first()->pivot->role;
        if (!in_array($authUserRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to update users.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'in:owner,admin,member,guest'],
            'status' => ['required', 'in:active,inactive'],
            'about_yourself' => ['nullable', 'string', 'max:500'],
        ]);

        // Prevent demoting the last owner
        if ($request->get('role') !== 'owner') {
            $ownerCount = $company->users()
                ->where('company_user.role', 'owner')
                ->count();

            $currentRole = $user->companies()
                ->where('companies.id', $company->id)
                ->first()->pivot->role;

            if ($currentRole === 'owner' && $ownerCount <= 1) {
                return back()->withErrors(['role' => 'Cannot change role. At least one owner must exist.']);
            }
        }

        // Update user info
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'status' => $validated['status'],
            'about_yourself' => $validated['about_yourself'] ?? null,
        ]);

        // Update role in company
        $user->companies()->updateExistingPivot($company->id, [
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Archive the specified user.
     */
    public function archive(User $user): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Check permissions
        $authUserRole = $company->users()->where('user_id', $authUser->id)->first()->pivot->role;
        if (!in_array($authUserRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to archive users.');
        }

        // Prevent archiving the last owner
        $currentRole = $user->companies()
            ->where('companies.id', $company->id)
            ->first()->pivot->role;

        if ($currentRole === 'owner') {
            $ownerCount = $company->users()
                ->where('company_user.role', 'owner')
                ->count();

            if ($ownerCount <= 1) {
                return back()->withErrors(['error' => 'Cannot archive the last owner.']);
            }
        }

        $user->update(['status' => 'archived']);

        return redirect()->route('users.index', ['tab' => 'archived'])
            ->with('success', "{$user->full_name} has been archived.");
    }

    /**
     * Restore an archived user.
     */
    public function restore(User $user): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Check permissions
        $authUserRole = $company->users()->where('user_id', $authUser->id)->first()->pivot->role;
        if (!in_array($authUserRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to restore users.');
        }

        $user->update(['status' => 'inactive']);

        return redirect()->route('users.index', ['tab' => 'inactive'])
            ->with('success', "{$user->full_name} has been restored to inactive status.");
    }
}
