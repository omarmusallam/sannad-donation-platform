@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'My donations' : 'تبرعاتي')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $isAr = !$isEn;

        $donationsUrl = locale_route('donor.donations');
        $donateUrl = locale_route('donate');

        $title = $isEn ? 'My donations' : 'تبرعاتي';
        $subtitle = $isEn
            ? 'A complete and organized view of all your donations, statuses, and receipts.'
            : 'عرض كامل ومنظم لجميع تبرعاتك وحالاتها وإيصالاتها.';

        $campaignTitle = function ($campaign) use ($isEn) {
            if (!$campaign) {
                return $isEn ? 'Unknown campaign' : 'حملة غير معروفة';
            }

            return $isEn ? ($campaign->title_en ?: $campaign->title_ar) : ($campaign->title_ar ?: $campaign->title_en);
        };

        $fmtMoney = function ($amount, $currency = null) {
            $amount = number_format((float) $amount, 2);
            return $currency ? $amount . ' ' . $currency : $amount;
        };

        $statusLabel = function ($status) use ($isEn) {
            return match ((string) $status) {
                'paid' => $isEn ? 'Paid' : 'مدفوع',
                'pending' => $isEn ? 'Pending' : 'قيد الانتظار',
                'failed' => $isEn ? 'Failed' : 'فشل',
                'refunded' => $isEn ? 'Refunded' : 'مسترد',
                default => (string) $status,
            };
        };

        $statusClass = function ($status) {
            return match ((string) $status) {
                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                'refunded' => 'bg-slate-100 text-slate-700 border-slate-200',
                default => 'bg-slate-100 text-slate-700 border-slate-200',
            };
        };

        $statusTabs = [
            '' => $isEn ? 'All' : 'الكل',
            'paid' => $isEn ? 'Paid' : 'مدفوع',
            'pending' => $isEn ? 'Pending' : 'قيد الانتظار',
            'failed' => $isEn ? 'Failed' : 'فشل',
            'refunded' => $isEn ? 'Refunded' : 'مسترد',
        ];
    @endphp

    <div class="max-w-7xl mx-auto space-y-8">
        <section class="relative overflow-hidden rounded-[32px] border border-border bg-surface p-6 sm:p-8 lg:p-10">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
            <div class="pointer-events-none absolute -top-16 -right-16 h-72 w-72 rounded-full blur-3xl opacity-20"
                style="background: radial-gradient(circle, rgba(var(--brand),.18), transparent 60%);"></div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <div class="text-sm text-subtext">
                        <a class="hover:underline underline-offset-4" href="{{ locale_route('donor.dashboard') }}">
                            {{ $isEn ? 'Account' : 'الحساب' }}
                        </a>
                        <span class="mx-2">/</span>
                        <span class="font-semibold text-text">{{ $title }}</span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">
                        {{ $title }}
                    </h1>

                    <p class="mt-2 text-subtext leading-relaxed max-w-3xl">
                        {{ $subtitle }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a class="btn btn-secondary justify-center" href="{{ locale_route('donor.dashboard') }}">
                        {{ $isEn ? 'Back to account' : 'العودة للحساب' }}
                    </a>

                    <a class="btn btn-primary justify-center" href="{{ $donateUrl }}">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <aside class="xl:col-span-1 space-y-4">
                @include('donor.partials.account-nav')

                <div class="card-muted p-5">
                    <div class="font-black text-text">
                        {{ $isEn ? 'Donations note' : 'ملاحظة التبرعات' }}
                    </div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">
                        {{ $isEn
                            ? 'Use filters to quickly find a donation by campaign or by payment status.'
                            : 'استخدم الفلاتر للوصول السريع إلى التبرعات حسب الحملة أو حالة الدفع.' }}
                    </div>
                </div>
            </aside>

            <div class="xl:col-span-3 space-y-6">
                <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                    <div class="card p-5">
                        <div class="text-sm text-subtext">{{ $isEn ? 'All' : 'الكل' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ (int) ($summary['total'] ?? 0) }}</div>
                    </div>

                    <div class="card p-5">
                        <div class="text-sm text-subtext">{{ $isEn ? 'Paid' : 'مدفوع' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ (int) ($summary['paid'] ?? 0) }}</div>
                    </div>

                    <div class="card p-5">
                        <div class="text-sm text-subtext">{{ $isEn ? 'Pending' : 'قيد الانتظار' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ (int) ($summary['pending'] ?? 0) }}</div>
                    </div>

                    <div class="card p-5">
                        <div class="text-sm text-subtext">{{ $isEn ? 'Failed' : 'فشل' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ (int) ($summary['failed'] ?? 0) }}</div>
                    </div>

                    <div class="card p-5">
                        <div class="text-sm text-subtext">{{ $isEn ? 'Refunded' : 'مسترد' }}</div>
                        <div class="mt-2 text-2xl font-black text-text">{{ (int) ($summary['refunded'] ?? 0) }}</div>
                    </div>
                </section>

                <section class="card p-6 sm:p-7">
                    <form method="GET" action="{{ $donationsUrl }}" class="space-y-5">
                        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Search by campaign' : 'بحث باسم الحملة' }}
                                </label>
                                <input type="text" name="q" value="{{ $search }}" class="input"
                                    placeholder="{{ $isEn ? 'Campaign title or slug...' : 'اسم الحملة أو الرابط...' }}">
                            </div>

                            <div class="lg:w-[260px]">
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Status filter' : 'فلتر الحالة' }}
                                </label>
                                <select name="status" class="input">
                                    @foreach ($statusTabs as $value => $label)
                                        <option value="{{ $value }}" @selected($activeStatus === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex gap-3">
                                <button class="btn btn-primary" type="submit">
                                    {{ $isEn ? 'Apply' : 'تطبيق' }}
                                </button>

                                <a class="btn btn-secondary" href="{{ $donationsUrl }}">
                                    {{ $isEn ? 'Reset' : 'إعادة ضبط' }}
                                </a>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($statusTabs as $value => $label)
                                <a href="{{ $donationsUrl . ($value !== '' ? '?status=' . $value : '') }}"
                                    class="badge {{ $activeStatus === $value ? 'ring-1' : '' }}"
                                    style="{{ $activeStatus === $value ? '--tw-ring-color: rgba(var(--brand), .18); color: rgb(var(--brand)); border-color: rgba(var(--brand), .2);' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </form>
                </section>

                <section class="card p-6 sm:p-7">
                    @if ($donations->count())
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[920px] text-sm">
                                <thead>
                                    <tr class="border-b text-subtext">
                                        <th class="py-3 {{ $isAr ? 'text-right' : 'text-left' }}">
                                            {{ $isEn ? 'Campaign' : 'الحملة' }}
                                        </th>
                                        <th class="py-3 text-center">
                                            {{ $isEn ? 'Amount' : 'المبلغ' }}
                                        </th>
                                        <th class="py-3 text-center">
                                            {{ $isEn ? 'Status' : 'الحالة' }}
                                        </th>
                                        <th class="py-3 text-center">
                                            {{ $isEn ? 'Date' : 'التاريخ' }}
                                        </th>
                                        <th class="py-3 text-center">
                                            {{ $isEn ? 'Receipt no.' : 'رقم الإيصال' }}
                                        </th>
                                        <th class="py-3 {{ $isAr ? 'text-left' : 'text-right' }}">
                                            {{ $isEn ? 'Action' : 'الإجراء' }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($donations as $donation)
                                        @php
                                            $status = (string) $donation->status;
                                            $date = optional($donation->paid_at ?? $donation->created_at)->format(
                                                'Y-m-d',
                                            );
                                            $receiptNo = $donation->receipt?->receipt_no;
                                        @endphp

                                        <tr class="border-b last:border-0">
                                            <td class="py-4 font-semibold text-text">
                                                {{ $campaignTitle($donation->campaign) }}
                                            </td>

                                            <td class="py-4 text-center font-black text-text">
                                                {{ $fmtMoney($donation->amount, $donation->currency) }}
                                            </td>

                                            <td class="py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusClass($status) }}">
                                                    {{ $statusLabel($status) }}
                                                </span>
                                            </td>

                                            <td class="py-4 text-center text-subtext">
                                                {{ $date }}
                                            </td>

                                            <td class="py-4 text-center text-subtext">
                                                {{ $receiptNo ?: '—' }}
                                            </td>

                                            <td class="py-4 {{ $isAr ? 'text-left' : 'text-right' }}">
                                                @if ($donation->receipt)
                                                    <a class="btn btn-secondary text-xs"
                                                        href="{{ locale_route('receipt.verify', ['receipt' => $donation->receipt]) }}">
                                                        {{ $isEn ? 'View receipt' : 'عرض الإيصال' }}
                                                    </a>
                                                @else
                                                    <span class="text-xs text-subtext/70">
                                                        {{ $isEn ? 'No receipt' : 'لا يوجد إيصال' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $donations->links() }}
                        </div>
                    @else
                        <div class="rounded-3xl border border-border bg-muted p-10 text-center">
                            <div class="text-xl font-black text-text">
                                {{ $isEn ? 'No donations found' : 'لا توجد تبرعات مطابقة' }}
                            </div>
                            <div class="mt-2 text-sm text-subtext">
                                {{ $isEn
                                    ? 'Try changing the filters or make a new donation to see activity here.'
                                    : 'جرّب تغيير الفلاتر أو أتمم تبرعًا جديدًا ليظهر النشاط هنا.' }}
                            </div>
                            <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a class="btn btn-secondary" href="{{ $donationsUrl }}">
                                    {{ $isEn ? 'Reset filters' : 'إعادة ضبط الفلاتر' }}
                                </a>
                                <a class="btn btn-primary" href="{{ $donateUrl }}">
                                    {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </section>
    </div>
@endsection
