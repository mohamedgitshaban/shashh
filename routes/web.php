<?php

use App\Http\Controllers\LocaleController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});

Route::get('locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if (!$request->hasValidSignature()) {
        abort(403, 'Invalid or expired verification link.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return view('auth.email-verified', [
        'email' => $user->email,
        'isOwner' => $user->isCompany(),
    ]);
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::get('/email/verify', function (Request $request) {
    return view('auth.email-verified', [
        'email' => 'mohamed@gmail.com',
        'isOwner' => $request->query('type') === 'company',
    ]);
});