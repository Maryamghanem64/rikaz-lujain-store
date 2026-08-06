<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\StorefrontController;



Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'login'])
        ->name('admin.login.submit');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('products', ProductController::class);Route::resource('products', ProductController::class);
        Route::post(
    'products/{product}/images',
    [ProductImageController::class, 'store']
)->name('products.images.store');

Route::patch(
    'products/{product}/images/{image}/primary',
    [ProductImageController::class, 'setPrimary']
)->name('products.images.primary');

Route::patch(
    'products/{product}/images-order',
    [ProductImageController::class, 'updateOrder']
)->name('products.images.order');

Route::delete(
    'products/{product}/images/{image}',
    [ProductImageController::class, 'destroy']
)->name('products.images.destroy');
Route::resource(
    'delivery-zones',
    DeliveryZoneController::class
)->only([
    'index',
    'store',
    'update',
    'destroy',
]);
Route::get(
    'settings',
    [SettingController::class, 'edit']
)->name('settings.edit');

Route::put(
    'settings',
    [SettingController::class, 'update']
)->name('settings.update');
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });

        Route::get(
    '/',
    [StorefrontController::class, 'home']
)->name('store.home');


Route::get(
    '/{sectionSlug}/product/{productSlug}',
    [StorefrontController::class, 'product']
)
    ->whereIn('sectionSlug', ['rikaz', 'lujain'])
    ->name('store.product');


Route::get(
    '/{sectionSlug}/{categorySlug}',
    [StorefrontController::class, 'category']
)
    ->whereIn('sectionSlug', ['rikaz', 'lujain'])
    ->name('store.category');


Route::get(
    '/{sectionSlug}',
    [StorefrontController::class, 'section']
)
    ->whereIn('sectionSlug', ['rikaz', 'lujain'])
    ->name('store.section');