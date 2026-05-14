<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
});