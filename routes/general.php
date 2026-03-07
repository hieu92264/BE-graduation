<?php

use App\Http\Controllers\PostTypeController;
use Illuminate\Support\Facades\Route;

Route::get('rooms/featured', [\App\Http\Controllers\RoomController::class, 'featured']);
Route::get('locations/cities', [\App\Http\Controllers\LocationController::class, 'city']);
Route::get('locations/districts', [\App\Http\Controllers\LocationController::class, 'district']);
Route::get('locations/wards', [\App\Http\Controllers\LocationController::class, 'ward']);
Route::apiResource('sliders', \App\Http\Controllers\SliderController::class);
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class);
Route::get('rooms/detail/{id}', [\App\Http\Controllers\RoomController::class, 'roomDetail']);

Route::post('contact/{id}', [\App\Http\Controllers\ContactController::class, 'store']);

Route::get('post-types', [PostTypeController::class, 'index']);
Route::get('post-types/options', [PostTypeController::class, 'options']);
