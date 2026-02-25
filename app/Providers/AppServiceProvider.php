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
        /**
         * Public Views Shared Data
         * - Settings (cached via SettingsService internally or here)
         * - Public pages list (for footer/nav)
         */
        View::composer('public.*', function ($view) {
            // Settings (safe fallback)
            $settings = [];
            try {
                $settings = app(\App\Services\SettingsService::class)->all();
            } catch (\Throwable $e) {
                $settings = [];
            }

            // Pages list (cached)
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

        /**
         * Admin Views Shared Data (optional)
         * If you want settings available in admin layouts too:
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
