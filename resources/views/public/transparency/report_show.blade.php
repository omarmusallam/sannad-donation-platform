@extends('layouts.public')
@section('title', $report->title)

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $urlReports = locale_route('reports.index');
        $urlTransparency = locale_route('transparency');
        $campaignTitle = $report->campaign ? ($isEn ? ($report->campaign->title_en ?: $report->campaign->title_ar) : ($report->campaign->title_ar ?: $report->campaign->title_en)) : null;
        $copyLabel = $isEn ? 'Copy link' : 'نسخ الرابط';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 text-sm text-subtext">
            <a class="hover:underline underline-offset-4" href="{{ $urlTransparency }}">{{ $isEn ? 'Transparency' : 'الشفافية' }}</a>
            <span class="mx-2">/</span>
            <a class="hover:underline underline-offset-4" href="{{ $urlReports }}">{{ $isEn ? 'Reports' : 'التقارير' }}</a>
            <span class="mx-2">/</span>
            <span class="text-text font-semibold">{{ $report->title }}</span>
        </div>

        <div class="section-shell p-7 sm:p-10 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="eyebrow">{{ $isEn ? 'Public report' : 'تقرير عام' }}</div>
                    <h1 class="mt-4 text-2xl sm:text-3xl font-black tracking-tight text-text">{{ $report->title }}</h1>
                    <div class="text-sm text-subtext mt-2">
                        {{ $report->period_label }}
                        @if ($campaignTitle)
                            <span class="mx-1">•</span>{{ $campaignTitle }}
                        @endif
                    </div>
                </div>

                <div class="shrink-0 inline-flex items-center gap-2 px-3 py-1 rounded-full border border-border bg-muted text-xs font-black text-text">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    PDF
                </div>
            </div>

            @if (!empty($report->summary))
                <p class="text-subtext mt-5 leading-relaxed">{{ $report->summary }}</p>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex flex-col sm:flex-row gap-3">
                    @if ($report->pdf_url)
                        <a class="btn btn-primary text-center" href="{{ $report->pdf_url }}" target="_blank" rel="noopener">
                            {{ $isEn ? 'Open PDF' : 'فتح ملف PDF' }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <div class="px-5 py-3 rounded-2xl border border-border text-subtext text-center bg-muted">{{ $isEn ? 'PDF not available' : 'ملف PDF غير متوفر' }}</div>
                    @endif

                    <button type="button" onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';" class="btn btn-secondary">{{ $copyLabel }}</button>
                </div>

                <a class="btn btn-secondary text-center" href="{{ $urlReports }}">{{ $isEn ? 'Back to reports' : 'العودة إلى التقارير' }}</a>
            </div>
        </div>
    </div>
@endsection
