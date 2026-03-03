<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('organizations')->middleware(['jwt.auth'])->group(function () {
    Route::prefix('permissions')
        ->middleware(['check.permission:org.permissions'])
        ->controller(PermissionController::class)->group(function () {
            Route::get('/', 'getAll')->name('permissions');
            Route::post('/create', 'create')->name('permissions.create');
            Route::patch('/update/{id}', 'update')->name('permissions.update');
            Route::delete('/delete/{id}', 'delete')->name('permissions.delete');
            Route::get('/options', 'getPermissionOptions')->name('permissions.options');
        });

    Route::prefix('users')
        ->middleware(['check.permission:org.users'])
        ->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('users');
            Route::post('/create', 'store')->name('users.create');
            Route::patch('/update/{id}', 'update')->name('users.update');
            Route::delete('/delete/{id}', 'destroy')->name('users.delete');

            Route::get('/{id}/permissions', 'getPermissions')->name('users.permissions');
            Route::put('/{id}/permissions', 'syncPermissions')->name('users.permissions.update');
        });

    Route::prefix('user-profiles')->middleware(['check.permission:org.user-profiles'])->controller(UserProfileController::class)->group(function () {
        Route::get('/', 'index')->name('user-profiles');
        Route::post('/create', 'store')->name('user-profiles.create');
        Route::patch('/update/{id}', 'update')->name('user-profiles.update');
        Route::delete('/delete/{id}', 'destroy')->name('user-profiles.delete');
    });

    Route::prefix('employees')->middleware(['check.permission:org.employees'])->controller(\App\Http\Controllers\EmployeeController::class)->group(function () {
        Route::get('/', 'getAll')->name('employees');
        Route::post('/create', 'create')->name('employees.create');
        Route::patch('/update/{id}', 'update')->name('employees.update');
        Route::delete('/delete/{id}', 'delete')->name('employees.delete');
        Route::get('/user-options', 'getUserOptions')->name('employees.user-options');
    });
});
