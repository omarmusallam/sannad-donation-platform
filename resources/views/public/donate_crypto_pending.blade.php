@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Crypto Transfer Submitted' : 'تم إرسال التحويل')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $amountText = number_format((float) $donation->amount, 2) . ' ' . $donation->currency;
    @endphp

    <div class="max-w-5xl mx-auto space-y-8">
        <section class="section-shell p-7 sm:p-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="eyebrow">{{ $isEn ? 'Transfer submitted' : 'تم إرسال التحويل' }}</div>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $isEn ? 'Your USDT transfer is now under review' : 'تحويل USDT الخاص بك قيد المراجعة الآن' }}</h1>
                    <p class="mt-3 text-subtext leading-relaxed">{{ $isEn ? 'The transfer details were received successfully. Once the review is completed, the final donation status and receipt access will appear through the secure tracking page.' : 'تم استلام تفاصيل التحويل بنجاح. بعد اكتمال المراجعة ستظهر حالة التبرع النهائية والوصول إلى الإيصال عبر صفحة التتبع الآمنة.' }}</p>
                </div>

                <div class="rounded-3xl border border-amber-200 bg-amber-50/70 p-5 sm:p-6 min-w-[280px]">
                    <div class="text-sm text-subtext">{{ $isEn ? 'Current status' : 'الحالة الحالية' }}</div>
                    <div class="mt-2 text-2xl font-black text-amber-700">{{ $isEn ? 'Pending review' : 'بانتظار المراجعة' }}</div>
                    <div class="mt-3 text-xs text-subtext">{{ $isEn ? 'Tracking reference' : 'مرجع التتبع' }}</div>
                    <div class="mt-1 font-mono text-sm break-all text-text">{{ $donation->public_id }}</div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6 sm:p-8 space-y-6">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Donation amount' : 'مبلغ التبرع' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $amountText }}</div></div>
                    <div class="kpi-tile"><div class="text-xs text-subtext">{{ $isEn ? 'Status' : 'الحالة' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $isEn ? 'Pending review' : 'بانتظار المراجعة' }}</div></div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Submitted transaction hash' : 'رقم العملية المرسل' }}</div>
                    <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-sm text-text break-all">{{ $donation->crypto_tx_hash ?: '—' }}</div>
                </div>

                <div class="card-muted p-5 text-sm text-subtext leading-relaxed">
                    {{ $isEn ? 'Save this secure tracking link. It will show the final status and the receipt once the transfer is approved.' : 'احفظ رابط التتبع الآمن هذا. سيعرض الحالة النهائية والإيصال بمجرد اعتماد التحويل.' }}
                    <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-xs text-text break-all">{{ $statusUrl }}</div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Actions' : 'إجراءات' }}</div>
                    <div class="mt-4 grid gap-3">
                        <a href="{{ $statusUrl }}" class="btn btn-primary justify-center">{{ $isEn ? 'Open status page' : 'فتح صفحة الحالة' }}</a>
                        <button type="button" class="btn btn-secondary justify-center" onclick="copyTrackingLink(this)">{{ $isEn ? 'Copy tracking link' : 'نسخ رابط التتبع' }}</button>
                        @auth('donor')
                            <a href="{{ locale_route('donor.donations') }}" class="btn btn-secondary justify-center">{{ $isEn ? 'My donations' : 'تبرعاتي' }}</a>
                        @endauth
                        <a href="{{ locale_route('donate') }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Donate again' : 'تبرّع مرة أخرى' }}</a>
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'What happens next?' : 'ماذا يحدث بعد ذلك؟' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>• {{ $isEn ? 'The transfer is reviewed manually.' : 'يتم مراجعة التحويل يدويًا.' }}</li>
                        <li>• {{ $isEn ? 'If approved, the donation status becomes complete.' : 'عند الاعتماد تتحول حالة التبرع إلى مكتملة.' }}</li>
                        <li>• {{ $isEn ? 'Receipt access appears on the same tracking page.' : 'يظهر الوصول إلى الإيصال في صفحة التتبع نفسها.' }}</li>
                    </ul>
                </div>
            </aside>
        </section>
    </div>

    @push('scripts')
        <script>
            function copyTrackingLink(button) {
                navigator.clipboard.writeText(@js($statusUrl));
                const original = button.innerText;
                button.innerText = @js($isEn ? 'Copied ✓' : 'تم النسخ ✓');
                setTimeout(() => button.innerText = original, 2000);
            }
        </script>
    @endpush
@endsection
