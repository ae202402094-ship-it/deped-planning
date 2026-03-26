<?php

Route::get('/schools/{id}', [App\Http\Controllers\SchoolCrudController::class, 'getApiData']);