<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\GuestInvite;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GuestInviteController extends Controller
{
    /**
     * Show the form for inviting a guest.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Get context if inviting from task or discussion
        $fromType = $request->get('from_type'); // 'task' or 'discussion'
        $fromId = $request->get('from_id');

        $context = null;
        if ($fromType === 'task' && $fromId) {
            $context = Task::with('project')->find($fromId);
        }

        return view('guests.invite', compact('company', 'fromType', 'fromId', 'context'));
    }

    /**
     * Send a guest invitation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'personal_message' => ['nullable', 'string', 'max:1000'],
            'from_type' => ['nullable', 'in:task,discussion,manual'],
            'from_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::user();
        $company = $user->companies->first();

        // Check if email already exists as a user in this company
        $existingUser = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id);
        })->where('email', $validated['email'])->first();

        if ($existingUser) {
            // Check their role
            $role = $existingUser->companies()
                ->where('companies.id', $company->id)
                ->first()
                ->pivot
                ->role;

            if ($role === 'guest') {
                // Existing guest - just add them to task/discussion if applicable
                return $this->addExistingGuestToContext($existingUser, $validated, $request);
            } else {
                return back()->withErrors(['email' => 'This email already belongs to a team member.']);
            }
        }

        // Check if there's already a pending invite
        $existingInvite = GuestInvite::where('email', $validated['email'])
            ->where('company_id', $company->id)
            ->where('is_accepted', false)
            ->first();

        if ($existingInvite && $existingInvite->isValid()) {
            // Resend the existing invite
            // TODO: Send email notification
            return back()->with('message', 'An invitation has already been sent to this email. Invite resent.');
        }

        // Create new invitation
        $invite = GuestInvite::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'token' => GuestInvite::generateToken(),
            'token_expires_at' => now()->addDays(7), // Expires in 7 days
            'invited_by_user_id' => $user->id,
            'company_id' => $company->id,
            'personal_message' => $validated['personal_message'] ?? null,
            'invited_from_type' => $validated['from_type'] ?? 'manual',
            'invited_from_id' => $validated['from_id'] ?? null,
        ]);

        // Send email notification
        \Mail::to($invite->email)->send(new \App\Mail\GuestInvitationMail($invite));

        // Log email
        \App\Models\EmailLog::create([
            'company_id' => $company->id,
            'recipient' => $invite->email,
            'subject' => 'Guest Invitation',
            'type' => 'guest-invite',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $message = "Guest invitation sent to {$validated['email']}.";

        // Redirect based on context
        if (isset($validated['from_type']) && $validated['from_type'] === 'task' && isset($validated['from_id'])) {
            $task = Task::find($validated['from_id']);
            return redirect()->route('tasks.show', [$task->project, $task])
                ->with('message', $message);
        }

        return redirect()->route('guests.index')->with('success', $message);
    }

    /**
     * Add existing guest to task or discussion context.
     */
    protected function addExistingGuestToContext(User $guest, array $validated, Request $request): RedirectResponse
    {
        if (isset($validated['from_type']) && $validated['from_type'] === 'task' && isset($validated['from_id'])) {
            $task = Task::find($validated['from_id']);

            // Check what action was requested
            $action = $request->get('action', 'watcher'); // 'assignee' or 'watcher'

            if ($action === 'assignee') {
                $task->update(['assignee_id' => $guest->id]);
                $message = "{$guest->full_name} assigned to task.";
            } else {
                if (!$task->watchers()->where('user_id', $guest->id)->exists()) {
                    $task->watchers()->attach($guest->id);
                }
                $message = "{$guest->full_name} added as watcher.";
            }

            return redirect()->route('tasks.show', [$task->project, $task])
                ->with('message', $message);
        }

        return back()->with('message', 'Guest already exists and has been added.');
    }

    /**
     * Show the guest signup form.
     */
    public function showSignup(string $token): View
    {
        $invite = GuestInvite::where('token', $token)->firstOrFail();

        if (!$invite->isValid()) {
            abort(410, 'This invitation has expired or has already been used.');
        }

        return view('guests.signup', compact('invite'));
    }

    /**
     * Process the guest signup.
     */
    public function signup(Request $request, string $token): RedirectResponse
    {
        $invite = GuestInvite::where('token', $token)->firstOrFail();

        if (!$invite->isValid()) {
            abort(410, 'This invitation has expired or has already been used.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->symbols()],
            'about_yourself' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $invite) {
            // Create the user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $invite->email,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'timezone' => 'UTC',
                'about_yourself' => $validated['about_yourself'] ?? null,
            ]);

            // Attach to company with guest role
            $user->companies()->attach($invite->company_id, [
                'role' => 'guest',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Handle task/discussion association based on invite context
            if ($invite->invited_from_type === 'task' && $invite->invited_from_id) {
                $task = Task::find($invite->invited_from_id);
                if ($task) {
                    // Add as watcher by default
                    $task->watchers()->attach($user->id);
                }
            }

            // Mark invite as accepted
            $invite->markAsAccepted();

            // Log the user in
            Auth::login($user);
        });

        return redirect()->route('guests.dashboard')->with('message', 'Welcome! Your account has been created.');
    }

    /**
     * Show the guest dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Get tasks where user is assignee or watcher
        $tasks = Task::query()
            ->whereHas('project', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhereHas('watchers', function ($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
            })
            ->with(['project', 'workflowStatus', 'assignee', 'tags'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // TODO: Get discussions where user is a participant
        $discussions = collect([]); // Placeholder

        return view('guests.dashboard', compact('tasks', 'discussions'));
    }

    /**
     * Show all pending guest invitations.
     */
    public function index(): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Check if user has permission (owner or admin)
        $userRole = $company->users()->where('user_id', $user->id)->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to view guest invitations.');
        }

        $pendingInvites = GuestInvite::where('company_id', $company->id)
            ->where('is_accepted', false)
            ->with('invitedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        $guests = User::whereHas('companies', function ($q) use ($company) {
            $q->where('companies.id', $company->id)
              ->where('company_user.role', 'guest');
        })->with('companies')->get();

        return view('guests.index', compact('pendingInvites', 'guests'));
    }

    /**
     * Resend invitation email.
     */
    public function resend(GuestInvite $invite): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Verify invite belongs to this company
        if ($invite->company_id !== $company->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if expired - regenerate token if needed
        if ($invite->isExpired()) {
            $invite->update([
                'token' => GuestInvite::generateToken(),
                'token_expires_at' => now()->addDays(7),
            ]);
        }

        // Resend email
        \Mail::to($invite->email)->send(new \App\Mail\GuestInvitationMail($invite));

        // Log email
        \App\Models\EmailLog::create([
            'company_id' => $invite->company_id,
            'recipient' => $invite->email,
            'subject' => 'Guest Invitation (Resent)',
            'type' => 'guest-invite-resend',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->route('guests.index')
            ->with('success', "Invitation resent to {$invite->email}.");
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvite(GuestInvite $invite): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->companies->first();

        // Verify invite belongs to this company
        if ($invite->company_id !== $company->id) {
            abort(403, 'Unauthorized action.');
        }

        $email = $invite->email;
        $invite->delete();

        return redirect()->route('guests.index')
            ->with('success', "Invitation to {$email} has been cancelled.");
    }

    /**
     * Remove a guest user from the company.
     */
    public function remove(User $user): RedirectResponse
    {
        $authUser = Auth::user();
        $company = $authUser->companies->first();

        // Verify user is a guest in this company
        $guestRole = $user->companies()
            ->where('companies.id', $company->id)
            ->first()
            ?->pivot
            ->role;

        if ($guestRole !== 'guest') {
            abort(403, 'This user is not a guest.');
        }

        $userName = $user->full_name;

        // Detach from company
        $user->companies()->detach($company->id);

        // Optionally delete user if they have no other companies
        if ($user->companies()->count() === 0) {
            $user->delete();
        }

        return redirect()->route('guests.index')
            ->with('success', "{$userName} has been removed as a guest.");
    }
}
