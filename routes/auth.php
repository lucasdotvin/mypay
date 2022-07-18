<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\SignUpController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)
    ->name('login');

Route::post('/signup', SignUpController::class)
    ->name('signup');

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/me', MeController::class)->name('me');
    });
