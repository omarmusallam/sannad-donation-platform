<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('locale_route')) {
    function locale_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $routeName = app()->isLocale('en') ? 'en.' . $name : $name;

        if (Route::has($routeName)) {
            return route($routeName, $parameters, $absolute);
        }

        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('switch_locale_url')) {
    function switch_locale_url(): string
    {
        $currentUri = request()->getRequestUri();
        $currentPath = request()->path();

        if (str_starts_with($currentPath, 'en/')) {
            return '/' . preg_replace('#^en/#', '', ltrim($currentUri, '/'));
        }

        if ($currentPath === 'en') {
            return '/';
        }

        return '/en' . $currentUri;
    }
}
