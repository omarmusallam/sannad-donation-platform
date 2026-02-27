@extends('layouts.admin')

@section('title', $page->title())
@section('page_title', 'معاينة الصفحة')

@section('page_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('pages.show', $page->slug) }}" target="_blank" rel="noopener"
            class="px-4 py-2 rounded-2xl text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition">
            فتح على الموقع ↗
        </a>

        @can('pages.edit')
            <a href="{{ route('admin.pages.edit', $page) }}"
                class="px-4 py-2 rounded-2xl text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
                تعديل
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm p-6">
            <div class="text-xs text-slate-500" dir="ltr">
                /p/{{ $page->slug }}
            </div>

            <div class="mt-2 text-2xl font-extrabold text-slate-900">
                {{ $page->title() }}
            </div>

            @if ($page->metaDescription())
                <div class="mt-3 text-sm text-slate-600">
                    {{ $page->metaDescription() }}
                </div>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold
                    {{ $page->is_public ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    {{ $page->is_public ? 'منشورة' : 'مخفية' }}
                </span>

                <span class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold bg-slate-50 text-slate-700 border-slate-200">
                    ترتيب: {{ $page->sort_order }}
                </span>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm p-6 md:p-8">
            <article class="prose prose-slate max-w-none">
                {!! $page->content() !!}
            </article>
        </div>

    </div>
@endsection
