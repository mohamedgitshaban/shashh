<?php

use App\Http\Controllers\api\auth\ClientAuthController;
use App\Http\Controllers\api\auth\CompanyAuthController;
use App\Http\Controllers\api\company\ScreenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('company')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [CompanyAuthController::class, 'register']);
        Route::post('/login', [CompanyAuthController::class, 'login']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [CompanyAuthController::class, 'profile']);
        Route::put('/profile', [CompanyAuthController::class, 'updateProfile']);
        Route::post('/logout', [CompanyAuthController::class, 'logout']);
        Route::apiResource('screens', ScreenController::class);
    });
});
Route::prefix('client')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [ClientAuthController::class, 'register']);
        Route::post('/login', [ClientAuthController::class, 'login']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ClientAuthController::class, 'logout']);
        Route::get('/profile', [ClientAuthController::class, 'profile']);
        Route::put('/profile', [ClientAuthController::class, 'updateProfile']);
    });
});

