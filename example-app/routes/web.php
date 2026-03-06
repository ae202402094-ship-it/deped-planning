<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\CensusController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// --- Public Routes ---
Route::get('/', [CensusController::class, 'listSchools'])->name('public.schools');
Route::get('/view-census', [CensusController::class, 'showPublic'])->name('public.view');

// --- Authentication Routes ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// --- Email Verification Routes ---

// 1. The Notice (Fixed typo: changed 'emails' to 'email')
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. The Verification Action (When user clicks link in email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Resend Notification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// --- Authenticated Admin Routes ---
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
    Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
    Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
});