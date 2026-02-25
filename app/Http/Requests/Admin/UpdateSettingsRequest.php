<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // لاحقًا نربطها بصلاحيات
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_locale' => ['required', 'in:ar,en'],
            'site_currency' => ['required', 'in:USD,EUR,ILS'],
            'site_timezone' => ['required', 'string', 'max:64'],

            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],

            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:255'],

            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],

            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:8192'],
            'site_favicon' => ['nullable', 'image', 'mimes:png,ico,jpg,jpeg,webp', 'max:2048'],
        ];
    }
}
