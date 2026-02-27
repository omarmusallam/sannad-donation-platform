@php
    $isAr = app()->isLocale('ar');
    $page = $page ?? null; // safe
@endphp

@csrf

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Content --}}
        <div class="bg-white border border-slate-200 rounded-[28px] shadow-sm p-6">
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <div class="text-xs text-slate-500">{{ $isAr ? 'المحتوى' : 'Content' }}</div>
                    <div class="text-lg font-extrabold text-slate-900">
                        {{ $isAr ? 'بيانات الصفحة' : 'Page content' }}
                    </div>
                    <div class="text-xs text-slate-500 mt-1">
                        {{ $isAr ? 'العناوين والمحتوى بالعربية والإنجليزية.' : 'Arabic and English titles & content.' }}
                    </div>
                </div>

                <span class="shrink-0 text-xs px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-700">
                    Arabic / English
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label
                        class="block text-sm font-semibold text-slate-800">{{ $isAr ? 'العنوان (AR)' : 'Title (AR)' }}</label>
                    <input name="title_ar" value="{{ old('title_ar', $page?->title_ar ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition"
                        required>
                    @error('title_ar')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-800">{{ $isAr ? 'العنوان (EN)' : 'Title (EN)' }}</label>
                    <input name="title_en" value="{{ old('title_en', $page?->title_en ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                    @error('title_en')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-5">
                <label
                    class="block text-sm font-semibold text-slate-800">{{ $isAr ? 'المحتوى (AR)' : 'Content (AR)' }}</label>
                <textarea name="content_ar" rows="10"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">{{ old('content_ar', $page?->content_ar ?? '') }}</textarea>
                <div class="text-xs text-slate-500 mt-2">
                    {{ $isAr ? 'يمكنك كتابة HTML بسيط (p, ul, h2...)' : 'You can write simple HTML (p, ul, h2...)' }}
                </div>
                @error('content_ar')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-5">
                <label
                    class="block text-sm font-semibold text-slate-800">{{ $isAr ? 'المحتوى (EN)' : 'Content (EN)' }}</label>
                <textarea name="content_en" rows="10"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">{{ old('content_en', $page?->content_en ?? '') }}</textarea>
                @error('content_en')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white border border-slate-200 rounded-[28px] shadow-sm p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <div class="text-xs text-slate-500">SEO</div>
                    <div class="text-lg font-extrabold text-slate-900">{{ $isAr ? 'بيانات السيو' : 'SEO settings' }}
                    </div>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-700">
                    Meta
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-800">Meta Title (AR)</label>
                    <input name="meta_title_ar" value="{{ old('meta_title_ar', $page?->meta_title_ar ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                    @error('meta_title_ar')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-800">Meta Title (EN)</label>
                    <input name="meta_title_en" value="{{ old('meta_title_en', $page?->meta_title_en ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                    @error('meta_title_en')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-800">Meta Description (AR)</label>
                    <textarea name="meta_description_ar" rows="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">{{ old('meta_description_ar', $page?->meta_description_ar ?? '') }}</textarea>
                    @error('meta_description_ar')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-800">Meta Description (EN)</label>
                    <textarea name="meta_description_en" rows="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">{{ old('meta_description_en', $page?->meta_description_en ?? '') }}</textarea>
                    @error('meta_description_en')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        <div class="bg-white border border-slate-200 rounded-[28px] shadow-sm p-6">
            <div class="text-lg font-extrabold text-slate-900 mb-1">{{ $isAr ? 'الإعدادات' : 'Settings' }}</div>
            <div class="text-xs text-slate-500 mb-5">
                {{ $isAr ? 'الظهور، السلاج، والترتيب.' : 'Visibility, slug and sorting.' }}</div>

            <div>
                <label class="block text-sm font-semibold text-slate-800">Slug</label>
                <input name="slug" value="{{ old('slug', $page?->slug ?? '') }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono
                           focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition"
                    placeholder="about-us" required>
                <div class="text-xs text-slate-500 mt-2">
                    {{ $isAr ? 'lowercase + dash فقط (مثال: privacy-policy)' : 'Lowercase + dash only (e.g. privacy-policy)' }}
                </div>
                @error('slug')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>

            <label
                class="mt-5 flex items-center justify-between gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                <div>
                    <div class="text-sm font-semibold text-slate-800">{{ $isAr ? 'إظهار للزوار' : 'Public page' }}
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        {{ $isAr ? 'إخفاء الصفحة يمنع ظهورها بالموقع.' : 'Disabling hides it from the website.' }}
                    </div>
                </div>
                <div class="shrink-0">
                    <input type="hidden" name="is_public" value="0">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $page?->is_public ?? true))
                        class="h-5 w-5 rounded border-slate-300 text-black focus:ring-black/10">
                </div>
            </label>

            <div class="mt-5">
                <label
                    class="block text-sm font-semibold text-slate-800">{{ $isAr ? 'الترتيب' : 'Sort order' }}</label>
                <input type="number" name="sort_order" min="0" max="9999"
                    value="{{ old('sort_order', $page?->sort_order ?? 0) }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                @error('sort_order')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-5 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                {{ $isAr ? 'رابط الصفحة:' : 'Page URL:' }}
                <span class="font-semibold text-slate-900" dir="ltr">
                    /p/{{ old('slug', $page?->slug ?? '{slug}') }}
                </span>
            </div>
        </div>

        <button type="submit"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            حفظ
        </button>

        <a href="{{ route('admin.pages.index') }}"
            class="block text-center w-full px-4 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-sm font-semibold text-slate-800 transition">
            رجوع
        </a>

    </div>
</div>
