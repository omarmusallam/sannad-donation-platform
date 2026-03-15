@extends('layouts.public')

@section('title', 'USDT (TRC20)')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $wallet = config('services.crypto.usdt_trc20_wallet');
        $amountText = number_format((float) $donation->amount, 2) . ' ' . $donation->currency;
        $submitUrl = locale_route('donate.crypto.submit', ['donation' => $donation->id]);
        $qrValue = "Tether (USDT)\nNetwork: TRC20\nAddress: {$wallet}";
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="card p-6 sm:p-8 space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-text">
                    {{ $isEn ? 'Donate with USDT (TRC20)' : 'التبرع عبر USDT (TRC20)' }}
                </h1>

                <p class="mt-2 text-subtext">
                    {{ $isEn
                        ? 'Send only through the TRC20 network to the wallet below, then submit the transaction hash.'
                        : 'أرسل فقط عبر شبكة TRC20 إلى المحفظة التالية، ثم أدخل رقم العملية (Tx Hash).' }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="card-muted p-4">
                    <div class="text-xs text-subtext">{{ $isEn ? 'Donation amount' : 'مبلغ التبرع' }}</div>
                    <div class="font-black text-text mt-2">{{ $amountText }}</div>
                </div>

                <div class="card-muted p-4">
                    <div class="text-xs text-subtext">{{ $isEn ? 'Network' : 'الشبكة' }}</div>
                    <div class="font-black text-text mt-2">USDT (TRC20)</div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 items-start">
                <div class="card-muted p-4">
                    <div class="text-xs text-subtext mb-3">{{ $isEn ? 'Wallet address' : 'عنوان المحفظة' }}</div>
                    <div class="font-mono text-sm break-all text-text">{{ $wallet }}</div>

                    <button type="button" class="btn btn-primary mt-4" onclick="copyWallet()">
                        {{ $isEn ? 'Copy wallet address' : 'نسخ عنوان المحفظة' }}
                    </button>
                </div>

                <div class="flex flex-col items-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($qrValue) }}"
                        alt="USDT TRC20 QR" class="rounded-2xl border border-border bg-white p-2">
                    <div class="text-xs text-subtext mt-3 text-center">
                        {{ $isEn ? 'Scan to copy the wallet address' : 'امسح لنسخ عنوان المحفظة' }}
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-300/30 bg-amber-500/10 p-4 text-sm leading-7 text-amber-200">
                {{ $isEn
                    ? 'Important: Send USDT on TRC20 only. Sending to this address from another network may result in permanent loss.'
                    : 'مهم: أرسل USDT عبر شبكة TRC20 فقط. الإرسال إلى هذا العنوان من شبكة أخرى قد يؤدي إلى فقدان المبلغ نهائيًا.' }}
            </div>

            <form method="POST" action="{{ $submitUrl }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-black mb-2 text-text">
                        {{ $isEn ? 'Transaction hash (Tx Hash)' : 'رقم العملية (Tx Hash)' }}
                    </label>

                    <input type="text" name="crypto_tx_hash" value="{{ old('crypto_tx_hash') }}" class="input"
                        placeholder="{{ $isEn ? 'Paste the blockchain transaction hash here' : 'ألصق رقم العملية هنا' }}">

                    @error('crypto_tx_hash')
                        <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-black mb-2 text-text">
                        {{ $isEn ? 'Sender wallet (optional)' : 'المحفظة المرسلة (اختياري)' }}
                    </label>

                    <input type="text" name="crypto_sender_wallet" value="{{ old('crypto_sender_wallet') }}"
                        class="input" placeholder="{{ $isEn ? 'Sender wallet address' : 'عنوان المحفظة المرسلة' }}">

                    @error('crypto_sender_wallet')
                        <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEn ? 'I sent the transfer' : 'لقد أرسلت التحويل' }}
                    </button>

                    <a href="{{ locale_route('donate') }}" class="btn btn-secondary">
                        {{ $isEn ? 'Back to donate page' : 'العودة لصفحة التبرع' }}
                    </a>
                </div>
            </form>
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
