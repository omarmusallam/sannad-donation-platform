<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_public, 404);

        $slug = (string) $page->slug;

        $pageType = match (true) {
            str_contains($slug, 'privacy') => 'privacy',
            str_contains($slug, 'terms') => 'terms',
            str_contains($slug, 'refund') => 'refund',
            str_contains($slug, 'cookie') => 'cookies',
            str_contains($slug, 'about') => 'about',
            default => 'general',
        };

        return view('public.pages.show', compact('page', 'pageType'));
    }
}
