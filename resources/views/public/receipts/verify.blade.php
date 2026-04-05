@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Receipt Verification' : 'التحقق من الإيصال')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $money = fn($value) => number_format((float) $value, 2);

        $isValid = $receipt->status === 'issued';
        $receiptUrl = locale_route('receipt.verify', ['receipt' => $receipt->uuid]);
        $downloadUrl = $receipt->pdf_path
            ? URL::temporarySignedRoute(
                app()->isLocale('en') ? 'en.receipt.download.public' : 'receipt.download.public',
                now()->addMinutes(30),
                ['receipt' => $receipt->uuid],
            )
            : null;

        $campaign = $receipt->donation?->campaign;
        $campaignTitle = $campaign
            ? ($isEn ? ($campaign->title_en ?: $campaign->title_ar) : ($campaign->title_ar ?: $campaign->title_en))
            : ($isEn ? 'Unknown campaign' : 'حملة غير معروفة');

        $verificationStatus = $isValid ? ($isEn ? 'Valid receipt' : 'إيصال صالح') : ($isEn ? 'Cancelled receipt' : 'إيصال ملغي');
        $statusTone = $isValid ? 'text-emerald-600' : 'text-rose-600';
        $statusCard = $isValid ? 'border-emerald-200 bg-emerald-50/70' : 'border-rose-200 bg-rose-50/70';
        $publicDonationId = $receipt->donation?->public_id ?: '—';
    @endphp

    <div class="max-w-5xl mx-auto space-y-8">
        <section class="section-shell p-7 sm:p-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="eyebrow">{{ $isEn ? 'Official receipt verification' : 'التحقق الرسمي من الإيصال' }}</div>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $isEn ? 'Official donation receipt' : 'إيصال تبرع رسمي' }}</h1>
                    <p class="mt-3 text-subtext leading-relaxed">{{ $isEn ? 'This page confirms whether the receipt is valid, issued by the platform, and connected to a recorded donation.' : 'تؤكد هذه الصفحة ما إذا كان الإيصال صالحًا وصادرًا عن المنصة ومرتبطًا بتبرع مسجل.' }}</p>
                </div>

                <div class="rounded-3xl border p-5 sm:p-6 min-w-[280px] {{ $statusCard }}">
                    <div class="text-sm text-subtext">{{ $isEn ? 'Verification result' : 'نتيجة التحقق' }}</div>
                    <div class="mt-2 text-2xl font-black {{ $statusTone }}">{{ $verificationStatus }}</div>
                    <div class="mt-3 text-xs text-subtext">{{ $isEn ? 'Issued by' : 'الجهة المصدرة' }}</div>
                    <div class="mt-1 font-black text-text">{{ config('app.name') }}</div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6 sm:p-8">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Receipt number' : 'رقم الإيصال' }}</div>
                        <div class="mt-2 font-mono text-lg font-bold text-text">{{ $receipt->receipt_no }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Issued at' : 'تاريخ الإصدار' }}</div>
                        <div class="mt-2 font-black text-text">{{ $receipt->issued_at?->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Donation amount' : 'مبلغ التبرع' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ $money($receipt->amount) }} <span class="text-base text-subtext">{{ $receipt->currency }}</span></div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                        <div class="mt-2 font-black text-text">{{ $campaignTitle }}</div>
                    </div>
                </div>

                <div class="mt-6 grid sm:grid-cols-2 gap-5">
                    <div class="card-muted p-5">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Public donation reference' : 'مرجع التبرع العام' }}</div>
                        <div class="mt-2 font-mono text-sm break-all text-text">{{ $publicDonationId }}</div>
                    </div>

                    <div class="card-muted p-5">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Receipt token' : 'رمز الإيصال' }}</div>
                        <div class="mt-2 font-mono text-sm break-all text-text">{{ $receipt->uuid }}</div>
                    </div>
                </div>

                <div class="mt-6 card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Secure verification link' : 'رابط التحقق الآمن' }}</div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Use this link to verify the receipt later or share it with the party that needs confirmation.' : 'استخدم هذا الرابط للتحقق من الإيصال لاحقًا أو مشاركته مع الجهة التي تحتاج إلى التأكيد.' }}</div>
                    <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-xs text-text break-all">{{ $receiptUrl }}</div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Actions' : 'إجراءات' }}</div>
                    <div class="mt-4 grid gap-3">
                        @if ($downloadUrl)
                            <a href="{{ $downloadUrl }}" class="btn btn-primary justify-center">{{ $isEn ? 'Download PDF' : 'تحميل PDF' }}</a>
                        @endif
                        <button type="button" onclick="copyReceiptLink(this)" class="btn btn-secondary justify-center">{{ $isEn ? 'Copy link' : 'نسخ الرابط' }}</button>
                        <button type="button" onclick="shareReceipt()" class="btn btn-secondary justify-center">{{ $isEn ? 'Share' : 'مشاركة' }}</button>
                        <button type="button" onclick="window.print()" class="btn btn-secondary justify-center">{{ $isEn ? 'Print' : 'طباعة' }}</button>
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Verification notes' : 'ملاحظات التحقق' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>• {{ $isEn ? 'The receipt is public-verifiable without exposing payment credentials.' : 'الإيصال قابل للتحقق العام دون كشف بيانات الدفع الحساسة.' }}</li>
                        <li>• {{ $isEn ? 'Any change to the recorded receipt state invalidates prior assumptions.' : 'أي تغيير في حالة الإيصال المسجلة يلغي الافتراضات السابقة.' }}</li>
                        <li>• {{ $isEn ? 'Signed download links expire automatically for safer access.' : 'روابط التحميل الموقعة تنتهي تلقائيًا لزيادة الأمان.' }}</li>
                    </ul>
                </div>
            </aside>
        </section>
    </div>

    @push('scripts')
        <script>
            function copyReceiptLink(button) {
                const text = @js($receiptUrl);
                navigator.clipboard.writeText(text).then(() => {
                    button.innerText = @js($isEn ? 'Copied ✓' : 'تم النسخ ✓');
                    setTimeout(() => {
                        button.innerText = @js($isEn ? 'Copy link' : 'نسخ الرابط');
                    }, 2000);
                });
            }

            function shareReceipt() {
                const url = @js($receiptUrl);
                if (navigator.share) {
                    navigator.share({
                        title: @js($isEn ? 'Official donation receipt' : 'إيصال تبرع رسمي'),
                        text: @js($isEn ? 'Use this link to verify the official receipt.' : 'استخدم هذا الرابط للتحقق من الإيصال الرسمي.'),
                        url,
                    });
                }
            }
        </script>
    @endpush
@endsection
