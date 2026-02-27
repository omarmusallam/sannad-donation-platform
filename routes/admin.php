<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\CampaignUpdateController as AdminCampaignUpdateController;
use App\Http\Controllers\Admin\FinanceReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ReceiptController as AdminReceiptController;

Route::prefix('admin')
    ->middleware(['auth', 'role:admin|super_admin|editor|finance'])
    ->name('admin.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | Dashboard
        |----------------------------------------------------------------------
        */
        Route::get('/', [AdminCampaignController::class, 'dashboard'])
            ->name('home');

        /*
        |----------------------------------------------------------------------
        | Campaigns
        |----------------------------------------------------------------------
        */
        Route::get('campaigns', [AdminCampaignController::class, 'index'])->name('campaigns.index');
        Route::get('campaigns/create', [AdminCampaignController::class, 'create'])->name('campaigns.create');
        Route::post('campaigns', [AdminCampaignController::class, 'store'])->name('campaigns.store');
        Route::get('campaigns/{campaign}/edit', [AdminCampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('campaigns/{campaign}', [AdminCampaignController::class, 'update'])->name('campaigns.update');
        Route::delete('campaigns/{campaign}', [AdminCampaignController::class, 'destroy'])->name('campaigns.destroy');

        /*
        |----------------------------------------------------------------------
        | Pages
        |----------------------------------------------------------------------
        */
        Route::resource('pages', AdminPageController::class)->except(['show']);

        /*
        |----------------------------------------------------------------------
        | Campaign Updates
        |----------------------------------------------------------------------
        */
        Route::prefix('campaigns/{campaign}/updates')->name('campaigns.updates.')->group(function () {
            Route::get('/', [AdminCampaignUpdateController::class, 'index'])->name('index');
            Route::get('/create', [AdminCampaignUpdateController::class, 'create'])->name('create');
            Route::post('/', [AdminCampaignUpdateController::class, 'store'])->name('store');
            Route::get('/{update}/edit', [AdminCampaignUpdateController::class, 'edit'])->name('edit');
            Route::put('/{update}', [AdminCampaignUpdateController::class, 'update'])->name('update');
            Route::delete('/{update}', [AdminCampaignUpdateController::class, 'destroy'])->name('destroy');
        });

        /*
        |----------------------------------------------------------------------
        | Donations
        |----------------------------------------------------------------------
        */
        Route::get('donations', [AdminDonationController::class, 'index'])->name('donations.index');
        Route::get('donations/{donation}', [AdminDonationController::class, 'show'])->name('donations.show');
        Route::post('donations/{donation}/receipt', [AdminDonationController::class, 'generateReceipt'])
            ->name('donations.generateReceipt');

        /*
        |----------------------------------------------------------------------
        | Receipts
        |----------------------------------------------------------------------
        */
        Route::get('receipts', [AdminReceiptController::class, 'index'])->name('receipts.index');
        Route::post('receipts/{receipt}/regenerate', [AdminReceiptController::class, 'regenerate'])->name('receipts.regenerate');
        Route::get('receipts/{receipt}/download', [AdminReceiptController::class, 'download'])->name('receipts.download');

        /*
        |----------------------------------------------------------------------
        | Reports
        |----------------------------------------------------------------------
        */
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/create', [AdminReportController::class, 'create'])->name('reports.create');
        Route::post('reports', [AdminReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{report}/edit', [AdminReportController::class, 'edit'])->name('reports.edit');
        Route::put('reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        Route::delete('reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        /*
        |----------------------------------------------------------------------
        | Finance Reports
        |----------------------------------------------------------------------
        */
        Route::prefix('finance-reports')
            ->name('finance_reports.')
            ->group(function () {
                Route::get('/', [FinanceReportController::class, 'index'])->name('index');
                Route::get('/monthly', [FinanceReportController::class, 'monthly'])->name('monthly');
                Route::get('/campaign', [FinanceReportController::class, 'campaign'])->name('campaign');
                Route::get('/gateway', [FinanceReportController::class, 'gateway'])->name('gateway');
                Route::get('/currency', [FinanceReportController::class, 'currency'])->name('currency');
                Route::get('/status', [FinanceReportController::class, 'status'])->name('status');
                Route::get('/payment-method', [FinanceReportController::class, 'paymentMethod'])->name('paymentMethod');
            });

        /*
        |----------------------------------------------------------------------
        | Settings
        |----------------------------------------------------------------------
        */
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

        /*
        |----------------------------------------------------------------------
        | Users
        |----------------------------------------------------------------------
        */
        Route::resource('users', UserController::class)->except(['show']);

        /*
        |----------------------------------------------------------------------
        | Roles
        |----------------------------------------------------------------------
        */
        Route::resource('roles', RoleController::class)->except(['show']);
    });
