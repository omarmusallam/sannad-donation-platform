@extends('layouts.public')

@section('title', __('ui.donate_now'))

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        // safe settings
        $getSetting = function (string $key, $default = null) {
            try {
                return function_exists('setting') ? setting($key, $default) : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        };

        $siteName = (string) $getSetting('site.name', 'GazaSannad');

        $defaultAmount = old('amount', $amount ?? 25);
        $selectedCampaignId = old('campaign_id', optional($campaign)->id);
        $selectedCurrency = old('currency', $campaign?->currency ?? 'USD');

        // prefer route if exists, fallback to url
        $formAction =
            function_exists('route') && \Illuminate\Support\Facades\Route::has('donate.submit')
                ? route('donate.submit')
                : url($base . '/donate');

        $urlCampaigns =
            function_exists('route') && \Illuminate\Support\Facades\Route::has('campaigns.index')
                ? route('campaigns.index')
                : url($base . '/campaigns');

        $urlHome = url($base ?: '/');

        // quick amounts
        $quickAmounts = [10, 25, 50, 100, 250];

        // map currencies by campaign id for auto currency switch
        $curMap = [];
        $nameMap = [];
        foreach ($campaigns as $c) {
            $curMap[$c->id] = $c->currency;
            $nameMap[$c->id] = $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);
        }

        $pageTitle = $isEn ? 'Donate now' : 'تبرّع الآن';
        $subtitle = $isEn
            ? 'Fast, clear, and secure. This is currently a mock flow until payment gateways are connected.'
            : 'سريع، واضح، وآمن. حاليًا هذه عملية تجريبية حتى يتم ربط بوابات الدفع.';
    @endphp

    <div class="max-w-6xl mx-auto">
        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 sm:p-10 mb-8">
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-transparent"></div>
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-40"
                style="background: radial-gradient(circle, rgba(79,70,229,.22), transparent 60%);"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-35"
                style="background: radial-gradient(circle, rgba(16,185,129,.18), transparent 60%);"></div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <div class="text-sm text-slate-500">
                        <a class="hover:underline underline-offset-4" href="{{ $urlHome }}">
                            {{ $isEn ? 'Home' : 'الرئيسية' }}
                        </a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-700 font-semibold">{{ $pageTitle }}</span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-950">
                        {{ $pageTitle }}
                    </h1>

                    <p class="mt-2 text-slate-600 leading-relaxed max-w-2xl">
                        {{ $subtitle }}
                    </p>

                    {{-- Trust badges --}}
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span
                            class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700 font-bold">
                            {{ $isEn ? 'Secure handling' : 'معالجة آمنة' }}
                        </span>
                        <span
                            class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700 font-bold">
                            {{ $isEn ? 'Transparency' : 'شفافية' }}
                        </span>
                        <span
                            class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700 font-bold">
                            {{ $isEn ? 'Instant confirmation' : 'تأكيد فوري' }}
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 text-sm text-slate-700 max-w-md">
                    <div class="font-extrabold text-slate-950 mb-1">
                        {{ $isEn ? 'Tip' : 'معلومة' }}
                    </div>
                    <div class="text-slate-600 leading-relaxed">
                        {{ $isEn
                            ? 'Donate with confidence — campaigns are backed by reports and updates'
                            : 'تبرع بثقة — كل حملة مرتبطة بتقارير وتحديثات لتوضيح الأثر.' }}
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form --}}
            <form method="POST" action="{{ $formAction }}"
                class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 space-y-6">
                @csrf

                {{-- Campaign --}}
                <div>
                    <label class="block text-sm font-extrabold mb-2 text-slate-950">
                        {{ $isEn ? 'Campaign' : 'الحملة' }}
                    </label>

                    <select id="campaign_id" name="campaign_id"
                        class="w-full border border-slate-200 rounded-2xl p-3 bg-white outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition">
                        @foreach ($campaigns as $c)
                            <option value="{{ $c->id }}" @selected($selectedCampaignId == $c->id)>
                                {{ $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en) }}
                            </option>
                        @endforeach
                    </select>

                    @error('campaign_id')
                        <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                    @enderror

                    <p class="mt-2 text-xs text-slate-500">
                        {{ $isEn ? 'Select the campaign you want to support.' : 'اختر الحملة التي تريد دعمها.' }}
                    </p>
                </div>

                {{-- Amount + Currency --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-extrabold mb-2 text-slate-950">
                            {{ $isEn ? 'Amount' : 'المبلغ' }}
                        </label>

                        <div class="relative">
                            <input id="amount" name="amount" type="number" min="1" step="0.01"
                                value="{{ $defaultAmount }}"
                                class="w-full border border-slate-200 rounded-2xl p-3 pe-20 bg-white outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition">
                            <div class="absolute top-1/2 -translate-y-1/2 {{ $isEn ? 'right-3' : 'left-3' }} text-xs text-slate-500 font-bold"
                                id="amount_currency_hint">
                                {{ $selectedCurrency }}
                            </div>
                        </div>

                        @error('amount')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror

                        {{-- Quick amounts --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($quickAmounts as $quick)
                                <button type="button"
                                    class="px-3 py-2 text-sm rounded-2xl border border-slate-200 hover:bg-slate-50 transition font-bold text-slate-800"
                                    onclick="
                                        const a=document.getElementById('amount');
                                        a.value='{{ $quick }}';
                                        a.dispatchEvent(new Event('input'));
                                    ">
                                    {{ $quick }}
                                </button>
                            @endforeach
                        </div>

                        <p class="text-xs text-slate-500 mt-2">
                            {{ $isEn ? 'Quick buttons just fill the amount field.' : 'الأزرار السريعة تعبئة فقط.' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold mb-2 text-slate-950">
                            {{ $isEn ? 'Currency' : 'العملة' }}
                        </label>

                        <select id="currency" name="currency"
                            class="w-full border border-slate-200 rounded-2xl p-3 bg-white outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition">
                            @foreach (['USD', 'EUR', 'ILS'] as $cur)
                                <option value="{{ $cur }}" @selected($selectedCurrency === $cur)>{{ $cur }}
                                </option>
                            @endforeach
                        </select>

                        @error('currency')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                            <div class="font-extrabold text-slate-950 mb-1">
                                {{ $isEn ? 'Security note' : 'ملاحظة أمنية' }}
                            </div>
                            <div class="text-slate-600 leading-relaxed">
                                {{ $isEn
                                    ? 'We do not store card data. Payments will be handled by secure providers.'
                                    : 'لا نخزن بيانات البطاقات. الدفع سيتم عبر مزودين آمنين.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Donor info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-extrabold mb-2 text-slate-950">
                            {{ $isEn ? 'Name (optional)' : 'الاسم (اختياري)' }}
                        </label>
                        <input id="donor_name" name="donor_name" value="{{ old('donor_name') }}"
                            class="w-full border border-slate-200 rounded-2xl p-3 bg-white outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition"
                            autocomplete="name">
                        @error('donor_name')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-extrabold mb-2 text-slate-950">
                            {{ $isEn ? 'Email (optional)' : 'البريد (اختياري)' }}
                        </label>
                        <input id="donor_email" name="donor_email" value="{{ old('donor_email') }}"
                            class="w-full border border-slate-200 rounded-2xl p-3 bg-white outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition"
                            autocomplete="email" inputmode="email">
                        @error('donor_email')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Anonymous --}}
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="flex items-center gap-3 text-sm">
                        <input id="is_anonymous" type="checkbox" name="is_anonymous" value="1"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                            @checked(old('is_anonymous'))>
                        <span class="font-extrabold text-slate-950">
                            {{ $isEn ? 'Donate anonymously' : 'التبرع كمجهول' }}
                        </span>
                    </label>

                    <div class="text-xs text-slate-500">
                        {{ $isEn ? 'Your name won’t be shown publicly.' : 'لن يظهر اسمك علنًا.' }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                    <button
                        class="w-full sm:w-auto px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Confirm donation' : 'تأكيد التبرع' }}
                        <span aria-hidden="true">→</span>
                    </button>

                    <a class="w-full sm:w-auto text-center px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-800"
                        href="{{ $urlCampaigns }}">
                        {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}
                    </a>
                </div>

                {{-- JSON maps for JS --}}
                <script type="application/json" id="campaign_currency_map">@json($curMap)</script>
                <script type="application/json" id="campaign_name_map">@json($nameMap)</script>
            </form>

            {{-- Summary sidebar --}}
            <aside class="lg:sticky lg:top-24 h-fit">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 space-y-4">
                    <div class="text-sm text-slate-500">
                        {{ $isEn ? 'Donation summary' : 'ملخص التبرع' }}
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Platform' : 'المنصة' }}</div>
                        <div class="font-extrabold text-slate-950 mt-1">{{ $siteName }}</div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                        <div class="font-extrabold text-slate-950 mt-1 line-clamp-2" id="summary_campaign">
                            {{ $selectedCampaignId ? $nameMap[$selectedCampaignId] ?? '' : '' }}
                        </div>
                        <div class="mt-2 text-xs text-slate-500">
                            {{ $isEn ? 'Currency auto-syncs with campaign.' : 'العملة تتزامن تلقائيًا مع الحملة.' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ $isEn ? 'Amount' : 'المبلغ' }}</div>
                            <div class="font-extrabold text-slate-950 mt-1">
                                <span id="summary_amount">{{ number_format((float) $defaultAmount, 2) }}</span>
                                <span id="summary_currency">{{ $selectedCurrency }}</span>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ $isEn ? 'Method' : 'الطريقة' }}</div>
                            <div class="font-extrabold text-slate-950 mt-1">
                                {{ $isEn ? 'Mock (temporary)' : 'تجريبي (مؤقت)' }}
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 leading-relaxed">
                        {{ $isEn
                            ? 'Payments will be processed via trusted providers once integrated.'
                            : 'سيتم تنفيذ الدفع عبر مزودين موثوقين عند الربط.' }}
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const amount = document.getElementById('amount');
                const currency = document.getElementById('currency');
                const campaign = document.getElementById('campaign_id');
                const isAnon = document.getElementById('is_anonymous');
                const donorName = document.getElementById('donor_name');
                const donorEmail = document.getElementById('donor_email');

                const sumAmount = document.getElementById('summary_amount');
                const sumCurrency = document.getElementById('summary_currency');
                const sumCampaign = document.getElementById('summary_campaign');
                const hint = document.getElementById('amount_currency_hint');

                const curMapEl = document.getElementById('campaign_currency_map');
                const nameMapEl = document.getElementById('campaign_name_map');
                const curMap = curMapEl ? JSON.parse(curMapEl.textContent || '{}') : {};
                const nameMap = nameMapEl ? JSON.parse(nameMapEl.textContent || '{}') : {};

                function syncAmount() {
                    const v = parseFloat(amount.value || 0);
                    const val = isFinite(v) ? v : 0;
                    sumAmount.textContent = val.toFixed(2);
                }

                function syncCurrency() {
                    sumCurrency.textContent = currency.value;
                    hint.textContent = currency.value;
                }

                function syncCampaignLabel() {
                    const cid = campaign.value;
                    sumCampaign.textContent = nameMap[cid] || '';
                }

                // auto currency from campaign
                function syncCurrencyFromCampaign() {
                    const cid = campaign.value;
                    if (curMap[cid]) {
                        currency.value = curMap[cid];
                        syncCurrency();
                    }
                    syncCampaignLabel();
                }

                // anonymous: disable donor inputs (UX only)
                function syncAnonymous() {
                    const on = !!isAnon?.checked;
                    if (donorName) donorName.disabled = on;
                    if (donorEmail) donorEmail.disabled = on;
                    if (on) {
                        if (donorName) donorName.value = '';
                        if (donorEmail) donorEmail.value = '';
                    }
                }

                amount?.addEventListener('input', syncAmount);
                currency?.addEventListener('change', syncCurrency);
                campaign?.addEventListener('change', syncCurrencyFromCampaign);
                isAnon?.addEventListener('change', syncAnonymous);

                // init
                syncAmount();
                syncCurrency();
                syncCampaignLabel();
                syncAnonymous();
            })();
        </script>
    @endpush
@endsection
