@extends('layouts.public')
@section('title', app()->getLocale() === 'en' ? 'Reports' : 'التقارير')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        $title = $isEn ? 'Reports' : 'التقارير';
        $subtitle = $isEn ? 'Monthly/annual reports and documents.' : 'تقارير شهرية/سنوية ووثائق.';

        $rTitle = fn($r) => $isEn ? ($r->title_en ?: $r->title_ar) : ($r->title_ar ?: $r->title_en);
        $rSummary = fn($r) => $isEn ? ($r->summary_en ?: $r->summary_ar) : ($r->summary_ar ?: $r->summary_en);

        $urlTransparency = url($base . '/transparency');
        $urlDonate = url($base . '/donate');
    @endphp

    {{-- Header --}}
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
            @foreach ($reports as $r)
                <a href="{{ url($base . '/transparency/reports/' . $r->id) }}" class="card p-6 hover:shadow-sm transition">
                    <div class="text-xs text-subtext">
                        {{ $r->period_year ? $r->period_year . '-' . $r->period_month : ($isEn ? 'General' : 'عام') }}
                        @if ($r->campaign)
                            ·
                            {{ $isEn ? ($r->campaign->title_en ?: $r->campaign->title_ar) : ($r->campaign->title_ar ?: $r->campaign->title_en) }}
                        @endif
                    </div>

                    <div class="mt-2 font-black text-lg text-text line-clamp-2">
                        {{ $rTitle($r) }}
                    </div>

                    <div class="text-sm text-subtext mt-2 line-clamp-3">
                        {{ $rSummary($r) ?: ($isEn ? 'Open to read details and access the PDF.' : 'افتح لقراءة التفاصيل والوصول إلى ملف PDF.') }}
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
