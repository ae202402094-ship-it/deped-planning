<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/
// Interactive Map / Landing Page
Route::get('/', [CensusController::class, 'showPublicMap'])->name('public.map');
// School List and Details
Route::get('/schools', [CensusController::class, 'showPublicList'])->name('public.schools');
Route::get('/schools/{id}', [CensusController::class, 'showPublicDetail'])->name('public.view');

/*
|--------------------------------------------------------------------------
| 2. Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| 3. Email Verification (Custom)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
        $user = \App\Models\User::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid or expired verification link.');
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return redirect('/login')->with('success', 'Email verified! Please wait for Super Admin approval.');
    })->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| 4. Shared Admin & Super Admin (School Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin,super_admin'])->group(function () {
    
    // Base Admin Gateway
    Route::get('/admin', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('admin.dashboard');
    })->name('admin.index');

    // Dashboard & Approvals (AdminController)
    Route::get('/admin/dashboard', [CensusController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // Logic Routes (Logical Order: Place specific paths BEFORE generic ones)
    Route::delete('/admin/schools/clear-all', [CensusController::class, 'clearAllSchools'])->name('schools.clear_all');
    Route::get('/admin/schools/download-sample', [CensusController::class, 'downloadSampleCSV'])->name('schools.sample');
    Route::post('/admin/schools/import', [CensusController::class, 'import'])->name('schools.import');
    Route::post('/admin/schools/confirm-import', [CensusController::class, 'confirmImport'])->name('schools.confirm_import');
    Route::post('/admin/schools/check-duplicate', [CensusController::class, 'checkDuplicate'])->name('schools.check');

    // School Management (Standard CRUD - Manually defined to match your Controller names)
    Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
    Route::get('/admin/schools/create', [CensusController::class, 'createSchool'])->name('schools.create');
    Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
    Route::get('/admin/schools/{id}/edit', [CensusController::class, 'editSchool'])->name('schools.edit');
    Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/admin/schools/{id}', [CensusController::class, 'destroySchool'])->name('schools.destroy');

    // Admin Tools
    Route::get('/admin/map', [CensusController::class, 'showMap'])->name('admin.map');
    
    // School Resource (Handles Create, Store, Edit, Update, Delete)
    Route::resource('admin/schools', CensusController::class)->except(['create'])->names([
    'index'   => 'admin.schools',
    'store'   => 'schools.store',
    'edit'    => 'schools.edit',
    'update'  => 'schools.update',
    'destroy' => 'schools.destroy',
]);

    
    Route::post('/admin/schools/check-duplicate', [CensusController::class, 'checkDuplicate'])->name('schools.check');
});

/*
|--------------------------------------------------------------------------
| 5. Super Admin Only (Approvals, Notifications, History)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    // Dashboard & Control Panel
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    
    // Notifications & User Approvals
    Route::get('/super-admin/notifications', [SuperAdminController::class, 'notifications'])->name('superadmin.notifications');
    Route::post('/super-admin/approve/{id}', [SuperAdminController::class, 'approveUser'])->name('superadmin.approve');
    Route::delete('/super-admin/reject/{id}', [SuperAdminController::class, 'rejectUser'])->name('superadmin.reject');
    
    // System History Log
    Route::get('/super-admin/history', [SuperAdminController::class, 'history'])->name('superadmin.history');
    // Update User Route
Route::put('/super-admin/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('superadmin.update_user');
});

/*
|--------------------------------------------------------------------------
| 6. Testing Route
|--------------------------------------------------------------------------
*/
Route::get('/test-mail', function () {
    try {
        Mail::raw('Hi! This is a test email from DepEd Zamboanga.', function ($message) {
            $message->to('pettyrequest@gmail.com') // Change this to your personal email
                    ->subject('Gmail Connection Test');
        });
        return "Email sent successfully! Check your inbox.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});