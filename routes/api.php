<?php

use Illuminate\Support\Facades\Route;

Route::get('/apartments', [\App\Http\Controllers\ApartmentController::class, 'index']);
Route::get('/apartments/{id}', [\App\Http\Controllers\ApartmentController::class, 'show']);
