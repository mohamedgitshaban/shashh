<?php

use App\Http\Controllers\api\auth\ClientAuthController;
use App\Http\Controllers\api\auth\CompanyAuthController;
use App\Http\Controllers\api\client\CampaignController;
use App\Http\Controllers\api\client\LookupController;
use App\Http\Controllers\api\client\ScreenBrowseController;
use App\Http\Controllers\api\company\ApprovalController;
use App\Http\Controllers\api\company\BookingDashboardController;
use App\Http\Controllers\api\company\ScreenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
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

        // Approval Center
        Route::get('/approvals', [ApprovalController::class, 'index']);
        Route::post('/approvals/{booking}/approve', [ApprovalController::class, 'approve']);
        Route::post('/approvals/{booking}/reject', [ApprovalController::class, 'reject']);

        // Bookings dashboard
        Route::get('/bookings/stats', [BookingDashboardController::class, 'stats']);
        Route::get('/bookings/export', [BookingDashboardController::class, 'export']);
        Route::get('/bookings', [BookingDashboardController::class, 'index']);
        Route::get('/bookings/most-sold', [BookingDashboardController::class, 'mostSold']);
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

        // Lookup (cities & companies for signage filters)
        Route::get('/cities', [LookupController::class, 'cities']);
        Route::get('/companies', [LookupController::class, 'companies']);

        // Browse approved screens with optional city/company filters
        Route::get('/screens', [ScreenBrowseController::class, 'index']);

        // Campaign CRUD
        Route::get('/campaigns', [CampaignController::class, 'index']);
        Route::post('/campaigns', [CampaignController::class, 'store']);
        Route::get('/campaigns/{id}', [CampaignController::class, 'show']);
    });
});
