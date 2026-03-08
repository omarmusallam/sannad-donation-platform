@extends('layouts.public')

@section('title', __('ui.donate_now'))

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $donor = auth('donor')->user();

        $siteName = (string) ($appSettings['site.name'] ?? 'GazaSannad');

        $defaultAmount = old('amount', $amount ?? 25);
        $selectedCampaignId = old('campaign_id', optional($campaign)->id);
        $selectedCurrency = old('currency', $campaign?->currency ?? 'USD');

        $formAction = locale_route('donate.submit');
        $urlCampaigns = locale_route('campaigns.index');
        $urlHome = locale_route('home');
        $urlDonorLogin = locale_route('donor.login');
        $urlDonorRegister = locale_route('donor.register');

        $quickAmounts = [10, 25, 50, 100, 250];

        $currencyMap = [];
        $nameMap = [];

        foreach ($campaigns ?? collect() as $item) {
            $currencyMap[$item->id] = $item->currency;
            $nameMap[$item->id] = $isEn ? ($item->title_en ?: $item->title_ar) : ($item->title_ar ?: $item->title_en);
        }

        $pageTitle = $isEn ? 'Donate now' : 'تبرّع الآن';
    @endphp

    <div class="max-w-6xl mx-auto">
        <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface p-7 sm:p-10 mb-8">
            <div class="text-sm text-subtext mb-3">
                <a class="hover:underline underline-offset-4" href="{{ $urlHome }}">
                    {{ $isEn ? 'Home' : 'الرئيسية' }}
                </a>
                <span class="mx-2">/</span>
                <span class="text-text font-semibold">{{ $pageTitle }}</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-black text-text">{{ $pageTitle }}</h1>

            <p class="mt-3 text-subtext max-w-2xl leading-relaxed">
                {{ $isEn
                    ? 'Choose a campaign, set your amount, and complete your donation securely.'
                    : 'اختر الحملة، حدّد المبلغ، وأكمل تبرعك بشكل آمن.' }}
            </p>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @if (!$donor)
                <div
                    class="lg:col-span-2 card-muted p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-subtext">
                        {{ $isEn
                            ? 'Login or create an account to track your donations and receipts.'
                            : 'سجّل حساباً أو ادخل لتتبع تبرعاتك وإيصالاتك.' }}
                    </div>

                    <div class="flex gap-2">
                        <a class="btn btn-secondary" href="{{ $urlDonorLogin }}">
                            {{ $isEn ? 'Login' : 'تسجيل دخول' }}
                        </a>

                        <a class="btn btn-primary" href="{{ $urlDonorRegister }}">
                            {{ $isEn ? 'Create account' : 'إنشاء حساب' }}
                        </a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" class="lg:col-span-2 card p-6 sm:p-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-black mb-2 text-text">
                        {{ $isEn ? 'Campaign' : 'الحملة' }}
                    </label>

                    <select id="campaign_id" name="campaign_id" class="input">
                        @foreach ($campaigns ?? collect() as $item)
                            <option value="{{ $item->id }}" @selected($selectedCampaignId == $item->id)>
                                {{ $isEn ? ($item->title_en ?: $item->title_ar) : ($item->title_ar ?: $item->title_en) }}
                            </option>
                        @endforeach
                    </select>

                    @error('campaign_id')
                        <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

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

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($quickAmounts as $quick)
                                <button type="button" class="btn btn-secondary px-3 py-2 text-sm"
                                    onclick="setQuickAmount('{{ $quick }}')">
                                    {{ $quick }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Currency' : 'العملة' }}
                        </label>

                        <select id="currency" name="currency" class="input">
                            @foreach (['USD', 'EUR', 'ILS'] as $currency)
                                <option value="{{ $currency }}" @selected($selectedCurrency === $currency)>
                                    {{ $currency }}
                                </option>
                            @endforeach
                        </select>

                        @error('currency')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Name (optional)' : 'الاسم (اختياري)' }}
                        </label>

                        <input id="donor_name" name="donor_name" value="{{ old('donor_name', $donor?->name) }}"
                            class="input" autocomplete="name">

                        @error('donor_name')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Email (optional)' : 'البريد (اختياري)' }}
                        </label>

                        <input id="donor_email" name="donor_email" value="{{ old('donor_email', $donor?->email) }}"
                            class="input" autocomplete="email">

                        @error('donor_email')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-2xl border border-border bg-muted p-4">
                    <label class="flex items-center gap-3 text-sm">
                        <input id="is_anonymous" type="checkbox" name="is_anonymous" value="1"
                            class="h-5 w-5 rounded border-border text-brand" @checked(old('is_anonymous'))>

                        <span class="font-black text-text">
                            {{ $isEn ? 'Donate anonymously' : 'التبرع كمجهول' }}
                        </span>
                    </label>
                </div>

                @error('is_anonymous')
                    <div class="text-red-600 text-xs -mt-3">{{ $message }}</div>
                @enderror

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                    <button type="submit" class="w-full sm:w-auto btn btn-primary">
                        {{ $isEn ? 'Confirm donation' : 'تأكيد التبرع' }}
                    </button>

                    <a class="w-full sm:w-auto btn btn-secondary text-center" href="{{ $urlCampaigns }}">
                        {{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}
                    </a>
                </div>

                <script type="application/json" id="campaign_currency_map">@json($currencyMap)</script>
                <script type="application/json" id="campaign_name_map">@json($nameMap)</script>
            </form>

            <aside class="lg:sticky lg:top-24 h-fit">
                <div class="card p-6 sm:p-7 space-y-4">
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Donation summary' : 'ملخص التبرع' }}
                    </div>

                    <div class="card-muted p-4">
                        <div class="text-xs text-subtext">
                            {{ $isEn ? 'Platform' : 'المنصة' }}
                        </div>

                        <div class="font-black text-text mt-1">
                            {{ $siteName }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="card-muted p-4">
                            <div class="text-xs text-subtext">
                                {{ $isEn ? 'Amount' : 'المبلغ' }}
                            </div>

                            <div class="font-black text-text mt-1">
                                <span id="summary_amount">{{ number_format((float) $defaultAmount, 2) }}</span>
                                <span id="summary_currency">{{ $selectedCurrency }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">
                        {{ $isEn
                            ? 'Your donation will be processed securely and a receipt will be issued after success.'
                            : 'سيتم تنفيذ التبرع بشكل آمن وسيتم إصدار إيصال بعد نجاح العملية.' }}
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
                const isAnonymous = document.getElementById('is_anonymous');
                const donorName = document.getElementById('donor_name');
                const donorEmail = document.getElementById('donor_email');

                const summaryAmount = document.getElementById('summary_amount');
                const summaryCurrency = document.getElementById('summary_currency');
                const amountCurrencyHint = document.getElementById('amount_currency_hint');

                const currencyMapElement = document.getElementById('campaign_currency_map');
                const campaignCurrencyMap = currencyMapElement ? JSON.parse(currencyMapElement.textContent) : {};

                function safeNumber(value) {
                    const parsed = parseFloat(value || 0);
                    return Number.isFinite(parsed) ? parsed : 0;
                }

                function syncAmount() {
                    if (summaryAmount) {
                        summaryAmount.textContent = safeNumber(amount?.value).toFixed(2);
                    }
                }

                function syncCurrency() {
                    const currentCurrency = currency?.value || '';

                    if (summaryCurrency) {
                        summaryCurrency.textContent = currentCurrency;
                    }

                    if (amountCurrencyHint) {
                        amountCurrencyHint.textContent = currentCurrency;
                    }
                }

                function syncCampaignCurrencyHint() {
                    const selectedCampaignId = campaign?.value;

                    if (!selectedCampaignId) {
                        syncCurrency();
                        return;
                    }

                    const campaignCurrency = campaignCurrencyMap[selectedCampaignId];

                    if (amountCurrencyHint) {
                        amountCurrencyHint.textContent = currency?.value || campaignCurrency || '';
                    }

                    syncCurrency();
                }

                function syncAnonymousState() {
                    const anonymous = !!isAnonymous?.checked;

                    if (donorName) {
                        donorName.disabled = anonymous;
                        if (anonymous) donorName.value = '';
                    }

                    if (donorEmail) {
                        donorEmail.disabled = anonymous;
                        if (anonymous) donorEmail.value = '';
                    }
                }

                window.setQuickAmount = function(value) {
                    if (!amount) return;
                    amount.value = value;
                    amount.dispatchEvent(new Event('input'));
                };

                amount?.addEventListener('input', syncAmount);
                currency?.addEventListener('change', syncCurrency);
                campaign?.addEventListener('change', syncCampaignCurrencyHint);
                isAnonymous?.addEventListener('change', syncAnonymousState);

                syncCurrency();
                syncCampaignCurrencyHint();
                syncAmount();
                syncAnonymousState();
            })();
        </script>
    @endpush
@endsection
