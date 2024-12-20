<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;

//로그인
Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login']);


//지역 라우팅
Route::get('/regions', [RegionController::class, 'regions']);


//갤러리 라우팅
Route::get('/regions/galleryList', [GalleryController::class, 'galleryList']);

Route::post('/regions/gallery', [GalleryController::class, 'galleryAdd'])->middleware(JwtMiddleware::class);
Route::delete('/regions/gallery', [GalleryController::class, 'galleryDelete'])->middleware(JwtMiddleware::class);



//게시글 라우팅
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


//코멘트 라우팅
Route::get('/regions/gallery/post/comments', [GalleryController::class, 'viewComment']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/regions/gallery/post/comments', [GalleryController::class, 'commentAdd']);
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::delete('/regions/gallery/post/comments', [GalleryController::class, 'commentDelete']);
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::put('/regions/gallery/post/comments', [GalleryController::class, 'commentUpdate']);
});

?>