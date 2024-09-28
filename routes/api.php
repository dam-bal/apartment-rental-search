<?php

use App\Http\Controllers\ApartmentController;
use Illuminate\Support\Facades\Route;

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
Route::get('/apartments-filter', [ApartmentController::class, 'filter']);
