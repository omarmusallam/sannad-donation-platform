@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Crypto Transfer Submitted' : 'تم إرسال التحويل')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $amountText = number_format((float) $donation->amount, 2) . ' ' . $donation->currency;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="card p-6 sm:p-8 space-y-6">
            <div class="text-center">
                <div class="text-5xl mb-4">⏳</div>

                <h1 class="text-2xl sm:text-3xl font-black text-text">
                    {{ $isEn ? 'Transfer Submitted Successfully' : 'تم إرسال بيانات التحويل بنجاح' }}
                </h1>

                <p class="mt-3 text-subtext">
                    {{ $isEn
                        ? 'Your USDT transfer is now pending review. The receipt will be issued after verification.'
                        : 'تحويل USDT الآن بانتظار المراجعة. سيتم إصدار الإيصال بعد التحقق.' }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="card-muted p-4">
                    <div class="text-xs text-subtext">{{ $isEn ? 'Donation amount' : 'مبلغ التبرع' }}</div>
                    <div class="font-black text-text mt-2">{{ $amountText }}</div>
                </div>

                <div class="card-muted p-4">
                    <div class="text-xs text-subtext">{{ $isEn ? 'Status' : 'الحالة' }}</div>
                    <div class="font-black text-text mt-2">
                        {{ $isEn ? 'Pending review' : 'بانتظار المراجعة' }}
                    </div>
                </div>
            </div>

            <div class="card-muted p-4">
                <div class="text-xs text-subtext">{{ $isEn ? 'Tx Hash' : 'رقم العملية' }}</div>
                <div class="font-mono text-sm break-all text-text mt-2">
                    {{ $donation->crypto_tx_hash ?: '—' }}
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ locale_route('donor.donations') }}" class="btn btn-primary">
                    {{ $isEn ? 'My donations' : 'تبرعاتي' }}
                </a>

                <a href="{{ locale_route('donate') }}" class="btn btn-secondary">
                    {{ $isEn ? 'Donate again' : 'تبرع مرة أخرى' }}
                </a>
            </div>
        </div>
    </div>
@endsection
