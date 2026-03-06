<?php
use App\Http\Controllers\CensusController;
use Illuminate\Support\Facades\Route;

// --- ADMIN DASHBOARD ROUTES ---
Route::get('/admin', [CensusController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/admin/schools', [CensusController::class, 'manageSchools'])->name('admin.schools'); // The List
Route::get('/admin/schools/create', [CensusController::class, 'createSchool'])->name('schools.create'); // The New Page
Route::post('/admin/schools', [CensusController::class, 'storeSchool'])->name('schools.store');
Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
Route::get('/admin/schools/{id}/edit', [CensusController::class, 'editSchool'])->name('schools.edit');
Route::get('/admin/map', [CensusController::class, 'showMap'])->name('admin.map');

// --- PUBLIC VIEWER ROUTES ---

// Set the Interactive Map as the Landing Page
Route::get('/', [CensusController::class, 'showPublicMap'])->name('public.schools');

// Individual School Profile View
Route::get('/view-census/{id}', [CensusController::class, 'showPublic'])->name('public.view');

// backup
// Route::get('/directory', [CensusController::class, 'listSchools'])->name('public.list');

//functions
Route::post('/admin/schools/check-duplicate', [CensusController::class, 'checkDuplicate'])->name('schools.check');
Route::delete('/admin/schools/{id}', [CensusController::class, 'destroySchool'])->name('schools.destroy');
Route::put('/admin/schools/{id}', [CensusController::class, 'updateSchool'])->name('schools.update');
