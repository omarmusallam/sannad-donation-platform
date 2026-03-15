@extends('layouts.admin')

@section('title', 'تفاصيل التبرع')
@section('page_title', 'تفاصيل التبرع')

@section('content')
    @php
        $statusMeta = function (?string $st) {
            return match ($st) {
                'paid' => ['label' => 'مدفوع', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'pending' => ['label' => 'قيد الانتظار', 'cls' => 'bg-amber-50 text-amber-800 border-amber-200'],
                'pending_crypto_review' => [
                    'label' => 'بانتظار مراجعة الكريبتو',
                    'cls' => 'bg-violet-50 text-violet-700 border-violet-200',
                ],
                'failed' => ['label' => 'فشل', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                'refunded' => ['label' => 'مسترجع', 'cls' => 'bg-sky-50 text-sky-700 border-sky-200'],
                default => ['label' => $st ?: '-', 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
            };
        };

        $meta = $statusMeta($donation->status);

        $donorName = $donation->is_anonymous ? 'مجهول' : ($donation->donor_name ?: '-');
        $donorPrivacy = $donation->is_anonymous ? 'تبرع مجهول' : 'اسم ظاهر';

        $receipt = $donation->receipt ?? null;
        $hasReceipt = (bool) $receipt;
        $canGenerateReceiptByStatus = $donation->status === 'paid';

        $isCrypto = $donation->payment_method === 'usdt_trc20';
        $canReviewCrypto = $isCrypto && in_array($donation->status, ['pending', 'pending_crypto_review'], true);
    @endphp

    <div class="mx-auto max-w-6xl">
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
            <div class="lg:col-span-8 space-y-6">
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
                                {{ $donation->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>

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
                                <div class="mt-1 font-semibold text-slate-900 font-mono break-all" dir="ltr">
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

                @if ($isCrypto)
                    <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <div class="text-base font-semibold text-slate-900">بيانات تحويل USDT</div>
                                <div class="text-xs text-slate-500 mt-1">تفاصيل التحويل المرسل من المتبرع.</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500">الشبكة</div>
                                <div class="mt-1 font-semibold text-slate-900">
                                    {{ strtoupper($donation->crypto_network ?: '-') }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200">
                                <div class="text-xs text-slate-500">وقت إرسال البيانات</div>
                                <div class="mt-1 font-semibold text-slate-900">
                                    {{ $donation->crypto_submitted_at?->format('Y-m-d H:i') ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                                <div class="text-xs text-slate-500">عنوان المحفظة المستلمة</div>
                                <div class="mt-1 font-semibold text-slate-900 font-mono break-all" dir="ltr">
                                    {{ $donation->crypto_wallet_address ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                                <div class="text-xs text-slate-500">Tx Hash</div>
                                <div class="mt-1 font-semibold text-slate-900 font-mono break-all" dir="ltr">
                                    {{ $donation->crypto_tx_hash ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                                <div class="text-xs text-slate-500">المحفظة المرسلة</div>
                                <div class="mt-1 font-semibold text-slate-900 font-mono break-all" dir="ltr">
                                    {{ $donation->crypto_sender_wallet ?: '-' }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                                <div class="text-xs text-slate-500">ملاحظة الإدارة</div>
                                <div class="mt-1 font-semibold text-slate-900 whitespace-pre-line">
                                    {{ $donation->admin_payment_note ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="text-base font-semibold text-slate-900">الحملة</div>
                            <div class="text-xs text-slate-500 mt-1">الحملة المرتبطة بهذا التبرع.</div>
                        </div>

                        @if ($donation->campaign)
                            @can('campaigns.edit')
                                <a href="{{ route('admin.campaigns.edit', $donation->campaign) }}"
                                    class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    إدارة الحملة
                                </a>
                            @endcan
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70">
                        <div class="font-semibold text-slate-900">
                            {{ $donation->campaign?->title_ar ?? ($donation->campaign?->title ?? '-') }}
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Campaign ID: <span class="font-mono">{{ $donation->campaign_id ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 space-y-6">
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

                <div class="bg-white border border-slate-200 rounded-[28px] p-5 md:p-6 shadow-sm">
                    <div class="text-base font-semibold text-slate-900">الحالة والإجراءات</div>
                    <div class="text-xs text-slate-500 mt-1 mb-4">اعتماد أو رفض دفعات الكريبتو عند الحاجة.</div>

                    <div class="flex flex-wrap items-center gap-2 mb-4">
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

                    @if ($canReviewCrypto)
                        <div
                            class="rounded-2xl border border-violet-200 bg-violet-50 p-4 text-sm text-violet-800 leading-6 mb-4">
                            هذه العملية دفعة USDT بانتظار مراجعة الإدارة. بعد الاعتماد سيتم تحويلها إلى <b>مدفوع</b> وإنشاء
                            الإيصال تلقائيًا.
                        </div>

                        <div class="space-y-4">
                            <form method="POST" action="{{ route('admin.donations.confirmCrypto', $donation) }}"
                                class="space-y-3">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">ملاحظة عند الاعتماد
                                        (اختياري)</label>
                                    <textarea name="admin_payment_note" rows="3"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                        placeholder="مثال: تم التحقق من Tx Hash ومطابقة المبلغ على شبكة TRC20">{{ old('admin_payment_note', $donation->admin_payment_note) }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-2xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                                    تأكيد دفعة USDT
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.donations.rejectCrypto', $donation) }}"
                                class="space-y-3">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">ملاحظة عند الرفض
                                        (اختياري)</label>
                                    <textarea name="admin_payment_note" rows="3"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                                        placeholder="مثال: Tx Hash غير صحيح أو لم يتم العثور على التحويل"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-2xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition">
                                    رفض دفعة USDT
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 leading-6">
                            لا توجد إجراءات مراجعة متاحة لهذه العملية حاليًا.
                        </div>
                    @endif
                </div>

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
                            @can('receipts.view')
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
                                <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 leading-6">
                                    لا تملك صلاحية عرض بيانات الإيصال.
                                </div>
                            @endcan
                        @else
                            @can('receipts.create')
                                @if ($canGenerateReceiptByStatus)
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
                            @else
                                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 leading-6">
                                    لا تملك صلاحية توليد الإيصالات.
                                </div>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
