<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit(SettingsService $settings)
    {
        $data = $settings->all();
        return view('admin.settings.edit', compact('data'));
    }

    public function update(UpdateSettingsRequest $request, SettingsService $settings)
    {
        $v = $request->validated();

        // ملفات الهوية
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('site', 'public');
            $settings->set('site.logo', $path, 'site', 'file');
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('site', 'public');
            $settings->set('site.favicon', $path, 'site', 'file');
        }

        // قيم نصية
        $settings->setMany([
            'site.name' => ['value' => $v['site_name'], 'type' => 'string'],
            'site.tagline' => ['value' => $v['site_tagline'] ?? '', 'type' => 'string'],
            'site.locale' => ['value' => $v['site_locale'], 'type' => 'string'],
            'site.default_currency' => ['value' => $v['site_currency'], 'type' => 'string'],
            'site.timezone' => ['value' => $v['site_timezone'], 'type' => 'string'],

            'contact.email' => ['value' => $v['contact_email'] ?? '', 'type' => 'string'],
            'contact.phone' => ['value' => $v['contact_phone'] ?? '', 'type' => 'string'],
            'contact.whatsapp' => ['value' => $v['contact_whatsapp'] ?? '', 'type' => 'string'],

            'seo.meta_title' => ['value' => $v['seo_title'] ?? '', 'type' => 'string'],
            'seo.meta_description' => ['value' => $v['seo_description'] ?? '', 'type' => 'string'],
        ], 'site'); // group هنا غير مهم لأنه يتم تمريره لكل key في setMany، لكنه موجود للتوافق

        // Social JSON
        $settings->set('social.links', [
            'facebook'  => $v['social_facebook'] ?? '',
            'x'         => $v['social_x'] ?? '',
            'instagram' => $v['social_instagram'] ?? '',
            'youtube'   => $v['social_youtube'] ?? '',
        ], 'social', 'json');

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
