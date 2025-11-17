<?php

use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('signup.email');
});

// Signup Routes
Route::prefix('signup')->name('signup.')->group(function () {
    // Step 0: Email Capture
    Route::get('/', [SignupController::class, 'showEmailForm'])->name('email');
    Route::post('/', [SignupController::class, 'submitEmail'])->name('email.submit');

    // Step 0b: Check Email
    Route::get('/check-email', [SignupController::class, 'showCheckEmail'])->name('check-email');
    Route::post('/resend', [SignupController::class, 'resendVerification'])
        ->name('resend')
        ->middleware('throttle:3,10'); // 3 attempts per 10 minutes

    // Email Verification
    Route::get('/verify/{token}', [SignupController::class, 'verifyEmail'])->name('verify');

    // Step 1: Profile Setup
    Route::get('/profile', [SignupController::class, 'showProfileForm'])->name('profile');
    Route::post('/profile', [SignupController::class, 'submitProfile'])->name('profile.submit');

    // Step 2: Company Setup
    Route::get('/company', [SignupController::class, 'showCompanyForm'])->name('company');
    Route::post('/company', [SignupController::class, 'submitCompany'])->name('company.submit');

    // Step 3: Team Invitations
    Route::get('/team', [SignupController::class, 'showTeamForm'])->name('team');
    Route::post('/team', [SignupController::class, 'submitTeam'])->name('team.submit');
    Route::post('/team/skip', [SignupController::class, 'skipTeam'])->name('team.skip');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Workspace (Not accessible to guests)
Route::get('/workspace', [App\Http\Controllers\WorkspaceController::class, 'index'])->middleware(['auth', 'ensure.not.guest'])->name('workspace');

// Workflows (Not accessible to guests)
Route::middleware(['auth', 'ensure.not.guest'])->group(function () {
    Route::get('/workflows', [App\Http\Controllers\WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/workflows/create', [App\Http\Controllers\WorkflowController::class, 'create'])->name('workflows.create');
    Route::post('/workflows', [App\Http\Controllers\WorkflowController::class, 'store'])->name('workflows.store');
    Route::get('/workflows/{workflow}/edit', [App\Http\Controllers\WorkflowController::class, 'edit'])->name('workflows.edit');
    Route::put('/workflows/{workflow}', [App\Http\Controllers\WorkflowController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{workflow}', [App\Http\Controllers\WorkflowController::class, 'destroy'])->name('workflows.destroy');
    Route::post('/workflows/{workflow}/duplicate', [App\Http\Controllers\WorkflowController::class, 'duplicate'])->name('workflows.duplicate');
});

// Projects (Browse/Create not accessible to guests, but viewing specific projects is allowed)
Route::middleware(['auth', 'ensure.not.guest'])->group(function () {
    Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/archive', [App\Http\Controllers\ProjectController::class, 'archive'])->name('projects.archive');
});

// Project show route (accessible to guests if they have tasks in it)
Route::middleware('auth')->group(function () {
    Route::get('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
});

// Tasks (View-only for guests, full access for members)
Route::middleware('auth')->group(function () {
    // Guests can view tasks index and individual tasks
    Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/projects/{project}/tasks/{task}', [App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');

    // Guests can comment on tasks they can access
    Route::post('/projects/{project}/tasks/{task}/comments', [App\Http\Controllers\TaskCommentController::class, 'store'])->name('task-comments.store');
});

// Task management (Not accessible to guests)
Route::middleware(['auth', 'ensure.not.guest'])->group(function () {
    Route::get('/projects/{project}/tasks/create', [App\Http\Controllers\TaskController::class, 'create'])->name('tasks.create');
    Route::post('/projects/{project}/tasks', [App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('/projects/{project}/tasks/{task}/edit', [App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/projects/{project}/tasks/{task}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/projects/{project}/tasks/{task}/duplicate', [App\Http\Controllers\TaskController::class, 'duplicate'])->name('tasks.duplicate');

    // Delete comments (guests cannot delete comments)
    Route::delete('/projects/{project}/tasks/{task}/comments/{comment}', [App\Http\Controllers\TaskCommentController::class, 'destroy'])->name('task-comments.destroy');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Users Management (Owner and Admin only)
Route::middleware(['auth', 'ensure.not.guest'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
    Route::post('/invite', [App\Http\Controllers\UserController::class, 'invite'])->name('invite');
    Route::post('/invitations/{invitation}/resend', [App\Http\Controllers\UserController::class, 'resendInvitation'])->name('invitations.resend');
    Route::delete('/invitations/{invitation}', [App\Http\Controllers\UserController::class, 'cancelInvitation'])->name('invitations.cancel');
    Route::put('/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('update');
    Route::post('/{user}/archive', [App\Http\Controllers\UserController::class, 'archive'])->name('archive');
    Route::post('/{user}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('restore');
});

// Discussions
Route::middleware('auth')->prefix('discussions')->name('discussions.')->group(function () {
    Route::get('/', [App\Http\Controllers\DiscussionController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\DiscussionController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\DiscussionController::class, 'store'])->name('store');
    Route::get('/{discussion}', [App\Http\Controllers\DiscussionController::class, 'show'])->name('show');
    Route::get('/{discussion}/edit', [App\Http\Controllers\DiscussionController::class, 'edit'])->name('edit');
    Route::put('/{discussion}', [App\Http\Controllers\DiscussionController::class, 'update'])->name('update');
    Route::delete('/{discussion}', [App\Http\Controllers\DiscussionController::class, 'destroy'])->name('destroy');

    // Comments
    Route::post('/{discussion}/comments', [App\Http\Controllers\DiscussionController::class, 'storeComment'])->name('comments.store');
    Route::delete('/{discussion}/comments/{comment}', [App\Http\Controllers\DiscussionController::class, 'destroyComment'])->name('comments.destroy');
});

// Login
Route::get('/login', [App\Http\Controllers\LoginController::class, 'show'])->name('login');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Team Invitation Acceptance
Route::get('/invitation/accept', [App\Http\Controllers\InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/accept', [App\Http\Controllers\InvitationController::class, 'accept'])->name('invitation.accept.submit');

// Guest Management (Admin/Member only - not guests)
Route::middleware(['auth', 'ensure.not.guest'])->prefix('guests')->name('guests.')->group(function () {
    Route::get('/', [App\Http\Controllers\GuestInviteController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\GuestInviteController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\GuestInviteController::class, 'store'])->name('store');
    Route::post('/{invite}/resend', [App\Http\Controllers\GuestInviteController::class, 'resend'])->name('resend');
    Route::delete('/invites/{invite}', [App\Http\Controllers\GuestInviteController::class, 'cancelInvite'])->name('invites.cancel');
    Route::delete('/{user}', [App\Http\Controllers\GuestInviteController::class, 'remove'])->name('remove');
});

// Guest Signup (Public - token-based)
Route::prefix('guests')->name('guests.')->group(function () {
    Route::get('/signup/{token}', [App\Http\Controllers\GuestInviteController::class, 'showSignup'])->name('signup');
    Route::post('/signup/{token}', [App\Http\Controllers\GuestInviteController::class, 'signup'])->name('signup.submit');
});

// Guest Dashboard (Guest users only)
Route::middleware('auth')->group(function () {
    Route::get('/guests/dashboard', [App\Http\Controllers\GuestInviteController::class, 'dashboard'])->name('guests.dashboard');
});

// Settings (Owner only)
Route::middleware(['auth', 'ensure.not.guest'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');
    Route::get('/account', [App\Http\Controllers\SettingsController::class, 'account'])->name('account');
    Route::put('/account', [App\Http\Controllers\SettingsController::class, 'updateAccount'])->name('account.update');
    Route::get('/billing', [App\Http\Controllers\SettingsController::class, 'billing'])->name('billing');
    Route::get('/logs', [App\Http\Controllers\SettingsController::class, 'logs'])->name('logs');
    Route::get('/export-data', [App\Http\Controllers\SettingsController::class, 'exportData'])->name('export');
    Route::post('/export-data/download', [App\Http\Controllers\SettingsController::class, 'downloadExport'])->name('export.download');
    Route::get('/calendar-sync', [App\Http\Controllers\SettingsController::class, 'calendarSync'])->name('calendar-sync');

    // Two-Factor Authentication
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/', [App\Http\Controllers\TwoFactorAuthController::class, 'index'])->name('index');
        Route::post('/enable', [App\Http\Controllers\TwoFactorAuthController::class, 'enable'])->name('enable');
        Route::get('/confirm', [App\Http\Controllers\TwoFactorAuthController::class, 'showConfirm'])->name('confirm');
        Route::post('/confirm', [App\Http\Controllers\TwoFactorAuthController::class, 'confirm'])->name('confirm');
        Route::get('/recovery-codes', [App\Http\Controllers\TwoFactorAuthController::class, 'showRecoveryCodes'])->name('recovery-codes');
        Route::post('/regenerate-codes', [App\Http\Controllers\TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('regenerate-codes');
        Route::delete('/disable', [App\Http\Controllers\TwoFactorAuthController::class, 'disable'])->name('disable');
    });

    // Access Requests
    Route::prefix('access-requests')->name('access-requests.')->group(function () {
        Route::get('/', [App\Http\Controllers\AccessRequestController::class, 'index'])->name('index');
        Route::post('/{accessRequest}/approve', [App\Http\Controllers\AccessRequestController::class, 'approve'])->name('approve');
        Route::post('/{accessRequest}/deny', [App\Http\Controllers\AccessRequestController::class, 'deny'])->name('deny');
    });
});

// Public Access Request Form
Route::get('/request-access/{companyId}', [App\Http\Controllers\AccessRequestController::class, 'create'])->name('access-requests.create');
Route::post('/request-access/{companyId}', [App\Http\Controllers\AccessRequestController::class, 'store'])->name('access-requests.store');
