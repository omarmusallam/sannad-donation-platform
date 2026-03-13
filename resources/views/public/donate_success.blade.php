```blade
@extends('layouts.public')

@section('title')
    {{ app()->isLocale('en') ? 'Donation Status' : 'حالة التبرع' }}
@endsection

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $money = fn($value) => number_format((float) $value, 2);

        $homeUrl = locale_route('home');
        $campaignsUrl = locale_route('campaigns.index');
        $donateUrl = locale_route('donate');

        $isPaid = $donation->status === 'paid';
        $isPending = $donation->status === 'pending';
        $isFailed = $donation->status === 'failed';

        $receiptNo = $isPaid ? $donation->receipt->receipt_no ?? null : null;
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="text-sm text-subtext mb-6">
            <a class="hover:underline underline-offset-4" href="{{ $homeUrl }}">
                {{ $isEn ? 'Home' : 'الرئيسية' }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-text font-semibold">
                {{ $isEn ? 'Donation status' : 'حالة التبرع' }}
            </span>
        </div>

        <div class="card p-10 text-center relative overflow-hidden">
            @if ($isPaid)
                <div class="absolute inset-0 -z-10 bg-gradient-to-br from-success/10 via-transparent to-transparent"></div>
                <div class="text-5xl font-black text-success mb-4">✔</div>
            @elseif ($isPending)
                <div class="absolute inset-0 -z-10 bg-gradient-to-br from-warning/10 via-transparent to-transparent"></div>
                <div class="text-5xl font-black text-warning mb-4">⏳</div>
            @else
                <div class="absolute inset-0 -z-10 bg-gradient-to-br from-danger/10 via-transparent to-transparent"></div>
                <div class="text-5xl font-black text-danger mb-4">✕</div>
            @endif

            <h1 class="text-3xl sm:text-4xl font-black text-text">
                @if ($isPaid)
                    {{ $isEn ? 'Thank You for Your Donation' : 'شكراً لتبرعك' }}
                @elseif ($isPending)
                    {{ $isEn ? 'Payment Confirmation Pending' : 'بانتظار تأكيد الدفع' }}
                @else
                    {{ $isEn ? 'Payment Not Completed' : 'لم يكتمل الدفع' }}
                @endif
            </h1>

            <p class="text-subtext mt-3 max-w-xl mx-auto leading-relaxed">
                @if ($isPaid)
                    {{ $isEn
                        ? 'An official receipt has been issued and can be verified securely using the link below.'
                        : 'تم إصدار إيصال رسمي ويمكن التحقق منه بأمان عبر الرابط أدناه.' }}
                @elseif ($isPending)
                    {{ $isEn
                        ? 'Your payment is being confirmed. Please refresh this page in a few moments.'
                        : 'جارٍ تأكيد عملية الدفع. يرجى تحديث الصفحة بعد لحظات.' }}
                @else
                    {{ $isEn ? 'The payment was not completed successfully.' : 'لم تكتمل عملية الدفع بنجاح.' }}
                @endif
            </p>
        </div>

        <div class="mt-8 card p-8">
            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div>
                    <div class="text-sm text-subtext">
                        {{ $isEn ? 'Receipt Number' : 'رقم الإيصال' }}
                    </div>

                    <div class="font-mono text-xl font-bold text-text mt-1">
                        {{ $isPaid ? $receiptNo ?? '—' : '—' }}
                    </div>

                    <div class="mt-6 text-sm text-subtext">
                        {{ $isEn ? 'Amount' : 'المبلغ' }}
                    </div>

                    <div class="text-3xl font-black text-text mt-1">
                        {{ $money($donation->amount) }}
                        <span class="text-lg text-subtext">
                            {{ $donation->currency }}
                        </span>
                    </div>

                    <div class="mt-6 text-xs text-subtext">
                        {{ $isEn ? 'Donation ID:' : 'رقم العملية:' }}
                        <span class="font-mono">{{ $donation->id }}</span>
                    </div>

                    <div class="mt-3 text-xs text-subtext">
                        {{ $isEn ? 'Status:' : 'الحالة:' }}
                        <span class="font-semibold">
                            @if ($isPaid)
                                {{ $isEn ? 'Paid' : 'مدفوع' }}
                            @elseif ($isPending)
                                {{ $isEn ? 'Pending' : 'قيد الانتظار' }}
                            @else
                                {{ $isEn ? 'Failed' : 'فشل' }}
                            @endif
                        </span>
                    </div>
                </div>

                @if ($isPaid && !empty($receiptUrl))
                    <div class="flex flex-col items-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($receiptUrl) }}"
                            class="rounded-xl border border-border shadow-sm"
                            alt="{{ $isEn ? 'Receipt QR Code' : 'رمز QR للإيصال' }}">

                        <div class="text-xs text-subtext mt-3 text-center">
                            {{ $isEn ? 'Scan to verify this receipt' : 'امسح للتحقق من الإيصال' }}
                        </div>
                    </div>
                @endif
            </div>

            @if ($isPaid && !empty($receiptUrl))
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $receiptUrl }}" class="btn btn-primary">
                        {{ $isEn ? 'Verify Receipt' : 'التحقق من الإيصال' }}
                    </a>

                    <button type="button" class="btn btn-secondary" onclick="copyText('{{ $receiptUrl }}', this)">
                        {{ $isEn ? 'Copy Link' : 'نسخ الرابط' }}
                    </button>

                    <button type="button" class="btn btn-secondary" onclick="shareReceipt()">
                        {{ $isEn ? 'Share' : 'مشاركة' }}
                    </button>

                    @if (!empty($downloadUrl))
                        <a href="{{ $downloadUrl }}" class="btn btn-secondary">
                            {{ $isEn ? 'Download PDF' : 'تحميل PDF' }}
                        </a>
                    @endif
                </div>
            @endif

            @if ($isPending)
                <div
                    class="mt-8 rounded-2xl border border-yellow-300/30 bg-yellow-500/10 p-4 text-sm text-yellow-200 leading-relaxed">
                    {{ $isEn
                        ? 'We have received your donation request and are waiting for payment confirmation from the payment gateway.'
                        : 'تم استلام طلب التبرع ونحن بانتظار تأكيد الدفع من بوابة الدفع.' }}
                </div>
            @endif

            @if ($isFailed)
                <div
                    class="mt-8 rounded-2xl border border-red-300/30 bg-red-500/10 p-4 text-sm text-red-200 leading-relaxed">
                    {{ $isEn
                        ? 'The payment did not complete successfully. You can try again using the button below.'
                        : 'لم تكتمل عملية الدفع بنجاح. يمكنك المحاولة مرة أخرى عبر الزر أدناه.' }}
                </div>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $donateUrl }}" class="btn btn-secondary">
                    {{ $isEn ? 'Donate again' : 'تبرع مرة أخرى' }}
                </a>

                <a href="{{ $campaignsUrl }}" class="btn btn-secondary">
                    {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                </a>
            </div>
        </div>

        <div class="mt-8 card-muted p-6 text-sm text-subtext leading-relaxed space-y-2">
            @if ($isPaid)
                <div>
                    ✔
                    {{ $isEn ? 'All receipts are digitally issued and verifiable.' : 'جميع الإيصالات موثقة رقمياً وقابلة للتحقق.' }}
                </div>
                <div>
                    ✔
                    {{ $isEn ? 'Download links are temporarily signed for security.' : 'رابط التحميل مؤقت لأسباب أمنية.' }}
                </div>
                <div>
                    ✔
                    {{ $isEn ? 'You may safely share this receipt link.' : 'يمكنك مشاركة رابط الإيصال بأمان.' }}
                </div>
            @elseif ($isPending)
                <div>
                    ✔
                    {{ $isEn
                        ? 'Payment confirmation may take a short moment depending on the payment gateway.'
                        : 'قد يستغرق تأكيد الدفع لحظات قليلة حسب بوابة الدفع.' }}
                </div>
                <div>
                    ✔
                    {{ $isEn
                        ? 'Do not repeat the payment unless you are sure the first attempt failed.'
                        : 'لا تُعد عملية الدفع إلا إذا تأكدت أن المحاولة الأولى فشلت.' }}
                </div>
            @else
                <div>
                    ✔
                    {{ $isEn ? 'You can safely return and try the donation again.' : 'يمكنك العودة والمحاولة مرة أخرى بأمان.' }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function copyText(text, button) {
                const copiedText = "{{ $isEn ? 'Copied ✓' : '✔ تم النسخ' }}";

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        button.innerText = copiedText;
                    });
                } else {
                    const input = document.createElement('textarea');
                    input.value = text;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    button.innerText = copiedText;
                }
            }

            function shareReceipt() {
                const url = "{{ $receiptUrl ?? '' }}";

                if (!url) return;

                if (navigator.share) {
                    navigator.share({
                        title: "{{ $isEn ? 'Donation Receipt' : 'إيصال تبرع' }}",
                        text: "{{ $isEn ? 'You can verify this official receipt via the link.' : 'يمكنك التحقق من هذا الإيصال الرسمي عبر الرابط.' }}",
                        url: url
                    });
                } else {
                    alert("{{ $isEn ? 'Sharing not supported in this browser.' : 'المشاركة غير مدعومة في هذا المتصفح.' }}");
                }
            }
        </script>
    @endpush
@endsection
```
