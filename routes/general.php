<?php

use Illuminate\Support\Facades\Route;

Route::get('rooms/featured', [\App\Http\Controllers\RoomController::class, 'featured']);
Route::get('locations/cities', [\App\Http\Controllers\LocationController::class, 'city']);
Route::get('locations/districts', [\App\Http\Controllers\LocationController::class, 'district']);
Route::get('locations/wards', [\App\Http\Controllers\LocationController::class, 'ward']);
Route::apiResource('sliders', \App\Http\Controllers\SliderController::class);
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class);
