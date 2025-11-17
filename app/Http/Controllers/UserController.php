<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\TeamInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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

        // Handle invitations tab separately
        if ($tab === 'invitations') {
            $invitations = Invitation::where('company_id', $company->id)
                ->where('status', 'pending')
                ->with('invitedBy')
                ->orderBy('created_at', 'desc')
                ->get();

            // Apply search filter to invitations
            if ($request->filled('search')) {
                $search = $request->get('search');
                $invitations = $invitations->filter(function ($invitation) use ($search) {
                    return stripos($invitation->invited_email, $search) !== false;
                });
            }

            $users = collect(); // Empty collection for users
        } else {
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
            $invitations = collect(); // Empty collection for invitations
        }

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

        $invitationsCount = Invitation::where('company_id', $company->id)
            ->where('status', 'pending')
            ->count();

        return view('users.index', compact(
            'users',
            'invitations',
            'tab',
            'activeCount',
            'inactiveCount',
            'archivedCount',
            'invitationsCount',
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

    /**
     * Send team member invitations.
     */
    public function invite(Request $request): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Check permissions - only owner and admin can invite
        $authUserRole = $company->users()->where('user_id', $authUser->id)->first()->pivot->role;
        if (!in_array($authUserRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to invite users.');
        }

        $validated = $request->validate([
            'team_emails' => ['nullable', 'array'],
            'team_emails.*' => ['nullable', 'email', 'max:255'],
        ]);

        $teamEmails = array_filter($validated['team_emails'] ?? [], fn($email) => !empty($email));

        if (empty($teamEmails)) {
            return back()->withErrors(['team_emails' => 'Please provide at least one email address.']);
        }

        $invitedCount = 0;
        $skippedEmails = [];

        DB::transaction(function () use ($teamEmails, $company, $authUser, &$invitedCount, &$skippedEmails) {
            foreach ($teamEmails as $email) {
                // Check if user already exists in company
                $existingUser = User::whereHas('companies', function ($q) use ($company) {
                    $q->where('companies.id', $company->id);
                })->where('email', $email)->first();

                if ($existingUser) {
                    $skippedEmails[] = $email . ' (already a member)';
                    continue;
                }

                // Check if there's already a pending invitation
                $existingInvite = Invitation::where('company_id', $company->id)
                    ->where('invited_email', $email)
                    ->where('status', 'pending')
                    ->first();

                if ($existingInvite && !$existingInvite->isTokenExpired()) {
                    $skippedEmails[] = $email . ' (already invited)';
                    continue;
                }

                // Create invitation
                $invitation = Invitation::create([
                    'company_id' => $company->id,
                    'invited_email' => $email,
                    'invited_by_user_id' => $authUser->id,
                    'invite_token' => Invitation::generateToken(),
                    'invite_token_expires_at' => now()->addDays(7),
                    'status' => 'pending',
                ]);

                // Send invitation email
                Notification::route('mail', $email)
                    ->notify(new TeamInvitation($invitation));

                // Log email
                \App\Models\EmailLog::create([
                    'company_id' => $company->id,
                    'recipient' => $email,
                    'subject' => 'Team Invitation',
                    'type' => 'team-invite',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $invitedCount++;
            }
        });

        $message = $invitedCount > 0
            ? "Successfully sent {$invitedCount} invitation(s)."
            : 'No invitations were sent.';

        if (!empty($skippedEmails)) {
            $message .= ' Skipped: ' . implode(', ', $skippedEmails);
        }

        return redirect()->route('users.index')
            ->with('success', $message);
    }

    /**
     * Resend a team invitation.
     */
    public function resendInvitation(Invitation $invitation): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Verify invitation belongs to this company
        if ($invitation->company_id !== $company->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if expired - regenerate token if needed
        if ($invitation->isTokenExpired()) {
            $invitation->update([
                'invite_token' => Invitation::generateToken(),
                'invite_token_expires_at' => now()->addDays(7),
            ]);
        }

        // Resend email
        Notification::route('mail', $invitation->invited_email)
            ->notify(new TeamInvitation($invitation));

        // Log email
        \App\Models\EmailLog::create([
            'company_id' => $invitation->company_id,
            'recipient' => $invitation->invited_email,
            'subject' => 'Team Invitation (Resent)',
            'type' => 'team-invite-resend',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->route('users.index', ['tab' => 'invitations'])
            ->with('success', "Invitation resent to {$invitation->invited_email}.");
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvitation(Invitation $invitation): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Verify invitation belongs to this company
        if ($invitation->company_id !== $company->id) {
            abort(403, 'Unauthorized action.');
        }

        $email = $invitation->invited_email;
        $invitation->delete();

        return redirect()->route('users.index', ['tab' => 'invitations'])
            ->with('success', "Invitation to {$email} has been cancelled.");
    }
}
