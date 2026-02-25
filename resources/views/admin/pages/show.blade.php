@extends('layouts.admin')

@section('title', $page->title())
@section('page_title', 'معاينة الصفحة')

@section('page_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
            class="px-4 py-2 rounded-2xl text-sm font-semibold border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
            فتح على الموقع ↗
        </a>

        @can('pages.edit')
            <a href="{{ route('admin.pages.edit', $page) }}"
                class="px-4 py-2 rounded-2xl text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
                تعديل
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        <div class="rounded-3xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-6">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                /p/{{ $page->slug }}
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">
                {{ $page->title() }}
            </div>

            @if ($page->metaDescription())
                <div class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                    {{ $page->metaDescription() }}
                </div>
            @endif
        </div>

        <div
            class="rounded-3xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-6 md:p-8">
            <article class="prose prose-slate dark:prose-invert max-w-none">
                {!! $page->content() !!}
            </article>
        </div>

    </div>
@endsection
