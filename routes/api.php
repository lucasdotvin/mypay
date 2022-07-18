<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', fn () => ['Laravel' => app()->version()]);

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('/payments', PaymentController::class)->only(['index', 'store']);
        Route::apiResource('/tokens', TokenController::class)->only(['index', 'destroy']);
        Route::apiResource('/users', UserController::class)->only(['index']);
    });
