<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomepageContentController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\HeroController;

Route::get('/settings', [SettingController::class, 'publicIndex']);
Route::get('/subcategories', [SubcategoryController::class, 'publicIndex']);
Route::get('/products', [ProductController::class, 'publicIndex']);
Route::get('/products/{id}', [ProductController::class, 'publicShow']);
Route::get('/testimonials', [TestimonialController::class, 'publicIndex']);
Route::post('/testimonials', [TestimonialController::class, 'publicStore']);
Route::get('/gallery', [GalleryController::class, 'publicIndex']);
Route::get('/homepage-content', [HomepageContentController::class, 'publicIndex']);
Route::get('/about-content', [HomepageContentController::class, 'publicAbout']);
Route::get('/legal-achievements', function () {
    $setting = App\Models\Setting::where('key', 'legal_achievements')->first();
    return response()->json($setting ? json_decode($setting->value, true) : []);
});
Route::get('/heroes', [HeroController::class, 'publicIndex']);

Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/seed', [AuthController::class, 'seed']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/me', [AuthController::class, 'me']);
    Route::get('/admin/stats', [AuthController::class, 'stats']);

    Route::get('/admin/subcategories', [SubcategoryController::class, 'index']);
    Route::post('/admin/subcategories', [SubcategoryController::class, 'store']);
    Route::put('/admin/subcategories/{id}', [SubcategoryController::class, 'update']);
    Route::delete('/admin/subcategories/{id}', [SubcategoryController::class, 'destroy']);

    Route::get('/admin/products', [ProductController::class, 'index']);
    Route::get('/admin/products/{id}', [ProductController::class, 'show']);
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    Route::post('/admin/products/{productId}/images', [ProductImageController::class, 'store']);
    Route::delete('/admin/products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);
    Route::put('/admin/products/{productId}/images/{imageId}/primary', [ProductImageController::class, 'setPrimary']);

    Route::get('/admin/testimonials', [TestimonialController::class, 'index']);
    Route::post('/admin/testimonials', [TestimonialController::class, 'store']);
    Route::put('/admin/testimonials/{id}', [TestimonialController::class, 'update']);
    Route::delete('/admin/testimonials/{id}', [TestimonialController::class, 'destroy']);

    Route::get('/admin/gallery', [GalleryController::class, 'index']);
    Route::post('/admin/gallery', [GalleryController::class, 'store']);
    Route::put('/admin/gallery/{id}', [GalleryController::class, 'update']);
    Route::delete('/admin/gallery/{id}', [GalleryController::class, 'destroy']);

    Route::get('/admin/homepage-content', [HomepageContentController::class, 'publicIndex']);
    Route::post('/admin/homepage-content', [HomepageContentController::class, 'store']);
    Route::get('/admin/about-content', [HomepageContentController::class, 'publicAbout']);
    Route::post('/admin/about-content', [HomepageContentController::class, 'storeAbout']);
    Route::post('/admin/upload', [UploadController::class, 'upload']);

    // RBAC & Settings
    Route::get('/admin/admins', [AdminController::class, 'index']);
    Route::post('/admin/admins', [AdminController::class, 'store']);
    Route::put('/admin/admins/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/admins/{id}', [AdminController::class, 'destroy']);

    Route::get('/admin/roles', [RoleController::class, 'index']);
    Route::post('/admin/roles', [RoleController::class, 'store']);
    Route::put('/admin/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/admin/roles/{id}', [RoleController::class, 'destroy']);

    Route::get('/admin/permissions', [PermissionController::class, 'index']);

    Route::get('/admin/settings', [SettingController::class, 'index']);
    Route::post('/admin/settings', [SettingController::class, 'store']);
    Route::put('/admin/settings/{id}', [SettingController::class, 'update']);
    Route::delete('/admin/settings/{id}', [SettingController::class, 'destroy']);

    Route::get('/admin/activity-logs', [ActivityLogController::class, 'index']);

    // Hero management
    Route::get('/admin/heroes', [HeroController::class, 'index']);
    Route::post('/admin/heroes', [HeroController::class, 'store']);
    Route::get('/admin/heroes/{id}', [HeroController::class, 'show']);
    Route::put('/admin/heroes/{id}', [HeroController::class, 'update']);
    Route::delete('/admin/heroes/{id}', [HeroController::class, 'destroy']);
    Route::post('/admin/heroes/reorder', [HeroController::class, 'reorder']);
});
