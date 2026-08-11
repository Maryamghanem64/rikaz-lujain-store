<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {
    if (! auth()->check()) {
        return redirect()->route('admin.login');
    }

    if (auth()->user()->role !== 'admin') {
        abort(403);
    }

    return redirect()->route('admin.dashboard');
})->name('admin.entry');

Route::middleware('guest')->group(function () {

    Route::get(
        '/admin/login',
        [AuthController::class, 'showLogin']
    )->name('admin.login');

    Route::post(
        '/admin/login',
        [AuthController::class, 'login']
    )
        ->middleware('throttle:5,1')
        ->name('admin.login.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');
        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::get(
            'orders',
            [OrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            'orders/{order}',
            [OrderController::class, 'show']
        )->name('orders.show');

        Route::post(
            'orders/{order}/confirm-cash',
            [OrderController::class, 'confirmCash']
        )->name('orders.confirm-cash');

        Route::post(
            'orders/{order}/cancel',
            [OrderController::class, 'cancel']
        )->name('orders.cancel');

        Route::patch(
            'orders/{order}/status',
            [OrderController::class, 'advance']
        )->name('orders.advance');

        Route::post(
            'orders/{order}/release-reservation',
            [OrderController::class, 'releaseReservation']
        )->name('orders.release-reservation');

        /*
        |--------------------------------------------------------------------------
        | Whish Payment Proofs
        |--------------------------------------------------------------------------
        */

        Route::get(
            'orders/{order}/payment-proofs/{proof}/file',
            [PaymentProofController::class, 'file']
        )->name('orders.payment-proofs.file');

        Route::post(
            'orders/{order}/payment-proofs/{proof}/verify',
            [PaymentProofController::class, 'verify']
        )->name('orders.payment-proofs.verify');

        Route::post(
            'orders/{order}/payment-proofs/{proof}/reject',
            [PaymentProofController::class, 'reject']
        )->name('orders.payment-proofs.reject');

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'categories',
            CategoryController::class
        )->only([
            'index',
            'store',
            'update',
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'products',
            ProductController::class
        )->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Product Images
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Delivery Zones
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'delivery-zones',
            DeliveryZoneController::class
        )->only([
            'index',
            'store',
            'update',
            'destroy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get(
            'settings',
            [SettingController::class, 'edit']
        )->name('settings.edit');

        Route::put(
            'settings',
            [SettingController::class, 'update']
        )->name('settings.update');

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        )->name('logout');
    });

/*
|--------------------------------------------------------------------------
| Public Store
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [StorefrontController::class, 'home']
)->name('store.home');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::get(
    '/cart',
    [CartController::class, 'index']
)->name('cart.index');

Route::post(
    '/cart/{product}',
    [CartController::class, 'store']
)->name('cart.store');

Route::patch(
    '/cart/{product}',
    [CartController::class, 'update']
)->name('cart.update');

Route::delete(
    '/cart/{product}',
    [CartController::class, 'destroy']
)->name('cart.destroy');

Route::delete(
    '/cart',
    [CartController::class, 'clear']
)->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::get(
    '/checkout',
    [CheckoutController::class, 'show']
)->name('checkout.show');

Route::post(
    '/checkout',
    [CheckoutController::class, 'store']
)->name('checkout.store');

Route::get(
    '/order/{orderNumber}/success',
    [CheckoutController::class, 'success']
)->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Storefront Products / Categories / Sections
|--------------------------------------------------------------------------
*/

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
