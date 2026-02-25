<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return app(SettingsService::class)->get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (! function_exists('site_name')) {
    function site_name(): string
    {
        return (string) setting('site.name', config('app.name', 'GazaSannad'));
    }
}

if (! function_exists('site_logo_url')) {
    function site_logo_url(): ?string
    {
        $path = setting('site.logo', null);
        return $path ? asset('storage/' . $path) : null;
    }
}

if (! function_exists('site_favicon_url')) {
    function site_favicon_url(): ?string
    {
        $path = setting('site.favicon', null);
        return $path ? asset('storage/' . $path) : null;
    }
}

if (! function_exists('seo_title')) {
    function seo_title(?string $fallback = null): string
    {
        return (string) setting('seo.meta_title', $fallback ?? site_name());
    }
}

if (! function_exists('seo_description')) {
    function seo_description(string $fallback = ''): string
    {
        return (string) setting('seo.meta_description', $fallback);
    }
}
