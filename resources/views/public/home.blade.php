@extends('layouts.public')

@section('title', app()->getLocale() === 'en' ? 'Home' : 'الرئيسية')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';

        $get = function (string $key, $default = null) {
            try {
                return function_exists('setting') ? setting($key, $default) : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        };

        $siteName = (string) $get('site.name', config('app.name'));
        $tagline = (string) $get('site.tagline', '');

        $base = $isEn ? '/en' : '';
        $urlDonate = url($base . '/donate');
        $urlCampaigns = url($base . '/campaigns');
        $urlTransparency = url($base . '/transparency');
        $urlReports = url($base . '/transparency/reports');

        $money = function ($v) {
            return number_format((float) $v, 2);
        };

        $campaignTitle = fn($c) => $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);
        $campaignDesc = fn($c) => $isEn
            ? ($c->description_en ?:
            $c->description_ar)
            : ($c->description_ar ?:
            $c->description_en);

        $pct = function ($current, $goal) {
            $goal = (float) $goal;
            if ($goal <= 0) {
                return 0;
            }
            $p = ((float) $current / $goal) * 100;
            return (int) max(0, min(100, round($p)));
        };

        $kTotalPaid = (float) ($totalPaid ?? 0);
        $kDonorsCount = (int) ($donorsCount ?? 0);
        $kActiveCampaign = (int) ($activeCampaigns ?? 0);
        $kPaidCount = (int) ($paidCount ?? 0);
        $kAvgDonation = (float) ($avgDonation ?? 0);

        $heroTitle = $isEn ? 'Donate with trust. See the impact clearly.' : 'تبرّع بثقة. وشاهد الأثر بوضوح.';
        $heroDesc =
            $tagline ?:
            ($isEn
                ? 'A transparency-first donation platform: real-time progress, public reports, and consistent updates.'
                : 'منصة تبرعات بأولوية الشفافية: تقدم لحظي، تقارير عامة، وتحديثات مستمرة.');

        $trustChips = [
            $isEn ? 'Verified reports' : 'تقارير موثقة',
            $isEn ? 'Public transparency' : 'شفافية عامة',
            $isEn ? 'Secure flow & receipts' : 'تدفق آمن وإيصالات',
        ];

        $steps = [
            [
                't' => $isEn ? 'Choose a campaign' : 'اختر حملة',
                'd' => $isEn
                    ? 'Pick a cause and review goals and details.'
                    : 'اختر قضية تهمك واطّلع على الهدف والتفاصيل.',
            ],
            [
                't' => $isEn ? 'Donate in minutes' : 'تبرّع خلال دقائق',
                'd' => $isEn
                    ? 'Clean flow with optional anonymity and instant confirmation.'
                    : 'تجربة بسيطة مع خيار إخفاء الاسم وتأكيد فوري.',
            ],
            [
                't' => $isEn ? 'Follow updates & reports' : 'تابع التحديثات والتقارير',
                'd' => $isEn
                    ? 'Track progress through updates and periodic public reports.'
                    : 'راقب التقدم عبر التحديثات والتقارير العامة الدورية.',
            ],
        ];
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface">
        {{-- premium background --}}
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, rgba(var(--brand),.22), transparent 60%);"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-20 h-72 w-72 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

        <div class="p-6 sm:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            {{-- LEFT --}}
            <div>
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-border bg-muted px-3 py-1.5 text-xs font-bold text-subtext">
                    <span class="h-2 w-2 rounded-full bg-success"></span>
                    <span>{{ $isEn ? 'Transparency-first donations' : 'تبرعات بأولوية الشفافية' }}</span>
                </div>

                <h1 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-text">
                    {{ $heroTitle }}
                </h1>

                <p class="mt-4 text-subtext leading-relaxed text-base sm:text-lg">
                    {{ $heroDesc }}
                </p>

                {{-- Trust chips --}}
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($trustChips as $chip)
                        <span class="badge">{{ $chip }}</span>
                    @endforeach
                </div>

                {{-- CTA --}}
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

                {{-- KPIs --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="card p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Total paid' : 'إجمالي المدفوع' }}</div>
                        <div class="mt-1 font-black text-text">{{ $money($kTotalPaid) }}</div>
                    </div>
                    <div class="card p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div>
                        <div class="mt-1 font-black text-text">{{ number_format($kDonorsCount) }}</div>
                    </div>
                    <div class="card p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Paid donations' : 'تبرعات مدفوعة' }}</div>
                        <div class="mt-1 font-black text-text">{{ number_format($kPaidCount) }}</div>
                    </div>
                    <div class="card p-4">
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

            {{-- RIGHT: Featured campaigns --}}
            <div class="card p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-black text-text">{{ $isEn ? 'Featured campaigns' : 'حملات بارزة' }}</div>
                    <a href="{{ $urlCampaigns }}" class="text-sm font-black text-brand hover:underline underline-offset-4">
                        {{ $isEn ? 'View all' : 'عرض الكل' }}
                    </a>
                </div>

                @if (!empty($featuredCampaigns) && $featuredCampaigns->count())
                    <div class="grid grid-cols-1 gap-3">
                        @foreach ($featuredCampaigns as $c)
                            @php
                                $p = $pct($c->current_amount, $c->goal_amount);
                                $img = $c->cover_image_path ? asset('storage/' . $c->cover_image_path) : null;
                            @endphp

                            <a href="{{ url($base . '/campaigns/' . $c->slug) }}"
                                class="group block rounded-3xl border border-border bg-surface hover:bg-muted transition overflow-hidden">
                                <div class="flex gap-4 p-4">
                                    <div class="shrink-0">
                                        @if ($img)
                                            <img src="{{ $img }}" alt=""
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
                                                    {{ $campaignTitle($c) }}
                                                </div>
                                                <div class="text-xs text-subtext mt-1 line-clamp-2">
                                                    {{ $campaignDesc($c) ?: ($isEn ? 'No description yet.' : 'لا يوجد وصف بعد.') }}
                                                </div>
                                            </div>

                                            @if ($c->is_featured)
                                                <span
                                                    class="text-[11px] px-2 py-0.5 rounded-full bg-muted border border-border font-black text-brand">
                                                    {{ $isEn ? 'Featured' : 'مميزة' }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- progress --}}
                                        <div class="mt-3 h-2 bg-muted rounded-full overflow-hidden border border-border">
                                            <div class="h-2 rounded-full"
                                                style="width: {{ $p }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                                            </div>
                                        </div>

                                        <div class="mt-2 flex justify-between text-xs text-subtext">
                                            <span class="font-black text-text">{{ $p }}%</span>
                                            <span>
                                                {{ $money($c->current_amount) }} {{ $c->currency }}
                                                /
                                                {{ $money($c->goal_amount) }} {{ $c->currency }}
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

                <div class="mt-6 card-muted p-4 text-sm text-subtext">
                    <span class="font-black text-text">{{ $siteName }}</span>
                    — {{ $isEn ? 'Built for measurable impact & trust.' : 'مبني لأثر قابل للقياس وثقة عالية.' }}
                </div>
            </div>
        </div>
    </section>

    {{-- TRUST / WHY --}}
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

    {{-- HOW IT WORKS --}}
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
                @foreach ($steps as $i => $s)
                    <div class="card-muted p-6">
                        <div class="text-xs font-black text-brand">
                            {{ $isEn ? 'STEP' : 'الخطوة' }} {{ $i + 1 }}
                        </div>
                        <div class="mt-2 text-lg font-black text-text">{{ $s['t'] }}</div>
                        <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $s['d'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- LATEST REPORTS --}}
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
            @forelse(($latestReports ?? collect()) as $r)
                @php
                    $title = $isEn ? ($r->title_en ?: $r->title_ar) : ($r->title_ar ?: $r->title_en);
                    $summary = $isEn ? ($r->summary_en ?: $r->summary_ar) : ($r->summary_ar ?: $r->summary_en);
                    $period = trim(($r->period_month ?? '') . '/' . ($r->period_year ?? ''));
                    $camp = $r->campaign ?? null;
                    $campTitle = $camp
                        ? ($isEn
                            ? ($camp->title_en ?:
                            $camp->title_ar)
                            : ($camp->title_ar ?:
                            $camp->title_en))
                        : null;
                @endphp

                <a href="{{ url($base . '/transparency/reports/' . $r->id) }}"
                    class="card p-6 hover:bg-muted transition">
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

                        @if ($campTitle)
                            <span class="font-black text-subtext line-clamp-1">{{ $campTitle }}</span>
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

    {{-- FINAL CTA --}}
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
