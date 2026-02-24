<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('organizations')->middleware(['jwt.auth'])->group(function () {
    /**
     * Gợi ý: Nên sử dụng Route::apiResource cho các resource để code gọn hơn.
     * Khi đổi qua apiResource, bạn nên refactor các method trong Controller tương ứng:
     * - getAll  => index // nên đổi thành index cho khớp chuẩn chung
     * - create  => store // cái create chuẩn ra nó sẽ trả về cái view để tạo cho xem
     * - delete  => destroy // nên đổi thành destroy cho khớp chuẩn chung
     *
     * Ví dụ: Route::apiResource('permissions', PermissionController::class);
     */
    Route::prefix('permissions')->middleware(['check.permission'])->controller(PermissionController::class)->group(function () {
        Route::get('/', 'getAll')->name('permissions');
        Route::post('/create', 'create')->name('permissions.create');
        Route::patch('/update/{id}', 'update')->name('permissions.update');
        Route::delete('/delete/{id}', 'delete')->name('permissions.delete');
    });

    /**
     * Tương tự với employees:
     * Route::apiResource('employees', EmployeeController::class);
     */
    Route::prefix('employees')->middleware(['check.permission'])->controller(\App\Http\Controllers\EmployeeController::class)->group(function () {
        Route::get('/', 'getAll')->name('employees');
        Route::post('/create', 'create')->name('employees.create');
        Route::patch('/update/{id}', 'update')->name('employees.update');
        Route::delete('/delete/{id}', 'delete')->name('employees.delete');
        Route::get('/user-options', 'getUserOptions')->name('employees.user-options');
    });
});
