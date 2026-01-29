<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('organizations')->middleware(['jwt.auth'])->group(function () {
    Route::prefix('permissions')->middleware(['check.permission'])->controller(PermissionController::class)->group(function () {
        Route::get('/', 'getAll')->name('permissions');
        Route::post('/create', 'create')->name('permissions.create');
        Route::patch('/update/{id}', 'update')->name('permissions.update');
        Route::delete('/delete/{id}', 'delete')->name('permissions.delete');
    });

    Route::prefix('employees')->middleware(['check.permission'])->controller(\App\Http\Controllers\EmployeeController::class)->group(function () {
        Route::get('/', 'getAll')->name('employees');
    });
});
