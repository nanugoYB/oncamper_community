<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\GalleryController;

Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login']);

Route::get('/regions', [RegionController::class, 'regions']);

Route::get('/regions/galleryList', [GalleryController::class, 'galleryList']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/regions/gallery', [GalleryController::class, 'galleryAdd']);
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::delete('/regions/gallery', [GalleryController::class, 'galleryDelete']);
});

Route::get('/regions/gallery/postList', [GalleryController::class, 'postList']);
Route::get('/regions/gallery/post', [GalleryController::class, 'viewPost']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/regions/gallery/post', [GalleryController::class, 'postAdd']);
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::delete('/regions/gallery/post', [GalleryController::class, 'postDelete']);
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::put('/regions/gallery/post', [GalleryController::class, 'postUpdate']);
});
?>