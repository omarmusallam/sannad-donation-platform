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

        Route::get('/', [AdminCampaignController::class, 'dashboard'])
            ->middleware('permission:dashboard.view')
            ->name('home');

        /*
        |--------------------------------------------------------------------------
        | Campaigns
        |--------------------------------------------------------------------------
        */
        Route::get('campaigns', [AdminCampaignController::class, 'index'])
            ->middleware('permission:campaigns.view')
            ->name('campaigns.index');

        Route::get('campaigns/create', [AdminCampaignController::class, 'create'])
            ->middleware('permission:campaigns.create')
            ->name('campaigns.create');

        Route::post('campaigns', [AdminCampaignController::class, 'store'])
            ->middleware('permission:campaigns.create')
            ->name('campaigns.store');

        Route::get('campaigns/{campaign}/edit', [AdminCampaignController::class, 'edit'])
            ->middleware('permission:campaigns.edit')
            ->name('campaigns.edit');

        Route::put('campaigns/{campaign}', [AdminCampaignController::class, 'update'])
            ->middleware('permission:campaigns.edit')
            ->name('campaigns.update');

        Route::delete('campaigns/{campaign}', [AdminCampaignController::class, 'destroy'])
            ->middleware('permission:campaigns.delete')
            ->name('campaigns.destroy');

        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */
        Route::resource('pages', AdminPageController::class)
            ->except(['show'])
            ->middleware('permission:pages.view|pages.create|pages.edit|pages.delete');

        /*
        |--------------------------------------------------------------------------
        | Campaign Updates
        |--------------------------------------------------------------------------
        */
        Route::prefix('campaigns/{campaign}/updates')->name('campaigns.updates.')->group(function () {
            Route::get('/', [AdminCampaignUpdateController::class, 'index'])
                ->middleware('permission:campaign_updates.view')
                ->name('index');

            Route::get('/create', [AdminCampaignUpdateController::class, 'create'])
                ->middleware('permission:campaign_updates.create')
                ->name('create');

            Route::post('/', [AdminCampaignUpdateController::class, 'store'])
                ->middleware('permission:campaign_updates.create')
                ->name('store');

            Route::get('/{update}/edit', [AdminCampaignUpdateController::class, 'edit'])
                ->middleware('permission:campaign_updates.edit')
                ->name('edit');

            Route::put('/{update}', [AdminCampaignUpdateController::class, 'update'])
                ->middleware('permission:campaign_updates.edit')
                ->name('update');

            Route::delete('/{update}', [AdminCampaignUpdateController::class, 'destroy'])
                ->middleware('permission:campaign_updates.delete')
                ->name('destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Donations
        |--------------------------------------------------------------------------
        */
        Route::get('donations', [AdminDonationController::class, 'index'])
            ->middleware('permission:donations.view')
            ->name('donations.index');

        Route::get('donations/{donation}', [AdminDonationController::class, 'show'])
            ->middleware('permission:donations.view')
            ->name('donations.show');

        Route::post('donations/{donation}/receipt', [AdminDonationController::class, 'generateReceipt'])
            ->middleware('permission:receipts.create')
            ->name('donations.generateReceipt');

        /*
        |--------------------------------------------------------------------------
        | Receipts (ADMIN)
        |--------------------------------------------------------------------------
        */
        Route::get('/receipts', [AdminReceiptController::class, 'index'])
            ->middleware('permission:receipts.view')
            ->name('receipts.index');

        Route::post('/receipts/{receipt}/regenerate', [AdminReceiptController::class, 'regenerate'])
            ->middleware('permission:receipts.create')
            ->name('receipts.regenerate');

        Route::get('/receipts/{receipt}/download', [AdminReceiptController::class, 'download'])
            ->middleware('permission:receipts.view')
            ->name('receipts.download');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */
        Route::get('reports', [AdminReportController::class, 'index'])
            ->middleware('permission:reports.view')
            ->name('reports.index');

        Route::get('reports/create', [AdminReportController::class, 'create'])
            ->middleware('permission:reports.create')
            ->name('reports.create');

        Route::post('reports', [AdminReportController::class, 'store'])
            ->middleware('permission:reports.create')
            ->name('reports.store');

        Route::get('reports/{report}/edit', [AdminReportController::class, 'edit'])
            ->middleware('permission:reports.edit')
            ->name('reports.edit');

        Route::put('reports/{report}', [AdminReportController::class, 'update'])
            ->middleware('permission:reports.edit')
            ->name('reports.update');

        Route::delete('reports/{report}', [AdminReportController::class, 'destroy'])
            ->middleware('permission:reports.delete')
            ->name('reports.destroy');


        Route::prefix('finance-reports')
            ->middleware('permission:finance_reports.view')
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
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::get('settings', [SettingsController::class, 'edit'])
            ->middleware('permission:settings.manage')
            ->name('settings.edit');

        Route::post('settings', [SettingsController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('settings.update');

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        Route::resource('users', UserController::class)
            ->except(['show'])
            ->middleware('permission:users.manage');

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:roles.manage');
    });
