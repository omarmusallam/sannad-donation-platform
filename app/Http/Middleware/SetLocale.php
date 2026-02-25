<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // /en/... => English
        if ($request->segment(1) === 'en') {
            app()->setLocale('en');
            return $next($request);
        }

        // الافتراضي عربي (مع إمكانية cookie لاحقًا)
        app()->setLocale('ar');
        return $next($request);
    }
}
