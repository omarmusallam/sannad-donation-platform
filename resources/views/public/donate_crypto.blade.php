@extends('layouts.public')

@section('title', 'USDT (TRC20)')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $wallet = config('services.crypto.usdt_trc20_wallet');
        $amountText = number_format((float) $donation->amount, 2) . ' ' . $donation->currency;
        $submitUrl = locale_route('donate.crypto.submit', ['donation' => $donation->public_id]);
        $statusUrl = locale_route('donate.success', ['donation' => $donation->public_id]);
    @endphp

    <div class="max-w-5xl mx-auto space-y-8">
        <section class="section-shell p-7 sm:p-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="eyebrow">{{ $isEn ? 'USDT transfer instructions' : 'تعليمات تحويل USDT' }}</div>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $isEn ? 'Complete your donation with USDT (TRC20)' : 'أكمل تبرعك عبر USDT (TRC20)' }}</h1>
                    <p class="mt-3 text-subtext leading-relaxed">{{ $isEn ? 'Send the exact donation amount through the TRC20 network only, then submit the blockchain transaction hash so the transfer can be reviewed.' : 'أرسل مبلغ التبرع عبر شبكة TRC20 فقط، ثم أرسل رقم العملية على البلوكشين حتى تتم مراجعة التحويل.' }}</p>
                </div>

                <div class="rounded-3xl border border-amber-200 bg-amber-50/70 p-5 sm:p-6 min-w-[280px]">
                    <div class="text-sm text-subtext">{{ $isEn ? 'Important rule' : 'قاعدة مهمة' }}</div>
                    <div class="mt-2 text-xl font-black text-amber-700">{{ $isEn ? 'TRC20 only' : 'TRC20 فقط' }}</div>
                    <div class="mt-3 text-sm text-amber-900 leading-relaxed">{{ $isEn ? 'Sending from a different network may permanently lose the funds.' : 'الإرسال من شبكة مختلفة قد يؤدي إلى فقدان المبلغ بشكل دائم.' }}</div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6 sm:p-8 space-y-6">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Donation amount' : 'مبلغ التبرع' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ $amountText }}</div>
                    </div>

                    <div class="kpi-tile">
                        <div class="text-xs text-subtext">{{ $isEn ? 'Network' : 'الشبكة' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">USDT (TRC20)</div>
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Wallet address' : 'عنوان المحفظة' }}</div>
                    <div class="mt-3 rounded-2xl border border-border bg-surface px-4 py-3 font-mono text-sm text-text break-all">{{ $wallet ?: ($isEn ? 'Wallet is not configured yet.' : 'لم يتم ضبط المحفظة بعد.') }}</div>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <button type="button" class="btn btn-primary" onclick="copyWallet(this)" @disabled(empty($wallet))>{{ $isEn ? 'Copy wallet address' : 'نسخ عنوان المحفظة' }}</button>
                        <a href="{{ $statusUrl }}" class="btn btn-secondary">{{ $isEn ? 'Open tracking page' : 'فتح صفحة التتبع' }}</a>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="card-muted p-5 text-sm text-subtext leading-relaxed">
                        {{ $isEn ? 'Use the exact address shown above and verify the network inside your wallet before sending.' : 'استخدم العنوان الظاهر أعلاه بدقة وتأكد من الشبكة داخل محفظتك قبل الإرسال.' }}
                    </div>
                    <div class="card-muted p-5 text-sm text-subtext leading-relaxed">
                        {{ $isEn ? 'Keep your tracking link. It lets you return later even if you are not signed in.' : 'احتفظ برابط التتبع. يتيح لك العودة لاحقًا حتى لو لم تكن مسجل دخول.' }}
                    </div>
                </div>

                <form method="POST" action="{{ $submitUrl }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Transaction hash (Tx Hash)' : 'رقم العملية (Tx Hash)' }}</label>
                        <input type="text" name="crypto_tx_hash" value="{{ old('crypto_tx_hash') }}" class="input" placeholder="{{ $isEn ? 'Paste the blockchain transaction hash here' : 'ألصق رقم العملية هنا' }}">
                        @error('crypto_tx_hash')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black mb-2 text-text">{{ $isEn ? 'Sender wallet (optional)' : 'المحفظة المرسلة (اختياري)' }}</label>
                        <input type="text" name="crypto_sender_wallet" value="{{ old('crypto_sender_wallet') }}" class="input" placeholder="{{ $isEn ? 'Sender wallet address' : 'عنوان المحفظة المرسلة' }}">
                        @error('crypto_sender_wallet')
                            <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rounded-2xl border border-border bg-muted p-5 text-sm text-subtext leading-relaxed">
                        {{ $isEn ? 'Submit the transaction hash only after you send the transfer. The status page will show the final result and receipt access after review.' : 'أرسل رقم العملية فقط بعد تنفيذ التحويل. ستعرض صفحة الحالة النتيجة النهائية والوصول إلى الإيصال بعد المراجعة.' }}
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="btn btn-primary">{{ $isEn ? 'I sent the transfer' : 'لقد أرسلت التحويل' }}</button>
                        <a href="{{ locale_route('donate') }}" class="btn btn-secondary">{{ $isEn ? 'Back to donate page' : 'العودة إلى صفحة التبرع' }}</a>
                    </div>
                </form>
            </div>

            <aside class="space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Checklist' : 'قائمة التحقق' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>• {{ $isEn ? 'Network must be TRC20.' : 'يجب أن تكون الشبكة TRC20.' }}</li>
                        <li>• {{ $isEn ? 'Amount should match the requested donation.' : 'يجب أن يطابق المبلغ قيمة التبرع المطلوبة.' }}</li>
                        <li>• {{ $isEn ? 'Keep the status link before leaving the page.' : 'احتفظ برابط الحالة قبل مغادرة الصفحة.' }}</li>
                    </ul>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Guest donor note' : 'ملاحظة للضيف' }}</div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'If you are not signed in, the tracking page is the safest way to return later and access the final receipt status.' : 'إذا لم تكن مسجل دخول، فإن صفحة التتبع هي الوسيلة الأكثر أمانًا للعودة لاحقًا والوصول إلى حالة الإيصال النهائية.' }}</div>
                </div>
            </aside>
        </section>
    </div>

    @push('scripts')
        <script>
            function copyWallet(button) {
                navigator.clipboard.writeText(@js($wallet));
                const original = button.innerText;
                button.innerText = @js($isEn ? 'Copied ✓' : 'تم النسخ ✓');
                setTimeout(() => button.innerText = original, 2000);
            }
        </script>
    @endpush
@endsection
