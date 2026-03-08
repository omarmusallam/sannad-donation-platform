<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\CampaignUpdateController as AdminCampaignUpdateController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\ReceiptController as AdminReceiptController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\FinanceReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageController as AdminPageController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'role:admin|super_admin|editor|finance,admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/', [AdminCampaignController::class, 'dashboard'])->name('home');

        /*
        |--------------------------------------------------------------------------
        | Campaigns
        |--------------------------------------------------------------------------
        */
        Route::controller(AdminCampaignController::class)
            ->prefix('campaigns')
            ->name('campaigns.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{campaign}/edit', 'edit')->name('edit');
                Route::put('/{campaign}', 'update')->name('update');
                Route::delete('/{campaign}', 'destroy')->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Campaign Updates (nested)
        |--------------------------------------------------------------------------
        */
        Route::prefix('campaigns/{campaign}/updates')
            ->name('campaigns.updates.')
            ->controller(AdminCampaignUpdateController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{update}/edit', 'edit')->name('edit');
                Route::put('/{update}', 'update')->name('update');
                Route::delete('/{update}', 'destroy')->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */
        Route::resource('pages', AdminPageController::class)->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Donations
        |--------------------------------------------------------------------------
        */
        Route::controller(AdminDonationController::class)
            ->prefix('donations')
            ->name('donations.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{donation}', 'show')->name('show');
                Route::post('/{donation}/receipt', 'generateReceipt')->name('generateReceipt');
            });

        /*
        |--------------------------------------------------------------------------
        | Receipts
        |--------------------------------------------------------------------------
        */
        Route::controller(AdminReceiptController::class)
            ->prefix('receipts')
            ->name('receipts.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/{receipt}/regenerate', 'regenerate')->name('regenerate');
                Route::get('/{receipt}/download', 'download')->name('download');
            });

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */
        Route::controller(AdminReportController::class)
            ->prefix('reports')
            ->name('reports.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{report}/edit', 'edit')->name('edit');
                Route::put('/{report}', 'update')->name('update');
                Route::delete('/{report}', 'destroy')->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Finance Reports
        |--------------------------------------------------------------------------
        */
        Route::prefix('finance-reports')
            ->name('finance_reports.')
            ->controller(FinanceReportController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/monthly', 'monthly')->name('monthly');
                Route::get('/campaign', 'campaign')->name('campaign');
                Route::get('/gateway', 'gateway')->name('gateway');
                Route::get('/currency', 'currency')->name('currency');
                Route::get('/status', 'status')->name('status');
                Route::get('/payment-method', 'paymentMethod')->name('paymentMethod');
            });

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::controller(SettingsController::class)
            ->group(function () {
                Route::get('settings', 'edit')->name('settings.edit');
                Route::post('settings', 'update')->name('settings.update');
            });

        /*
        |--------------------------------------------------------------------------
        | Users & Roles
        |--------------------------------------------------------------------------
        */
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
    });
