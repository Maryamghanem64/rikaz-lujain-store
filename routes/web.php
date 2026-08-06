<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;

Route::get('/', function () {
    return view('welcome');
});

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
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });

        