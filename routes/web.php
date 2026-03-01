<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CampaignController;
use App\Http\Controllers\Public\DonateController;
use App\Http\Controllers\Public\TransparencyController;
use App\Http\Controllers\Public\ReportController;
use App\Http\Controllers\Public\PageController as PublicPageController;

/*
|--------------------------------------------------------------------------
| Receipt Verify (PUBLIC)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ReceiptController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (AR default + EN prefix)
|--------------------------------------------------------------------------
*/

$publicRoutes = function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

    Route::get('/donate', [DonateController::class, 'show'])->name('donate');
    Route::post('/donate', [DonateController::class, 'submit'])->name('donate.submit');
    Route::get('/donate/success', [DonateController::class, 'success'])->name('donate.success');

    Route::get('/transparency', [TransparencyController::class, 'index'])->name('transparency');

    Route::get('/transparency/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/transparency/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    Route::get('/p/{page:slug}', [PublicPageController::class, 'show'])->name('pages.show');


    Route::get('/verify/receipt/{receipt:uuid}', [ReceiptController::class, 'verify'])
        ->name('receipt.verify');

    Route::get('/verify/receipt/{receipt:uuid}/download', [ReceiptController::class, 'download'])
        ->middleware('signed')
        ->name('receipt.download.public');
};

/*
|--------------------------------------------------------------------------
| Arabic (default - no prefix)
|--------------------------------------------------------------------------
*/
Route::group([], $publicRoutes);

/*
|--------------------------------------------------------------------------
| English (prefix + name prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('en')->name('en.')->group($publicRoutes);

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (separated file)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin.php';

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze/Jetstream/etc)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
