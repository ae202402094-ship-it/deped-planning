<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolImportController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PublicSchoolController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SchoolCrudController;
use App\Http\Controllers\SchoolReportController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/
// Interactive Map / Landing Page
Route::get('/', [MapController::class, 'showPublicMap'])->name('public.map');

// School List and Details
Route::get('/schools', [PublicSchoolController::class, 'listSchools'])->name('public.schools');
Route::get('/schools/{id}', [PublicSchoolController::class, 'showPublicDetail'])->name('public.view');

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

    // Dashboard & Approvals
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // Data Sync & Import logic
    Route::delete('/admin/schools/clear-all', [SchoolImportController::class, 'clearAllSchools'])->name('schools.clear_all');
    Route::get('/admin/schools/download-sample', [SchoolImportController::class, 'downloadSampleCSV'])->name('schools.sample');
    Route::post('/admin/schools/import', [SchoolImportController::class, 'import'])->name('schools.import');
    Route::post('/admin/schools/confirm-import', [SchoolImportController::class, 'confirmImport'])->name('schools.confirm_import');
    Route::post('/admin/schools/check-duplicate', [SchoolCrudController::class, 'checkDuplicate'])->name('schools.check');

    // School Management CRUD
    Route::get('/admin/schools', [SchoolCrudController::class, 'manageSchools'])->name('admin.schools');
    Route::get('/admin/schools/create', [SchoolCrudController::class, 'createSchool'])->name('schools.create');
    Route::post('/admin/schools', [SchoolCrudController::class, 'storeSchool'])->name('schools.store');
    Route::get('/admin/schools/{id}/edit', [SchoolCrudController::class, 'editSchool'])->name('schools.edit');
    Route::put('/admin/schools/{id}', [SchoolCrudController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/admin/schools/{id}', [SchoolCrudController::class, 'destroySchool'])->name('schools.destroy');
    Route::post('/admin/schools/{id}/restore', [SchoolCrudController::class, 'restoreSchool'])->name('schools.restore');

    // Admin Tools & Reporting
    Route::get('/admin/map', [MapController::class, 'showMap'])->name('admin.map');
    Route::get('/admin/history', [SchoolReportController::class, 'viewHistory'])->name('admin.history');
    Route::get('/admin/schools/{id}/report', [SchoolReportController::class, 'generateReport'])->name('schools.report');

    Route::get('/admin/reports/data-health', [SchoolReportController::class, 'dataHealthReport'])->name('admin.health_report');

    Route::get('/admin/schools/archive', [SchoolCrudController::class, 'archivedSchools'])->name('schools.archive');
    Route::post('/admin/schools/{id}/restore', [SchoolCrudController::class, 'restoreSchool'])->name('schools.restore');
    Route::delete('/admin/schools/{id}/force-delete', [SchoolCrudController::class, 'forceDeleteSchool'])->name('schools.force_delete');

});

/*
|--------------------------------------------------------------------------
| 5. Super Admin Only (Restoration, Archiving, & User Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    // Dashboard & User Approvals
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/super-admin/notifications', [SuperAdminController::class, 'notifications'])->name('superadmin.notifications');
    Route::post('/super-admin/approve/{id}', [SuperAdminController::class, 'approveUser'])->name('superadmin.approve');
    Route::delete('/super-admin/reject/{id}', [SuperAdminController::class, 'rejectUser'])->name('superadmin.reject');
    
    // System Oversight
    Route::get('/super-admin/history', [SuperAdminController::class, 'history'])->name('superadmin.history');
    Route::put('/super-admin/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('superadmin.update_user');

    // Archiving & Data Restoration
    Route::get('/super-admin/archive', [SuperAdminController::class, 'archive'])->name('admin.schools.archive');
    Route::post('/super-admin/schools/{id}/restore', [SuperAdminController::class, 'restoreSchool'])->name('superadmin.restore_school');
    Route::delete('/super-admin/schools/{id}/force-delete', [SuperAdminController::class, 'forceDeleteSchool'])->name('superadmin.force_delete_school');
});

/*
|--------------------------------------------------------------------------
| 6. Debugging & Testing
|--------------------------------------------------------------------------
*/
Route::get('/test-mail', function () {
    try {
        Mail::raw('Hi! This is a test email from DepEd Zamboanga.', function ($message) {
            $message->to('pettyrequest@gmail.com') 
                    ->subject('Gmail Connection Test');
        });
        return "Email sent successfully! Check your inbox.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});