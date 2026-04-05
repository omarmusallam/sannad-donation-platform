@extends('layouts.public')

@section('title', $campaign->title)

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $campaignsUrl = locale_route('campaigns.index');
        $donateBaseUrl = locale_route('donate');
        $reportsUrl = locale_route('reports.index');
        $statusText = $campaign->status === 'active' ? ($isEn ? 'Active' : 'نشطة') : ($isEn ? 'Paused' : 'متوقفة مؤقتًا');
        $statusClass = $campaign->status === 'active'
            ? 'bg-success/10 text-success border-success/25'
            : 'bg-warning/10 text-warning border-warning/25';
        $money = fn($value) => number_format((float) $value, 2);
        $remaining = max(0, (float) $campaign->goal_amount - (float) $campaign->current_amount);
        $updateTitle = fn($update) => $isEn ? $update->title_en ?? ($update->title_ar ?? '') : $update->title_ar ?? ($update->title_en ?? '');
        $updateBody = fn($update) => $isEn ? $update->body_en ?? ($update->body_ar ?? '') : $update->body_ar ?? ($update->body_en ?? '');
        $reportTitle = fn($report) => $isEn ? ($report->title_en ?: $report->title_ar) : ($report->title_ar ?: $report->title_en);
        $shareLabel = $isEn ? 'Copy campaign link' : 'نسخ رابط الحملة';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
        $progress = max(0, min(100, (int) ($campaign->progress_percent ?? 0)));
        $heroImage = $campaign->cover_url ?? ($campaign->cover_image_path ? asset('storage/' . $campaign->cover_image_path) : null);
    @endphp

    <section class="section-shell mb-8">
        <div class="text-sm text-subtext">
            <a class="hover:underline underline-offset-4" href="{{ $campaignsUrl }}">{{ $isEn ? 'Campaigns' : 'الحملات' }}</a>
            <span class="mx-2">/</span>
            <span class="text-text font-semibold">{{ $campaign->title }}</span>
        </div>

        <div class="mt-5 max-w-3xl">
            <div class="eyebrow">{{ $isEn ? 'Verified campaign' : 'حملة موثقة' }}</div>
            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-text">{{ $campaign->title }}</h1>
            <p class="mt-4 text-subtext leading-relaxed">
                {{ $campaign->description ?: ($isEn ? 'This campaign is published with a clear goal, visible progress, and public updates that help donors give with confidence.' : 'هذه الحملة منشورة بهدف واضح وتقدم معلن وتحديثات عامة تساعد المتبرع على التبرع بثقة.') }}
            </p>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <span class="badge">{{ $isEn ? 'USD only' : 'USD فقط' }}</span>
            <span class="badge">{{ $isEn ? 'Secure checkout' : 'دفع آمن' }}</span>
            <span class="badge">{{ $isEn ? 'Public reports' : 'تقارير عامة' }}</span>
            <span class="badge">{{ $isEn ? 'Tracked receipts' : 'إيصالات قابلة للتتبع' }}</span>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <section class="lg:col-span-2 space-y-8">
            <div class="rounded-[28px] border border-border bg-surface overflow-hidden">
                <div class="relative h-72 bg-muted">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" class="w-full h-full object-cover" alt="">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-muted to-bg"></div>
                    @endif

                    <div class="absolute top-4 {{ $isEn ? 'right-4' : 'left-4' }} flex flex-wrap gap-2">
                        @if ($campaign->is_featured)
                            <span class="px-3 py-1 rounded-full border text-xs font-black"
                                style="border-color: rgba(var(--brand),.25); color: rgb(var(--brand)); background: rgba(var(--brand),.08);">
                                {{ $isEn ? 'Featured' : 'مميزة' }}
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full border text-xs font-black {{ $statusClass }}">{{ $statusText }}</span>
                    </div>

                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                </div>

                <div class="p-7 sm:p-8">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <div class="text-subtext">
                            <span class="font-black text-text">{{ $money($campaign->current_amount) }} {{ $campaign->currency }}</span>
                            <span class="text-subtext">{{ $isEn ? 'raised' : 'تم جمعه' }}</span>
                        </div>
                        <div class="font-black text-text">{{ $progress }}%</div>
                    </div>

                    <div class="h-3 bg-muted rounded-full overflow-hidden border border-border">
                        <div class="h-3 transition-all duration-700 rounded-full"
                            style="width: {{ $progress }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Goal' : 'الهدف' }}</div><div class="mt-1 font-black text-text">{{ $money($campaign->goal_amount) }}</div></div>
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Raised' : 'المجموع' }}</div><div class="mt-1 font-black text-text">{{ $money($campaign->current_amount) }}</div></div>
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Remaining' : 'المتبقي' }}</div><div class="mt-1 font-black text-text">{{ $money($remaining) }}</div></div>
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div><div class="mt-1 font-black text-text">{{ number_format((int) $donorsCount) }}</div></div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="card-muted p-4"><div class="text-xs text-subtext">{{ $isEn ? 'Currency' : 'العملة' }}</div><div class="mt-1 font-black text-text">{{ $campaign->currency }}</div></div>
                        <div class="card-muted p-4"><div class="text-xs text-subtext">{{ $isEn ? 'Payment support' : 'خيارات الدفع' }}</div><div class="mt-1 font-black text-text">{{ $isEn ? 'Card and USDT' : 'بطاقات وUSDT' }}</div></div>
                        <div class="card-muted p-4"><div class="text-xs text-subtext">{{ $isEn ? 'Transparency' : 'الشفافية' }}</div><div class="mt-1 font-black text-text">{{ $isEn ? 'Reports and updates available' : 'تقارير وتحديثات متاحة' }}</div></div>
                    </div>
                </div>
            </div>

            <div class="card p-6 sm:p-7">
                <div class="eyebrow">{{ $isEn ? 'Why this page is trustworthy' : 'لماذا هذه الصفحة موثوقة' }}</div>
                <h2 class="mt-3 text-xl font-black text-text">{{ $isEn ? 'Clear information before the donor takes action' : 'معلومات واضحة قبل أي خطوة تبرع' }}</h2>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="card-muted p-5"><div class="font-black text-text">{{ $isEn ? 'Visible progress' : 'تقدم معلن' }}</div><p class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Goal, raised amount, and remaining target stay visible in one place.' : 'الهدف والمبلغ المجموع والمتبقي يبقون واضحين في مكان واحد.' }}</p></div>
                    <div class="card-muted p-5"><div class="font-black text-text">{{ $isEn ? 'Receipts and tracking' : 'إيصالات وتتبع' }}</div><p class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Completed donations are paired with receipt access and clear follow-up visibility.' : 'التبرعات المكتملة تقترن بوصول واضح إلى الإيصال وإمكانية متابعة الحالة.' }}</p></div>
                    <div class="card-muted p-5"><div class="font-black text-text">{{ $isEn ? 'Unified currency' : 'عملة موحدة' }}</div><p class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'All campaigns and donations use USD to keep records easier to review and compare.' : 'جميع الحملات والتبرعات تعتمد USD لتبقى السجلات أوضح وأسهل في المراجعة.' }}</p></div>
                </div>
            </div>

            <div class="card p-6 sm:p-7">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <h2 class="text-xl font-black text-text">{{ $isEn ? 'Reports & Proofs' : 'التقارير والإثباتات' }}</h2>
                    <a class="text-sm font-black text-brand hover:underline underline-offset-4" href="{{ $reportsUrl }}">{{ $isEn ? 'Browse all' : 'عرض الكل' }}</a>
                </div>

                @if ($reports->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($reports as $report)
                            @php
                                $pdfUrl = $report->pdf_path ? asset('storage/' . $report->pdf_path) : null;
                                $dateText = $report->period_year ? $report->period_year . '-' . $report->period_month : optional($report->created_at)->format('Y-m-d') ?? '';
                            @endphp
                            <a target="_blank" rel="noopener" href="{{ $pdfUrl ?? '#' }}" class="rounded-2xl border border-border bg-surface hover:bg-muted transition p-4">
                                <div class="font-black text-text line-clamp-1">{{ $reportTitle($report) }}</div>
                                <div class="mt-1 text-xs text-subtext">{{ $dateText }}</div>
                                <div class="mt-3 text-sm font-black text-text">PDF ↗</div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-subtext">{{ $isEn ? 'No public reports for this campaign yet.' : 'لا توجد تقارير عامة لهذه الحملة بعد.' }}</div>
                @endif
            </div>

            <div class="card p-6 sm:p-7">
                <h2 class="text-xl font-black text-text mb-5">{{ $isEn ? 'Latest Donations' : 'آخر التبرعات' }}</h2>
                <div class="space-y-3">
                    @forelse($latestDonations as $donation)
                        <div class="flex items-center justify-between rounded-2xl border border-border bg-muted px-4 py-3 hover:bg-muted/70 transition">
                            <div>
                                <div class="font-bold text-text">{{ $donation->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($donation->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}</div>
                                <div class="text-xs text-subtext mt-0.5">{{ optional($donation->created_at)->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="font-black text-text">{{ $money($donation->amount) }} {{ $donation->currency }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-subtext">{{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}</div>
                    @endforelse
                </div>
            </div>

            <div class="card p-6 sm:p-7">
                <h2 class="text-xl font-black text-text mb-5">{{ $isEn ? 'Campaign Updates' : 'تحديثات الحملة' }}</h2>
                <div class="space-y-5">
                    @forelse($updates as $update)
                        <div class="rounded-2xl border border-border bg-surface p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="font-black text-text">{{ $updateTitle($update) }}</div>
                                <div class="text-xs text-subtext shrink-0">{{ optional($update->published_at)->format('Y-m-d') ?? optional($update->created_at)->format('Y-m-d') }}</div>
                            </div>
                            <p class="mt-2 text-sm text-subtext leading-relaxed whitespace-pre-line">{{ $updateBody($update) }}</p>
                        </div>
                    @empty
                        <div class="text-sm text-subtext">{{ $isEn ? 'No updates yet.' : 'لا توجد تحديثات بعد.' }}</div>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="lg:sticky lg:top-24 h-fit space-y-4">
            <div class="rounded-[28px] border border-border bg-surface p-6 sm:p-7 space-y-5">
                <div>
                    <div class="eyebrow">{{ $isEn ? 'Donation panel' : 'لوحة التبرع' }}</div>
                    <h3 class="text-xl font-black text-text">{{ $isEn ? 'Support this campaign' : 'ادعم هذه الحملة' }}</h3>
                    <p class="text-sm text-subtext mt-1">{{ $isEn ? 'Choose an amount and continue through a secure USD-only donation flow.' : 'اختر مبلغًا وتابع عبر مسار تبرع آمن وموحد بالدولار.' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ([10, 25, 50, 100] as $amount)
                        <a href="{{ $donateBaseUrl }}?campaign={{ $campaign->slug }}&amount={{ $amount }}" class="btn btn-secondary py-3">{{ $amount }} {{ $campaign->currency }}</a>
                    @endforeach
                </div>

                <a href="{{ $donateBaseUrl }}?campaign={{ $campaign->slug }}" class="btn btn-primary w-full">
                    {{ $isEn ? 'Continue to donate' : 'إكمال التبرع' }}
                    <span aria-hidden="true">→</span>
                </a>

                <button type="button" onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';" class="btn btn-secondary w-full">
                    {{ $shareLabel }}
                </button>

                <div class="grid grid-cols-1 gap-3">
                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Card details are handled by the payment provider and are not stored on the platform.' : 'بيانات البطاقات تتم معالجتها عبر مزود الدفع ولا يتم تخزينها على المنصة.' }}</div>
                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'For guest USDT donations, email helps deliver the tracking link and receipt access after review.' : 'في تبرعات USDT للضيف، يساعد البريد الإلكتروني على إيصال رابط التتبع والوصول إلى الإيصال بعد المراجعة.' }}</div>
                </div>
            </div>

            <div>
                <a href="{{ $campaignsUrl }}" class="text-sm font-black hover:underline underline-offset-4 text-subtext">← {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}</a>
            </div>
        </aside>
    </div>
@endsection
