@extends('layouts.admin')

@section('title', 'إدارة الإيصالات')
@section('page_title', 'إدارة الإيصالات')

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $statusMeta = function (?string $st) {
            return match ($st) {
                'issued' => ['label' => 'Issued', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'void' => ['label' => 'Void', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                default => ['label' => $st ?: '-', 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
            };
        };
    @endphp

    <div class="mx-auto max-w-7xl space-y-5">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">الإيصالات</h1>
                <p class="text-sm text-slate-500 mt-1">إدارة جميع إيصالات التبرعات، البحث والفلترة وتحميل PDF.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.receipts.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 12a9 9 0 10-3.3 6.9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M21 12v-7m0 7h-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    تحديث
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-slate-200 rounded-[22px] p-4 md:p-5 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">بحث</label>
                    <div class="relative">
                        <span class="absolute {{ $isAr ? 'right-3' : 'left-3' }} top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M11 19a8 8 0 110-16 8 8 0 010 16z" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="بحث برقم الإيصال / الاسم / الإيميل"
                            class="w-full rounded-2xl border border-slate-200 bg-white {{ $isAr ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-3 text-sm
                                   focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    </div>
                </div>

                <div class="w-full lg:w-56">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">الحالة</label>
                    <select name="status"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                        <option value="">كل الحالات</option>
                        <option value="issued" @selected(request('status') === 'issued')>Issued</option>
                        <option value="void" @selected(request('status') === 'void')>Void</option>
                    </select>
                </div>

                <div class="w-full lg:w-56">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">العملة</label>
                    <select name="currency"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                        <option value="">كل العملات</option>
                        {{-- ضع العملات التي تستخدمها فعلياً --}}
                        @foreach (['USD', 'EUR', 'ILS', 'GBP'] as $cur)
                            <option value="{{ $cur }}" @selected(request('currency') === $cur)>{{ $cur }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                        فلترة
                    </button>

                    <a href="{{ route('admin.receipts.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        مسح
                    </a>
                </div>
            </div>

            {{-- Active filters hint --}}
            @if (request('search') || request('status') || request('currency'))
                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-slate-500">الفلاتر الحالية:</span>

                    @if (request('search'))
                        <span class="px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-700">
                            بحث: <span class="font-semibold">{{ request('search') }}</span>
                        </span>
                    @endif

                    @if (request('status'))
                        @php($m = $statusMeta(request('status')))
                        <span class="px-2.5 py-1 rounded-full border {{ $m['cls'] }}">
                            حالة: <span class="font-semibold">{{ $m['label'] }}</span>
                        </span>
                    @endif

                    @if (request('currency'))
                        <span class="px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-700">
                            عملة: <span class="font-semibold">{{ request('currency') }}</span>
                        </span>
                    @endif
                </div>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    العدد المعروض:
                    <span class="font-semibold text-slate-900">{{ $receipts->count() }}</span>
                    <span class="text-slate-400">/</span>
                    الإجمالي:
                    <span class="font-semibold text-slate-900">{{ $receipts->total() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white">
                        <tr class="text-right border-b border-slate-200">
                            <th class="p-4 md:p-5 font-semibold text-slate-700">#</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">رقم الإيصال</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">المتبرع</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">المبلغ</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">الحالة</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">تاريخ الإصدار</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">إجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($receipts as $receipt)
                            @php($m = $statusMeta($receipt->status))
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 md:p-5 text-slate-700 font-mono">#{{ $receipt->id }}</td>

                                <td class="p-4 md:p-5">
                                    <div class="font-mono text-xs text-slate-800 break-all" dir="ltr">
                                        {{ $receipt->receipt_no }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        UUID: <span class="font-mono" dir="ltr">{{ $receipt->uuid }}</span>
                                    </div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <div class="font-semibold text-slate-900">
                                        {{ $receipt->donor_name ?: '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 break-all" dir="ltr">
                                        {{ $receipt->donor_email ?: '—' }}
                                    </div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <div class="font-semibold text-slate-900">
                                        {{ number_format((float) $receipt->amount, 2) }} {{ $receipt->currency }}
                                    </div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $m['cls'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                        {{ $m['label'] }}
                                    </span>
                                </td>

                                <td class="p-4 md:p-5 text-slate-600">
                                    <div class="font-medium text-slate-900">
                                        {{ $receipt->issued_at?->format('Y-m-d') ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $receipt->issued_at?->format('H:i') ?? '' }}
                                    </div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- PDF --}}
                                        @can('receipts.view')
                                            <a href="{{ route('admin.receipts.download', $receipt) }}"
                                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                                                PDF
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"
                                                        stroke="currentColor" stroke-width="2" />
                                                    <path d="M14 3v4h4" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- Verify (public route) --}}
                                        <a href="{{ route('receipt.verify', $receipt->uuid) }}" target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            تحقق
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>

                                        {{-- Regenerate --}}
                                        @can('receipts.create')
                                            <form method="POST" action="{{ route('admin.receipts.regenerate', $receipt) }}"
                                                onsubmit="return confirm('هل تريد إعادة توليد ملف PDF لهذا الإيصال؟');">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold hover:bg-amber-100 transition">
                                                    إعادة توليد
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-10">
                                    <div
                                        class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/60 p-8 text-center">
                                        <div
                                            class="mx-auto w-12 h-12 rounded-2xl bg-white border border-slate-200 grid place-items-center text-slate-400">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                                <path d="M21 12a9 9 0 10-18 0 9 9 0 0018 0z" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-slate-900 font-semibold">لا توجد إيصالات</div>
                                        <div class="mt-1 text-sm text-slate-500">جرّب تعديل الفلاتر أو امسحها لعرض الكل.
                                        </div>
                                        <div class="mt-5">
                                            <a href="{{ route('admin.receipts.index') }}"
                                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                                عرض الكل
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 md:p-5 border-t border-slate-200">
                {{ $receipts->links() }}
            </div>
        </div>
    </div>
@endsection
