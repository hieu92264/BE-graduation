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

    Route::prefix('categories')
        ->middleware(['check.permission:org.categories'])
        ->controller(\App\Http\Controllers\CategoryController::class)->group(function () {
            Route::get('/', 'getAll')->name('categories');
            Route::post('/create', 'create')->name('categories.create');
            Route::patch('/update/{id}', 'update')->name('categories.update');
            Route::delete('/delete/{id}', 'delete')->name('categories.delete');
        });

    Route::prefix('sliders')
        ->middleware(['check.permission:org.sliders'])
        ->controller(\App\Http\Controllers\SliderController::class)
        ->group(function () {
            Route::get('/', 'index')->name('sliders');
            Route::post('/create', 'store')->name('sliders.create');
            Route::post('/update/{id}', 'update')->name('sliders.update');
            Route::delete('/delete/{id}', 'destroy')->name('sliders.delete');
        });

    Route::prefix('post-types')
        ->middleware(['check.permission:org.post-types'])
        ->controller(\App\Http\Controllers\PostTypeController::class)
        ->group(function () {
            Route::get('/', 'index')->name('post-types');
            Route::get('/options', 'options')->name('post-types.options');
            Route::post('/create', 'store')->name('post-types.create');
            Route::patch('/update/{id}', 'update')->name('post-types.update');
            Route::delete('/delete/{id}', 'destroy')->name('post-types.delete');
        });

    Route::prefix('landlord')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\LandlordDashboardController::class, 'index']);

        Route::prefix('rooms')
            ->controller(\App\Http\Controllers\LandlordRoomController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/create', 'store');
                Route::patch('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'destroy');
            });

        Route::prefix('rooms/{roomId}/photos')
            ->controller(\App\Http\Controllers\LandlordRoomPhotoController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/upload', 'upload');
                Route::patch('/update/{photoId}', 'update');
                Route::patch('/sort', 'sort');
                Route::delete('/delete/{photoId}', 'destroy');
            });

        Route::prefix('contacts')
            ->controller(\App\Http\Controllers\ContactManagementController::class)
            ->group(function () {
                Route::get('/', 'landlordIndex');
                Route::get('/{id}', 'landlordShow');
                Route::patch('/update-status/{id}', 'landlordUpdateStatus');
            });
    });

    Route::prefix('rooms/moderation')
        ->middleware(['check.permission:org.room-moderation'])
        ->controller(\App\Http\Controllers\AdminRoomModerationController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::patch('/update-status/{id}', 'updateStatus');
        });

    Route::prefix('contacts')
        ->middleware(['check.permission:org.contacts'])
        ->controller(\App\Http\Controllers\ContactManagementController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::patch('/update-status/{id}', 'updateStatus');
        });

    Route::prefix('bookings')
        ->middleware(['check.permission:org.bookings'])
        ->controller(\App\Http\Controllers\BookingController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/create', 'store');
            Route::patch('/update/{id}', 'update');
            Route::delete('/delete/{id}', 'destroy');
        });

    Route::prefix('reviews')
        ->middleware(['check.permission:org.reviews'])
        ->controller(\App\Http\Controllers\ReviewModerationController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::patch('/update-status/{id}', 'updateStatus');
        });
});
