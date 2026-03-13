@extends('layouts.public')

@section('title', 'USDT (TRC20)')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $wallet = config('services.crypto.usdt_trc20_wallet');
        $amountText = number_format((float) $donation->amount, 2) . ' ' . $donation->currency;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="card p-6 sm:p-8 space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-text">
                    {{ $isEn ? 'Donate with USDT (TRC20)' : 'التبرع عبر USDT (TRC20)' }}
                </h1>

                <p class="mt-2 text-subtext">
                    {{ $isEn ? 'Send only via TRC20 network to the address below.' : 'أرسل فقط عبر شبكة TRC20 إلى العنوان التالي.' }}
                </p>
            </div>

            <div class="card-muted p-4">
                <div class="text-xs text-subtext">{{ $isEn ? 'Campaign amount' : 'مبلغ التبرع' }}</div>
                <div class="font-black text-text mt-1">{{ $amountText }}</div>
            </div>

            <div class="card-muted p-4">
                <div class="text-xs text-subtext">{{ $isEn ? 'Wallet address' : 'عنوان المحفظة' }}</div>
                <div class="font-mono text-sm break-all text-text mt-2">{{ $wallet }}</div>
            </div>

            <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm leading-7 text-amber-900">
                {{ $isEn
                    ? 'Important: Send USDT on TRC20 only. Sending on another network may result in permanent loss.'
                    : 'مهم: أرسل USDT عبر شبكة TRC20 فقط. الإرسال عبر شبكة أخرى قد يؤدي إلى فقدان المبلغ نهائيًا.' }}
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" class="btn btn-primary" onclick="copyWallet()">
                    {{ $isEn ? 'Copy wallet address' : 'نسخ عنوان المحفظة' }}
                </button>

                <a href="{{ locale_route('donate.success', ['donation' => $donation->id]) }}" class="btn btn-secondary">
                    {{ $isEn ? 'I sent the transfer' : 'لقد أرسلت التحويل' }}
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyWallet() {
                navigator.clipboard.writeText(@json($wallet));
            }
        </script>
    @endpush
@endsection
