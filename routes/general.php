<?php

use App\Http\Controllers\PostTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SliderController;
use Illuminate\Support\Facades\Route;

Route::get('rooms/featured', [RoomController::class, 'featured']);
Route::get('rooms', [RoomController::class, 'index']);
Route::get('rooms/{slugOrId}', [RoomController::class, 'showPublic']);

Route::get('locations/cities', [\App\Http\Controllers\LocationController::class, 'city']);
Route::get('locations/districts', [\App\Http\Controllers\LocationController::class, 'district']);
Route::get('locations/wards', [\App\Http\Controllers\LocationController::class, 'ward']);

Route::get('sliders', [SliderController::class, 'publicIndex']);

Route::post('contact/{id}', [\App\Http\Controllers\ContactController::class, 'store']);

Route::get('post-types', [PostTypeController::class, 'index']);
Route::get('post-types/options', [PostTypeController::class, 'options']);
