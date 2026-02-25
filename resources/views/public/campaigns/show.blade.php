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
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                : 'bg-amber-50 text-amber-800 border-amber-200';

        $money = fn($v) => number_format((float) $v, 2);

        $remaining = max(0, (float) $campaign->goal_amount - (float) $campaign->current_amount);

        // Updates locale-safe (حتى لو ما عندك accessors في CampaignUpdate)
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
    @endphp

    {{-- Breadcrumb --}}
    <div class="mb-6 text-sm text-slate-500">
        <a class="hover:underline underline-offset-4" href="{{ url($base . '/campaigns') }}">
            {{ $isEn ? 'Campaigns' : 'الحملات' }}
        </a>
        <span class="mx-2">/</span>
        <span class="text-slate-700">{{ $campaign->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- MAIN --}}
        <section class="lg:col-span-2 space-y-8">

            {{-- HERO --}}
            <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
                <div class="relative h-72 bg-slate-100">
                    @if ($campaign->cover_url)
                        <img src="{{ $campaign->cover_url }}" class="w-full h-full object-cover" alt="">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-100 to-white"></div>
                    @endif

                    {{-- top badges --}}
                    <div class="absolute top-4 {{ $isEn ? 'right-4' : 'left-4' }} flex flex-wrap gap-2">
                        @if ($campaign->is_featured)
                            <span
                                class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-extrabold">
                                {{ $isEn ? 'Featured' : 'مميزة' }}
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full border text-xs font-extrabold {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    {{-- subtle overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                </div>

                <div class="p-7 sm:p-8">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-950">
                        {{ $campaign->title }}
                    </h1>

                    <p class="mt-4 text-slate-600 leading-relaxed">
                        {{ $campaign->description ?: ($isEn ? 'No description available yet.' : 'لا يوجد وصف بعد.') }}
                    </p>

                    {{-- PROGRESS --}}
                    <div class="mt-7">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <div class="text-slate-700">
                                <span class="font-extrabold text-slate-950">
                                    {{ $money($campaign->current_amount) }} {{ $campaign->currency }}
                                </span>
                                <span class="text-slate-500">{{ $isEn ? 'raised' : 'تم جمعه' }}</span>
                            </div>
                            <div class="font-extrabold text-slate-950">{{ $campaign->progress_percent }}%</div>
                        </div>

                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-3 transition-all duration-700 rounded-full"
                                style="width: {{ $campaign->progress_percent }}%; background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Goal' : 'الهدف' }}</div>
                                <div class="mt-1 font-bold text-slate-950">{{ $money($campaign->goal_amount) }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Raised' : 'المجموع' }}</div>
                                <div class="mt-1 font-bold text-slate-950">{{ $money($campaign->current_amount) }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Remaining' : 'المتبقي' }}</div>
                                <div class="mt-1 font-bold text-slate-950">{{ $money($remaining) }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Donors' : 'المتبرعون' }}</div>
                                <div class="mt-1 font-bold text-slate-950">{{ number_format((int) $donorsCount) }}</div>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-slate-500">
                            {{ $isEn ? 'Currency:' : 'العملة:' }}
                            <span class="font-bold text-slate-700">{{ $campaign->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- REPORTS (Documents) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <h2 class="text-xl font-extrabold text-slate-950">
                        {{ $isEn ? 'Reports & Proofs' : 'التقارير والإثباتات' }}
                    </h2>
                    <a class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4"
                        href="{{ url($base . '/transparency/reports') }}">
                        {{ $isEn ? 'Browse all' : 'عرض الكل' }}
                    </a>
                </div>

                @if ($reports->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($reports as $r)
                            <a target="_blank" rel="noopener" href="{{ asset('storage/' . $r->pdf_path) }}"
                                class="rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition p-4">
                                <div class="font-extrabold text-slate-950 line-clamp-1">
                                    {{ $rTitle($r) }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $r->period_year ? $r->period_year . '-' . $r->period_month : $r->created_at?->format('Y-m-d') ?? '' }}
                                </div>
                                <div class="mt-3 text-sm font-extrabold text-slate-900">
                                    PDF ↗
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-slate-600">
                        {{ $isEn ? 'No public reports for this campaign yet.' : 'لا توجد تقارير عامة لهذه الحملة بعد.' }}
                    </div>
                @endif
            </div>

            {{-- LATEST DONATIONS --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7">
                <h2 class="text-xl font-extrabold text-slate-950 mb-5">
                    {{ $isEn ? 'Latest Donations' : 'آخر التبرعات' }}
                </h2>

                <div class="space-y-3">
                    @forelse($latestDonations as $d)
                        <div
                            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 hover:bg-slate-50/70 transition">
                            <div>
                                <div class="font-bold text-slate-900">
                                    {{ $d->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($d->donor_name ?: ($isEn ? 'Donor' : 'متبرع')) }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $d->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <div class="font-extrabold text-slate-950">
                                {{ $money($d->amount) }} {{ $d->currency }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-600">
                            {{ $isEn ? 'No donations yet.' : 'لا توجد تبرعات بعد.' }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- UPDATES --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7">
                <h2 class="text-xl font-extrabold text-slate-950 mb-5">
                    {{ $isEn ? 'Campaign Updates' : 'تحديثات الحملة' }}
                </h2>

                <div class="space-y-5">
                    @forelse($updates as $u)
                        <div class="relative rounded-2xl border border-slate-200 p-5 bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div class="font-extrabold text-slate-950">
                                    {{ $uTitle($u) }}
                                </div>
                                <div class="text-xs text-slate-500 shrink-0">
                                    {{ optional($u->published_at)->format('Y-m-d') ?? $u->created_at->format('Y-m-d') }}
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $uBody($u) }}
                            </p>
                        </div>
                    @empty
                        <div class="text-sm text-slate-600">
                            {{ $isEn ? 'No updates yet.' : 'لا توجد تحديثات بعد.' }}
                        </div>
                    @endforelse
                </div>
            </div>

        </section>

        {{-- SIDEBAR --}}
        <aside class="lg:sticky lg:top-24 h-fit space-y-4">

            {{-- Donate Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 space-y-5">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-950">
                        {{ $isEn ? 'Support this campaign' : 'ادعم هذه الحملة' }}
                    </h3>
                    <p class="text-sm text-slate-600 mt-1">
                        {{ $isEn ? 'Choose an amount to continue.' : 'اختر مبلغًا للمتابعة.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ([10, 25, 50, 100] as $amt)
                        <a href="{{ $donateBase }}?campaign={{ $campaign->slug }}&amount={{ $amt }}"
                            class="py-3 text-center rounded-2xl border border-slate-200 hover:bg-slate-50 transition font-extrabold text-slate-900">
                            {{ $amt }} {{ $campaign->currency }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ $donateBase }}?campaign={{ $campaign->slug }}"
                    class="block text-center py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                    style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                    {{ $isEn ? 'Continue to donate' : 'إكمال التبرع' }}
                    <span aria-hidden="true">→</span>
                </a>

                <button type="button"
                    onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';"
                    class="w-full text-center px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-800">
                    {{ $shareLabel }}
                </button>

                <div class="text-xs text-slate-500 text-center leading-relaxed">
                    {{ $isEn ? 'Secure donation. We do not store card data.' : 'تبرع آمن. لا نقوم بتخزين بيانات البطاقات.' }}
                </div>
            </div>

            {{-- Back link --}}
            <div>
                <a href="{{ url($base . '/campaigns') }}"
                    class="text-sm font-bold hover:underline underline-offset-4 text-slate-700">
                    ← {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}
                </a>
            </div>

        </aside>

    </div>
@endsection
