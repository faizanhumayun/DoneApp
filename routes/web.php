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
