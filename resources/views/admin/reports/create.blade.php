@extends('layouts.admin')

@section('title', 'إضافة تقرير')
@section('page_title', 'إضافة تقرير')

@section('content')
    <div class="max-w-6xl">

        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إضافة تقرير</h1>
            <p class="text-sm text-slate-500 mt-1">قم بإنشاء تقرير جديد وربطه بحملة إن رغبت.</p>
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
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                                @error('title_ar')
                                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (English)</label>
                                <input name="title_en" value="{{ old('title_en') }}"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 transition">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">الملخص</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <textarea name="summary_ar" rows="4"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition"
                                placeholder="الملخص بالعربي">{{ old('summary_ar') }}</textarea>

                            <textarea name="summary_en" rows="4"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 transition"
                                placeholder="Summary in English">{{ old('summary_en') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-4">الفترة</div>

                        <div class="space-y-4">
                            <input name="period_month" value="{{ old('period_month') }}" placeholder="الشهر (01-12)"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10">

                            <input name="period_year" value="{{ old('period_year') }}" placeholder="السنة (2026)"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10">
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-4">إعدادات</div>

                        <select name="campaign_id"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 mb-4">
                            <option value="">بدون حملة</option>
                            @foreach ($campaigns as $c)
                                <option value="{{ $c->id }}" @selected(old('campaign_id') == $c->id)>
                                    {{ $c->title_ar }}
                                </option>
                            @endforeach
                        </select>

                        <label
                            class="flex items-center justify-between p-4 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition">
                            <span class="text-sm font-semibold text-slate-800">عام (ظاهر للزوار)</span>
                            <input type="checkbox" name="is_public" value="1" @checked(old('is_public', true))
                                class="w-5 h-5 text-black border-slate-300 rounded">
                        </label>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-4">ملف PDF</div>

                        <input type="file" name="pdf" accept="application/pdf"
                            class="block w-full text-sm file:mr-3 file:rounded-2xl file:border-0 file:bg-black file:px-4 file:py-2 file:text-white file:text-sm file:font-semibold hover:file:opacity-95">

                        @error('pdf')
                            <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        class="w-full px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
                        حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
