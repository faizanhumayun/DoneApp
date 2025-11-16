<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorAuthController extends Controller
{
    public function __construct(private TwoFactorAuthService $twoFactorService)
    {
    }

    /**
     * Show 2FA setup page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.two-factor.index', compact('user', 'company'));
    }

    /**
     * Enable 2FA and generate QR code.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        // Generate secret and recovery codes
        $secret = $this->twoFactorService->generateSecret();
        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        // Store in session temporarily until confirmed
        session([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return redirect()->route('settings.two-factor.confirm');
    }

    /**
     * Show confirmation page with QR code.
     */
    public function showConfirm(): View
    {
        if (!session()->has('two_factor_secret')) {
            return redirect()->route('settings.two-factor.index')
                ->withErrors(['error' => 'Please start the 2FA setup process first.']);
        }

        $user = Auth::user();
        $company = $user->companies->first();
        $secret = session('two_factor_secret');
        $recoveryCodes = session('two_factor_recovery_codes');

        $qrCodeUrl = $this->twoFactorService->getQRCodeUrl(
            $company->name,
            $user->email,
            $secret
        );

        return view('settings.two-factor.confirm', compact('qrCodeUrl', 'secret', 'recoveryCodes'));
    }

    /**
     * Confirm and activate 2FA.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = session('two_factor_secret');
        $recoveryCodes = session('two_factor_recovery_codes');

        if (!$secret || !$recoveryCodes) {
            return back()->withErrors(['error' => 'Session expired. Please start over.']);
        }

        // Verify the code
        if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
            return back()->withErrors(['code' => 'The verification code is invalid.']);
        }

        // Enable 2FA for the user
        $user = Auth::user();
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ]);

        // Clear session
        session()->forget(['two_factor_secret', 'two_factor_recovery_codes']);

        return redirect()->route('settings.two-factor.index')
            ->with('success', 'Two-factor authentication has been enabled successfully.');
    }

    /**
     * Show recovery codes.
     */
    public function showRecoveryCodes(): View
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            abort(403, 'Two-factor authentication is not enabled.');
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return view('settings.two-factor.recovery-codes', compact('recoveryCodes'));
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        if (!$user->two_factor_enabled) {
            abort(403, 'Two-factor authentication is not enabled.');
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        return redirect()->route('settings.two-factor.recovery-codes')
            ->with('success', 'Recovery codes have been regenerated.');
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('settings.two-factor.index')
            ->with('success', 'Two-factor authentication has been disabled.');
    }
}
