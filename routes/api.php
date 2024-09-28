<?php

use App\Http\Controllers\ApartmentController;
use Illuminate\Support\Facades\Route;

Route::get('/apartments', [ApartmentController::class, 'filter']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
Route::patch('/apartments/{id}', [ApartmentController::class, 'update']);
