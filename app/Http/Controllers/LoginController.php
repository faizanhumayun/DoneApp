<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\LoginLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show(): View
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        // Attempt to authenticate the user
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Log the login
            $user = Auth::user();
            $company = $user->companies->first();

            if ($company) {
                $loginLog = LoginLog::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_at' => now(),
                ]);

                // Store login log ID in session for logout tracking
                $request->session()->put('login_log_id', $loginLog->id);
            }

            // Redirect to intended page or dashboard
            return redirect()->intended(route('dashboard'))
                ->with('message', 'Welcome back, ' . Auth::user()->full_name . '!');
        }

        // Authentication failed
        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->withInput($request->only('email', 'remember'));
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Update login log with logout time and duration
        $loginLogId = $request->session()->get('login_log_id');

        if ($loginLogId) {
            $loginLog = LoginLog::find($loginLogId);

            if ($loginLog) {
                $logoutAt = now();
                $duration = $logoutAt->diffInSeconds($loginLog->login_at);

                $loginLog->update([
                    'logout_at' => $logoutAt,
                    'session_duration' => $duration,
                ]);
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'You have been logged out successfully.');
    }
}
