<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\Company;
use App\Models\GuestInvite;
use App\Mail\GuestInvitationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AccessRequestController extends Controller
{
    /**
     * Show the public access request form.
     */
    public function create(string $companySlug): View
    {
        // For now, we'll use company ID. In production, you'd want a slug
        $company = Company::findOrFail($companySlug);

        return view('access-requests.create', compact('company'));
    }

    /**
     * Store a new access request.
     */
    public function store(Request $request, string $companySlug): RedirectResponse
    {
        $company = Company::findOrFail($companySlug);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if there's already a pending request
        $existing = AccessRequest::where('company_id', $company->id)
            ->where('email', $validated['email'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'You already have a pending access request.']);
        }

        AccessRequest::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'message' => $validated['message'] ?? null,
            'company_id' => $company->id,
            'status' => 'pending',
        ]);

        return redirect()->route('access-requests.create', $company->id)
            ->with('success', 'Your access request has been submitted. The company owner will review it shortly.');
    }

    /**
     * List all access requests (Owner only).
     */
    public function index(Request $request): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        $status = $request->get('status', 'pending');

        $requests = AccessRequest::where('company_id', $company->id)
            ->where('status', $status)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('settings.access-requests.index', compact('requests', 'status'));
    }

    /**
     * Approve an access request.
     */
    public function approve(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        if ($accessRequest->company_id !== $company->id) {
            abort(403);
        }

        if ($accessRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'This request has already been reviewed.']);
        }

        $validated = $request->validate([
            'send_invite' => ['boolean'],
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Mark as approved
        $accessRequest->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        // Optionally send guest invite
        if ($validated['send_invite'] ?? false) {
            $invite = GuestInvite::create([
                'first_name' => $accessRequest->first_name,
                'last_name' => $accessRequest->last_name,
                'email' => $accessRequest->email,
                'token' => GuestInvite::generateToken(),
                'token_expires_at' => now()->addDays(7),
                'invited_by_user_id' => $user->id,
                'company_id' => $company->id,
                'personal_message' => "Your access request has been approved!",
                'invited_from_type' => 'access_request',
                'invited_from_id' => $accessRequest->id,
            ]);

            $accessRequest->update(['guest_invite_id' => $invite->id]);

            // Send invitation email
            Mail::to($invite->email)->send(new GuestInvitationMail($invite));

            // Log email
            \App\Models\EmailLog::create([
                'company_id' => $company->id,
                'recipient' => $invite->email,
                'subject' => 'Guest Invitation (Access Request Approved)',
                'type' => 'access-request-approved',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return back()->with('success', 'Access request approved' . (($validated['send_invite'] ?? false) ? ' and invitation sent' : '') . '.');
    }

    /**
     * Deny an access request.
     */
    public function deny(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        if ($accessRequest->company_id !== $company->id) {
            abort(403);
        }

        if ($accessRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'This request has already been reviewed.']);
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $accessRequest->update([
            'status' => 'denied',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return back()->with('success', 'Access request denied.');
    }

    /**
     * Ensure the authenticated user is an owner.
     */
    private function ensureOwner(): void
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            abort(403, 'No company associated with your account.');
        }

        $role = $company->users()->where('user_id', $user->id)->first()?->pivot?->role;

        if ($role !== 'owner') {
            abort(403, 'Only company owners can manage access requests.');
        }
    }
}
