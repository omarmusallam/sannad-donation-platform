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
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <div class="text-sm text-slate-500">
                <a class="hover:underline underline-offset-4" href="{{ $urlTransparency }}">
                    {{ $isEn ? 'Transparency' : 'الشفافية' }}
                </a>
                <span class="mx-2">/</span>
                <span class="text-slate-700 font-semibold">{{ $title }}</span>
            </div>

            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-950">
                {{ $title }}
            </h1>
            <p class="mt-2 text-slate-600">{{ $subtitle }}</p>
        </div>

        <a href="{{ url($base . '/donate') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
            style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
            {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
            <span aria-hidden="true">→</span>
        </a>
    </div>

    @if ($reports->count())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($reports as $r)
                <a href="{{ url($base . '/transparency/reports/' . $r->id) }}"
                    class="bg-white border border-slate-200 rounded-3xl p-6 hover:shadow-sm hover:border-slate-300 transition">
                    <div class="text-xs text-slate-500">
                        {{ $r->period_year ? $r->period_year . '-' . $r->period_month : ($isEn ? 'General' : 'عام') }}
                        @if ($r->campaign)
                            ·
                            {{ $isEn ? ($r->campaign->title_en ?: $r->campaign->title_ar) : ($r->campaign->title_ar ?: $r->campaign->title_en) }}
                        @endif
                    </div>

                    <div class="mt-2 font-extrabold text-lg text-slate-950 line-clamp-2">
                        {{ $rTitle($r) }}
                    </div>

                    <div class="text-sm text-slate-600 mt-2 line-clamp-3">
                        {{ $rSummary($r) ?: ($isEn ? 'Open to read details and access the PDF.' : 'افتح لقراءة التفاصيل والوصول إلى ملف PDF.') }}
                    </div>

                    <div class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold text-indigo-700">
                        {{ $isEn ? 'Open report' : 'فتح التقرير' }}
                        <span class="text-slate-400">↗</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @else
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
            <div class="text-lg font-extrabold text-slate-950">{{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}
            </div>
            <div class="mt-2 text-sm text-slate-600">{{ $isEn ? 'Please check back soon.' : 'يرجى العودة لاحقًا.' }}</div>
        </div>
    @endif
@endsection
