@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Donation Status' : 'حالة التبرع')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $money = fn($value) => number_format((float) $value, 2);

        $homeUrl = locale_route('home');
        $campaignsUrl = locale_route('campaigns.index');
        $donateUrl = locale_route('donate');

        $isPaid = $donation->status === 'paid';
        $isPending = $donation->status === 'pending';
        $isPendingCryptoReview = $donation->status === 'pending_crypto_review';
        $isFailed = $donation->status === 'failed';

        $receiptNo = $isPaid ? $donation->receipt->receipt_no ?? null : null;
        $statusLabel = match (true) {
            $isPaid => $isEn ? 'Donation completed' : 'تم التبرع بنجاح',
            $isPendingCryptoReview => $isEn ? 'Pending transfer review' : 'بانتظار مراجعة التحويل',
            $isPending => $isEn ? 'Pending confirmation' : 'بانتظار تأكيد الدفع',
            default => $isEn ? 'Payment not completed' : 'لم يكتمل الدفع',
        };

        $statusTone = match (true) {
            $isPaid => 'text-emerald-600',
            $isPending || $isPendingCryptoReview => 'text-amber-600',
            default => 'text-rose-600',
        };

        $statusCard = match (true) {
            $isPaid => 'border-emerald-200 bg-emerald-50/70',
            $isPending || $isPendingCryptoReview => 'border-amber-200 bg-amber-50/70',
            default => 'border-rose-200 bg-rose-50/70',
        };
    @endphp

    <div class="max-w-5xl mx-auto space-y-8">
        <section class="section-shell p-7 sm:p-10">
            <div class="text-sm text-subtext">
                <a class="hover:underline underline-offset-4" href="{{ $homeUrl }}">{{ $isEn ? 'Home' : 'الرئيسية' }}</a>
                <span class="mx-2">/</span>
                <span class="text-text font-semibold">{{ $isEn ? 'Donation status' : 'حالة التبرع' }}</span>
            </div>

            <div class="mt-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="eyebrow">{{ $isEn ? 'Donation follow-up' : 'متابعة التبرع' }}</div>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $statusLabel }}</h1>
                    <p class="mt-3 text-subtext leading-relaxed">
                        @if ($isPaid)
                            {{ $isEn ? 'Your donation was recorded successfully. Receipt access and secure verification are now available.' : 'تم تسجيل تبرعك بنجاح. أصبح الوصول إلى الإيصال والتحقق الآمن متاحًا الآن.' }}
                        @elseif ($isPendingCryptoReview)
                            {{ $isEn ? 'Your USDT transfer details were received and are under review before receipt issuance.' : 'تم استلام تفاصيل تحويل USDT وهي الآن قيد المراجعة قبل إصدار الإيصال.' }}
                        @elseif ($isPending)
                            {{ $isEn ? 'Your donation request was created and payment confirmation is still pending.' : 'تم إنشاء طلب التبرع ولا يزال تأكيد الدفع قيد الانتظار.' }}
                        @else
                            {{ $isEn ? 'The payment did not complete successfully. You can review the status and try again safely.' : 'لم تكتمل عملية الدفع بنجاح. يمكنك مراجعة الحالة والمحاولة مرة أخرى بأمان.' }}
                        @endif
                    </p>
                </div>

                <div class="rounded-3xl border p-5 sm:p-6 min-w-[280px] {{ $statusCard }}">
                    <div class="text-sm text-subtext">{{ $isEn ? 'Current status' : 'الحالة الحالية' }}</div>
                    <div class="mt-2 text-2xl font-black {{ $statusTone }}">{{ $statusLabel }}</div>
                    <div class="mt-3 text-xs text-subtext">{{ $isEn ? 'Public tracking reference' : 'مرجع التتبع العام' }}</div>
                    <div class="mt-1 font-mono text-sm break-all text-text">{{ $donation->public_id }}</div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6 sm:p-8">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Amount' : 'المبلغ' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ $money($donation->amount) }} <span class="text-base text-subtext">{{ $donation->currency }}</span></div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Payment method' : 'طريقة الدفع' }}</div>
                        <div class="mt-2 font-black text-text">{{ $donation->payment_method === 'usdt_trc20' ? 'USDT (TRC20)' : ($isEn ? 'Card / Wallet checkout' : 'بطاقة / دفع إلكتروني') }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Receipt number' : 'رقم الإيصال' }}</div>
                        <div class="mt-2 font-mono text-lg font-bold text-text">{{ $receiptNo ?: '—' }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Recorded at' : 'تاريخ التسجيل' }}</div>
                        <div class="mt-2 font-black text-text">{{ optional($donation->paid_at ?? $donation->created_at)->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <div class="mt-6 card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Secure tracking link' : 'رابط التتبع الآمن' }}</div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Keep this link if you want to return later, especially when donating as a guest.' : 'احتفظ بهذا الرابط إذا أردت العودة لاحقًا، خاصة عند التبرع كضيف.' }}</div>
                    <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-xs text-text break-all">{{ $statusUrl ?? request()->fullUrl() }}</div>
                </div>

                @if ($isPaid && !empty($receiptUrl))
                    <div class="mt-6 card-muted p-5">
                        <div class="font-black text-text">{{ $isEn ? 'Receipt access' : 'الوصول إلى الإيصال' }}</div>
                        <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Your official receipt is available through a verification page and, when present, a signed PDF download.' : 'إيصالك الرسمي متاح عبر صفحة تحقق، ومعه تحميل PDF موقّع عند توفره.' }}</div>
                        <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-xs text-text break-all">{{ $receiptUrl }}</div>
                    </div>
                @endif

                @if ($isPending || $isPendingCryptoReview)
                    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/70 p-5 text-sm text-amber-900 leading-relaxed">
                        {{ $isPendingCryptoReview
                            ? ($isEn ? 'Your transfer details are stored and waiting for manual review. Once approved, the receipt will appear on this page.' : 'تم حفظ تفاصيل التحويل وهي بانتظار المراجعة اليدوية. عند الاعتماد سيظهر الإيصال في هذه الصفحة.')
                            : ($isEn ? 'The platform is still waiting for payment confirmation. Avoid retrying immediately unless you are sure the first attempt failed.' : 'لا تزال المنصة بانتظار تأكيد الدفع. تجنب إعادة المحاولة مباشرة ما لم تكن متأكدًا من فشل المحاولة الأولى.') }}
                    </div>
                @endif

                @if ($isFailed)
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/70 p-5 text-sm text-rose-900 leading-relaxed">{{ $isEn ? 'The payment did not complete. You can safely start a new donation attempt from the donation page.' : 'لم تكتمل عملية الدفع. يمكنك بدء محاولة تبرع جديدة بأمان من صفحة التبرع.' }}</div>
                @endif
            </div>

            <aside class="space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Actions' : 'إجراءات' }}</div>
                    <div class="mt-4 grid gap-3">
                        <button type="button" class="btn btn-secondary justify-center" onclick="copyText(@js($statusUrl ?? request()->fullUrl()), this)">{{ $isEn ? 'Copy status link' : 'نسخ رابط الحالة' }}</button>

                        @if ($isPaid && !empty($receiptUrl))
                            <a href="{{ $receiptUrl }}" class="btn btn-primary justify-center">{{ $isEn ? 'Open receipt' : 'فتح الإيصال' }}</a>
                            <button type="button" class="btn btn-secondary justify-center" onclick="copyText(@js($receiptUrl), this)">{{ $isEn ? 'Copy receipt link' : 'نسخ رابط الإيصال' }}</button>
                            @if (!empty($downloadUrl))
                                <a href="{{ $downloadUrl }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Download PDF' : 'تحميل PDF' }}</a>
                            @endif
                        @endif

                        <a href="{{ $donateUrl }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Donate again' : 'تبرّع مرة أخرى' }}</a>
                        <a href="{{ $campaignsUrl }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}</a>
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Important notes' : 'ملاحظات مهمة' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        @if ($isPaid)
                            <li>• {{ $isEn ? 'Receipt verification stays available through the public-safe receipt link.' : 'يبقى التحقق من الإيصال متاحًا عبر رابط الإيصال العام الآمن.' }}</li>
                            <li>• {{ $isEn ? 'Signed PDF links expire automatically for safer access.' : 'تنتهي روابط PDF الموقعة تلقائيًا لزيادة الأمان.' }}</li>
                        @elseif ($isPending || $isPendingCryptoReview)
                            <li>• {{ $isEn ? 'Keep this page or its link if you are not signed in.' : 'احتفظ بهذه الصفحة أو رابطها إذا لم تكن مسجل دخول.' }}</li>
                            <li>• {{ $isEn ? 'Status updates will appear here once the review or confirmation changes.' : 'ستظهر تحديثات الحالة هنا عند تغير المراجعة أو التأكيد.' }}</li>
                        @else
                            <li>• {{ $isEn ? 'A failed payment does not issue a receipt.' : 'الدفع الفاشل لا يصدر إيصالًا.' }}</li>
                            <li>• {{ $isEn ? 'You can start a clean new attempt from the donation page.' : 'يمكنك بدء محاولة جديدة نظيفة من صفحة التبرع.' }}</li>
                        @endif
                    </ul>
                </div>
            </aside>
        </section>
    </div>

    @push('scripts')
        <script>
            function copyText(text, button) {
                const copiedText = @js($isEn ? 'Copied ✓' : 'تم النسخ ✓');
                const originalText = button.innerText;

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        button.innerText = copiedText;
                        setTimeout(() => button.innerText = originalText, 2000);
                    });
                    return;
                }

                const input = document.createElement('textarea');
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                button.innerText = copiedText;
                setTimeout(() => button.innerText = originalText, 2000);
            }
        </script>
    @endpush
@endsection
