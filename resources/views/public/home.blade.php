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
                ? 'A modern donation platform built for transparency: real-time progress, public reports, and consistent updates.'
                : 'منصة تبرعات حديثة مبنية على الشفافية: تقدم واضح، تقارير عامة، وتحديثات مستمرة.');

        $steps = [
            [
                't' => $isEn ? 'Choose a campaign' : 'اختر حملة',
                'd' => $isEn
                    ? 'Pick a cause you care about and review its goals.'
                    : 'اختر قضية تهمك واطلع على الهدف والتفاصيل.',
            ],
            [
                't' => $isEn ? 'Donate in minutes' : 'تبرع خلال دقائق',
                'd' => $isEn
                    ? 'A clean flow with optional anonymity and instant confirmation.'
                    : 'تجربة بسيطة مع خيار التبرع المجهول وتأكيد فوري.',
            ],
            [
                't' => $isEn ? 'Follow updates & reports' : 'تابع التحديثات والتقارير',
                'd' => $isEn
                    ? 'Track progress through updates and periodic public reports.'
                    : 'راقب التقدم عبر التحديثات والتقارير العامة الدورية.',
            ],
        ];
    @endphp

    {{-- HERO (world-class) --}}
    <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-transparent"></div>
        <div class="absolute -right-16 -top-16 h-72 w-72 rounded-full blur-3xl opacity-40"
            style="background: radial-gradient(circle, rgba(79,70,229,.25), transparent 60%);"></div>
        <div class="absolute -left-16 -bottom-16 h-72 w-72 rounded-full blur-3xl opacity-35"
            style="background: radial-gradient(circle, rgba(16,185,129,.22), transparent 60%);"></div>

        <div class="px-6 sm:px-10 py-10 sm:py-14 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-white text-xs text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold">
                        {{ $isEn ? 'Transparency-first donations' : 'تبرعات بأولوية الشفافية' }}
                    </span>
                </div>

                <h1
                    class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-slate-950">
                    {{ $heroTitle }}
                </h1>

                <p class="mt-4 text-slate-600 leading-relaxed text-base sm:text-lg">
                    {{ $heroDesc }}
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $urlDonate }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>

                    <a href="{{ $urlCampaigns }}"
                        class="px-6 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 transition font-bold text-slate-800">
                        {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                    </a>

                    <a href="{{ $urlTransparency }}"
                        class="px-6 py-3 rounded-2xl text-slate-700 hover:bg-slate-50 transition font-bold">
                        {{ $isEn ? 'Transparency center' : 'مركز الشفافية' }}
                    </a>
                </div>

                {{-- KPIs --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Total paid' : 'إجمالي المدفوع' }}</div>
                        <div class="mt-1 font-extrabold text-slate-950">{{ $money($kTotalPaid) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div>
                        <div class="mt-1 font-extrabold text-slate-950">{{ number_format($kDonorsCount) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Paid donations' : 'تبرعات مدفوعة' }}</div>
                        <div class="mt-1 font-extrabold text-slate-950">{{ number_format($kPaidCount) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Active campaigns' : 'حملات نشطة' }}</div>
                        <div class="mt-1 font-extrabold text-slate-950">{{ number_format($kActiveCampaign) }}</div>
                    </div>
                </div>

                @if ($kAvgDonation > 0)
                    <div class="mt-3 text-xs text-slate-500">
                        {{ $isEn ? 'Average donation:' : 'متوسط التبرع:' }}
                        <span class="font-bold text-slate-700">{{ $money($kAvgDonation) }}</span>
                    </div>
                @endif
            </div>

            {{-- Featured campaigns (cards) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-bold text-slate-900">
                        {{ $isEn ? 'Featured campaigns' : 'حملات بارزة' }}
                    </div>
                    <a href="{{ $urlCampaigns }}"
                        class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4">
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
                                class="group block rounded-3xl border border-slate-200 hover:border-slate-300 bg-white overflow-hidden transition">
                                <div class="flex gap-4 p-4">
                                    <div class="shrink-0">
                                        @if ($img)
                                            <img src="{{ $img }}" alt=""
                                                class="w-16 h-16 rounded-2xl object-cover border border-slate-200 bg-slate-50">
                                        @else
                                            <div
                                                class="w-16 h-16 rounded-2xl border border-slate-200 bg-slate-50 grid place-items-center">
                                                <span
                                                    class="text-slate-400 text-xs">{{ $isEn ? 'No image' : 'بدون صورة' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div
                                                    class="font-extrabold text-slate-950 line-clamp-1 group-hover:underline underline-offset-4">
                                                    {{ $campaignTitle($c) }}
                                                </div>
                                                <div class="text-xs text-slate-500 mt-1 line-clamp-2">
                                                    {{ $campaignDesc($c) ?: ($isEn ? 'No description yet.' : 'لا يوجد وصف بعد.') }}
                                                </div>
                                            </div>

                                            @if ($c->is_featured)
                                                <span
                                                    class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 font-bold">
                                                    {{ $isEn ? 'Featured' : 'مميزة' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-2 rounded-full"
                                                style="width: {{ $p }}%; background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                                            </div>
                                        </div>

                                        <div class="mt-2 flex justify-between text-xs text-slate-500">
                                            <span class="font-bold text-slate-700">{{ $p }}%</span>
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
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
                        {{ $isEn ? 'No campaigns available yet.' : 'لا توجد حملات متاحة بعد.' }}
                    </div>
                @endif

                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <span class="font-extrabold text-slate-900">{{ $siteName }}</span>
                    — {{ $isEn ? 'Built for measurable impact & trust.' : 'مبني لأثر قابل للقياس وثقة عالية.' }}
                </div>
            </div>
        </div>
    </section>

    {{-- TRUST / WHY --}}
    <section class="mt-12">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950">
                    {{ $isEn ? 'A modern donation experience.' : 'تجربة تبرع حديثة واحترافية.' }}
                </h2>
                <p class="mt-2 text-slate-600">
                    {{ $isEn ? 'Clear steps, visible progress, and public reporting.' : 'خطوات واضحة، تقدم مرئي، وتقارير عامة.' }}
                </p>
            </div>

            <a class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition font-bold"
                href="{{ $urlTransparency }}">
                {{ $isEn ? 'Transparency center' : 'مركز الشفافية' }}
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-extrabold text-slate-950">{{ $isEn ? 'Verified reports' : 'تقارير موثقة' }}</div>
                <div class="mt-2 text-sm text-slate-600 leading-relaxed">
                    {{ $isEn ? 'Periodic public documents linked to campaigns.' : 'وثائق عامة دورية مرتبطة بالحملات.' }}
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-extrabold text-slate-950">{{ $isEn ? 'Continuous updates' : 'تحديثات مستمرة' }}
                </div>
                <div class="mt-2 text-sm text-slate-600 leading-relaxed">
                    {{ $isEn ? 'Follow progress with scheduled and published updates.' : 'تابع التقدم عبر تحديثات منشورة ومجدولة.' }}
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-extrabold text-slate-950">{{ $isEn ? 'Clean donation flow' : 'تجربة تبرع سلسة' }}
                </div>
                <div class="mt-2 text-sm text-slate-600 leading-relaxed">
                    {{ $isEn ? 'Minimal steps with clear confirmation and receipts.' : 'خطوات قليلة مع تأكيد واضح وإيصالات.' }}
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="mt-12">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950">
                        {{ $isEn ? 'How it works' : 'كيف يعمل التبرع' }}
                    </h2>
                    <p class="mt-2 text-slate-600">
                        {{ $isEn ? 'A simple flow designed for clarity.' : 'مسار بسيط مصمم للوضوح.' }}
                    </p>
                </div>
                <a href="{{ $urlDonate }}"
                    class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition font-bold">
                    {{ $isEn ? 'Start donating' : 'ابدأ التبرع' }}
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($steps as $i => $s)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <div class="text-xs font-extrabold text-indigo-700">
                            {{ $isEn ? 'STEP' : 'الخطوة' }} {{ $i + 1 }}
                        </div>
                        <div class="mt-2 text-lg font-extrabold text-slate-950">{{ $s['t'] }}</div>
                        <div class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $s['d'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- LATEST REPORTS --}}
    <section class="mt-12">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950">
                    {{ $isEn ? 'Latest reports' : 'أحدث التقارير' }}
                </h2>
                <p class="mt-2 text-slate-600">
                    {{ $isEn ? 'Public documents and verified updates.' : 'وثائق عامة وتحديثات موثقة.' }}
                </p>
            </div>

            <a class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition font-bold"
                href="{{ $urlReports }}">
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
                    class="rounded-3xl border border-slate-200 bg-white p-6 hover:shadow-sm hover:border-slate-300 transition">
                    <div class="text-xs text-slate-500">{{ $period ?: ($isEn ? 'Report' : 'تقرير') }}</div>
                    <div class="mt-2 font-extrabold text-slate-950 line-clamp-2">{{ $title }}</div>
                    <div class="text-sm text-slate-600 mt-2 line-clamp-3">
                        {{ $summary ?: ($isEn ? 'Open to read details and PDF.' : 'افتح لقراءة التفاصيل وملف PDF.') }}
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ $isEn ? 'Public' : 'عام' }}
                        </span>

                        @if ($campTitle)
                            <span class="font-bold text-slate-600 line-clamp-1">{{ $campTitle }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="md:col-span-3 rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
                    <div class="text-lg font-extrabold text-slate-950">
                        {{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}
                    </div>
                    <div class="mt-2 text-sm text-slate-600">
                        {{ $isEn ? 'Please check back soon.' : 'يرجى العودة لاحقًا.' }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6 sm:hidden">
            <a class="w-full inline-flex justify-center px-4 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 transition font-bold"
                href="{{ $urlReports }}">
                {{ $isEn ? 'All reports' : 'كل التقارير' }}
            </a>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="mt-12">
        <div
            class="rounded-3xl p-8 sm:p-10 border border-slate-200 bg-gradient-to-br from-slate-50 to-white overflow-hidden relative">
            <div class="absolute -right-16 -top-16 h-72 w-72 rounded-full blur-3xl opacity-40"
                style="background: radial-gradient(circle, rgba(79,70,229,.25), transparent 60%);"></div>
            <div class="absolute -left-16 -bottom-16 h-72 w-72 rounded-full blur-3xl opacity-35"
                style="background: radial-gradient(circle, rgba(16,185,129,.22), transparent 60%);"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="text-2xl font-extrabold text-slate-950">
                        {{ $isEn ? 'Ready to help today?' : 'جاهز تساعد اليوم؟' }}
                    </div>
                    <div class="mt-2 text-slate-600">
                        {{ $isEn ? 'Choose a campaign and donate securely with visibility.' : 'اختر حملة وتبرع بأمان مع وضوح كامل.' }}
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ $urlDonate }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
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
