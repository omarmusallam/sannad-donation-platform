<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التحقق من الإيصال - {{ config('app.name') }}</title>

    {{-- إذا تستخدم Tailwind في مشروعك، ممتاز. إن لم يكن موجود، أخبرني لأعطيك CSS خام --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-900">
    @php
        $isValid = $receipt->status === 'issued';
        $statusLabel = $isValid ? 'إيصال صالح' : 'إيصال ملغي';
        $statusCls = $isValid
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';

        $campaignTitle = $receipt->donation?->campaign?->title_ar ?? null;
    @endphp

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="p-6 md:p-8 border-b border-slate-200 bg-gradient-to-l from-slate-50 to-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-500">التحقق من الإيصال</div>
                            <h1 class="text-2xl md:text-3xl font-extrabold mt-1">
                                {{ config('app.name', 'GazaSannad') }}
                            </h1>

                            <div
                                class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-sm font-bold {{ $statusCls }}">
                                <span class="w-2 h-2 rounded-full bg-current opacity-60"></span>
                                {{ $statusLabel }}
                            </div>
                        </div>

                        <div class="text-left">
                            <div class="text-xs text-slate-500">Receipt No</div>
                            <div class="font-mono text-sm font-semibold">{{ $receipt->receipt_no }}</div>
                        </div>
                    </div>

                    <p class="text-slate-500 text-sm mt-4 leading-relaxed">
                        هذه الصفحة تؤكد صحة الإيصال الصادر من النظام. إذا كانت الحالة "ملغي" فلا يعتبر صالحًا.
                    </p>
                </div>

                {{-- Body --}}
                <div class="p-6 md:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-xs text-slate-500">المتبرع</div>
                            <div class="mt-1 font-bold">
                                {{ $receipt->donor_name ?: '—' }}
                            </div>
                            <div class="text-sm text-slate-600 mt-1 break-words">
                                {{ $receipt->donor_email ?: '—' }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-xs text-slate-500">المبلغ</div>
                            <div class="mt-1 text-2xl font-extrabold">
                                {{ number_format((float) $receipt->amount, 2) }}
                                <span class="text-base font-bold text-slate-600">{{ $receipt->currency }}</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                تاريخ الإصدار: {{ $receipt->issued_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Campaign (optional) --}}
                    @if ($campaignTitle)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                            <div class="text-xs text-slate-500">الحملة</div>
                            <div class="mt-1 font-bold">{{ $campaignTitle }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                Donation ID: <span class="font-mono">{{ $receipt->donation_id }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col md:flex-row gap-3">
                        {{-- ✅ تحميل عام (اختياري) --}}
                        @if ($receipt->pdf_path)
                            <a href="{{ route('receipt.download.public', $receipt->uuid) }}"
                                class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                                تحميل PDF
                            </a>
                        @endif

                        <a href="{{ url('/') }}"
                            class="inline-flex items-center justify-center px-5 py-3 rounded-2xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50 transition">
                            العودة للموقع
                        </a>
                    </div>

                    {{-- Security Note --}}
                    <div class="text-xs text-slate-500 leading-relaxed">
                        رمز التحقق: <span class="font-mono">{{ $receipt->uuid }}</span>
                        <br>
                        إذا كانت لديك شكوك حول الإيصال، تواصل مع إدارة المنصة.
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 md:p-8 border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
                    © {{ date('Y') }} {{ config('app.name', 'GazaSannad') }} — نظام الإيصالات والتحقق
                </div>
            </div>
        </div>
    </div>
</body>

</html>
