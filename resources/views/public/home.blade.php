@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Home' : 'الرئيسية')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $siteName = (string) ($appSettings['site.name'] ?? config('app.name'));
        $tagline = (string) ($appSettings['site.tagline'] ?? '');

        $urlDonate = locale_route('donate');
        $urlCampaigns = locale_route('campaigns.index');
        $urlTransparency = locale_route('transparency');
        $urlReports = locale_route('reports.index');

        $money = fn($value) => number_format((float) $value, 2);

        $campaignTitle = fn($campaign) => $isEn
            ? ($campaign->title_en ?:
            $campaign->title_ar)
            : ($campaign->title_ar ?:
            $campaign->title_en);

        $campaignDesc = fn($campaign) => $isEn
            ? ($campaign->description_en ?:
            $campaign->description_ar)
            : ($campaign->description_ar ?:
            $campaign->description_en);

        $campaignUrl = fn($campaign) => locale_route('campaigns.show', ['slug' => $campaign->slug]);
        $reportUrl = fn($report) => locale_route('reports.show', ['report' => $report->id]);

        $pct = function ($current, $goal) {
            $goal = (float) $goal;

            if ($goal <= 0) {
                return 0;
            }

            $percent = ((float) $current / $goal) * 100;

            return (int) max(0, min(100, round($percent)));
        };

        $kTotalPaid = (float) ($totalPaid ?? 0);
        $kDonorsCount = (int) ($donorsCount ?? 0);
        $kActiveCampaign = (int) ($activeCampaigns ?? 0);
        $kPaidCount = (int) ($paidCount ?? 0);
        $kAvgDonation = (float) ($avgDonation ?? 0);

        $heroTitle = $isEn ? 'Verified giving with a calm, clear experience.' : 'تبرع موثّق بتجربة هادئة وواضحة.';

        $heroDesc =
            $tagline ?:
            ($isEn
                ? 'Support trusted campaigns through a professional donation flow built around clarity, reports, receipts, and secure follow-up.'
                : 'ادعم الحملات الموثوقة عبر مسار تبرع احترافي مبني على الوضوح، والتقارير، والإيصالات، والمتابعة الآمنة.');

        $trustChips = [
            $isEn ? 'Verified reports' : 'تقارير موثقة',
            $isEn ? 'USD-only consistency' : 'اتساق مالي بالدولار',
            $isEn ? 'Secure receipts & tracking' : 'إيصالات وتتبع آمن',
        ];

        $steps = [
            [
                'title' => $isEn ? 'Choose a campaign' : 'اختر حملة',
                'desc' => $isEn
                    ? 'Pick a cause and review goals and details.'
                    : 'اختر قضية تهمك واطّلع على الهدف والتفاصيل.',
            ],
            [
                'title' => $isEn ? 'Donate in minutes' : 'تبرّع خلال دقائق',
                'desc' => $isEn
                    ? 'Clean flow with optional anonymity and instant confirmation.'
                    : 'تجربة بسيطة مع خيار إخفاء الاسم وتأكيد فوري.',
            ],
            [
                'title' => $isEn ? 'Follow updates & reports' : 'تابع التحديثات والتقارير',
                'desc' => $isEn
                    ? 'Track progress through updates and periodic public reports.'
                    : 'راقب التقدم عبر التحديثات والتقارير العامة الدورية.',
            ],
        ];
    @endphp

    <section class="section-shell relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, rgba(var(--brand),.22), transparent 60%);"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-20 h-72 w-72 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

        <div class="p-6 sm:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <div class="eyebrow">
                    <span class="h-2 w-2 rounded-full bg-success"></span>
                    <span>{{ $isEn ? 'Trust-centered giving' : 'تبرع يرتكز على الثقة' }}</span>
                </div>

                <h1 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-text">
                    {{ $heroTitle }}
                </h1>

                <p class="mt-4 text-subtext leading-relaxed text-base sm:text-lg">
                    {{ $heroDesc }}
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($trustChips as $chip)
                        <span class="badge">{{ $chip }}</span>
                    @endforeach
                </div>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $urlDonate }}" class="btn btn-primary">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>

                    <a href="{{ $urlCampaigns }}" class="btn btn-secondary">
                        {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                    </a>

                    <a href="{{ $urlTransparency }}" class="btn btn-ghost">
                        {{ $isEn ? 'Transparency center' : 'مركز الشفافية' }}
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Total paid' : 'إجمالي المدفوع' }}</div>
                        <div class="mt-1 font-black text-text">{{ $money($kTotalPaid) }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div>
                        <div class="mt-1 font-black text-text">{{ number_format($kDonorsCount) }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Paid donations' : 'تبرعات مدفوعة' }}</div>
                        <div class="mt-1 font-black text-text">{{ number_format($kPaidCount) }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Active campaigns' : 'حملات نشطة' }}</div>
                        <div class="mt-1 font-black text-text">{{ number_format($kActiveCampaign) }}</div>
                    </div>
                </div>

                @if ($kAvgDonation > 0)
                    <div class="mt-3 text-xs text-subtext">
                        {{ $isEn ? 'Average donation:' : 'متوسط التبرع:' }}
                        <span class="font-black text-text">{{ $money($kAvgDonation) }}</span>
                    </div>
                @endif
            </div>

            <div class="card p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-black text-text">{{ $isEn ? 'Featured campaigns' : 'حملات بارزة' }}</div>
                    <a href="{{ $urlCampaigns }}" class="text-sm font-black text-brand hover:underline underline-offset-4">
                        {{ $isEn ? 'View all' : 'عرض الكل' }}
                    </a>
                </div>

                @if (!empty($featuredCampaigns) && $featuredCampaigns->count())
                    <div class="grid grid-cols-1 gap-3">
                        @foreach ($featuredCampaigns as $campaign)
                            @php
                                $progress = $pct($campaign->current_amount, $campaign->goal_amount);
                                $imageUrl = $campaign->cover_image_path
                                    ? asset('storage/' . $campaign->cover_image_path)
                                    : null;
                            @endphp

                            <a href="{{ $campaignUrl($campaign) }}"
                                class="group block rounded-3xl border border-border bg-surface hover:bg-muted transition overflow-hidden">
                                <div class="flex gap-4 p-4">
                                    <div class="shrink-0">
                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt=""
                                                class="w-16 h-16 rounded-2xl object-cover border border-border bg-muted">
                                        @else
                                            <div
                                                class="w-16 h-16 rounded-2xl border border-border bg-muted grid place-items-center">
                                                <span class="text-subtext text-xs font-bold">
                                                    {{ $isEn ? 'No image' : 'بدون صورة' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div
                                                    class="font-black text-text line-clamp-1 group-hover:underline underline-offset-4">
                                                    {{ $campaignTitle($campaign) }}
                                                </div>
                                                <div class="text-xs text-subtext mt-1 line-clamp-2">
                                                    {{ $campaignDesc($campaign) ?: ($isEn ? 'No description yet.' : 'لا يوجد وصف بعد.') }}
                                                </div>
                                            </div>

                                            @if ($campaign->is_featured)
                                                <span
                                                    class="text-[11px] px-2 py-0.5 rounded-full bg-muted border border-border font-black text-brand">
                                                    {{ $isEn ? 'Featured' : 'مميزة' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 h-2 bg-muted rounded-full overflow-hidden border border-border">
                                            <div class="h-2 rounded-full"
                                                style="width: {{ $progress }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                                            </div>
                                        </div>

                                        <div class="mt-2 flex justify-between text-xs text-subtext">
                                            <span class="font-black text-text">{{ $progress }}%</span>
                                            <span>
                                                {{ $money($campaign->current_amount) }} {{ $campaign->currency }}
                                                /
                                                {{ $money($campaign->goal_amount) }} {{ $campaign->currency }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="card-muted p-6 text-sm text-subtext">
                        {{ $isEn ? 'No campaigns available yet.' : 'لا توجد حملات متاحة بعد.' }}
                    </div>
                @endif

                <div class="mt-6 card-muted p-4 text-sm text-subtext leading-relaxed">
                    <span class="font-black text-text">{{ $siteName }}</span>
                    {{ $isEn ? ' is built for measurable impact, quiet confidence, and donor trust.' : ' مبني لأثر قابل للقياس، وثقة هادئة، وتجربة متبرع احترافية.' }}
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-text">
                    {{ $isEn ? 'A modern donation experience.' : 'تجربة تبرع حديثة واحترافية.' }}
                </h2>
                <p class="mt-2 text-subtext">
                    {{ $isEn ? 'Clear steps, visible progress, and public reporting.' : 'خطوات واضحة، تقدم مرئي، وتقارير عامة.' }}
                </p>
            </div>

            <a class="hidden sm:inline-flex btn btn-secondary px-4 py-2.5" href="{{ $urlTransparency }}">
                {{ $isEn ? 'Transparency center' : 'مركز الشفافية' }}
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card p-6">
                <div class="text-sm font-black text-text">{{ $isEn ? 'Verified reports' : 'تقارير موثقة' }}</div>
                <div class="mt-2 text-sm text-subtext leading-relaxed">
                    {{ $isEn ? 'Periodic public documents linked to campaigns.' : 'وثائق عامة دورية مرتبطة بالحملات.' }}
                </div>
            </div>

            <div class="card p-6">
                <div class="text-sm font-black text-text">{{ $isEn ? 'Continuous updates' : 'تحديثات مستمرة' }}</div>
                <div class="mt-2 text-sm text-subtext leading-relaxed">
                    {{ $isEn ? 'Follow progress with scheduled and published updates.' : 'تابع التقدم عبر تحديثات منشورة ومجدولة.' }}
                </div>
            </div>

            <div class="card p-6">
                <div class="text-sm font-black text-text">{{ $isEn ? 'Clean donation flow' : 'تجربة تبرع سلسة' }}</div>
                <div class="mt-2 text-sm text-subtext leading-relaxed">
                    {{ $isEn ? 'Minimal steps with clear confirmation and receipts.' : 'خطوات قليلة مع تأكيد واضح وإيصالات.' }}
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12">
        <div class="card p-6 sm:p-8">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-text">
                        {{ $isEn ? 'How it works' : 'كيف يعمل التبرع' }}
                    </h2>
                    <p class="mt-2 text-subtext">
                        {{ $isEn ? 'A simple flow designed for clarity.' : 'مسار بسيط مصمم للوضوح.' }}
                    </p>
                </div>

                <a href="{{ $urlDonate }}" class="hidden sm:inline-flex btn btn-secondary px-4 py-2.5">
                    {{ $isEn ? 'Start donating' : 'ابدأ التبرع' }}
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($steps as $index => $step)
                    <div class="card-muted p-6">
                        <div class="text-xs font-black text-brand">
                            {{ $isEn ? 'STEP' : 'الخطوة' }} {{ $index + 1 }}
                        </div>
                        <div class="mt-2 text-lg font-black text-text">{{ $step['title'] }}</div>
                        <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $step['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-12">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-text">
                    {{ $isEn ? 'Latest reports' : 'أحدث التقارير' }}
                </h2>
                <p class="mt-2 text-subtext">
                    {{ $isEn ? 'Public documents and verified updates.' : 'وثائق عامة وتحديثات موثقة.' }}
                </p>
            </div>

            <a class="hidden sm:inline-flex btn btn-secondary px-4 py-2.5" href="{{ $urlReports }}">
                {{ $isEn ? 'All reports' : 'كل التقارير' }}
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse(($latestReports ?? collect()) as $report)
                @php
                    $title = $isEn
                        ? ($report->title_en ?:
                        $report->title_ar)
                        : ($report->title_ar ?:
                        $report->title_en);
                    $summary = $isEn
                        ? ($report->summary_en ?:
                        $report->summary_ar)
                        : ($report->summary_ar ?:
                        $report->summary_en);
                    $period = trim(($report->period_month ?? '') . '/' . ($report->period_year ?? ''));

                    $campaign = $report->campaign ?? null;
                    $campaignTitleForReport = $campaign
                        ? ($isEn
                            ? ($campaign->title_en ?:
                            $campaign->title_ar)
                            : ($campaign->title_ar ?:
                            $campaign->title_en))
                        : null;
                @endphp

                <a href="{{ $reportUrl($report) }}" class="card p-6 hover:bg-muted transition">
                    <div class="text-xs text-subtext">{{ $period ?: ($isEn ? 'Report' : 'تقرير') }}</div>
                    <div class="mt-2 font-black text-text line-clamp-2">{{ $title }}</div>
                    <div class="text-sm text-subtext mt-2 line-clamp-3">
                        {{ $summary ?: ($isEn ? 'Open to read details and PDF.' : 'افتح لقراءة التفاصيل وملف PDF.') }}
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-subtext">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-success"></span>
                            {{ $isEn ? 'Public' : 'عام' }}
                        </span>

                        @if ($campaignTitleForReport)
                            <span class="font-black text-subtext line-clamp-1">{{ $campaignTitleForReport }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="md:col-span-3 card-muted p-10 text-center">
                    <div class="text-lg font-black text-text">
                        {{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}
                    </div>
                    <div class="mt-2 text-sm text-subtext">
                        {{ $isEn ? 'Please check back soon.' : 'يرجى العودة لاحقًا.' }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6 sm:hidden">
            <a class="w-full btn btn-secondary" href="{{ $urlReports }}">
                {{ $isEn ? 'All reports' : 'كل التقارير' }}
            </a>
        </div>
    </section>

    <section class="mt-12">
        <div
            class="relative overflow-hidden rounded-[28px] border border-border bg-gradient-to-b from-muted to-bg p-8 sm:p-10">
            <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full blur-3xl opacity-25"
                style="background: radial-gradient(circle, rgba(var(--brand),.22), transparent 60%);"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-20 h-72 w-72 rounded-full blur-3xl opacity-20"
                style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="text-2xl font-black text-text">
                        {{ $isEn ? 'Ready to help today?' : 'جاهز تساعد اليوم؟' }}
                    </div>
                    <div class="mt-2 text-subtext">
                        {{ $isEn ? 'Choose a campaign and donate securely with visibility.' : 'اختر حملة وتبرع بأمان مع وضوح كامل.' }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ $urlDonate }}" class="btn btn-primary">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
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