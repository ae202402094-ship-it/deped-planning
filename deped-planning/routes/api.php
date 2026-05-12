<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SchoolApiController; // <-- Don't forget this!

// Laravel's default user route (you can leave this here)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// YOUR NEW ROUTE:
// Note: Laravel automatically adds "/api" to the front of all routes in this file!
Route::middleware('auth:sanctum')->get('/schools/summary', [SchoolApiController::class, 'getSchoolSummary']);