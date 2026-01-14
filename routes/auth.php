<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login')->name('auth.login');
        Route::post('register', 'register')->name('auth.register');
        Route::post('refresh', 'refreshToken')->name('auth.refresh');
        Route::middleware('jwt.auth')->group(function () {
            Route::get('me', 'me')->name('auth.me');
            Route::post('logout', 'logout')->name('auth.logout');
        });
    });

Route::prefix('permissions')->middleware(['jwt.auth', 'check.permission'])->controller(PermissionController::class)
    ->group(function () {

    });
