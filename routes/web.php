<?php

use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

// Dashboard (placeholder for now)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Login (placeholder for now)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');
