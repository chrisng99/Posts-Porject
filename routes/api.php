<?php

use App\Http\Controllers\api\v1\AuthenticateSessionController;
use App\Http\Controllers\api\v1\PostController;
use App\Http\Controllers\api\v1\RegisterUserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::name('api.')->group(function () {
        // Unprotected routes by Sanctum
        Route::post('register', RegisterUserController::class)->name('register');
        Route::post('login', [AuthenticateSessionController::class, 'store'])->name('login');

        Route::apiResource('posts', PostController::class)->only(['index', 'show']);

        // Protected routes by Sanctum
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('logout', [AuthenticateSessionController::class, 'destroy'])->name('logout');
            Route::apiResource('posts', PostController::class)->only(['store', 'update', 'destroy']);
        });
    });
});
