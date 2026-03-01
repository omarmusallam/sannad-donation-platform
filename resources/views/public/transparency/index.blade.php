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

        $kTotalPaid = (float) ($totalPaid ?? 0);
        $kPaidCount = (int) ($paidDonationsCount ?? 0);
        $kDonorsCount = (int) ($donorsCount ?? 0);
        $kActiveCampaigns = (int) ($activeCampaigns ?? 0);
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface p-7 sm:p-10 mb-8">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, rgba(var(--brand),.20), transparent 60%);"></div>
        <div class="pointer-events-none absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="min-w-0">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface text-xs text-subtext">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-black text-text">{{ $isEn ? 'Transparency-first' : 'الشفافية أولاً' }}</span>
                </div>

                <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">
                    {{ $title }}
                </h1>
                <p class="mt-2 text-subtext leading-relaxed">
                    {{ $subtitle }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ $urlReports }}" class="btn btn-primary">
                        {{ $isEn ? 'Browse reports' : 'عرض التقارير' }}
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $urlCampaigns }}" class="btn btn-secondary">
                        {{ $isEn ? 'View campaigns' : 'عرض الحملات' }}
                    </a>
                    <a href="{{ $urlDonate }}" class="btn btn-ghost">
                        {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
                    </a>
                </div>
            </div>

            {{-- Mini note --}}
            <div class="rounded-2xl border border-border bg-surface/70 p-5 text-sm max-w-md">
                <div class="font-black text-text mb-2">
                    {{ $isEn ? 'What you’ll find here' : 'ماذا ستجد هنا' }}
                </div>
                <ul class="text-subtext space-y-1 list-disc ps-5">
                    <li>{{ $isEn ? 'Totals for paid donations' : 'إجماليات التبرعات المدفوعة' }}</li>
                    <li>{{ $isEn ? 'Public reports and PDF documents' : 'تقارير عامة وملفات PDF' }}</li>
                    <li>{{ $isEn ? 'Top campaigns and latest activity' : 'أعلى الحملات وآخر النشاط' }}</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- KPI GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="card p-6">
            <div class="text-sm text-subtext">{{ $isEn ? 'Total paid donations' : 'إجمالي التبرعات المدفوعة' }}</div>
            <div class="mt-2 text-2xl font-black text-text">{{ $money($kTotalPaid) }}</div>
        </div>

        <div class="card p-6">
            <div class="text-sm text-subtext">{{ $isEn ? 'Paid donations' : 'عدد التبرعات المدفوعة' }}</div>
            <div class="mt-2 text-2xl font-black text-text">{{ number_format($kPaidCount) }}</div>
        </div>

        <div class="card p-6">
            <div class="text-sm text-subtext">{{ $isEn ? 'Donors (unique)' : 'المتبرعون (فريد)' }}</div>
            <div class="mt-2 text-2xl font-black text-text">{{ number_format($kDonorsCount) }}</div>
        </div>

        <div class="card p-6">
            <div class="text-sm text-subtext">{{ $isEn ? 'Active campaigns' : 'الحملات النشطة' }}</div>
            <div class="mt-2 text-2xl font-black text-text">{{ number_format($kActiveCampaigns) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top campaigns --}}
        <div class="card p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-black text-text">{{ $isEn ? 'Top campaigns' : 'أعلى الحملات' }}</h2>
                <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlCampaigns }}">
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
                        class="block rounded-2xl border border-border hover:bg-muted transition p-4">
                        <div class="flex justify-between gap-3">
                            <div class="font-black text-text line-clamp-1">
                                {{ $campaignTitle($c) }}
                                @if ($c->is_featured)
                                    <span class="ms-2 inline-flex align-middle badge badge-brand">
                                        {{ $isEn ? 'Featured' : 'مميزة' }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm font-black text-text">{{ $p }}%</div>
                        </div>

                        <div class="mt-2 h-2 bg-muted rounded-full overflow-hidden">
                            <div class="h-2 rounded-full"
                                style="width: {{ $p }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                            </div>
                        </div>

                        <div class="mt-2 text-xs text-subtext">
                            {{ $money($paid) }} {{ $c->currency }} / {{ $money($c->goal_amount) }} {{ $c->currency }}
                        </div>
                    </a>
                @empty
                    <div class="text-sm text-subtext">{{ $isEn ? 'No campaigns yet.' : 'لا توجد حملات بعد.' }}</div>
                @endforelse
            </div>
        </div>

        {{-- Latest reports --}}
        <div class="card p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-black text-text">{{ $isEn ? 'Latest reports' : 'أحدث التقارير' }}</h2>
                <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlReports }}">
                    {{ $isEn ? 'All reports' : 'كل التقارير' }}
                </a>
            </div>

            <div class="space-y-3">
                @forelse($latestReports as $r)
                    <a href="{{ url($base . '/transparency/reports/' . $r->id) }}"
                        class="block rounded-2xl border border-border hover:bg-muted transition p-4">
                        <div class="font-black text-text line-clamp-1">{{ $reportTitle($r) }}</div>
                        <div class="text-xs text-subtext mt-1">
                            {{ $r->period_year ? $r->period_year . '-' . $r->period_month : ($isEn ? 'General' : 'عام') }}
                            @if ($r->campaign)
                                ·
                                {{ $isEn ? ($r->campaign->title_en ?: $r->campaign->title_ar) : ($r->campaign->title_ar ?: $r->campaign->title_en) }}
                            @endif
                        </div>
                        <div class="mt-2 text-sm text-subtext line-clamp-2">
                            {{ $reportSummary($r) ?: ($isEn ? 'Open to view details and PDF.' : 'افتح لعرض التفاصيل وملف PDF.') }}
                        </div>
                    </a>
                @empty
                    <div class="text-sm text-subtext">{{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Latest donations --}}
    <div class="mt-6 card p-6">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="text-lg font-black text-text">{{ $isEn ? 'Latest donations' : 'آخر التبرعات' }}</h2>
            <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlDonate }}">
                {{ $isEn ? 'Donate' : 'تبرع الآن' }}
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($latestDonations as $d)
                <div class="rounded-2xl border border-border bg-muted p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-bold text-text min-w-0">
                            <span class="line-clamp-1">
                                {{ $d->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($d->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}
                            </span>
                        </div>
                        <div class="text-sm font-black text-text shrink-0">
                            {{ $money($d->amount) }} {{ $d->currency }}
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-subtext">
                        {{ $d->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            @empty
                <div class="text-sm text-subtext">{{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}</div>
            @endforelse
        </div>
    </div>

    {{-- CTA --}}
    <section class="mt-10">
        <div class="relative overflow-hidden rounded-[28px] border border-border bg-surface p-8 sm:p-10">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-25"
                style="background: radial-gradient(circle, rgba(var(--brand),.20), transparent 60%);"></div>
            <div class="pointer-events-none absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-20"
                style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="text-2xl font-black text-text">
                        {{ $isEn ? 'Want to help today?' : 'بدك تساهم اليوم؟' }}
                    </div>
                    <div class="mt-2 text-subtext">
                        {{ $isEn ? 'Choose a campaign and donate securely with full visibility.' : 'اختر حملة وتبرع بأمان مع وضوح كامل.' }}
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ $urlDonate }}" class="btn btn-primary">
                        {{ $isEn ? 'Donate now' : 'تبرع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $urlCampaigns }}" class="btn btn-secondary">
                        {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
