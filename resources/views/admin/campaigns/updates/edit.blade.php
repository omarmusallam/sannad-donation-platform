@extends('layouts.admin')

@section('title', 'تعديل تحديث')
@section('page_title', 'تعديل تحديث: ' . $campaign->title_ar)

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">تعديل تحديث</h1>
                <p class="text-sm text-slate-500 mt-1">قم بتحديث العناوين والمحتوى وإعدادات النشر.</p>
            </div>

            <a href="{{ route('admin.campaigns.updates.index', $campaign) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('admin.campaigns.updates.update', [$campaign, $update]) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">العناوين</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (عربي)</label>
                                <input name="title_ar" value="{{ old('title_ar', $update->title_ar) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Title (English)</label>
                                <input name="title_en" value="{{ old('title_en', $update->title_en) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900 mb-5">المحتوى</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">المحتوى (عربي)</label>
                                <textarea name="body_ar" rows="8"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">{{ old('body_ar', $update->body_ar) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Body (English)</label>
                                <textarea name="body_en" rows="8"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">{{ old('body_en', $update->body_en) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
                        <div class="text-base font-semibold text-slate-900">إعدادات النشر</div>
                        <div class="text-xs text-slate-500 mt-1 mb-5">التحكم بالظهور وتاريخ النشر.</div>

                        <label
                            class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">عام (ظاهر للزوار)</div>
                                <div class="text-xs text-slate-500 mt-0.5">إلغاءها يجعل التحديث داخلي.</div>
                            </div>
                            <input type="checkbox" name="is_public" value="1"
                                class="w-5 h-5 rounded border-slate-300 text-black focus:ring-black/10"
                                @checked(old('is_public', $update->is_public))>
                        </label>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">تاريخ النشر</label>
                            <input type="datetime-local" name="published_at"
                                value="{{ old('published_at', optional($update->published_at)->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                        </div>
                    </div>

                    <button
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
                        تحديث
                    </button>

                    <a href="{{ route('admin.campaigns.updates.index', $campaign) }}"
                        class="w-full inline-flex items-center justify-center px-5 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
