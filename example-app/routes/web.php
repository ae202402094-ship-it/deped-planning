<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\CensusController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Interactive Map is the Landing Page
Route::get('/', [CensusController::class, 'showPublicMap'])->name('public.schools');
// Individual School Profile View
Route::get('/view-census/{id}', [CensusController::class, 'showPublic'])->name('public.view');

/*
|--------------------------------------------------------------------------
| Authentication & Email Verification
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Email Verification Notices & Actions
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/admin')->with('verified', true);
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Admin & Authenticated Routes
|--------------------------------------------------------------------------
*/
//Route::middleware(['auth'])->group(function () {
    
    // Dashboard & User Management (Managed by AdminController)
    Route::get('/admin', [CensusController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // School Registry Management (Managed by CensusController)
    Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
    Route::get('/admin/schools/create', [CensusController::class, 'createSchool'])->name('schools.create');
    Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
    //For Import
    Route::get('/admin/schools/download-sample', [CensusController::class, 'downloadSampleCSV'])->name('schools.sample');
    Route::post('/admin/schools/import', [CensusController::class, 'import'])->name('schools.import');
    Route::post('/admin/schools/confirm-import', [CensusController::class, 'confirmImport'])->name('schools.confirm_import');

    Route::get('/admin/schools/{id}/edit', [CensusController::class, 'editSchool'])->name('schools.edit');
    Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/admin/schools/{id}', [CensusController::class, 'destroySchool'])->name('schools.destroy');

    // Admin Tools
    Route::get('/admin/map', [CensusController::class, 'showMap'])->name('admin.map');
    
    // Live Duplicate Checker with Security Throttling
    Route::post('/admin/schools/check-duplicate', [CensusController::class, 'checkDuplicate'])
        ->middleware('throttle:60,1') 
        ->name('schools.check');
//});