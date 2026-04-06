<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $s = app(SettingsService::class);

        $s->set('site.name', 'Sannad', 'site');
        $s->set('site.tagline', 'منصة تبرعات موثوقة بحملات واضحة وتجربة احترافية هادئة.', 'site');
        $s->set('site.default_currency', 'USD', 'site');
        $s->set('site.locale', 'ar', 'site');
        $s->set('site.timezone', 'Asia/Gaza', 'site');

        $s->set('contact.email', 'info@sannad.org', 'contact');
        $s->set('contact.phone', '+970 59 000 0000', 'contact');
        $s->set('contact.whatsapp', '+970 59 000 0000', 'contact');

        $s->set('social.links', [
            'facebook' => 'https://www.youtube.com/',
            'x' => 'https://www.youtube.com/',
            'instagram' => 'https://www.youtube.com/',
            'youtube' => 'https://www.youtube.com/',
        ], 'social', 'json');

        $s->set('seo.meta_title', 'Sannad', 'seo');
        $s->set('seo.meta_description', 'Sannad منصة تبرعات احترافية تقدم حملات واضحة وتقارير موثقة وتجربة تبرع هادئة وآمنة.', 'seo');
    }
}
