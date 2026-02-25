@extends('layouts.admin')

@section('title', 'إعدادات الموقع')
@section('page_title', 'إعدادات الموقع')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إعدادات الموقع</h1>
            <p class="text-sm text-slate-500 mt-1">تحكم كامل بالهوية، التواصل، والسيو من مكان واحد.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- General --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">إعدادات عامة</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">اسم الموقع</label>
                                <input name="site_name" value="{{ old('site_name', $data['site.name'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('site_name')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">وصف مختصر</label>
                                <input name="site_tagline" value="{{ old('site_tagline', $data['site.tagline'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('site_tagline')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">اللغة الافتراضية</label>
                                <select name="site_locale"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                    @foreach (['ar' => 'العربية', 'en' => 'English'] as $k => $label)
                                        <option value="{{ $k }}" @selected(old('site_locale', $data['site.locale'] ?? 'ar') === $k)>{{ $label }}
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
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                    @foreach (['USD', 'EUR', 'ILS'] as $cur)
                                        <option value="{{ $cur }}" @selected(old('site_currency', $data['site.default_currency'] ?? 'USD') === $cur)>
                                            {{ $cur }}</option>
                                    @endforeach
                                </select>
                                @error('site_currency')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Timezone</label>
                                <input name="site_timezone"
                                    value="{{ old('site_timezone', $data['site.timezone'] ?? config('app.timezone')) }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('site_timezone')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">بيانات التواصل</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                <input name="contact_email"
                                    value="{{ old('contact_email', $data['contact.email'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('contact_email')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                                <input name="contact_phone"
                                    value="{{ old('contact_phone', $data['contact.phone'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('contact_phone')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">WhatsApp</label>
                                <input name="contact_whatsapp"
                                    value="{{ old('contact_whatsapp', $data['contact.whatsapp'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('contact_whatsapp')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SEO --}}
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">SEO</div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Meta Title</label>
                                <input name="seo_title" value="{{ old('seo_title', $data['seo.meta_title'] ?? '') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('seo_title')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Meta Description</label>
                                <textarea name="seo_description" rows="4"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">{{ old('seo_description', $data['seo.meta_description'] ?? '') }}</textarea>
                                @error('seo_description')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
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

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                                <input type="file" name="site_logo" accept="image/*"
                                    class="block w-full text-sm file:mr-3 file:rounded-2xl file:border-0 file:bg-black file:px-4 file:py-2.5 file:text-white file:text-sm file:font-semibold hover:file:opacity-95 transition">
                                @error('site_logo')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror

                                @if (!empty($data['site.logo']))
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/' . $data['site.logo']) }}"
                                            class="h-14 rounded-2xl border border-slate-200 bg-white p-2" alt="">
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                                <input type="file" name="site_favicon" accept="image/*"
                                    class="block w-full text-sm file:mr-3 file:rounded-2xl file:border-0 file:bg-black file:px-4 file:py-2.5 file:text-white file:text-sm file:font-semibold hover:file:opacity-95 transition">
                                @error('site_favicon')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror

                                @if (!empty($data['site.favicon']))
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/' . $data['site.favicon']) }}"
                                            class="h-10 w-10 rounded-2xl border border-slate-200 bg-white p-2"
                                            alt="">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Social --}}
                    @php($social = $data['social.links'] ?? [])
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">روابط السوشال</div>

                        <div class="space-y-3">
                            <input name="social_facebook" value="{{ old('social_facebook', $social['facebook'] ?? '') }}"
                                placeholder="Facebook URL"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition">
                            @error('social_facebook')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_x" value="{{ old('social_x', $social['x'] ?? '') }}"
                                placeholder="X / Twitter URL"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition">
                            @error('social_x')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_instagram"
                                value="{{ old('social_instagram', $social['instagram'] ?? '') }}"
                                placeholder="Instagram URL"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition">
                            @error('social_instagram')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror

                            <input name="social_youtube" value="{{ old('social_youtube', $social['youtube'] ?? '') }}"
                                placeholder="YouTube URL"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition">
                            @error('social_youtube')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Save --}}
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
                        حفظ الإعدادات
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
