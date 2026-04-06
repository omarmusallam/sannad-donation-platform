@extends('layouts.admin')

@section('title', app()->isLocale('ar') ? 'التبرعات' : 'Donations')
@section('page_title', app()->isLocale('ar') ? 'التبرعات' : 'Donations')

@section('content')
    @php
        $isAr = app()->isLocale('ar');
        $t = fn(string $ar, string $en) => $isAr ? $ar : $en;

        $statusMeta = function (?string $st) use ($t) {
            return match ($st) {
                'paid' => ['label' => $t('مدفوع', 'Paid'), 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'pending' => ['label' => $t('قيد الانتظار', 'Pending'), 'cls' => 'bg-amber-50 text-amber-800 border-amber-200'],
                'pending_crypto_review' => [
                    'label' => $t('بانتظار مراجعة الكريبتو', 'Pending crypto review'),
                    'cls' => 'bg-violet-50 text-violet-700 border-violet-200',
                ],
                'failed' => ['label' => $t('فشل', 'Failed'), 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                'refunded' => ['label' => $t('مسترجع', 'Refunded'), 'cls' => 'bg-sky-50 text-sky-700 border-sky-200'],
                default => ['label' => $st ?: '-', 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
            };
        };

        $statusOptions = [
            'pending' => $t('قيد الانتظار', 'Pending'),
            'pending_crypto_review' => $t('بانتظار مراجعة الكريبتو', 'Pending crypto review'),
            'paid' => $t('مدفوع', 'Paid'),
            'failed' => $t('فشل', 'Failed'),
            'refunded' => $t('مسترجع', 'Refunded'),
        ];
    @endphp

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $t('التبرعات', 'Donations') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $t('استعرض التبرعات وفلترتها بسرعة وبشكل واضح.', 'Review and filter donations quickly with a clear workflow.') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <form class="bg-white border border-slate-200 rounded-[22px] p-4 md:p-5 mb-4 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-600 mb-2">{{ $t('بحث', 'Search') }}</label>
                <div class="relative">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M11 19a8 8 0 110-16 8 8 0 010 16z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </span>
                    <input name="search" value="{{ request('search') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white pr-10 pl-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                        placeholder="{{ $t('بحث بالاسم / الإيميل / ID / Tx Hash', 'Search by name / email / ID / Tx Hash') }}">
                </div>
            </div>

            <div class="w-full lg:w-64">
                <label class="block text-xs font-semibold text-slate-600 mb-2">{{ $t('الحالة', 'Status') }}</label>
                <select name="status"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    <option value="">{{ $t('كل الحالات', 'All statuses') }}</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    {{ $t('تصفية', 'Filter') }}
                </button>

                <a href="{{ route('admin.donations.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    {{ $t('مسح', 'Reset') }}
                </a>
            </div>
        </div>

        @if (request('search') || request('status'))
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-500">{{ $t('الفلاتر الحالية:', 'Active filters:') }}</span>

                @if (request('search'))
                    <span class="px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-700">
                        {{ $t('بحث', 'Search') }}: <span class="font-semibold">{{ request('search') }}</span>
                    </span>
                @endif

                @if (request('status'))
                    @php($m = $statusMeta(request('status')))
                    <span class="px-2.5 py-1 rounded-full border {{ $m['cls'] }}">
                        {{ $t('حالة', 'Status') }}: <span class="font-semibold">{{ $m['label'] }}</span>
                    </span>
                @endif
            </div>
        @endif
    </form>

    <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
        <div class="p-4 md:p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
            <div class="text-sm text-slate-600">
                {{ $t('العدد المعروض', 'Shown') }}:
                <span class="font-semibold text-slate-900">{{ $donations->count() }}</span>
                <span class="text-slate-400">/</span>
                {{ $t('الإجمالي', 'Total') }}:
                <span class="font-semibold text-slate-900">{{ $donations->total() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white">
                    <tr class="text-{{ $isAr ? 'right' : 'left' }} border-b border-slate-200">
                        <th class="p-4 md:p-5 font-semibold text-slate-700">ID</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('الحملة', 'Campaign') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('المتبرع', 'Donor') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('المبلغ', 'Amount') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('طريقة الدفع', 'Payment method') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('الحالة', 'Status') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('التاريخ', 'Date') }}</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">{{ $t('عرض', 'View') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($donations as $d)
                        @php($meta = $statusMeta($d->status))
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 md:p-5 font-mono text-slate-700">
                                #{{ $d->id }}
                            </td>

                            <td class="p-4 md:p-5">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900 truncate">
                                        {{ $isAr ? ($d->campaign?->title_ar ?? ($d->campaign?->title ?? '—')) : ($d->campaign?->title ?? ($d->campaign?->title_ar ?? '—')) }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        Campaign ID: <span class="font-mono">{{ $d->campaign_id ?? '—' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 md:p-5">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900">
                                        {{ $d->is_anonymous ? $t('مجهول', 'Anonymous') : ($d->donor_name ?: '-') }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 truncate" dir="ltr">
                                        {{ $d->donor_email ?: '—' }}
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 md:p-5">
                                <div class="font-semibold text-slate-900">
                                    {{ number_format((float) $d->amount, 2) }} {{ $d->currency }}
                                </div>
                            </td>

                            <td class="p-4 md:p-5">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700">
                                    {{ $d->payment_method ?: '-' }}
                                </span>
                            </td>

                            <td class="p-4 md:p-5">
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $meta['cls'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>

                            <td class="p-4 md:p-5 text-slate-600">
                                <div class="font-medium text-slate-900">
                                    {{ $d->created_at?->format('Y-m-d') }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ $d->created_at?->format('H:i') }}
                                </div>
                            </td>

                            <td class="p-4 md:p-5">
                                <a href="{{ route('admin.donations.show', $d) }}"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    {{ $t('تفاصيل', 'Details') }}
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10">
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
                                    <div class="mt-4 text-slate-900 font-semibold">{{ $t('لا توجد نتائج', 'No results found') }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $t('جرّب تعديل الفلاتر أو مسحها لعرض الكل.', 'Try adjusting or clearing the filters to see all donations.') }}</div>
                                    <div class="mt-5">
                                        <a href="{{ route('admin.donations.index') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                            {{ $t('عرض كل التبرعات', 'View all donations') }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $donations->links() }}
    </div>
@endsection
