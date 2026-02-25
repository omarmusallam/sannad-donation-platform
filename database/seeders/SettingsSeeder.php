<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SettingsService;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $s = app(SettingsService::class);

        $s->set('site.name', 'GazaSannad', 'site');
        $s->set('site.tagline', 'منصة لدعم الحملات والتبرعات', 'site');
        $s->set('site.default_currency', 'USD', 'site');
        $s->set('site.locale', 'ar', 'site');
        $s->set('site.timezone', config('app.timezone'), 'site');

        $s->set('contact.email', 'info@example.com', 'contact');
        $s->set('contact.phone', '', 'contact');
        $s->set('contact.whatsapp', '', 'contact');

        $s->set('social.links', [
            'facebook' => '',
            'x' => '',
            'instagram' => '',
            'youtube' => '',
        ], 'social', 'json');

        $s->set('seo.meta_title', 'GazaSannad', 'seo');
        $s->set('seo.meta_description', 'منصة حملات وتبرعات', 'seo');
    }
}
