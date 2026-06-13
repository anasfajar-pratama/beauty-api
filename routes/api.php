<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomepageContentController;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'publicIndex']);
Route::get('/products/{id}', [ProductController::class, 'publicShow']);
Route::get('/testimonials', [TestimonialController::class, 'publicIndex']);
Route::get('/gallery', [GalleryController::class, 'publicIndex']);
Route::get('/homepage-content', [HomepageContentController::class, 'publicIndex']);

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/seed', [AuthController::class, 'seed']);

/*
|--------------------------------------------------------------------------
| Admin Routes (protected by auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/stats', [AuthController::class, 'stats']);

    // Products
    Route::get('/admin/products', [ProductController::class, 'index']);
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    // Testimonials
    Route::get('/admin/testimonials', [TestimonialController::class, 'index']);
    Route::post('/admin/testimonials', [TestimonialController::class, 'store']);
    Route::put('/admin/testimonials/{id}', [TestimonialController::class, 'update']);
    Route::delete('/admin/testimonials/{id}', [TestimonialController::class, 'destroy']);

    // Gallery
    Route::get('/admin/gallery', [GalleryController::class, 'index']);
    Route::post('/admin/gallery', [GalleryController::class, 'store']);
    Route::put('/admin/gallery/{id}', [GalleryController::class, 'update']);
    Route::delete('/admin/gallery/{id}', [GalleryController::class, 'destroy']);

    // Homepage Content
    Route::post('/admin/homepage-content', [HomepageContentController::class, 'store']);

    // Upload
    Route::post('/admin/upload', [UploadController::class, 'upload']);
});
