<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\GalleryController;

Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login']);

Route::get('/regions', [RegionController::class, 'regions']);

Route::get('/regions/gallery', [GalleryController::class, 'galleryList']);
Route::post('/regions/gallery', [GalleryController::class, 'galleryAdd']); 
Route::delete('/regions/gallery', [GalleryController::class, 'galleryDelete']);

?>