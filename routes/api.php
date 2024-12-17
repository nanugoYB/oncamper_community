<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;


Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login']);

Route::get('/regions', [RegionController::class, 'regions']);

?>