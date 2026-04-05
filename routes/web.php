<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Donor\AccountController;

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CampaignController;
use App\Http\Controllers\Public\DonateController;
use App\Http\Controllers\Public\TransparencyController;
use App\Http\Controllers\Public\ReportController;
use App\Http\Controllers\Public\PageController as PublicPageController;

use App\Http\Controllers\ReceiptController;

use App\Http\Controllers\Donor\Auth\AuthenticatedSessionController as DonorAuthenticatedSessionController;
use App\Http\Controllers\Donor\Auth\RegisteredUserController;
use App\Http\Controllers\Donor\Auth\SocialAuthController;

/*
|--------------------------------------------------------------------------
| User-Facing Routes (Public + Donor)
|--------------------------------------------------------------------------
|
| Arabic: default without prefix
| English: /en prefix + en. route names
|
*/

$userFacingRoutes = function () {
    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

    Route::get('/donate', [DonateController::class, 'show'])->name('donate');
    Route::post('/donate', [DonateController::class, 'submit'])
        ->middleware('throttle:donation-submit')
        ->name('donate.submit');

    Route::get('/transparency', [TransparencyController::class, 'index'])->name('transparency');

    Route::get('/transparency/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/transparency/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    Route::get('/p/{page:slug}', [PublicPageController::class, 'show'])->name('pages.show');

    Route::get('/verify/receipt/{receipt:uuid}', [ReceiptController::class, 'verify'])
        ->name('receipt.verify');

    Route::get('/verify/receipt/{receipt:uuid}/download', [ReceiptController::class, 'download'])
        ->middleware('signed')
        ->name('receipt.download.public');

    Route::get('/donate/status/{donation:public_id}', [DonateController::class, 'success'])
        ->name('donate.success');

    Route::get('/donate/cancel/{donation:public_id}', [DonateController::class, 'cancel'])
        ->name('donate.cancel');

    Route::get('/donate/crypto/{donation:public_id}', [DonateController::class, 'crypto'])
        ->name('donate.crypto');

    Route::post('/donate/crypto/{donation:public_id}/submit', [DonateController::class, 'submitCryptoTransfer'])
        ->middleware('throttle:donation-crypto-submit')
        ->name('donate.crypto.submit');

    Route::get('/donate/crypto/{donation:public_id}/pending', [DonateController::class, 'cryptoPending'])
        ->name('donate.crypto.pending');

    /*
    |--------------------------------------------------------------------------
    | Donor Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('guest:donor')->group(function () {
        Route::get('/login', [DonorAuthenticatedSessionController::class, 'create'])->name('donor.login');
        Route::post('/login', [DonorAuthenticatedSessionController::class, 'store'])->name('donor.login.store');

        Route::get('/register', [RegisteredUserController::class, 'create'])->name('donor.register');
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('throttle:donor-register')
            ->name('donor.register.store');

        Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
            ->whereIn('provider', ['google', 'facebook'])
            ->name('donor.social.redirect');

        Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
            ->whereIn('provider', ['google', 'facebook'])
            ->name('donor.social.callback');
    });

    /*
    |--------------------------------------------------------------------------
    | Donor Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:donor')->group(function () {
        Route::post('/logout', [DonorAuthenticatedSessionController::class, 'destroy'])->name('donor.logout');

        Route::get('/account', [AccountController::class, 'dashboard'])->name('donor.dashboard');
        Route::get('/my-donations', [AccountController::class, 'donations'])->name('donor.donations');

        Route::get('/account/profile', [AccountController::class, 'profile'])->name('donor.profile');
        Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('donor.profile.update');

        Route::get('/account/security', [AccountController::class, 'security'])->name('donor.security');
        Route::put('/account/security', [AccountController::class, 'updatePassword'])->name('donor.security.update');
    });
};

/*
|--------------------------------------------------------------------------
| Arabic (default - no prefix)
|--------------------------------------------------------------------------
*/
Route::group([], $userFacingRoutes);

/*
|--------------------------------------------------------------------------
| English (prefix + route name prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('en')->name('en.')->group($userFacingRoutes);

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin.php';

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze) - disabled for now
|--------------------------------------------------------------------------
*/
// require __DIR__ . '/auth.php';
