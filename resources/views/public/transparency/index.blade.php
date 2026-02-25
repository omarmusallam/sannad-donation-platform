@extends('layouts.public')
@section('title', app()->getLocale() === 'en' ? 'Transparency' : 'الشفافية')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';
        $money = fn($v) => number_format((float) $v, 2);

        $title = $isEn ? 'Transparency Hub' : 'مركز الشفافية';
        $subtitle = $isEn
            ? 'Clear totals, verified reports, and proof of impact.'
            : 'إجماليات واضحة، تقارير موثقة، وإثبات أثر.';

        $campaignTitle = fn($c) => $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);

        $reportTitle = fn($r) => $isEn ? ($r->title_en ?: $r->title_ar) : ($r->title_ar ?: $r->title_en);
        $reportSummary = fn($r) => $isEn ? ($r->summary_en ?: $r->summary_ar) : ($r->summary_ar ?: $r->summary_en);

        $pct = function ($paidTotal, $goal) {
            $goal = (float) $goal;
            if ($goal <= 0) {
                return 0;
            }
            $p = ((float) $paidTotal / $goal) * 100;
            return (int) max(0, min(100, round($p)));
        };

        $urlReports = url($base . '/transparency/reports');
        $urlCampaigns = url($base . '/campaigns');
        $urlDonate = url($base . '/donate');
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 sm:p-10 mb-8">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-transparent"></div>
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-40"
            style="background: radial-gradient(circle, rgba(79,70,229,.22), transparent 60%);"></div>
        <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-35"
            style="background: radial-gradient(circle, rgba(16,185,129,.18), transparent 60%);"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="min-w-0">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-white text-xs text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-bold">{{ $isEn ? 'Transparency-first' : 'الشفافية أولاً' }}</span>
                </div>

                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-950">
                    {{ $title }}
                </h1>
                <p class="mt-2 text-slate-600 leading-relaxed">
                    {{ $subtitle }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ $urlReports }}"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Browse reports' : 'عرض التقارير' }}
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $urlCampaigns }}"
                        class="px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-800">
                        {{ $isEn ? 'View campaigns' : 'عرض الحملات' }}
                    </a>
                    <a href="{{ $urlDonate }}"
                        class="px-5 py-3 rounded-2xl text-slate-700 hover:bg-slate-50 transition font-bold">
                        {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
                    </a>
                </div>
            </div>

            {{-- Mini note --}}
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 text-sm text-slate-700 max-w-md">
                <div class="font-extrabold text-slate-950 mb-1">
                    {{ $isEn ? 'What you’ll find here' : 'ماذا ستجد هنا' }}
                </div>
                <ul class="text-slate-600 space-y-1 list-disc ps-5">
                    <li>{{ $isEn ? 'Totals for paid donations' : 'إجماليات التبرعات المدفوعة' }}</li>
                    <li>{{ $isEn ? 'Public reports and PDF documents' : 'تقارير عامة وملفات PDF' }}</li>
                    <li>{{ $isEn ? 'Top campaigns and latest activity' : 'أعلى الحملات وآخر النشاط' }}</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="text-sm text-slate-500">{{ $isEn ? 'Total paid donations' : 'إجمالي التبرعات المدفوعة' }}</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $money($totalPaid ?? 0) }}</div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="text-sm text-slate-500">{{ $isEn ? 'Paid donations' : 'عدد التبرعات المدفوعة' }}</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ number_format((int) ($paidDonationsCount ?? 0)) }}
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="text-sm text-slate-500">{{ $isEn ? 'Donors (unique)' : 'المتبرعون (فريد)' }}</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ number_format((int) ($donorsCount ?? 0)) }}</div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="text-sm text-slate-500">{{ $isEn ? 'Active campaigns' : 'الحملات النشطة' }}</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ number_format((int) ($activeCampaigns ?? 0)) }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top campaigns --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-extrabold text-slate-950">{{ $isEn ? 'Top campaigns' : 'أعلى الحملات' }}</h2>
                <a class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4" href="{{ $urlCampaigns }}">
                    {{ $isEn ? 'View all' : 'عرض الكل' }}
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($topCampaigns as $c)
                    @php
                        $paid = (float) ($c->paid_total ?? 0);
                        $p = $pct($paid, $c->goal_amount);
                        $href = url($base . '/campaigns/' . $c->slug);
                    @endphp

                    <a href="{{ $href }}"
                        class="block rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition p-4">
                        <div class="flex justify-between gap-3">
                            <div class="font-extrabold text-slate-950 line-clamp-1">
                                {{ $campaignTitle($c) }}
                                @if ($c->is_featured)
                                    <span
                                        class="ms-2 text-[11px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 align-middle font-bold">
                                        {{ $isEn ? 'Featured' : 'مميزة' }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm font-extrabold text-slate-700">{{ $p }}%</div>
                        </div>

                        <div class="mt-2 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-2 rounded-full"
                                style="width: {{ $p }}%; background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                            </div>
                        </div>

                        <div class="mt-2 text-xs text-slate-500">
                            {{ $money($paid) }} {{ $c->currency }}
                            /
                            {{ $money($c->goal_amount) }} {{ $c->currency }}
                        </div>
                    </a>
                @empty
                    <div class="text-sm text-slate-600">{{ $isEn ? 'No campaigns yet.' : 'لا توجد حملات بعد.' }}</div>
                @endforelse
            </div>
        </div>

        {{-- Latest reports --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-extrabold text-slate-950">{{ $isEn ? 'Latest reports' : 'أحدث التقارير' }}</h2>
                <a class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4" href="{{ $urlReports }}">
                    {{ $isEn ? 'All reports' : 'كل التقارير' }}
                </a>
            </div>

            <div class="space-y-3">
                @forelse($latestReports as $r)
                    <a href="{{ url($base . '/transparency/reports/' . $r->id) }}"
                        class="block rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition p-4">
                        <div class="font-extrabold text-slate-950 line-clamp-1">{{ $reportTitle($r) }}</div>
                        <div class="text-xs text-slate-500 mt-1">
                            {{ $r->period_year ? $r->period_year . '-' . $r->period_month : ($isEn ? 'General' : 'عام') }}
                            @if ($r->campaign)
                                ·
                                {{ $isEn ? ($r->campaign->title_en ?: $r->campaign->title_ar) : ($r->campaign->title_ar ?: $r->campaign->title_en) }}
                            @endif
                        </div>
                        <div class="mt-2 text-sm text-slate-600 line-clamp-2">
                            {{ $reportSummary($r) ?: ($isEn ? 'Open to view details and PDF.' : 'افتح لعرض التفاصيل وملف PDF.') }}
                        </div>
                    </a>
                @empty
                    <div class="text-sm text-slate-600">{{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Latest donations --}}
    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="text-lg font-extrabold text-slate-950">{{ $isEn ? 'Latest donations' : 'آخر التبرعات' }}</h2>
            <a class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4" href="{{ $urlDonate }}">
                {{ $isEn ? 'Donate' : 'تبرع الآن' }}
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($latestDonations as $d)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-bold text-slate-950 min-w-0">
                            <span class="line-clamp-1">
                                {{ $d->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($d->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}
                            </span>
                        </div>
                        <div class="text-sm font-extrabold text-slate-950 shrink-0">
                            {{ $money($d->amount) }} {{ $d->currency }}
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ $d->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            @empty
                <div class="text-sm text-slate-600">{{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}</div>
            @endforelse
        </div>
    </div>

    {{-- CTA --}}
    <section class="mt-10">
        <div
            class="rounded-3xl p-8 sm:p-10 border border-slate-200 bg-gradient-to-br from-slate-50 to-white overflow-hidden relative">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-40"
                style="background: radial-gradient(circle, rgba(79,70,229,.22), transparent 60%);"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-35"
                style="background: radial-gradient(circle, rgba(16,185,129,.18), transparent 60%);"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="text-2xl font-extrabold text-slate-950">
                        {{ $isEn ? 'Want to help today?' : 'بدك تساهم اليوم؟' }}
                    </div>
                    <div class="mt-2 text-slate-600">
                        {{ $isEn ? 'Choose a campaign and donate securely with full visibility.' : 'اختر حملة وتبرع بأمان مع وضوح كامل.' }}
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ $urlDonate }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $urlCampaigns }}"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-800">
                        {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
