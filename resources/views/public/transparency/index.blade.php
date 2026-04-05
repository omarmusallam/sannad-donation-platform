@extends('layouts.public')
@section('title', app()->isLocale('en') ? 'Transparency' : 'الشفافية')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $money = fn($value) => number_format((float) $value, 2);

        $title = $isEn ? 'Transparency hub' : 'مركز الشفافية';
        $subtitle = $isEn
            ? 'Clear totals, public reports, campaign proof, and donor-facing visibility in one place.'
            : 'إجماليات واضحة وتقارير عامة وإثباتات للحملات ووضوح موجّه للمتبرع في مكان واحد.';

        $campaignTitle = fn($campaign) => $isEn ? ($campaign->title_en ?: $campaign->title_ar) : ($campaign->title_ar ?: $campaign->title_en);
        $reportTitle = fn($report) => $isEn ? ($report->title_en ?: $report->title_ar) : ($report->title_ar ?: $report->title_en);
        $reportSummary = fn($report) => $isEn ? ($report->summary_en ?: $report->summary_ar) : ($report->summary_ar ?: $report->summary_en);

        $pct = function ($paidTotal, $goal) {
            $goal = (float) $goal;
            if ($goal <= 0) {
                return 0;
            }

            $percent = ((float) $paidTotal / $goal) * 100;
            return (int) max(0, min(100, round($percent)));
        };

        $urlReports = locale_route('reports.index');
        $urlCampaigns = locale_route('campaigns.index');
        $urlDonate = locale_route('donate');
        $campaignShowUrl = fn($campaign) => locale_route('campaigns.show', ['slug' => $campaign->slug]);
        $reportShowUrl = fn($report) => locale_route('reports.show', ['report' => $report->id]);

        $kTotalPaid = (float) ($totalPaid ?? 0);
        $kPaidCount = (int) ($paidDonationsCount ?? 0);
        $kDonorsCount = (int) ($donorsCount ?? 0);
        $kActiveCampaigns = (int) ($activeCampaigns ?? 0);
    @endphp

    <section class="section-shell p-7 sm:p-10 mb-8 relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-25" style="background: radial-gradient(circle, rgba(var(--brand),.20), transparent 60%);"></div>
        <div class="pointer-events-none absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-20" style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="min-w-0">
                <div class="eyebrow">{{ $isEn ? 'Public reporting and proof' : 'تقارير وإثباتات عامة' }}</div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $title }}</h1>
                <p class="mt-3 text-subtext leading-relaxed max-w-3xl">{{ $subtitle }}</p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ $urlReports }}" class="btn btn-primary">{{ $isEn ? 'Browse reports' : 'عرض التقارير' }}</a>
                    <a href="{{ $urlCampaigns }}" class="btn btn-secondary">{{ $isEn ? 'View campaigns' : 'عرض الحملات' }}</a>
                    <a href="{{ $urlDonate }}" class="btn btn-secondary">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }}</a>
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-surface/70 p-5 text-sm max-w-md">
                <div class="font-black text-text mb-2">{{ $isEn ? 'What this hub gives donors' : 'ماذا يقدم هذا المركز للمتبرع' }}</div>
                <ul class="text-subtext space-y-2">
                    <li>• {{ $isEn ? 'Paid totals and campaign-level progress' : 'إجماليات مدفوعة وتقدم على مستوى الحملات' }}</li>
                    <li>• {{ $isEn ? 'Public reports and downloadable proof documents' : 'تقارير عامة ووثائق إثبات قابلة للتحميل' }}</li>
                    <li>• {{ $isEn ? 'A clearer view of the platform operating model' : 'صورة أوضح عن طريقة عمل المنصة' }}</li>
                </ul>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="kpi-tile"><div class="text-sm text-subtext">{{ $isEn ? 'Total paid donations' : 'إجمالي التبرعات المدفوعة' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $money($kTotalPaid) }} USD</div></div>
        <div class="kpi-tile"><div class="text-sm text-subtext">{{ $isEn ? 'Paid donations' : 'عدد التبرعات المدفوعة' }}</div><div class="mt-2 text-2xl font-black text-text">{{ number_format($kPaidCount) }}</div></div>
        <div class="kpi-tile"><div class="text-sm text-subtext">{{ $isEn ? 'Unique donors' : 'المتبرعون الفريدون' }}</div><div class="mt-2 text-2xl font-black text-text">{{ number_format($kDonorsCount) }}</div></div>
        <div class="kpi-tile"><div class="text-sm text-subtext">{{ $isEn ? 'Active campaigns' : 'الحملات النشطة' }}</div><div class="mt-2 text-2xl font-black text-text">{{ number_format($kActiveCampaigns) }}</div></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <div class="eyebrow">{{ $isEn ? 'Campaign visibility' : 'وضوح الحملات' }}</div>
                    <h2 class="mt-3 text-lg font-black text-text">{{ $isEn ? 'Top campaigns' : 'أعلى الحملات' }}</h2>
                </div>
                <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlCampaigns }}">{{ $isEn ? 'View all' : 'عرض الكل' }}</a>
            </div>

            <div class="space-y-3">
                @forelse ($topCampaigns as $campaign)
                    @php
                        $paid = (float) ($campaign->paid_total ?? 0);
                        $progress = $pct($paid, $campaign->goal_amount);
                    @endphp

                    <a href="{{ $campaignShowUrl($campaign) }}" class="block rounded-2xl border border-border hover:bg-muted transition p-4">
                        <div class="flex justify-between gap-3">
                            <div class="font-black text-text line-clamp-1">{{ $campaignTitle($campaign) }}</div>
                            <div class="text-sm font-black text-text">{{ $progress }}%</div>
                        </div>
                        <div class="mt-2 h-2 bg-muted rounded-full overflow-hidden"><div class="h-2 rounded-full" style="width: {{ $progress }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));"></div></div>
                        <div class="mt-2 text-xs text-subtext">{{ $money($paid) }} {{ $campaign->currency }} / {{ $money($campaign->goal_amount) }} {{ $campaign->currency }}</div>
                    </a>
                @empty
                    <div class="text-sm text-subtext">{{ $isEn ? 'No campaigns yet.' : 'لا توجد حملات بعد.' }}</div>
                @endforelse
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <div class="eyebrow">{{ $isEn ? 'Published documents' : 'وثائق منشورة' }}</div>
                    <h2 class="mt-3 text-lg font-black text-text">{{ $isEn ? 'Latest reports' : 'أحدث التقارير' }}</h2>
                </div>
                <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlReports }}">{{ $isEn ? 'All reports' : 'كل التقارير' }}</a>
            </div>

            <div class="space-y-3">
                @forelse($latestReports as $report)
                    <a href="{{ $reportShowUrl($report) }}" class="block rounded-2xl border border-border hover:bg-muted transition p-4">
                        <div class="font-black text-text line-clamp-1">{{ $reportTitle($report) }}</div>
                        <div class="text-xs text-subtext mt-1">{{ $report->period_year ? $report->period_year . '-' . $report->period_month : ($isEn ? 'General' : 'عام') }}</div>
                        <div class="mt-2 text-sm text-subtext line-clamp-2">{{ $reportSummary($report) ?: ($isEn ? 'Open to view details and supporting PDF.' : 'افتح لعرض التفاصيل وملف الإثبات PDF.') }}</div>
                    </a>
                @empty
                    <div class="text-sm text-subtext">{{ $isEn ? 'No reports yet.' : 'لا توجد تقارير بعد.' }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 card p-6">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <div class="eyebrow">{{ $isEn ? 'Recent donor activity' : 'نشاط المتبرعين الأخير' }}</div>
                <h2 class="mt-3 text-lg font-black text-text">{{ $isEn ? 'Latest donations' : 'آخر التبرعات' }}</h2>
            </div>
            <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $urlDonate }}">{{ $isEn ? 'Donate' : 'تبرّع الآن' }}</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($latestDonations as $donation)
                <div class="rounded-2xl border border-border bg-muted p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-bold text-text min-w-0"><span class="line-clamp-1">{{ $donation->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($donation->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}</span></div>
                        <div class="text-sm font-black text-text shrink-0">{{ $money($donation->amount) }} {{ $donation->currency }}</div>
                    </div>
                    <div class="mt-1 text-xs text-subtext">{{ $donation->created_at->format('Y-m-d H:i') }}</div>
                </div>
            @empty
                <div class="text-sm text-subtext">{{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}</div>
            @endforelse
        </div>
    </div>
@endsection
