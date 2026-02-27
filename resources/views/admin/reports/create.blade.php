@extends('layouts.admin')

@section('title', 'إضافة تقرير')
@section('page_title', 'إضافة تقرير')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إضافة تقرير</h1>
                <p class="text-sm text-slate-500 mt-1">قم بإنشاء تقرير جديد وربطه بحملة إن رغبت.</p>
            </div>

            <a href="{{ route('admin.reports.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('admin.reports.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">البيانات الأساسية</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (عربي)</label>
                                <input name="title_ar" value="{{ old('title_ar') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('title_ar')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (English)</label>
                                <input name="title_en" value="{{ old('title_en') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('title_en')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">الملخص</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">الملخص (عربي)</label>
                                <textarea name="summary_ar" rows="5"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="الملخص بالعربي">{{ old('summary_ar') }}</textarea>
                                @error('summary_ar')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Summary (English)</label>
                                <textarea name="summary_en" rows="5"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                    placeholder="Summary in English">{{ old('summary_en') }}</textarea>
                                @error('summary_en')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900">الفترة</div>
                        <div class="text-xs text-slate-500 mt-1 mb-5">اختياري — لتصنيف التقرير شهريًا/سنويًا.</div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">الشهر (01-12)</label>
                                <input name="period_month" value="{{ old('period_month') }}" placeholder="مثال: 01"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('period_month')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">السنة</label>
                                <input name="period_year" value="{{ old('period_year') }}" placeholder="مثال: 2026"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                @error('period_year')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900">إعدادات</div>
                        <div class="text-xs text-slate-500 mt-1 mb-5">ربط بحملة + حالة الظهور.</div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">ربط بحملة (اختياري)</label>
                            <select name="campaign_id"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                                <option value="">بدون حملة</option>
                                @foreach ($campaigns as $c)
                                    <option value="{{ $c->id }}" @selected(old('campaign_id') == $c->id)>
                                        {{ $c->title_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('campaign_id')
                                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <label
                            class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">عام (ظاهر للزوار)</div>
                                <div class="text-xs text-slate-500 mt-0.5">تعطيله يجعل التقرير داخلي.</div>
                            </div>
                            <input type="checkbox" name="is_public" value="1" @checked(old('is_public', true))
                                class="w-5 h-5 rounded border-slate-300 text-black focus:ring-black/10">
                        </label>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900">ملف PDF</div>
                        <div class="text-xs text-slate-500 mt-1 mb-5">PDF مطلوب عند الإنشاء.</div>

                        <input type="file" name="pdf" accept="application/pdf"
                            class="block w-full text-sm text-slate-600
                                   file:mr-3 file:rounded-2xl file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-white file:text-sm file:font-semibold
                                   hover:file:bg-slate-800 transition">

                        @error('pdf')
                            <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
                        حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
