<?php
use App\Http\Controllers\CensusController;
use Illuminate\Support\Facades\Route;

// --- ADMIN DASHBOARD ROUTES ---
Route::get('/admin', [CensusController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools');
Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
Route::get('/admin/schools/{id}/edit', [CensusController::class, 'editSchool'])->name('schools.edit');
Route::get('/admin/map', [CensusController::class, 'showMap'])->name('admin.map');

// --- PUBLIC VIEWER ROUTES ---
// The main landing page for users to see all schools
Route::get('/', [CensusController::class, 'listSchools'])->name('public.schools');
Route::get('/view-census', [CensusController::class, 'showPublic'])->name('public.view');
