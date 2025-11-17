<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInvitationRequest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Show the invitation acceptance form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('signup.email')
                ->withErrors(['token' => 'No invitation token provided.']);
        }

        // Find and validate invitation
        $invitation = Invitation::where('invite_token', $token)->first();

        // Check if invitation exists
        if (!$invitation) {
            return view('invitation.error', [
                'title' => 'Invalid Invitation',
                'message' => 'This invitation link is invalid.',
                'suggestion' => 'Please contact your administrator for a new invitation.',
            ]);
        }

        // Check if already accepted
        if ($invitation->status === 'accepted') {
            return view('invitation.error', [
                'title' => 'Invitation Already Used',
                'message' => 'This invitation has already been accepted.',
                'suggestion' => 'Please log in to your account.',
                'showLoginLink' => true,
            ]);
        }

        // Check if expired
        if ($invitation->isTokenExpired()) {
            return view('invitation.error', [
                'title' => 'Invitation Expired',
                'message' => 'This invitation link has expired.',
                'suggestion' => 'Please ask your administrator to send you a new invitation.',
            ]);
        }

        // Check if revoked
        if ($invitation->status === 'revoked') {
            return view('invitation.error', [
                'title' => 'Invitation Revoked',
                'message' => 'This invitation is no longer valid.',
                'suggestion' => 'Please contact your administrator.',
            ]);
        }

        // Load company relationship
        $invitation->load('company');

        // Check if user already exists with this email
        $existingUser = User::where('email', $invitation->invited_email)->first();

        return view('invitation.accept', [
            'invitation' => $invitation,
            'company' => $invitation->company,
            'existingUser' => $existingUser,
            'timezones' => config('signup.timezones'),
        ]);
    }

    /**
     * Handle the invitation acceptance submission.
     */
    public function accept(AcceptInvitationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $token = $validated['token'];

        // Re-validate invitation (in case it changed since form load)
        $invitation = Invitation::where('invite_token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation || $invitation->isTokenExpired()) {
            return redirect()->route('invitation.show', ['token' => $token])
                ->withErrors(['token' => 'This invitation is no longer valid.']);
        }

        DB::transaction(function () use ($invitation, $validated) {
            // Check if user already exists
            $existingUser = User::where('email', $invitation->invited_email)->first();

            if ($existingUser) {
                // Case A: Existing user - just associate with company
                $user = $existingUser;

                // Update profile fields if provided
                $user->update([
                    'timezone' => $validated['timezone'],
                    'about_yourself' => $validated['about_yourself'] ?? null,
                ]);
            } else {
                // Case B: New user - create account
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $invitation->invited_email,
                    'password' => $validated['password'],
                    'timezone' => $validated['timezone'],
                    'about_yourself' => $validated['about_yourself'] ?? null,
                    'email_verified_at' => now(), // Auto-verify via invitation
                ]);
            }

            // Associate user with company (if not already)
            if (!$user->companies()->where('company_id', $invitation->company_id)->exists()) {
                $user->companies()->attach($invitation->company_id, [
                    'role' => $invitation->role ?? 'member',
                ]);
            }

            // Mark invitation as accepted
            $invitation->update([
                'status' => 'accepted',
            ]);

            // Create welcome notification for new user
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'company_id' => $invitation->company_id,
                'type' => 'invite',
                'title' => 'Welcome to the team!',
                'message' => "You've successfully joined {$invitation->company->name}",
                'data' => [
                    'link' => route('dashboard'),
                ],
            ]);

            // Log in the user
            Auth::login($user);
        });

        // Redirect to dashboard
        return redirect()->route('dashboard')
            ->with('welcome', true)
            ->with('message', 'Welcome! You have successfully joined ' . $invitation->company->name . '.');
    }
}
