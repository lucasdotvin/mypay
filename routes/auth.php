<?php

use App\Http\Controllers\Auth\TokenController;
use Illuminate\Support\Facades\Route;

Route::name('tokens.')
    ->group(function () {
        Route::post('/tokens', [TokenController::class, 'store'])->name('store');

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::get('/tokens', [TokenController::class, 'index'])->name('index');

                Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('destroy');
            });
    });
