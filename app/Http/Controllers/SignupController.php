<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupCompanyRequest;
use App\Http\Requests\SignupEmailRequest;
use App\Http\Requests\SignupProfileRequest;
use App\Http\Requests\SignupTeamInvitationsRequest;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\SignupPending;
use App\Models\User;
use App\Notifications\TeamInvitation;
use App\Notifications\VerifyEmailSignup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class SignupController extends Controller
{
    /**
     * Show the email capture form (Step 0).
     */
    public function showEmailForm(): View
    {
        return view('signup.email');
    }

    /**
     * Handle email submission and send verification email (Step 0).
     */
    public function submitEmail(SignupEmailRequest $request): RedirectResponse
    {
        $email = $request->validated()['work_email'];

        // Create or update pending signup record
        $signupPending = SignupPending::updateOrCreate(
            ['email' => $email],
            [
                'token' => SignupPending::generateToken(),
                'token_expires_at' => now()->addHours(config('signup.verification_token_expiry_hours')),
                'verified_at' => null,
            ]
        );

        // Send verification email
        Notification::route('mail', $email)
            ->notify(new VerifyEmailSignup($signupPending));

        return redirect()->route('signup.check-email')->with('email', $email);
    }

    /**
     * Show the "Check your email" screen (Step 0b).
     */
    public function showCheckEmail(Request $request): View
    {
        $email = $request->session()->get('email');

        if (!$email) {
            return redirect()->route('signup.email');
        }

        return view('signup.check-email', ['email' => $email]);
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $email = $request->input('email');

        if (!$email) {
            return redirect()->route('signup.email')->withErrors(['email' => 'Email is required.']);
        }

        $signupPending = SignupPending::where('email', $email)->first();

        if (!$signupPending) {
            return redirect()->route('signup.email')->withErrors(['email' => 'No pending signup found for this email.']);
        }

        // Update token and expiry
        $signupPending->update([
            'token' => SignupPending::generateToken(),
            'token_expires_at' => now()->addHours(config('signup.verification_token_expiry_hours')),
        ]);

        // Send verification email
        Notification::route('mail', $email)
            ->notify(new VerifyEmailSignup($signupPending));

        return redirect()->route('signup.check-email')->with(['email' => $email, 'resent' => true]);
    }

    /**
     * Verify email token and redirect to Step 1.
     */
    public function verifyEmail(Request $request, string $token): RedirectResponse
    {
        $signupPending = SignupPending::where('token', $token)->first();

        // Validate token
        if (!$signupPending) {
            return redirect()->route('signup.email')
                ->withErrors(['token' => 'This link is invalid or has expired.']);
        }

        if ($signupPending->isTokenExpired()) {
            return redirect()->route('signup.email')
                ->withErrors(['token' => 'This link has expired. Please request a new verification link.'])
                ->with('expired_email', $signupPending->email);
        }

        if ($signupPending->isVerified()) {
            // Check if user already completed signup
            $user = User::where('email', $signupPending->email)->first();
            if ($user) {
                return redirect()->route('login')
                    ->with('message', 'Your email is already verified. Please log in.');
            }
        }

        // Mark as verified
        if (!$signupPending->isVerified()) {
            $signupPending->markAsVerified();
        }

        // Store in session and redirect to Step 1
        session(['signup_token' => $token, 'signup_email' => $signupPending->email]);

        return redirect()->route('signup.profile');
    }

    /**
     * Show the profile form (Step 1).
     */
    public function showProfileForm(): View
    {
        $email = session('signup_email');

        if (!$email) {
            return redirect()->route('signup.email');
        }

        return view('signup.profile', [
            'email' => $email,
            'timezones' => config('signup.timezones'),
        ]);
    }

    /**
     * Handle profile submission (Step 1).
     */
    public function submitProfile(SignupProfileRequest $request): RedirectResponse
    {
        $email = session('signup_email');

        if (!$email) {
            return redirect()->route('signup.email');
        }

        $validated = $request->validated();

        // Create user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $email,
            'password' => $validated['password'],
            'timezone' => $validated['timezone'],
            'email_verified_at' => now(),
        ]);

        // Log in the user
        Auth::login($user);

        // Store completion in session
        session(['signup_step_1_complete' => true]);

        return redirect()->route('signup.company');
    }

    /**
     * Show the company form (Step 2).
     */
    public function showCompanyForm(): View
    {
        if (!Auth::check() || !session('signup_step_1_complete')) {
            return redirect()->route('signup.email');
        }

        return view('signup.company', [
            'company_sizes' => config('signup.company_sizes'),
            'industries' => config('signup.industries'),
        ]);
    }

    /**
     * Handle company submission (Step 2).
     */
    public function submitCompany(SignupCompanyRequest $request): RedirectResponse
    {
        if (!Auth::check() || !session('signup_step_1_complete')) {
            return redirect()->route('signup.email');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // Create company
            $company = Company::create([
                'name' => $validated['company_name'],
                'size' => $validated['company_size'],
                'industry' => $validated['industry_type'],
            ]);

            // Associate user with company as owner
            $company->users()->attach(Auth::id(), ['role' => 'owner']);

            // Create default built-in workflows
            $company->createDefaultWorkflows();

            // Store company ID in session
            session(['signup_company_id' => $company->id, 'signup_step_2_complete' => true]);
        });

        return redirect()->route('signup.team');
    }

    /**
     * Show the team invitation form (Step 3).
     */
    public function showTeamForm(): View
    {
        if (!Auth::check() || !session('signup_step_2_complete')) {
            return redirect()->route('signup.email');
        }

        return view('signup.team');
    }

    /**
     * Handle team invitation submission (Step 3).
     */
    public function submitTeam(SignupTeamInvitationsRequest $request): RedirectResponse
    {
        if (!Auth::check() || !session('signup_step_2_complete')) {
            return redirect()->route('signup.email');
        }

        $validated = $request->validated();
        $companyId = session('signup_company_id');
        $teamEmails = $validated['team_member_emails'] ?? [];

        if (!empty($teamEmails)) {
            DB::transaction(function () use ($teamEmails, $companyId) {
                foreach ($teamEmails as $email) {
                    // Create invitation
                    $invitation = Invitation::create([
                        'company_id' => $companyId,
                        'invited_email' => $email,
                        'invited_by_user_id' => Auth::id(),
                        'invite_token' => Invitation::generateToken(),
                        'invite_token_expires_at' => now()->addDays(config('signup.invitation_token_expiry_days')),
                        'status' => 'pending',
                    ]);

                    // Send invitation email
                    Notification::route('mail', $email)
                        ->notify(new TeamInvitation($invitation));
                }
            });
        }

        // Clear signup session data
        session()->forget([
            'signup_token',
            'signup_email',
            'signup_step_1_complete',
            'signup_step_2_complete',
            'signup_company_id',
        ]);

        // Clean up signup_pending record
        SignupPending::where('email', Auth::user()->email)->delete();

        return redirect()->route('dashboard')->with('welcome', true);
    }

    /**
     * Skip team invitation step.
     */
    public function skipTeam(): RedirectResponse
    {
        if (!Auth::check() || !session('signup_step_2_complete')) {
            return redirect()->route('signup.email');
        }

        // Clear signup session data
        session()->forget([
            'signup_token',
            'signup_email',
            'signup_step_1_complete',
            'signup_step_2_complete',
            'signup_company_id',
        ]);

        // Clean up signup_pending record
        SignupPending::where('email', Auth::user()->email)->delete();

        return redirect()->route('dashboard')->with('welcome', true);
    }
}
