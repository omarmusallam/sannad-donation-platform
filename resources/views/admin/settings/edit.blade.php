@extends('layouts.admin')

@section('title', 'إعدادات الموقع')
@section('page_title', 'إعدادات الموقع')

@section('content')
    @php
        $isAr = app()->isLocale('ar');
        $social = $data['social.links'] ?? [];
    @endphp

    <div class="max-w-6xl">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إعدادات الموقع</h1>
            <p class="text-sm text-slate-500 mt-1">تحكم كامل بالهوية، التواصل، والسيو من مكان واحد.</p>
        </div>

        {{-- حماية UI إضافية: في حال وصل المستخدم للصفحة بدون صلاحية --}}
        @cannot('settings.manage')
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                <div class="font-bold">تنبيه</div>
                <div class="text-sm mt-1">لا تملك صلاحية تعديل إعدادات الموقع.</div>
            </div>
        @endcannot

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- General --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-5">
                            <div>
                                <div class="text-base font-semibold text-slate-900">إعدادات عامة</div>
                                <div class="text-xs text-slate-500 mt-1">اسم المنصة، اللغة، العملة والمنطقة الزمنية.</div>
                            </div>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-600 font-semibold">
                                Site
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">اسم الموقع</label>
                                <input name="site_name" autocomplete="organization"
                                    value="{{ old('site_name', $data['site.name'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('site_name')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">وصف مختصر</label>
                                <input name="site_tagline" value="{{ old('site_tagline', $data['site.tagline'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('site_tagline')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">اللغة الافتراضية</label>
                                <select name="site_locale"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                    @foreach (['ar' => 'العربية', 'en' => 'English'] as $k => $label)
                                        <option value="{{ $k }}" @selected(old('site_locale', $data['site.locale'] ?? 'ar') === $k)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_locale')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">العملة الافتراضية</label>
                                <select name="site_currency"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                    @foreach (['USD', 'EUR', 'ILS'] as $cur)
                                        <option value="{{ $cur }}" @selected(old('site_currency', $data['site.default_currency'] ?? 'USD') === $cur)>
                                            {{ $cur }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_currency')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Timezone</label>
                                <input name="site_timezone" dir="ltr"
                                    value="{{ old('site_timezone', $data['site.timezone'] ?? config('app.timezone')) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="Asia/Jerusalem">
                                @error('site_timezone')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                                <div class="text-xs text-slate-500 mt-2">مثال: Asia/Jerusalem</div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-5">
                            <div>
                                <div class="text-base font-semibold text-slate-900">بيانات التواصل</div>
                                <div class="text-xs text-slate-500 mt-1">تظهر في الموقع حسب تصميم الواجهة.</div>
                            </div>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-600 font-semibold">
                                Contact
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                <input name="contact_email" dir="ltr" autocomplete="email"
                                    value="{{ old('contact_email', $data['contact.email'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="name@example.com">
                                @error('contact_email')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                                <input name="contact_phone" dir="ltr" autocomplete="tel"
                                    value="{{ old('contact_phone', $data['contact.phone'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="+970...">
                                @error('contact_phone')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">WhatsApp</label>
                                <input name="contact_whatsapp" dir="ltr"
                                    value="{{ old('contact_whatsapp', $data['contact.whatsapp'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="+970...">
                                @error('contact_whatsapp')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SEO --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-5">
                            <div>
                                <div class="text-base font-semibold text-slate-900">SEO</div>
                                <div class="text-xs text-slate-500 mt-1">تحسين ظهور الموقع في محركات البحث.</div>
                            </div>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-600 font-semibold">
                                SEO
                            </span>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Meta Title</label>
                                <input name="seo_title" value="{{ old('seo_title', $data['seo.meta_title'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('seo_title')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Meta Description</label>
                                <textarea name="seo_description" rows="4"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">{{ old('seo_description', $data['seo.meta_description'] ?? '') }}</textarea>
                                @error('seo_description')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                                <div class="text-xs text-slate-500 mt-2">يفضل 140–160 حرف.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Branding --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-1">الهوية البصرية</div>
                        <div class="text-xs text-slate-500 mb-5">Logo و Favicon.</div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                                <input type="file" name="site_logo" accept="image/*"
                                    class="block w-full text-sm text-slate-700
                                           file:mr-3 file:rounded-2xl file:border-0 file:bg-slate-900 file:px-4 file:py-2.5
                                           file:text-white file:text-sm file:font-semibold hover:file:bg-slate-800 transition">
                                @error('site_logo')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror

                                <div class="text-xs text-slate-500 mt-2">يفضل PNG بخلفية شفافة.</div>

                                @if (!empty($data['site.logo']))
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $data['site.logo']) }}"
                                            class="h-14 rounded-2xl border border-slate-200 bg-white p-2" alt="Logo">
                                        <div class="text-xs text-slate-500">
                                            اللوغو الحالي — رفع ملف جديد سيستبدله.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-slate-200">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                                <input type="file" name="site_favicon" accept="image/png,image/x-icon,image/svg+xml"
                                    class="block w-full text-sm text-slate-700
                                           file:mr-3 file:rounded-2xl file:border-0 file:bg-slate-900 file:px-4 file:py-2.5
                                           file:text-white file:text-sm file:font-semibold hover:file:bg-slate-800 transition">
                                @error('site_favicon')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror

                                <div class="text-xs text-slate-500 mt-2">يفضل 32×32 أو 48×48.</div>

                                @if (!empty($data['site.favicon']))
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $data['site.favicon']) }}"
                                            class="h-10 w-10 rounded-2xl border border-slate-200 bg-white p-2"
                                            alt="Favicon">
                                        <div class="text-xs text-slate-500">
                                            الأيقونة الحالية — رفع ملف جديد سيستبدلها.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Social --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">روابط السوشال</div>

                        <div class="space-y-3">
                            <input name="social_facebook" dir="ltr"
                                value="{{ old('social_facebook', $social['facebook'] ?? '') }}"
                                placeholder="Facebook URL"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            @error('social_facebook')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_x" dir="ltr" value="{{ old('social_x', $social['x'] ?? '') }}"
                                placeholder="X / Twitter URL"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            @error('social_x')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_instagram" dir="ltr"
                                value="{{ old('social_instagram', $social['instagram'] ?? '') }}"
                                placeholder="Instagram URL"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            @error('social_instagram')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_youtube" dir="ltr"
                                value="{{ old('social_youtube', $social['youtube'] ?? '') }}" placeholder="YouTube URL"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            @error('social_youtube')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <div class="text-xs text-slate-500 pt-1">
                                اترك الحقل فارغًا إذا لا تريد إظهار الرابط.
                            </div>
                        </div>
                    </div>

                    {{-- Save --}}
                    @can('settings.manage')
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
                            حفظ الإعدادات
                        </button>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700 text-sm">
                            لا يمكنك حفظ التغييرات لأنك لا تملك صلاحية <span class="font-mono">settings.manage</span>.
                        </div>
                    @endcan
                </div>
            </div>
        </form>
    </div>
@endsection
