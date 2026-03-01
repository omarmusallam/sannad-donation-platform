@extends('layouts.public')

@section('title', app()->getLocale() === 'ar' ? 'التحقق من الإيصال' : 'Receipt Verification')

@section('content')
    @php
        $isAr = app()->getLocale() === 'ar';
        $money = fn($v) => number_format((float) $v, 2);
        $isValid = $receipt->status === 'issued';

        $statusText = $isValid ? ($isAr ? 'إيصال صالح' : 'Valid Receipt') : ($isAr ? 'ملغي' : 'Cancelled');

        $statusColor = $isValid ? 'text-emerald-500' : 'text-red-500';

        $receiptUrl = route('receipt.verify', $receipt->uuid);

        $downloadUrl = $receipt->pdf_path
            ? URL::temporarySignedRoute('receipt.download.public', now()->addMinutes(30), ['receipt' => $receipt->uuid])
            : null;
    @endphp

    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="card p-10 relative overflow-hidden">

            <div class="flex justify-between items-start flex-wrap gap-8">

                <div>
                    <div class="text-sm text-subtext">
                        {{ $isAr ? 'الجهة المصدرة' : 'Issued By' }}
                    </div>

                    <div class="text-2xl font-black text-text mt-1">
                        {{ config('app.name') }}
                    </div>

                    <div class="mt-3 text-xs text-subtext">
                        {{ $isAr ? 'بوابة التحقق الرسمية للإيصالات الرقمية' : 'Official Digital Receipt Verification Portal' }}
                    </div>
                </div>

                <div class="{{ $isAr ? 'text-left' : 'text-right' }}">
                    <div class="text-xs text-subtext">
                        {{ $isAr ? 'رقم الإيصال' : 'Receipt Number' }}
                    </div>

                    <div class="font-mono text-lg font-bold text-text">
                        {{ $receipt->receipt_no }}
                    </div>

                    <div class="mt-2 text-xs text-subtext">
                        {{ $isAr ? 'تاريخ الإصدار:' : 'Issued at:' }}
                        {{ $receipt->issued_at?->format('Y-m-d H:i') }}
                    </div>
                </div>

            </div>

            {{-- Status --}}
            <div class="mt-8 flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-current {{ $statusColor }}"></div>
                <div class="font-black text-lg {{ $statusColor }}">
                    {{ $statusText }}
                </div>
            </div>

        </div>


        {{-- Body --}}
        <div class="mt-8 card p-10">

            <div class="grid md:grid-cols-2 gap-10 items-start">

                {{-- Left --}}
                <div>

                    <div class="text-xs text-subtext">
                        {{ $isAr ? 'المبلغ المتبرع به' : 'Donation Amount' }}
                    </div>

                    <div class="text-4xl font-black text-text mt-2">
                        {{ $money($receipt->amount) }}
                        <span class="text-xl text-subtext">
                            {{ $receipt->currency }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-5">

                        <div>
                            <div class="text-xs text-subtext">
                                {{ $isAr ? 'الحملة' : 'Campaign' }}
                            </div>
                            <div class="font-semibold text-text mt-1">
                                {{ $isAr ? $receipt->donation?->campaign?->title_ar : $receipt->donation?->campaign?->title_en }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-subtext">
                                {{ $isAr ? 'رقم التبرع' : 'Donation ID' }}
                            </div>
                            <div class="font-mono text-text">
                                {{ $receipt->donation_id }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-subtext">
                                {{ $isAr ? 'رمز التحقق' : 'Verification Token' }}
                            </div>
                            <div class="font-mono text-text break-all">
                                {{ $receipt->uuid }}
                            </div>
                        </div>

                    </div>

                </div>

                {{-- QR --}}
                <div class="flex flex-col items-center">

                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($receiptUrl) }}"
                        class="rounded-2xl border border-border shadow-sm">

                    <div class="text-xs text-subtext mt-4 text-center">
                        {{ $isAr ? 'امسح الرمز للتحقق من صحة الإيصال' : 'Scan to verify this receipt' }}
                    </div>

                </div>

            </div>

        </div>


        {{-- Security Section --}}
        <div class="mt-8 card-muted p-8">

            <div class="font-bold text-text mb-4 text-lg">
                {{ $isAr ? 'الأمان والتحقق' : 'Security & Verification' }}
            </div>

            <ul class="text-sm text-subtext space-y-3">
                <li>✔
                    {{ $isAr
                        ? 'الإيصال صادر إلكترونيًا وقابل للتحقق العام.'
                        : 'This receipt is digitally issued and publicly verifiable.' }}
                </li>

                <li>✔
                    {{ $isAr ? 'أي تعديل يؤدي إلى إبطال الإيصال.' : 'Any alteration invalidates this receipt.' }}
                </li>

                <li>✔
                    {{ $isAr
                        ? 'رابط التحقق الرسمي متاح عبر QR أو الرابط المباشر.'
                        : 'Official verification available via QR or direct link.' }}
                </li>
            </ul>

        </div>


        {{-- Actions --}}
        <div class="mt-8 flex flex-wrap gap-4">

            <button onclick="window.print()" class="btn btn-secondary">
                {{ $isAr ? 'طباعة' : 'Print' }}
            </button>

            <button onclick="copyReceiptLink(this)" class="btn btn-secondary">
                {{ $isAr ? 'نسخ الرابط' : 'Copy Link' }}
            </button>

            <button onclick="shareReceipt()" class="btn btn-secondary">
                {{ $isAr ? 'مشاركة' : 'Share' }}
            </button>

            @if ($downloadUrl)
                <a href="{{ $downloadUrl }}" class="btn btn-primary">
                    {{ $isAr ? 'تحميل الإيصال الرسمي PDF' : 'Download Official PDF' }}
                </a>
            @endif

        </div>

    </div>


    {{-- Scripts --}}
    <script>
        function copyReceiptLink(button) {
            const text = "{{ $receiptUrl }}";

            navigator.clipboard.writeText(text).then(() => {
                button.innerText = "{{ $isAr ? '✔ تم النسخ' : '✔ Copied' }}";
                setTimeout(() => {
                    button.innerText = "{{ $isAr ? 'نسخ الرابط' : 'Copy Link' }}";
                }, 2000);
            });
        }

        function shareReceipt() {
            const url = "{{ $receiptUrl }}";

            if (navigator.share) {
                navigator.share({
                    title: "{{ $isAr ? 'التحقق من الإيصال' : 'Receipt Verification' }}",
                    text: "{{ $isAr ? 'تحقق من هذا الإيصال الرسمي' : 'Verify this official receipt' }}",
                    url: url
                });
            } else {
                alert("{{ $isAr ? 'المشاركة غير مدعومة في هذا المتصفح.' : 'Sharing not supported in this browser.' }}");
            }
        }
    </script>

@endsection
