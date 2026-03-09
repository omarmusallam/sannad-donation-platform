<?php

namespace App\Providers;

use App\Models\Donation;
use App\Models\Page;
use App\Observers\DonationObserver;
use App\Observers\PageObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Donation::observe(DonationObserver::class);
        Page::observe(PageObserver::class);

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

        View::composer('admin.*', function ($view) {
            $settings = [];

            try {
                $settings = app(\App\Services\SettingsService::class)->all();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $view->with('appSettings', $settings);
        });

        RateLimiter::for('donor-register', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(3)->by(($email !== '' ? $email : 'guest') . '|' . $request->ip()),
            ];
        });
    }
}
