@extends('layouts.admin')

@section('title', 'تفاصيل التبرع')
@section('page_title', 'تفاصيل التبرع')

@section('content')
    @php
        $statusMeta = function (?string $st) {
            return match ($st) {
                'paid' => ['label' => 'مدفوع', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'pending' => ['label' => 'قيد الانتظار', 'cls' => 'bg-amber-50 text-amber-800 border-amber-200'],
                'failed' => ['label' => 'فشل', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                'refunded' => ['label' => 'مسترجع', 'cls' => 'bg-sky-50 text-sky-700 border-sky-200'],
                default => ['label' => $st ?: '-', 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
            };
        };

        $meta = $statusMeta($donation->status);

        $kv = function ($label, $value, $mono = false, $muted = false) {
            return [
                'label' => $label,
                'value' => $value,
                'mono' => $mono,
                'muted' => $muted,
            ];
        };

        $donorName = $donation->is_anonymous ? 'مجهول' : ($donation->donor_name ?: '-');
        $donorPrivacy = $donation->is_anonymous ? 'تبرع مجهول' : 'اسم ظاهر';

        $receipt = $donation->receipt ?? null;
        $hasReceipt = (bool) $receipt;
        $canGenerateReceipt = $donation->status === 'paid';
    @endphp

    <div class="mx-auto max-w-6xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                            تفاصيل التبرع <span class="text-slate-400">#{{ $donation->id }}</span>
                        </h1>

                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $meta['cls'] }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            {{ $meta['label'] }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-500 mt-2">
                        معلومات الدفع، المتبرع، والحملة المرتبطة بالتبرع.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.donations.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition w-full sm:w-auto">
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Main --}}
            <div class="lg:col-span-8 space-y-6">
                {{-- Donation summary (top quick cards) --}}
                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="flex flex-col gap-1 mb-5">
                        <div class="text-base font-semibold text-slate-900">بيانات التبرع</div>
                        <div class="text-xs text-slate-500">القيمة والعملة وتواريخ الإنشاء/الدفع.</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-xs text-slate-500">المبلغ</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-xs text-slate-500">طريقة الدفع</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $donation->payment_method ?: '-' }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="text-xs text-slate-500">Paid at</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $donation->paid_at?->format('Y-m-d H:i') ?: '-' }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="text-xs text-slate-500">Created</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $donation->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Extra meta row (optional useful for admins) --}}
                    <div class="mt-5 pt-5 border-t border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500">Provider</div>
                                <div class="mt-1 font-semibold text-slate-900">
                                    {{ $donation->provider ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500">Reference</div>
                                <div class="mt-1 font-semibold text-slate-900 font-mono break-all">
                                    {{ $donation->provider_ref ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500">Fees</div>
                                <div class="mt-1 font-semibold text-slate-900">
                                    {{ number_format((float) ($donation->fees ?? 0), 2) }} {{ $donation->currency }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campaign --}}
                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="text-base font-semibold text-slate-900">الحملة</div>
                            <div class="text-xs text-slate-500 mt-1">الحملة المرتبطة بهذا التبرع.</div>
                        </div>

                        @if ($donation->campaign)
                            <a href="{{ route('admin.campaigns.edit', $donation->campaign) }}"
                                class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                إدارة الحملة
                            </a>
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70">
                        <div class="font-semibold text-slate-900">
                            {{ $donation->campaign->title_ar ?? '-' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Campaign ID: <span class="font-mono">{{ $donation->campaign_id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Donor --}}
                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-semibold text-slate-900">المتبرع</div>
                            <div class="text-xs text-slate-500 mt-1">الاسم والبريد وحالة الخصوصية.</div>
                        </div>

                        <span
                            class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $donation->is_anonymous ? 'bg-slate-50 text-slate-700 border-slate-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                            {{ $donation->is_anonymous ? 'مجهول' : 'معلن' }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="p-4 rounded-2xl border border-slate-200">
                            <div class="text-xs text-slate-500">الاسم</div>
                            <div class="mt-1 font-semibold text-slate-900 break-words">
                                {{ $donorName }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200">
                            <div class="text-xs text-slate-500">الإيميل</div>
                            <div class="mt-1 font-semibold text-slate-900 break-words" dir="ltr">
                                {{ $donation->donor_email ?: '-' }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-xs text-slate-500">الخصوصية</div>
                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $donorPrivacy }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="text-base font-semibold text-slate-900">الحالة</div>
                    <div class="text-xs text-slate-500 mt-1 mb-4">تصنيف حالة الدفع بصريًا.</div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $meta['cls'] }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            {{ $meta['label'] }}
                        </span>

                        @if ($donation->refunded_at)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold bg-sky-50 text-sky-700 border-sky-200">
                                Refunded: {{ $donation->refunded_at?->format('Y-m-d') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Receipt --}}
                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-semibold text-slate-900">الإيصال</div>
                            <div class="text-xs text-slate-500 mt-1">توليد/تحميل/تحقق من الإيصال.</div>
                        </div>

                        <span
                            class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $hasReceipt ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                            {{ $hasReceipt ? 'جاهز' : 'غير موجود' }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm">
                        @if ($hasReceipt)
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-xs text-slate-500">رقم الإيصال</div>
                                        <div class="mt-1 font-semibold text-slate-900 font-mono break-all" dir="ltr">
                                            {{ $receipt->receipt_no }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-xs text-slate-500">تاريخ الإصدار</div>
                                        <div class="mt-1 font-semibold text-slate-900" dir="ltr">
                                            {{ $receipt->issued_at?->format('Y-m-d H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-xs text-slate-500">الحالة</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $receipt->status ?? '-' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-xs text-slate-500">PDF</div>
                                        <div class="mt-1 font-semibold text-slate-900">
                                            {{ $receipt->pdf_path ? 'متوفر' : 'غير متوفر' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <a href="{{ route('admin.receipts.download', $receipt) }}"
                                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                    تحميل PDF
                                </a>

                                <a href="{{ route('receipt.verify', $receipt->uuid) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                                    فتح التحقق
                                </a>
                            </div>

                            {{-- Optional: quick copy verify url --}}
                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500 mb-2">رابط التحقق</div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-mono text-slate-700 break-all" dir="ltr">
                                            {{ route('receipt.verify', $receipt->uuid) }}
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="shrink-0 inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                        onclick="navigator.clipboard?.writeText(@js(route('receipt.verify', $receipt->uuid)))">
                                        نسخ
                                    </button>
                                </div>
                            </div>
                        @else
                            @if ($canGenerateReceipt)
                                <form method="POST" action="{{ route('admin.donations.generateReceipt', $donation) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                                        توليد إيصال
                                    </button>
                                </form>

                                <div class="text-xs text-slate-500 leading-6">
                                    سيتم إنشاء إيصال رسمي وربطه بالتبرع تلقائيًا.
                                </div>
                            @else
                                <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 leading-6">
                                    لا يمكن توليد إيصال لأن حالة التبرع ليست <b>مدفوع</b>.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
