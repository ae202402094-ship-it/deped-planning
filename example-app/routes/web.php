<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\CensusController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController; // Ensure your custom AuthController is used

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

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. The Verification Action (Customized for Pending Admins)
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    // Verify the secure signature
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid or expired verification link.');
    }

    // Mark email as verified if it hasn't been already
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Redirect to login with a clean success message
    return redirect('/login')->with('success', 'Email successfully verified! You can log in once the Super Admin approves your account.');
    
})->middleware(['signed'])->name('verification.verify'); // Removed the 'auth' middleware



Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==========================================
// 3. ADMIN ROUTES (CRUD Operations)
// Access: Both 'admin' and 'super_admin'
// ==========================================
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    Route::get('/admin/map', [CensusController::class, 'showMap'])->name('admin.map');
    
    // School CRUD
    Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
    Route::get('/admin/schools/create', [CensusController::class, 'createSchool'])->name('schools.create');
    Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
    Route::get('/admin/schools/{id}/edit', [CensusController::class, 'editSchool'])->name('schools.edit');
    Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/admin/schools/{id}', [CensusController::class, 'destroySchool'])->name('schools.destroy');
    Route::post('/admin/schools/check-duplicate', [CensusController::class, 'checkDuplicate'])->name('schools.check');
});

// ==========================================
// 4. SUPER ADMIN ROUTES (System Control)
// Access: ONLY 'super_admin'
// ==========================================
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');
});
