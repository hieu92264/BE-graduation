<?php

use App\Http\Controllers\PermissionController;

Route::prefix('organizations')->middleware(['jwt.auth'])->group(function () {
    Route::prefix('permissions')->middleware(['check.permission'])->controller(PermissionController::class)->group(function () {
        Route::get('/', 'getAll')->name('permissions');
        Route::post('/create', 'create')->name('permissions.create');
        Route::patch('/update/{id}', 'update')->name('permissions.update');
        Route::delete('/delete/{id}', 'delete')->name('permissions.delete');
    });
});
