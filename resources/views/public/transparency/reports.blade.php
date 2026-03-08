@extends('layouts.public')
@section('title', app()->isLocale('en') ? 'Reports' : 'التقارير')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $title = $isEn ? 'Reports' : 'التقارير';
        $subtitle = $isEn ? 'Monthly/annual reports and documents.' : 'تقارير شهرية/سنوية ووثائق.';

        $reportTitle = fn($report) => $isEn
            ? ($report->title_en ?:
            $report->title_ar)
            : ($report->title_ar ?:
            $report->title_en);

        $reportSummary = fn($report) => $isEn
            ? ($report->summary_en ?:
            $report->summary_ar)
            : ($report->summary_ar ?:
            $report->summary_en);

        $urlTransparency = locale_route('transparency');
        $urlDonate = locale_route('donate');
        $reportShowUrl = fn($report) => locale_route('reports.show', ['report' => $report->id]);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <div class="text-sm text-subtext">
                <a class="hover:underline underline-offset-4" href="{{ $urlTransparency }}">
                    {{ $isEn ? 'Transparency' : 'الشفافية' }}
                </a>
                <span class="mx-2">/</span>
                <span class="text-text font-semibold">{{ $title }}</span>
            </div>

            <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">
                {{ $title }}
            </h1>
            <p class="mt-2 text-subtext">{{ $subtitle }}</p>
        </div>

        <a href="{{ $urlDonate }}" class="btn btn-primary">
            {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
            <span aria-hidden="true">→</span>
        </a>
    </div>

    @if ($reports->count())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($reports as $report)
                <a href="{{ $reportShowUrl($report) }}" class="card p-6 hover:shadow-sm transition">
                    <div class="text-xs text-subtext">
                        {{ $report->period_year ? $report->period_year . '-' . $report->period_month : ($isEn ? 'General' : 'عام') }}
                        @if ($report->campaign)
                            ·
                            {{ $isEn ? ($report->campaign->title_en ?: $report->campaign->title_ar) : ($report->campaign->title_ar ?: $report->campaign->title_en) }}
                        @endif
                    </div>

                    <div class="mt-2 font-black text-lg text-text line-clamp-2">
                        {{ $reportTitle($report) }}
                    </div>

                    <div class="text-sm text-subtext mt-2 line-clamp-3">
                        {{ $reportSummary($report) ?: ($isEn ? 'Open to read details and access the PDF.' : 'افتح لقراءة التفاصيل والوصول إلى ملف PDF.') }}
                    </div>

                    <div class="mt-5 inline-flex items-center gap-2 text-sm font-black text-brand">
                        {{ $isEn ? 'Open report' : 'فتح التقرير' }}
                        <span class="text-subtext">↗</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @else
        <div class="rounded-[28px] border border-border bg-muted p-10 text-center">
            <div class="text-lg font-black text-text">
                {{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}
            </div>
            <div class="mt-2 text-sm text-subtext">
                {{ $isEn ? 'Please check back soon.' : 'يرجى العودة لاحقًا.' }}
            </div>
        </div>
    @endif
@endsection
