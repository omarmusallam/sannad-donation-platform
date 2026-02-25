@extends('layouts.public')
@section('title', $report->title)

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        $urlReports = url($base . '/transparency/reports');
        $urlTransparency = url($base . '/transparency');

        $campTitle = $report->campaign
            ? (app()->getLocale() === 'en'
                ? ($report->campaign->title_en ?:
                $report->campaign->title_ar)
                : ($report->campaign->title_ar ?:
                $report->campaign->title_en))
            : null;

        $copyLabel = $isEn ? 'Copy link' : 'نسخ الرابط';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
    @endphp

    <div class="max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="mb-6 text-sm text-slate-500">
            <a class="hover:underline underline-offset-4"
                href="{{ $urlTransparency }}">{{ $isEn ? 'Transparency' : 'الشفافية' }}</a>
            <span class="mx-2">/</span>
            <a class="hover:underline underline-offset-4" href="{{ $urlReports }}">{{ $isEn ? 'Reports' : 'التقارير' }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $report->title }}</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-7 sm:p-10 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-40"
                style="background: radial-gradient(circle, rgba(79,70,229,.18), transparent 60%);"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-35"
                style="background: radial-gradient(circle, rgba(16,185,129,.14), transparent 60%);"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">
                        {{ $report->title }}
                    </h1>

                    <div class="text-sm text-slate-500 mt-2">
                        {{ $report->period_label }}
                        @if ($campTitle)
                            · {{ $campTitle }}
                        @endif
                    </div>
                </div>

                <div
                    class="shrink-0 px-3 py-1 rounded-full border border-slate-200 bg-slate-50 text-xs font-extrabold text-slate-700">
                    PDF
                </div>
            </div>

            @if (!empty($report->summary))
                <p class="text-slate-700 mt-5 leading-relaxed">
                    {{ $report->summary }}
                </p>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex flex-col sm:flex-row gap-3">
                    @if ($report->pdf_url)
                        <a class="px-5 py-3 rounded-2xl font-extrabold text-white text-center shadow-sm hover:shadow transition"
                            style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));"
                            href="{{ $report->pdf_url }}" target="_blank" rel="noopener">
                            {{ $isEn ? 'Open PDF' : 'فتح ملف PDF' }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <div class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 text-center bg-slate-50">
                            {{ $isEn ? 'PDF not available' : 'ملف PDF غير متوفر' }}
                        </div>
                    @endif

                    <button type="button"
                        onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';"
                        class="px-5 py-3 rounded-2xl border border-slate-200 text-center hover:bg-slate-50 transition font-bold text-slate-800">
                        {{ $copyLabel }}
                    </button>
                </div>

                <a class="px-5 py-3 rounded-2xl border border-slate-200 text-center hover:bg-slate-50 transition font-bold"
                    href="{{ route('reports.index') }}">
                    {{ $isEn ? 'Back to reports' : 'العودة للتقارير' }}
                </a>
            </div>
        </div>
    </div>
@endsection
