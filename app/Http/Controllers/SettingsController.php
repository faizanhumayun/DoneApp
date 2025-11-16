<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index(): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.index', compact('company'));
    }

    /**
     * Display account settings.
     */
    public function account(): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.account', compact('company'));
    }

    /**
     * Update account settings.
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email_footer' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $company->update([
            'name' => $validated['name'],
            'email_footer' => $validated['email_footer'] ?? null,
            'logo_path' => $validated['logo_path'] ?? $company->logo_path,
        ]);

        return redirect()->route('settings.account')
            ->with('success', 'Account settings updated successfully.');
    }

    /**
     * Display billing information.
     */
    public function billing(): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.billing', compact('company'));
    }

    /**
     * Display logs (Login & Email).
     */
    public function logs(Request $request): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();
        $tab = $request->get('tab', 'login');

        // Fetch login logs (last 6 months)
        $loginLogs = \App\Models\LoginLog::where('company_id', $company->id)
            ->where('login_at', '>=', now()->subMonths(6))
            ->with('user')
            ->orderBy('login_at', 'desc')
            ->paginate(20);

        // Fetch email logs (last 6 months)
        $emailLogs = \App\Models\EmailLog::where('company_id', $company->id)
            ->where('sent_at', '>=', now()->subMonths(6))
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('settings.logs', compact('company', 'tab', 'loginLogs', 'emailLogs'));
    }

    /**
     * Display export data page.
     */
    public function exportData(): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.export-data', compact('company'));
    }

    /**
     * Generate and download data export.
     */
    public function downloadExport(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        $fileName = $company->id . '_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($company) {
            $handle = fopen('php://output', 'w');

            // Export Users
            fputcsv($handle, ['=== USERS ===']);
            fputcsv($handle, ['ID', 'First Name', 'Last Name', 'Email', 'Role', 'Status', 'Created At']);

            $users = $company->users()->get();
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->pivot->role ?? 'member',
                    $user->status ?? 'active',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fputcsv($handle, []); // Empty line

            // Export Projects
            fputcsv($handle, ['=== PROJECTS ===']);
            fputcsv($handle, ['ID', 'Name', 'Description', 'Status', 'Created At']);

            $projects = $company->projects()->get();
            foreach ($projects as $project) {
                fputcsv($handle, [
                    $project->id,
                    $project->name,
                    $project->description ?? '',
                    $project->status ?? 'active',
                    $project->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fputcsv($handle, []); // Empty line

            // Export Tasks
            fputcsv($handle, ['=== TASKS ===']);
            fputcsv($handle, ['ID', 'Task Number', 'Title', 'Description', 'Project', 'Status', 'Priority', 'Assignee', 'Creator', 'Due Date', 'Created At']);

            $tasks = \App\Models\Task::whereHas('project', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with(['project', 'assignee', 'creator'])->get();

            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->id,
                    $task->task_number ?? '',
                    $task->title,
                    strip_tags($task->description ?? ''),
                    $task->project->name ?? '',
                    $task->status ?? '',
                    $task->priority ?? '',
                    $task->assignee->full_name ?? '',
                    $task->creator->full_name ?? '',
                    $task->due_date?->format('Y-m-d') ?? '',
                    $task->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fputcsv($handle, []); // Empty line

            // Export Discussions
            fputcsv($handle, ['=== DISCUSSIONS ===']);
            fputcsv($handle, ['ID', 'Title', 'Body', 'Project', 'Type', 'Privacy', 'Creator', 'Created At']);

            $discussions = \App\Models\Discussion::where('company_id', $company->id)
                ->with(['creator', 'project'])->get();

            foreach ($discussions as $discussion) {
                fputcsv($handle, [
                    $discussion->id,
                    $discussion->title,
                    strip_tags($discussion->body ?? ''),
                    $discussion->project->name ?? 'Standalone',
                    $discussion->type ?? '',
                    $discussion->is_private ? 'Private' : 'Public',
                    $discussion->creator->full_name ?? '',
                    $discussion->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display calendar sync page.
     */
    public function calendarSync(): View
    {
        $this->ensureOwner();

        $user = Auth::user();
        $company = $user->companies->first();

        return view('settings.calendar-sync', compact('company', 'user'));
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
            abort(403, 'Only company owners can access settings.');
        }
    }
}
