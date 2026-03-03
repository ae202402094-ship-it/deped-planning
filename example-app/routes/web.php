<?php
use App\Http\Controllers\CensusController;
use Illuminate\Support\Facades\Route;

// --- ADMIN DASHBOARD ROUTES ---
Route::get('/admin', [CensusController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
Route::match(['get', 'post'], '/admin/teachers', [CensusController::class, 'manageTeachers'])->name('admin.teachers');

// --- PUBLIC VIEWER ROUTES ---
// The main landing page for users to see all schools
Route::get('/', [CensusController::class, 'listSchools'])->name('public.schools');
// The page to view teachers for a specific school
Route::get('/view-census', [CensusController::class, 'showPublic'])->name('public.view');