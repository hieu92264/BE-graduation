<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login')->name('auth.login');
        Route::post('register', 'register')->name('auth.register');
        Route::middleware('jwt.auth')->group(function () {
            Route::get('me', 'me')->name('auth.me');
            Route::post('refresh', 'refreshToken')->name('auth.refresh');
            Route::post('logout', 'logout')->name('auth.logout');
        });
    });
