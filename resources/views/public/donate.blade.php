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

        $formAction =
            function_exists('route') && \Illuminate\Support\Facades\Route::has('donate.submit')
                ? route('donate.submit')
                : url($base . '/donate');

        $urlCampaigns =
            function_exists('route') && \Illuminate\Support\Facades\Route::has('campaigns.index')
                ? route('campaigns.index')
                : url($base . '/campaigns');

        $urlHome = $base ? url($base) : url('/');

        $quickAmounts = [10, 25, 50, 100, 250];

        // currency/name maps for JS
        $curMap = [];
        $nameMap = [];
        foreach ($campaigns ?? collect() as $c) {
            $curMap[$c->id] = $c->currency;
            $nameMap[$c->id] = $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);
        }

        $pageTitle = $isEn ? 'Donate now' : 'تبرّع الآن';
        $subtitle = $isEn
            ? 'Fast, clear, and secure. This is currently a mock flow until payment gateways are connected.'
            : 'سريع، واضح، وآمن. حاليًا هذه عملية تجريبية حتى يتم ربط بوابات الدفع.';

        $steps = [
            [
                't' => $isEn ? 'Choose campaign' : 'اختر الحملة',
                'd' => $isEn ? 'Select where your donation goes.' : 'حدّد أين يذهب تبرعك.',
            ],
            [
                't' => $isEn ? 'Set amount' : 'حدّد المبلغ',
                'd' => $isEn ? 'Pick quick amounts or enter any value.' : 'اختر مبلغًا سريعًا أو أدخل قيمة.',
            ],
            [
                't' => $isEn ? 'Confirm' : 'تأكيد',
                'd' => $isEn ? 'Submit and get an instant confirmation.' : 'أرسل الطلب واحصل على تأكيد فوري.',
            ],
        ];
    @endphp

    <div class="max-w-6xl mx-auto">

        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface p-7 sm:p-10 mb-8">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full blur-3xl opacity-25"
                style="background: radial-gradient(circle, rgba(var(--brand),.20), transparent 60%);"></div>
            <div class="pointer-events-none absolute -left-16 -bottom-16 h-64 w-64 rounded-full blur-3xl opacity-20"
                style="background: radial-gradient(circle, rgba(var(--brand2),.18), transparent 60%);"></div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <div class="text-sm text-subtext">
                        <a class="hover:underline underline-offset-4" href="{{ $urlHome }}">
                            {{ $isEn ? 'Home' : 'الرئيسية' }}
                        </a>
                        <span class="mx-2">/</span>
                        <span class="text-text font-semibold">{{ $pageTitle }}</span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">
                        {{ $pageTitle }}
                    </h1>

                    <p class="mt-2 text-subtext leading-relaxed max-w-2xl">
                        {{ $subtitle }}
                    </p>

                    {{-- Trust badges --}}
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="badge">{{ $isEn ? 'Secure handling' : 'معالجة آمنة' }}</span>
                        <span class="badge">{{ $isEn ? 'Transparency' : 'شفافية' }}</span>
                        <span class="badge">{{ $isEn ? 'Instant confirmation' : 'تأكيد فوري' }}</span>
                    </div>
                </div>

                {{-- steps mini --}}
                <div class="rounded-2xl border border-border bg-surface/70 p-5 text-sm max-w-md">
                    <div class="font-black text-text mb-3">{{ $isEn ? 'How it works' : 'كيف تعمل' }}</div>
                    <div class="space-y-3">
                        @foreach ($steps as $i => $s)
                            <div class="flex gap-3">
                                <div
                                    class="shrink-0 w-7 h-7 rounded-xl grid place-items-center border border-border bg-muted font-black text-text">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-text">{{ $s['t'] }}</div>
                                    <div class="text-xs text-subtext mt-0.5">{{ $s['d'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form --}}
            <form method="POST" action="{{ $formAction }}" class="lg:col-span-2 card p-6 sm:p-8 space-y-6">
                @csrf

                {{-- Campaign --}}
                <div>
                    <label class="block text-sm font-black mb-2 text-text">
                        {{ $isEn ? 'Campaign' : 'الحملة' }}
                    </label>

                    <select id="campaign_id" name="campaign_id" class="input">
                        @foreach ($campaigns ?? collect() as $c)
                            <option value="{{ $c->id }}" @selected($selectedCampaignId == $c->id)>
                                {{ $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en) }}
                            </option>
                        @endforeach
                    </select>

                    @error('campaign_id')
                        <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                    @enderror

                    <p class="mt-2 text-xs text-subtext">
                        {{ $isEn ? 'Select the campaign you want to support.' : 'اختر الحملة التي تريد دعمها.' }}
                    </p>
                </div>

                {{-- Amount + Currency --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Amount' : 'المبلغ' }}
                        </label>

                        <div class="relative">
                            <input id="amount" name="amount" type="number" min="1" step="0.01"
                                value="{{ $defaultAmount }}" class="input pe-20">
                            <div class="absolute top-1/2 -translate-y-1/2 {{ $isEn ? 'right-3' : 'left-3' }} text-xs text-subtext font-black"
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
                                <button type="button" class="btn btn-secondary px-3 py-2 text-sm"
                                    onclick="
                                        const a=document.getElementById('amount');
                                        a.value='{{ $quick }}';
                                        a.dispatchEvent(new Event('input'));
                                    ">
                                    {{ $quick }}
                                </button>
                            @endforeach
                        </div>

                        <p class="text-xs text-subtext mt-2">
                            {{ $isEn ? 'Quick buttons only fill the amount field.' : 'الأزرار السريعة تعبئة فقط.' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Currency' : 'العملة' }}
                        </label>

                        <select id="currency" name="currency" class="input">
                            @foreach (['USD', 'EUR', 'ILS'] as $cur)
                                <option value="{{ $cur }}" @selected($selectedCurrency === $cur)>{{ $cur }}
                                </option>
                            @endforeach
                        </select>

                        @error('currency')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror

                        <div class="mt-4 card-muted p-4 text-sm">
                            <div class="font-black text-text mb-1">
                                {{ $isEn ? 'Security note' : 'ملاحظة أمنية' }}
                            </div>
                            <div class="text-subtext leading-relaxed">
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
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Name (optional)' : 'الاسم (اختياري)' }}
                        </label>
                        <input id="donor_name" name="donor_name" value="{{ old('donor_name') }}" class="input"
                            autocomplete="name">
                        @error('donor_name')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Email (optional)' : 'البريد (اختياري)' }}
                        </label>
                        <input id="donor_email" name="donor_email" value="{{ old('donor_email') }}" class="input"
                            autocomplete="email" inputmode="email">
                        @error('donor_email')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Anonymous --}}
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-border bg-muted p-4">
                    <label class="flex items-center gap-3 text-sm">
                        <input id="is_anonymous" type="checkbox" name="is_anonymous" value="1"
                            class="h-5 w-5 rounded border-border text-brand focus:ring-2 focus:ring-[rgba(var(--brand),.25)]"
                            @checked(old('is_anonymous'))>
                        <span class="font-black text-text">
                            {{ $isEn ? 'Donate anonymously' : 'التبرع كمجهول' }}
                        </span>
                    </label>

                    <div class="text-xs text-subtext">
                        {{ $isEn ? 'Your name won’t be shown publicly.' : 'لن يظهر اسمك علنًا.' }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                    <button class="w-full sm:w-auto btn btn-primary">
                        {{ $isEn ? 'Confirm donation' : 'تأكيد التبرع' }}
                        <span aria-hidden="true">→</span>
                    </button>

                    <a class="w-full sm:w-auto btn btn-secondary text-center" href="{{ $urlCampaigns }}">
                        {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}
                    </a>
                </div>

                {{-- JSON maps for JS --}}
                <script type="application/json" id="campaign_currency_map">@json($curMap)</script>
                <script type="application/json" id="campaign_name_map">@json($nameMap)</script>
            </form>

            {{-- Summary sidebar --}}
            <aside class="lg:sticky lg:top-24 h-fit">
                <div class="card p-6 sm:p-7 space-y-4">
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Donation summary' : 'ملخص التبرع' }}
                    </div>

                    <div class="card-muted p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Platform' : 'المنصة' }}</div>
                        <div class="font-black text-text mt-1">{{ $siteName }}</div>
                    </div>

                    <div class="card-muted p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                        <div class="font-black text-text mt-1 line-clamp-2" id="summary_campaign">
                            {{ $selectedCampaignId ? $nameMap[$selectedCampaignId] ?? '' : '' }}
                        </div>
                        <div class="mt-2 text-xs text-subtext">
                            {{ $isEn ? 'Currency auto-syncs with campaign.' : 'العملة تتزامن تلقائيًا مع الحملة.' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="card-muted p-4">
                            <div class="text-xs text-subtext">{{ $isEn ? 'Amount' : 'المبلغ' }}</div>
                            <div class="font-black text-text mt-1">
                                <span id="summary_amount">{{ number_format((float) $defaultAmount, 2) }}</span>
                                <span id="summary_currency">{{ $selectedCurrency }}</span>
                            </div>
                        </div>

                        <div class="card-muted p-4">
                            <div class="text-xs text-subtext">{{ $isEn ? 'Method' : 'الطريقة' }}</div>
                            <div class="font-black text-text mt-1">
                                {{ $isEn ? 'Mock (temporary)' : 'تجريبي (مؤقت)' }}
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-subtext leading-relaxed">
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

                let curMap = {};
                let nameMap = {};

                try {
                    curMap = curMapEl ? JSON.parse(curMapEl.textContent || '{}') : {};
                } catch (e) {}
                try {
                    nameMap = nameMapEl ? JSON.parse(nameMapEl.textContent || '{}') : {};
                } catch (e) {}

                function safeNum(v) {
                    const x = parseFloat(v || 0);
                    return Number.isFinite(x) ? x : 0;
                }

                function syncAmount() {
                    if (!sumAmount) return;
                    sumAmount.textContent = safeNum(amount?.value).toFixed(2);
                }

                function syncCurrency() {
                    if (sumCurrency) sumCurrency.textContent = currency?.value || '';
                    if (hint) hint.textContent = currency?.value || '';
                }

                function syncCampaignLabel() {
                    if (!sumCampaign) return;
                    const cid = campaign?.value;
                    sumCampaign.textContent = (cid && nameMap[cid]) ? nameMap[cid] : '';
                }

                function syncCurrencyFromCampaign() {
                    const cid = campaign?.value;
                    if (cid && curMap[cid] && currency) {
                        currency.value = curMap[cid];
                        syncCurrency();
                    }
                    syncCampaignLabel();
                }

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
