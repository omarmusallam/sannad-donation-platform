@extends('layouts.public')
@section('title', $campaign->title)

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';
        $donateBase = url($base . '/donate');

        $statusText =
            $campaign->status === 'active' ? ($isEn ? 'Active' : 'نشطة') : ($isEn ? 'Paused' : 'متوقفة مؤقتًا');

        $statusClass =
            $campaign->status === 'active'
                ? 'bg-success/10 text-success border-success/25'
                : 'bg-warning/10 text-warning border-warning/25';

        $money = fn($v) => number_format((float) $v, 2);

        $remaining = max(0, (float) $campaign->goal_amount - (float) $campaign->current_amount);

        // Updates locale-safe
        $uTitle = function ($u) use ($isEn) {
            return $isEn ? $u->title_en ?? ($u->title_ar ?? '') : $u->title_ar ?? ($u->title_en ?? '');
        };
        $uBody = function ($u) use ($isEn) {
            return $isEn ? $u->body_en ?? ($u->body_ar ?? '') : $u->body_ar ?? ($u->body_en ?? '');
        };

        $rTitle = function ($r) use ($isEn) {
            return $isEn ? ($r->title_en ?: $r->title_ar) : ($r->title_ar ?: $r->title_en);
        };

        $shareLabel = $isEn ? 'Copy campaign link' : 'نسخ رابط الحملة';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';

        $progress = (int) ($campaign->progress_percent ?? 0);
        $progress = max(0, min(100, $progress));
    @endphp

    {{-- Breadcrumb --}}
    <div class="mb-6 text-sm text-subtext">
        <a class="hover:underline underline-offset-4" href="{{ url($base . '/campaigns') }}">
            {{ $isEn ? 'Campaigns' : 'الحملات' }}
        </a>
        <span class="mx-2">/</span>
        <span class="text-text font-semibold">{{ $campaign->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- MAIN --}}
        <section class="lg:col-span-2 space-y-8">

            {{-- HERO --}}
            <div class="rounded-[28px] border border-border bg-surface overflow-hidden">
                <div class="relative h-72 bg-muted">
                    @php
                        $heroImg =
                            $campaign->cover_url ??
                            ($campaign->cover_image_path ? asset('storage/' . $campaign->cover_image_path) : null);
                    @endphp

                    @if ($heroImg)
                        <img src="{{ $heroImg }}" class="w-full h-full object-cover" alt="">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-muted to-bg"></div>
                    @endif

                    {{-- top badges --}}
                    <div class="absolute top-4 {{ $isEn ? 'right-4' : 'left-4' }} flex flex-wrap gap-2">
                        @if ($campaign->is_featured)
                            <span class="px-3 py-1 rounded-full border text-xs font-black"
                                style="border-color: rgba(var(--brand),.25); color: rgb(var(--brand)); background: rgba(var(--brand),.08);">
                                {{ $isEn ? 'Featured' : 'مميزة' }}
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full border text-xs font-black {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                </div>

                <div class="p-7 sm:p-8">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-text">
                        {{ $campaign->title }}
                    </h1>

                    <p class="mt-4 text-subtext leading-relaxed">
                        {{ $campaign->description ?: ($isEn ? 'No description available yet.' : 'لا يوجد وصف بعد.') }}
                    </p>

                    {{-- PROGRESS --}}
                    <div class="mt-7">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <div class="text-subtext">
                                <span class="font-black text-text">
                                    {{ $money($campaign->current_amount) }} {{ $campaign->currency }}
                                </span>
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
                            <div class="card-muted p-4">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Goal' : 'الهدف' }}</div>
                                <div class="mt-1 font-black text-text">{{ $money($campaign->goal_amount) }}</div>
                            </div>

                            <div class="card-muted p-4">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Raised' : 'المجموع' }}</div>
                                <div class="mt-1 font-black text-text">{{ $money($campaign->current_amount) }}</div>
                            </div>

                            <div class="card-muted p-4">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Remaining' : 'المتبقي' }}</div>
                                <div class="mt-1 font-black text-text">{{ $money($remaining) }}</div>
                            </div>

                            <div class="card-muted p-4">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div>
                                <div class="mt-1 font-black text-text">{{ number_format((int) $donorsCount) }}</div>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-subtext">
                            {{ $isEn ? 'Currency:' : 'العملة:' }}
                            <span class="font-black text-text">{{ $campaign->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- REPORTS (Documents) --}}
            <div class="card p-6 sm:p-7">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <h2 class="text-xl font-black text-text">
                        {{ $isEn ? 'Reports & Proofs' : 'التقارير والإثباتات' }}
                    </h2>
                    <a class="text-sm font-black text-brand hover:underline underline-offset-4"
                        href="{{ url($base . '/transparency/reports') }}">
                        {{ $isEn ? 'Browse all' : 'عرض الكل' }}
                    </a>
                </div>

                @if ($reports->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($reports as $r)
                            @php
                                $pdf = $r->pdf_path ? asset('storage/' . $r->pdf_path) : null;
                                $dateText = $r->period_year
                                    ? $r->period_year . '-' . $r->period_month
                                    : optional($r->created_at)->format('Y-m-d') ?? '';
                            @endphp
                            <a target="_blank" rel="noopener" href="{{ $pdf ?? '#' }}"
                                class="rounded-2xl border border-border bg-surface hover:bg-muted transition p-4">
                                <div class="font-black text-text line-clamp-1">
                                    {{ $rTitle($r) }}
                                </div>
                                <div class="mt-1 text-xs text-subtext">{{ $dateText }}</div>
                                <div class="mt-3 text-sm font-black text-text">PDF ↗</div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-subtext">
                        {{ $isEn ? 'No public reports for this campaign yet.' : 'لا توجد تقارير عامة لهذه الحملة بعد.' }}
                    </div>
                @endif
            </div>

            {{-- LATEST DONATIONS --}}
            <div class="card p-6 sm:p-7">
                <h2 class="text-xl font-black text-text mb-5">
                    {{ $isEn ? 'Latest Donations' : 'آخر التبرعات' }}
                </h2>

                <div class="space-y-3">
                    @forelse($latestDonations as $d)
                        <div
                            class="flex items-center justify-between rounded-2xl border border-border bg-muted px-4 py-3 hover:bg-muted/70 transition">
                            <div>
                                <div class="font-bold text-text">
                                    {{ $d->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($d->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}
                                </div>
                                <div class="text-xs text-subtext mt-0.5">
                                    {{ optional($d->created_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <div class="font-black text-text">
                                {{ $money($d->amount) }} {{ $d->currency }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-subtext">
                            {{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- UPDATES --}}
            <div class="card p-6 sm:p-7">
                <h2 class="text-xl font-black text-text mb-5">
                    {{ $isEn ? 'Campaign Updates' : 'تحديثات الحملة' }}
                </h2>

                <div class="space-y-5">
                    @forelse($updates as $u)
                        <div class="rounded-2xl border border-border bg-surface p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="font-black text-text">
                                    {{ $uTitle($u) }}
                                </div>
                                <div class="text-xs text-subtext shrink-0">
                                    {{ optional($u->published_at)->format('Y-m-d') ?? optional($u->created_at)->format('Y-m-d') }}
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-subtext leading-relaxed whitespace-pre-line">
                                {{ $uBody($u) }}
                            </p>
                        </div>
                    @empty
                        <div class="text-sm text-subtext">
                            {{ $isEn ? 'No updates yet.' : 'لا توجد تحديثات بعد.' }}
                        </div>
                    @endforelse
                </div>
            </div>

        </section>

        {{-- SIDEBAR --}}
        <aside class="lg:sticky lg:top-24 h-fit space-y-4">

            {{-- Donate Card --}}
            <div class="rounded-[28px] border border-border bg-surface p-6 sm:p-7 space-y-5">
                <div>
                    <h3 class="text-xl font-black text-text">
                        {{ $isEn ? 'Support this campaign' : 'ادعم هذه الحملة' }}
                    </h3>
                    <p class="text-sm text-subtext mt-1">
                        {{ $isEn ? 'Choose an amount to continue.' : 'اختر مبلغًا للمتابعة.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ([10, 25, 50, 100] as $amt)
                        <a href="{{ $donateBase }}?campaign={{ $campaign->slug }}&amount={{ $amt }}"
                            class="btn btn-secondary py-3">
                            {{ $amt }} {{ $campaign->currency }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ $donateBase }}?campaign={{ $campaign->slug }}" class="btn btn-primary w-full">
                    {{ $isEn ? 'Continue to donate' : 'إكمال التبرع' }}
                    <span aria-hidden="true">→</span>
                </a>

                <button type="button"
                    onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';"
                    class="btn btn-secondary w-full">
                    {{ $shareLabel }}
                </button>

                <div class="text-xs text-subtext text-center leading-relaxed">
                    {{ $isEn ? 'Secure donation. We do not store card data.' : 'تبرع آمن. لا نقوم بتخزين بيانات البطاقات.' }}
                </div>
            </div>

            {{-- Back link --}}
            <div>
                <a href="{{ url($base . '/campaigns') }}"
                    class="text-sm font-black hover:underline underline-offset-4 text-subtext">
                    ← {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}
                </a>
            </div>

        </aside>

    </div>
@endsection
