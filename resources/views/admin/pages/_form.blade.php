@php $isAr = app()->isLocale('ar'); @endphp

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Content --}}
        <div class="bg-white border border-slate-200/70 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <div class="text-xs text-slate-500">Content</div>
                    <div class="text-lg font-extrabold text-slate-900">المحتوى</div>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-700">
                    Arabic / English
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-800">العنوان (AR)</label>
                    <input name="title_ar" value="{{ old('title_ar', $page->title_ar ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                        required>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-800">Title (EN)</label>
                    <input name="title_en" value="{{ old('title_en', $page->title_en ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-slate-800">المحتوى (AR)</label>
                <textarea name="content_ar" rows="10"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">{{ old('content_ar', $page->content_ar ?? '') }}</textarea>
                <div class="text-xs text-slate-500 mt-2">يمكنك كتابة HTML بسيط (p, ul, h2...)</div>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-slate-800">Content (EN)</label>
                <textarea name="content_en" rows="10"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">{{ old('content_en', $page->content_en ?? '') }}</textarea>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white border border-slate-200/70 rounded-3xl shadow-sm p-6">
            <div class="text-lg font-extrabold text-slate-900 mb-5">SEO</div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-slate-800">Meta Title (AR)</label>
                    <input name="meta_title_ar" value="{{ old('meta_title_ar', $page->meta_title_ar ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Meta Title (EN)</label>
                    <input name="meta_title_en" value="{{ old('meta_title_en', $page->meta_title_en ?? '') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="text-sm font-semibold text-slate-800">Meta Description (AR)</label>
                    <textarea name="meta_description_ar" rows="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('meta_description_ar', $page->meta_description_ar ?? '') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Meta Description (EN)</label>
                    <textarea name="meta_description_en" rows="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('meta_description_en', $page->meta_description_en ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        <div class="bg-white border border-slate-200/70 rounded-3xl shadow-sm p-6">
            <div class="text-lg font-extrabold text-slate-900 mb-5">الإعدادات</div>

            <div>
                <label class="text-sm font-semibold text-slate-800">Slug</label>
                <input name="slug" value="{{ old('slug', $page->slug ?? '') }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-mono"
                    placeholder="about-us" required>
                <div class="text-xs text-slate-500 mt-2">lowercase + dash فقط (مثال: privacy-policy)</div>
            </div>

            <div
                class="mt-5 flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <div>
                    <div class="text-sm font-semibold text-slate-800">إظهار للزوار</div>
                    <div class="text-xs text-slate-500">إخفاء الصفحة يمنع ظهورها بالموقع.</div>
                </div>
                <div>
                    <input type="hidden" name="is_public" value="0">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $page->is_public ?? true))
                        class="h-5 w-5 rounded border-slate-300">
                </div>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-slate-800">الترتيب</label>
                <input type="number" name="sort_order" min="0" max="9999"
                    value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div class="mt-5 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                رابط الصفحة:
                <span class="font-semibold text-slate-900">/p/{{ old('slug', $page->slug ?? '{slug}') }}</span>
            </div>
        </div>

        <button type="submit" class="w-full px-4 py-3 rounded-2xl bg-black text-white font-semibold hover:opacity-90">
            حفظ
        </button>

        <a href="{{ route('admin.pages.index') }}"
            class="block text-center w-full px-4 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-sm font-semibold text-slate-800">
            رجوع
        </a>

    </div>
</div>
