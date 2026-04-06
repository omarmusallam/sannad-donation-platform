@extends('layouts.public')

@section('title', __('ui.donate_now'))

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $donor = auth('donor')->user();
        $siteName = (string) ($appSettings['site.name'] ?? 'GazaSannad');
        $defaultAmount = old('amount', $amount ?? 25);
        $selectedCampaignId = old('campaign_id', optional($campaign)->id);
        $selectedCurrency = 'USD';
        $formAction = locale_route('donate.submit');
        $urlCampaigns = locale_route('campaigns.index');
        $urlHome = locale_route('home');
        $urlDonorLogin = locale_route('donor.login');
        $urlDonorRegister = locale_route('donor.register');
        $quickAmounts = [10, 25, 50, 100, 250];
        $nameMap = [];

        foreach ($campaigns ?? collect() as $item) {
            $nameMap[$item->id] = $isEn ? ($item->title_en ?: $item->title_ar) : ($item->title_ar ?: $item->title_en);
        }

        $pageTitle = $isEn ? 'Donate now' : 'تبرّع الآن';
    @endphp

    <div class="max-w-6xl mx-auto">
<section class="section-shell mb-8 overflow-hidden p-6 sm:p-8 lg:p-10">
            <div class="text-sm text-subtext">
                <a class="hover:underline underline-offset-4" href="{{ $urlHome }}">{{ $isEn ? 'Home' : 'الرئيسية' }}</a>
                <span class="mx-2">/</span>
                <span class="text-text font-semibold">{{ $pageTitle }}</span>
            </div>

            <div class="mt-5 max-w-3xl">
                <div class="eyebrow">{{ $isEn ? 'Secure donation flow' : 'مسار تبرع آمن' }}</div>
                <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-text">{{ $pageTitle }}</h1>
                <p class="mt-4 text-subtext leading-relaxed">{{ $isEn ? 'Choose a campaign, confirm your amount in USD, and complete your donation through a clear, professional flow.' : 'اختر الحملة وحدد المبلغ بالدولار وأكمل تبرعك عبر مسار واضح واحترافي.' }}</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <span class="badge">{{ $isEn ? 'USD only' : 'USD فقط' }}</span>
                <span class="badge">{{ $isEn ? 'Receipt access' : 'وصول إلى الإيصال' }}</span>
                <span class="badge">{{ $isEn ? 'Guest tracking for USDT' : 'تتبع الضيف في USDT' }}</span>
                <span class="badge">{{ $isEn ? 'Secure payment methods' : 'وسائل دفع آمنة' }}</span>
            </div>
        </section>

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-300/30 bg-red-500/10 p-4 text-sm text-red-200 font-semibold">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-300/30 bg-green-500/10 p-4 text-sm text-green-200 font-semibold">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @if (!$donor)
                <div class="lg:col-span-2 card-muted p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-subtext leading-relaxed">{{ $isEn ? 'Create an account or sign in to keep all receipts and donation history in one secure dashboard. You can still donate as a guest.' : 'أنشئ حسابًا أو سجّل الدخول للاحتفاظ بالإيصالات وسجل التبرعات في لوحة واحدة آمنة. ويمكنك التبرع أيضًا كضيف.' }}</div>
                    <div class="flex gap-2">
                        <a class="btn btn-secondary" href="{{ $urlDonorLogin }}">{{ $isEn ? 'Login' : 'تسجيل دخول' }}</a>
                        <a class="btn btn-primary" href="{{ $urlDonorRegister }}">{{ $isEn ? 'Create account' : 'إنشاء حساب' }}</a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" class="lg:col-span-2 card p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Campaign' : 'الحملة' }}</label>
                        <select id="campaign_id" name="campaign_id" class="input">
                            @foreach ($campaigns ?? collect() as $item)
                                <option value="{{ $item->id }}" @selected($selectedCampaignId == $item->id)>
                                    {{ $isEn ? ($item->title_en ?: $item->title_ar) : ($item->title_ar ?: $item->title_en) }}
                                </option>
                            @endforeach
                        </select>
                        @error('campaign_id')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Currency' : 'العملة' }}</label>
                        <input id="currency" name="currency" type="hidden" value="USD">
                        <div class="input flex items-center justify-between font-black text-text"><span>USD</span><span class="text-xs text-subtext">{{ $isEn ? 'Fixed' : 'ثابتة' }}</span></div>
                        <div class="mt-2 text-xs text-subtext">{{ $isEn ? 'All campaigns and donations are processed in USD for consistency across reports and receipts.' : 'جميع الحملات والتبرعات تتم بالدولار الأمريكي فقط لضمان وضوح التقارير والإيصالات.' }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Amount' : 'المبلغ' }}</label>
                    <div class="relative">
                        <input id="amount" name="amount" type="number" min="1" step="0.01" value="{{ $defaultAmount }}" class="input pe-20">
                        <div class="absolute top-1/2 -translate-y-1/2 {{ $isEn ? 'right-3' : 'left-3' }} text-xs text-subtext font-black" id="amount_currency_hint">{{ $selectedCurrency }}</div>
                    </div>
                    @error('amount')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror

                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($quickAmounts as $quick)
                            <button type="button" class="btn btn-secondary px-3 py-2 text-sm" onclick="setQuickAmount('{{ $quick }}')">{{ $quick }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Name (optional)' : 'الاسم (اختياري)' }}</label>
                        <input id="donor_name" name="donor_name" value="{{ old('donor_name', $donor?->name) }}" class="input" autocomplete="name">
                        @error('donor_name')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Email (optional)' : 'البريد (اختياري)' }}</label>
                        <input id="donor_email" name="donor_email" value="{{ old('donor_email', $donor?->email) }}" class="input" autocomplete="email">
                        <div class="mt-2 text-xs text-subtext">{{ $isEn ? 'Email is strongly recommended for receipt access and is required for guest USDT follow-up.' : 'يُنصح بالبريد للوصول إلى الإيصال، ويصبح مهمًا لمتابعة تبرعات USDT للضيف.' }}</div>
                        @error('donor_email')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-2xl border border-border bg-muted p-4">
                    <label class="flex items-center gap-3 text-sm">
                        <input id="is_anonymous" type="checkbox" name="is_anonymous" value="1" class="h-5 w-5 rounded border-border text-brand" @checked(old('is_anonymous'))>
                        <span class="font-black text-text">{{ $isEn ? 'Donate anonymously' : 'التبرع كمجهول' }}</span>
                    </label>
                    <span class="text-xs text-subtext">{{ $isEn ? 'Your name stays hidden publicly' : 'لن يظهر اسمك للعامة' }}</span>
                </div>

                @error('is_anonymous')<div class="text-red-600 text-xs -mt-3">{{ $message }}</div>@enderror

                <div>
                    <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Payment method' : 'طريقة الدفع' }}</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="card-muted p-4 flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="mt-1" {{ old('payment_method', 'card') === 'card' ? 'checked' : '' }}>
                            <div>
                                <div class="font-black text-text">{{ $isEn ? 'Card / Apple Pay / Google Pay' : 'بطاقة / Apple Pay / Google Pay' }}</div>
                                <div class="text-sm text-subtext mt-1">{{ $isEn ? 'Fast secure checkout via Stripe' : 'دفع آمن وسريع عبر Stripe' }}</div>
                            </div>
                        </label>

                        <label class="card-muted p-4 flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="payment_method" value="usdt_trc20" class="mt-1" {{ old('payment_method') === 'usdt_trc20' ? 'checked' : '' }}>
                            <div>
                                <div class="font-black text-text">{{ $isEn ? 'USDT (TRC20)' : 'USDT (TRC20)' }}</div>
                                <div class="text-sm text-subtext mt-1">{{ $isEn ? 'Manual wallet transfer with status tracking and receipt access' : 'تحويل من المحفظة مع تتبع للحالة ووصول إلى الإيصال' }}</div>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')<div class="text-red-600 text-xs mt-2">{{ $message }}</div>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'You review the campaign and amount before leaving this page.' : 'تراجع الحملة والمبلغ بوضوح قبل الانتقال للدفع.' }}</div>
                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Receipts become available after success, and public-safe tracking is available when needed.' : 'الإيصالات تصبح متاحة بعد النجاح، مع تتبع آمن عند الحاجة.' }}</div>
                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Card details are handled by the payment provider, not stored by the platform.' : 'بيانات البطاقة تتم معالجتها عبر مزود الدفع ولا تُخزن على المنصة.' }}</div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                    <button type="submit" class="w-full sm:w-auto btn btn-primary">{{ $isEn ? 'Continue to payment' : 'المتابعة إلى الدفع' }}</button>
                    <a class="w-full sm:w-auto btn btn-secondary text-center" href="{{ $urlCampaigns }}">{{ $isEn ? 'Back to campaigns' : 'العودة للحملات' }}</a>
                </div>

                <script type="application/json" id="campaign_name_map">@json($nameMap)</script>
            </form>

            <aside class="lg:sticky lg:top-24 h-fit space-y-4">
                <div class="card p-6 sm:p-7 space-y-4">
                    <div>
                        <div class="eyebrow">{{ $isEn ? 'Donation summary' : 'ملخص التبرع' }}</div>
                        <div class="mt-3 text-xl font-black text-text">{{ $isEn ? 'A calm and clear payment review' : 'مراجعة هادئة وواضحة قبل الدفع' }}</div>
                    </div>

                    <div class="card-muted p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Platform' : 'المنصة' }}</div>
                        <div class="font-black text-text mt-1">{{ $siteName }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Amount' : 'المبلغ' }}</div><div class="mt-1 font-black text-text"><span id="summary_amount">{{ number_format((float) $defaultAmount, 2) }}</span></div></div>
                        <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Currency' : 'العملة' }}</div><div class="mt-1 font-black text-text"><span id="summary_currency">{{ $selectedCurrency }}</span></div></div>
                    </div>

                    <div class="card-muted p-4">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                        <div class="mt-1 font-black text-text" id="summary_campaign">{{ $selectedCampaignId && isset($nameMap[$selectedCampaignId]) ? $nameMap[$selectedCampaignId] : ($isEn ? 'Select campaign' : 'اختر الحملة') }}</div>
                    </div>

                    <div class="card-muted p-4 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Your donation will be processed securely in USD. After success, receipt access will be available. For guest USDT donations, tracking remains available through the secure public link.' : 'سيتم تنفيذ التبرع بشكل آمن وبالدولار. بعد النجاح يصبح الإيصال متاحًا. وفي تبرعات USDT للضيف يبقى التتبع متاحًا عبر الرابط العام الآمن.' }}</div>
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
                const campaignMap = JSON.parse(document.getElementById('campaign_name_map')?.textContent || '{}');
                const summaryAmount = document.getElementById('summary_amount');
                const summaryCurrency = document.getElementById('summary_currency');
                const summaryCampaign = document.getElementById('summary_campaign');
                const amountCurrencyHint = document.getElementById('amount_currency_hint');

                function safeNumber(value) {
                    const parsed = parseFloat(value || 0);
                    return Number.isFinite(parsed) ? parsed : 0;
                }

                function syncAmount() {
                    if (summaryAmount) summaryAmount.textContent = safeNumber(amount?.value).toFixed(2);
                }

                function syncCurrency() {
                    const currentCurrency = currency?.value || 'USD';
                    if (summaryCurrency) summaryCurrency.textContent = currentCurrency;
                    if (amountCurrencyHint) amountCurrencyHint.textContent = currentCurrency;
                }

                function syncCampaign() {
                    if (!summaryCampaign || !campaign) return;
                    summaryCampaign.textContent = campaignMap[campaign.value] || summaryCampaign.textContent;
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
                campaign?.addEventListener('change', syncCampaign);
                isAnonymous?.addEventListener('change', syncAnonymousState);

                syncAmount();
                syncCurrency();
                syncCampaign();
                syncAnonymousState();
            })();
        </script>
    @endpush
@endsection
