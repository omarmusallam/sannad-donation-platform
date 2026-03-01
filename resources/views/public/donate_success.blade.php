@extends('layouts.public')

@section('title', app()->getLocale() === 'en' ? 'Donation Completed' : 'تم إتمام التبرع')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $money = fn($v) => number_format((float) $v, 2);

        $receiptNo = $donation->receipt->receipt_no ?? null;
    @endphp

    <div class="max-w-4xl mx-auto">

        {{-- Success Header --}}
        <div class="card p-10 text-center relative overflow-hidden">

            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-success/10 via-transparent to-transparent"></div>

            <div class="text-5xl font-black text-success mb-4">✔</div>

            <h1 class="text-3xl sm:text-4xl font-black text-text">
                {{ $isEn ? 'Thank You for Your Donation' : 'شكراً لتبرعك' }}
            </h1>

            <p class="text-subtext mt-3 max-w-xl mx-auto leading-relaxed">
                {{ $isEn
                    ? 'An official receipt has been issued and can be verified securely using the link below.'
                    : 'تم إصدار إيصال رسمي ويمكن التحقق منه بأمان عبر الرابط أدناه.' }}
            </p>

        </div>

        {{-- Receipt Card --}}
        <div class="mt-8 card p-8">

            <div class="grid md:grid-cols-2 gap-8 items-start">

                <div>
                    <div class="text-sm text-subtext">
                        {{ $isEn ? 'Receipt Number' : 'رقم الإيصال' }}
                    </div>

                    <div class="font-mono text-xl font-bold text-text mt-1">
                        {{ $receiptNo ?? '—' }}
                    </div>

                    <div class="mt-6 text-sm text-subtext">
                        {{ $isEn ? 'Amount Paid' : 'المبلغ المدفوع' }}
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
                </div>

                {{-- QR --}}
                @if (!empty($receiptUrl))
                    <div class="flex flex-col items-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($receiptUrl) }}"
                            class="rounded-xl border border-border shadow-sm">
                        <div class="text-xs text-subtext mt-3 text-center">
                            {{ $isEn ? 'Scan to verify this receipt' : 'امسح للتحقق من الإيصال' }}
                        </div>
                    </div>
                @endif

            </div>

            {{-- Actions --}}
            @if (!empty($receiptUrl))
                <div class="mt-8 flex flex-wrap gap-3">

                    <a href="{{ $receiptUrl }}" class="btn btn-primary">
                        {{ $isEn ? 'Verify Receipt' : 'التحقق من الإيصال' }}
                    </a>

                    <button class="btn btn-secondary" onclick="copyText('{{ $receiptUrl }}', this)">
                        {{ $isEn ? 'Copy Link' : 'نسخ الرابط' }}
                    </button>

                    <button class="btn btn-secondary" onclick="shareReceipt()">
                        {{ $isEn ? 'Share' : 'مشاركة' }}
                    </button>

                    @if (!empty($downloadUrl))
                        <a href="{{ $downloadUrl }}" class="btn btn-secondary">
                            {{ $isEn ? 'Download PDF' : 'تحميل PDF' }}
                        </a>
                    @endif

                </div>
            @endif

        </div>

        {{-- Trust Section --}}
        <div class="mt-8 card-muted p-6 text-sm text-subtext leading-relaxed space-y-2">
            <div>✔
                {{ $isEn ? 'All receipts are digitally issued and verifiable.' : 'جميع الإيصالات موثقة رقمياً وقابلة للتحقق.' }}
            </div>
            <div>✔ {{ $isEn ? 'Download links are temporarily signed for security.' : 'رابط التحميل مؤقت لأسباب أمنية.' }}
            </div>
            <div>✔ {{ $isEn ? 'You may safely share this receipt link.' : 'يمكنك مشاركة رابط الإيصال بأمان.' }}</div>
        </div>

    </div>

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

@endsection
