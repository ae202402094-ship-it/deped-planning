<?php

use Illuminate\Support\Facades\Route;

Route::any('/census', [App\Http\Controllers\CensusController::class, 'index']);
Route::get('/', function () {
    return redirect('/census');
});
