<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        Cache::forget('public_pages_list');
    }

    public function deleted(Page $page): void
    {
        Cache::forget('public_pages_list');
    }
}
