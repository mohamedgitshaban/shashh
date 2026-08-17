<?php

use App\Http\Controllers\api\auth\ClientAuthController;
use App\Http\Controllers\api\auth\CompanyAuthController;
use App\Http\Controllers\api\client\CampaignController;
use App\Http\Controllers\api\client\DashboardController as ClientDashboardController;
use App\Http\Controllers\api\client\LookupController;
use App\Http\Controllers\api\client\PaymentController;
use App\Http\Controllers\api\client\ScreenBrowseController;
use App\Http\Controllers\api\company\ApprovalController;
use App\Http\Controllers\api\company\BookingDashboardController;
use App\Http\Controllers\api\company\DashboardController;
use App\Http\Controllers\api\company\EarningsController;
use App\Http\Controllers\api\company\ScreenController;
use App\Http\Controllers\api\company\WithdrawRequestController;
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
        Route::post('/email/resend', [CompanyAuthController::class, 'resendVerificationEmail']);
    });
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/profile', [CompanyAuthController::class, 'profile']);
        Route::put('/profile', [CompanyAuthController::class, 'updateProfile']);
        Route::post('/logout', [CompanyAuthController::class, 'logout']);
        Route::apiResource('screens', ScreenController::class);
        Route::get('screens/cities', [ScreenController::class, 'cities']);
        // Approval Center
        Route::get('/approvals', [ApprovalController::class, 'index']);
        Route::post('/approvals/{booking}/approve', [ApprovalController::class, 'approve']);
        Route::post('/approvals/{booking}/reject', [ApprovalController::class, 'reject']);

        // Company Full Dashboard Data
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Bookings dashboard
        Route::get('/bookings/stats', [BookingDashboardController::class, 'stats']);
        Route::get('/bookings/export', [BookingDashboardController::class, 'export']);
        Route::get('/bookings', [BookingDashboardController::class, 'index']);
        Route::get('/bookings/most-sold', [BookingDashboardController::class, 'mostSold']);

        // Earnings — revenue/net-earnings/fees breakdown + trend charts (see EarningsController)
        Route::get('/earnings', [EarningsController::class, 'index']);
        
        // Payout requests — balance is credited automatically as campaigns are paid;
        // withdrawing it goes through admin review (see admin.withdraw-requests routes)
        Route::get('/withdraw-requests', [WithdrawRequestController::class, 'index']);
        Route::post('/withdraw-requests', [WithdrawRequestController::class, 'store']);
        Route::get('/withdraw-requests/{id}/invoice', [WithdrawRequestController::class, 'invoice']);
    });
});

Route::prefix('client')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [ClientAuthController::class, 'register']);
        Route::post('/login', [ClientAuthController::class, 'login']);
        Route::post('/email/resend', [ClientAuthController::class, 'resendVerificationEmail']);
    });
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/logout', [ClientAuthController::class, 'logout']);
        Route::get('/profile', [ClientAuthController::class, 'profile']);
        Route::put('/profile', [ClientAuthController::class, 'updateProfile']);

        // Lookup (cities & companies for signage filters)
        Route::get('/cities', [LookupController::class, 'cities']);
        Route::get('/companies', [LookupController::class, 'companies']);

        // Browse approved screens with optional city/company filters
        Route::get('/screens', [ScreenBrowseController::class, 'index']);

        // Client dashboard stats
        Route::get('/dashboard', [ClientDashboardController::class, 'stats']);

        // Campaign CRUD — creating a campaign also creates its Booking rows and
        // triggers a Tap Payments charge (see CampaignController::store).
        Route::get('/campaigns', [CampaignController::class, 'index']);
        Route::post('/campaigns', [CampaignController::class, 'store']);
        Route::get('/campaigns/{id}', [CampaignController::class, 'show']);
        Route::post('/campaigns/{id}/pay', [CampaignController::class, 'pay']);
    });
});

// Tap Payments callback/webhook — unauthenticated (Tap does not hold a Sanctum
// token). Authenticity is instead established via signature verification in
// PaymentController::webhook, and by re-fetching the charge server-to-server
// in PaymentController::callback rather than trusting query params.
Route::prefix('payments')->group(function () {
    Route::get('/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
});

// No /api/admin/* JSON routes: admins work through the Filament panel at /admin
// (session-based, its own auth) — see app/Filament/Admin/{Pages/Fulfillment.php,
// Resources/WithdrawRequestResource.php}. Only companies/clients use the JSON API.
