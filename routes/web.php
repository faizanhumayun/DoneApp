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

// Workspace
Route::get('/workspace', [App\Http\Controllers\WorkspaceController::class, 'index'])->middleware('auth')->name('workspace');

// Workflows
Route::middleware('auth')->group(function () {
    Route::get('/workflows', [App\Http\Controllers\WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/workflows/create', [App\Http\Controllers\WorkflowController::class, 'create'])->name('workflows.create');
    Route::post('/workflows', [App\Http\Controllers\WorkflowController::class, 'store'])->name('workflows.store');
    Route::get('/workflows/{workflow}/edit', [App\Http\Controllers\WorkflowController::class, 'edit'])->name('workflows.edit');
    Route::put('/workflows/{workflow}', [App\Http\Controllers\WorkflowController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{workflow}', [App\Http\Controllers\WorkflowController::class, 'destroy'])->name('workflows.destroy');
    Route::post('/workflows/{workflow}/duplicate', [App\Http\Controllers\WorkflowController::class, 'duplicate'])->name('workflows.duplicate');
});

// Projects
Route::middleware('auth')->group(function () {
    Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/archive', [App\Http\Controllers\ProjectController::class, 'archive'])->name('projects.archive');
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
