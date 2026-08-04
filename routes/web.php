<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'login'])
        ->name('admin.login.submit');
});

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        abort_unless(auth()->user()->role === 'admin', 403);

        return 'Admin Dashboard';
    })->name('admin.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('admin.logout');
});