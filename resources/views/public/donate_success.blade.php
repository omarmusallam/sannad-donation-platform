@extends('layouts.public')

@section('title', app()->getLocale() === 'en' ? 'Donation successful' : 'نجاح التبرع')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';
        $money = fn($v) => number_format((float) $v, 2);

        // safer urls
        $urlCampaign = url($base . '/campaigns/' . $donation->campaign->slug);
        $urlCampaigns = url($base . '/campaigns');

        $copyLabel = $isEn ? 'Copy receipt link' : 'نسخ رابط الإيصال';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-3xl p-7 sm:p-10 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-40"
                style="background: radial-gradient(circle, rgba(79,70,229,.18), transparent 60%);"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-35"
                style="background: radial-gradient(circle, rgba(16,185,129,.14), transparent 60%);"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">
                        {{ $isEn ? 'Thank you!' : 'شكرًا لك!' }}
                    </h1>
                    <p class="mt-2 text-slate-600 leading-relaxed">
                        {{ $isEn ? 'Your donation has been recorded successfully.' : 'تم تسجيل تبرعك بنجاح.' }}
                    </p>
                </div>

                <div
                    class="shrink-0 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold">
                    {{ $isEn ? 'Success' : 'تم' }}
                </div>
            </div>

            <div class="relative mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                    <div class="mt-1 font-extrabold text-slate-950">
                        {{ $donation->campaign->title }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        Slug: <span class="font-mono">{{ $donation->campaign->slug }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">{{ $isEn ? 'Amount' : 'المبلغ' }}</div>
                    <div class="mt-1 font-extrabold text-slate-950 text-lg">
                        {{ $money($donation->amount) }} {{ $donation->currency }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ $isEn ? 'Method' : 'الطريقة' }}: {{ $donation->payment_method }}
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">{{ $isEn ? 'Donor' : 'المتبرع' }}</div>
                    <div class="mt-1 font-extrabold text-slate-950">
                        {{ $donation->display_donor_name ?? ($donation->is_anonymous ? ($isEn ? 'Anonymous' : 'مجهول') : ($donation->donor_name ?: ($isEn ? 'Donor' : 'متبرع'))) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ $isEn ? 'Status' : 'الحالة' }}: {{ $donation->status }}
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs text-slate-500">{{ $isEn ? 'Time' : 'الوقت' }}</div>
                    <div class="mt-1 font-extrabold text-slate-950">
                        {{ optional($donation->paid_at)->format('Y-m-d H:i') ?? $donation->created_at->format('Y-m-d H:i') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ $isEn ? 'Reference' : 'مرجع' }}: #{{ $donation->id }}
                    </div>
                </div>
            </div>

            <div class="relative mt-8 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <a class="px-6 py-3 rounded-2xl font-extrabold text-white text-center shadow-sm hover:shadow transition"
                    style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));"
                    href="{{ $urlCampaign }}">
                    {{ $isEn ? 'Back to campaign' : 'العودة للحملة' }}
                    <span aria-hidden="true">→</span>
                </a>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button"
                        onclick="navigator.clipboard.writeText(window.location.href); this.innerText='{{ $copiedLabel }}';"
                        class="px-6 py-3 rounded-2xl border border-slate-200 text-center hover:bg-slate-50 transition font-bold">
                        {{ $copyLabel }}
                    </button>

                    <a class="px-6 py-3 rounded-2xl border border-slate-200 text-center hover:bg-slate-50 transition font-bold"
                        href="{{ $urlCampaigns }}">
                        {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                    </a>
                </div>
            </div>

            <div class="relative mt-6 text-xs text-slate-500 leading-relaxed">
                {{ $isEn
                    ? 'This is a temporary mock flow. Once payment is integrated, you will receive an official receipt and provider reference.'
                    : 'هذه عملية تجريبية مؤقتًا. بعد ربط الدفع ستصلك إيصالات رسمية ومرجع مزود الدفع.' }}
            </div>
        </div>
    </div>
@endsection
