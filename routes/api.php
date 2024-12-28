<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GalleryPostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;

//로그인
Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

//지역 라우팅
Route::get('/regions', [RegionController::class, 'regions']);


//갤러리 라우팅
Route::get('/regions/galleryList', [GalleryController::class, 'galleryList']);

Route::post('/regions/gallery', [GalleryController::class, 'galleryAdd'])->middleware(JwtMiddleware::class);
Route::delete('/regions/gallery', [GalleryController::class, 'galleryDelete'])->middleware(JwtMiddleware::class);



//게시글 라우팅
Route::get('/regions/gallery/postList', [GalleryPostController::class, 'postList']);
Route::get('/regions/gallery/post', [GalleryPostController::class, 'viewPost']);

Route::post('/regions/gallery/post', [GalleryPostController::class, 'postAdd'])->middleware(JwtMiddleware::class);
Route::delete('/regions/gallery/post', [GalleryPostController::class, 'postDelete'])->middleware(JwtMiddleware::class);
Route::put('/regions/gallery/post', [GalleryPostController::class, 'postUpdate'])->middleware(JwtMiddleware::class);



//코멘트 라우팅
Route::get('/regions/gallery/post/comments', [CommentController::class, 'viewComment']);

Route::post('/regions/gallery/post/comments', [CommentController::class, 'commentAdd'])->middleware(JwtMiddleware::class);
Route::delete('/regions/gallery/post/comments', [CommentController::class, 'commentDelete'])->middleware(JwtMiddleware::class);
Route::put('/regions/gallery/post/comments', [CommentController::class, 'commentUpdate'])->middleware(JwtMiddleware::class);

//통합 검색
Route::get('/total/search', [GalleryController::class, 'viewComment']);
?>