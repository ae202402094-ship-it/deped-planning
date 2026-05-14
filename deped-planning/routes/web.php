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
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolReportController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/
Route::get('/', [MapController::class, 'showPublicMap'])->name('public.map');

Route::prefix('schools')->group(function () {
    Route::get('/', [PublicSchoolController::class, 'listSchools'])->name('public.schools');
    Route::get('/{id}', [PublicSchoolController::class, 'showPublicDetail'])->name('public.view');
});

/*
|--------------------------------------------------------------------------
| 2. Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
/*
|--------------------------------------------------------------------------
| 3. Email Verification Routes
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
| 4. Protected Admin & Super Admin Routes (Shared)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    
    // Gateway: Redirects based on specific role
    Route::get('/', function () {
        return auth()->user()->isSuperAdmin() 
            ? redirect()->route('superadmin.dashboard') 
            : redirect()->route('admin.dashboard');
    })->name('admin.index');

    // Dashboard & Approvals
    Route::get('/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // School Management (CRUD)
    Route::prefix('schools')->group(function () {
        Route::get('/', [SchoolCrudController::class, 'manageSchools'])->name('admin.schools');
        Route::get('/create', [SchoolCrudController::class, 'createSchool'])->name('schools.create');
        Route::post('/admin/schools/store', [SchoolCrudController::class, 'storeSchool'])->name('schools.store');
        Route::get('/{id}/edit', [SchoolCrudController::class, 'editSchool'])->name('schools.edit');
        Route::put('/{id}', [SchoolCrudController::class, 'updateSchool'])->name('schools.update');
        Route::delete('/{id}', [SchoolCrudController::class, 'destroySchool'])->name('schools.destroy');
        
        // Archiving and Batch Actions
        Route::get('/archive', [SchoolCrudController::class, 'archivedSchools'])->name('schools.archive');
        Route::post('/{id}/restore', [SchoolCrudController::class, 'restoreSchool'])->name('schools.restore');
        Route::delete('/{id}/force-delete', [SchoolCrudController::class, 'forceDeleteSchool'])->name('schools.force_delete');
       Route::delete('/force-delete-batch', [SchoolCrudController::class, 'forceDeleteBatch'])->name('superadmin.force_delete_batch');
        
        // Duplication Check
        Route::post('/check-duplicate', [SchoolCrudController::class, 'checkDuplicate'])->name('schools.check');
    });

    // Data Sync & CSV Import
    Route::prefix('sync')->group(function () {
        Route::get('/download-sample', [SchoolImportController::class, 'downloadSampleCSV'])->name('schools.sample');
        Route::post('/import', [SchoolImportController::class, 'import'])->name('schools.import');
        Route::get('/import-preview', [SchoolImportController::class, 'showPreview'])->name('schools.import.preview');
        Route::post('/confirm-import', [SchoolImportController::class, 'confirmImport'])->name('schools.confirm_import');
    });

    // Reports & Tools
    Route::get('/map', [MapController::class, 'showMap'])->name('admin.map');
    Route::get('/history', [SchoolReportController::class, 'viewHistory'])->name('admin.history');
    Route::get('/reports/data-health', [SchoolReportController::class, 'dataHealthReport'])->name('admin.health_report');
    Route::get('/schools/{id}/report', [SchoolReportController::class, 'generateReport'])->name('schools.report');
});

/*
|--------------------------------------------------------------------------
| 5. Super Admin Exclusive Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/notifications', [SuperAdminController::class, 'notifications'])->name('superadmin.notifications');
    Route::post('/approve/{id}', [SuperAdminController::class, 'approveUser'])->name('superadmin.approve');
    Route::delete('/reject/{id}', [SuperAdminController::class, 'rejectUser'])->name('superadmin.reject');
    Route::get('/history', [SuperAdminController::class, 'history'])->name('superadmin.history');
    Route::put('/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('superadmin.update_user');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('superadmin.users.create');
    Route::post('/users/store', [SuperAdminController::class, 'storeUser'])->name('superadmin.users.store');
    Route::put('/users/{id}/reset-password', [SuperAdminController::class, 'adminResetPassword'])
        ->name('superadmin.users.reset_password');
    Route::post('/users/{id}/approve-password', [SuperAdminController::class, 'approvePasswordChange'])
        ->name('superadmin.approve_password');
    Route::delete('/users/{id}', [SuperAdminController::class, 'destroyUser'])->name('superadmin.users.destroy');


    // Super Admin specific Archive access
    Route::get('/archive', [SuperAdminController::class, 'archive'])->name('superadmin.archive');
    Route::post('/schools/{id}/restore', [SuperAdminController::class, 'restoreSchool'])->name('superadmin.restore_school');
    Route::delete('/schools/{id}/force-delete', [SuperAdminController::class, 'forceDeleteSchool'])->name('superadmin.force_delete_school');
});

/*
|--------------------------------------------------------------------------
| 6. Debugging & Testing
|--------------------------------------------------------------------------
*/
Route::get('/test-mail', function () {
    try {
        Mail::raw('Hi! This is a test email from DepEd Zamboanga.', function ($message) {
            $message->to('pettyrequest@gmail.com')->subject('Gmail Connection Test');
        });
        return "Email sent successfully! Check your inbox.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::middleware(['auth', 'verified', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    
    // ... other routes ...

    Route::prefix('schools')->group(function () {
        Route::get('/', [SchoolCrudController::class, 'manageSchools'])->name('admin.schools');
        Route::get('/archive', [SchoolCrudController::class, 'archivedSchools'])->name('schools.archive');
        
        // Single Actions
        Route::post('/{id}/restore', [SchoolCrudController::class, 'restoreSchool'])->name('schools.restore');
        Route::delete('/{id}/force-delete', [SchoolCrudController::class, 'forceDeleteSchool'])->name('schools.force_delete');
        
        // FIX: Pointing to the correct Controller for Batch Actions
        Route::delete('/force-delete-batch', [SchoolCrudController::class, 'forceDeleteBatch'])->name('superadmin.force_delete_batch');
        
        Route::post('/check-duplicate', [SchoolCrudController::class, 'checkDuplicate'])->name('schools.check');
    });
});