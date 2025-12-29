<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login')->name('auth.login');
        Route::middleware('jwt.auth')->group(function () {
            Route::get('me', 'me')->name('auth.me');
            Route::post('refresh', 'refreshToken')->name('auth.refresh');
            Route::post('logout', 'logout')->name('auth.logout');
            
            Route::post('register', 'register')
                ->middleware('check.permission')
                ->name('auth.register');
        });
    });
