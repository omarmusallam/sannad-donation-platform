<?php

namespace App\Providers;

use App\Models\Donation;
use App\Models\Page;
use App\Observers\DonationObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Observers\PageObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Observers
        Donation::observe(DonationObserver::class);
        Page::observe(PageObserver::class);

        /*
        |--------------------------------------------------------------------------
        | Shared data for user-facing views
        |--------------------------------------------------------------------------
        |
        | public.*  => public pages
        | donor.*   => donor auth/dashboard pages
        | layouts.public => in case layout is rendered directly
        |
        */
        View::composer(['public.*', 'donor.*', 'layouts.public'], function ($view) {
            $settings = [];

            try {
                $settings = app(\App\Services\SettingsService::class)->all();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $pages = Cache::remember('public_pages_list', now()->addHours(6), function () {
                return Page::query()
                    ->where('is_public', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['slug', 'title_ar', 'title_en']);
            });

            $view->with([
                'appSettings' => $settings,
                'publicPages' => $pages,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Shared data for admin views
        |--------------------------------------------------------------------------
        */
        View::composer('admin.*', function ($view) {
            $settings = [];

            try {
                $settings = app(\App\Services\SettingsService::class)->all();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $view->with('appSettings', $settings);
        });
    }
}
