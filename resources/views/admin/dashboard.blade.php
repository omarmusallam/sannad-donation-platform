@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'الداشبورد' : 'Dashboard')
@section('page_title', app()->getLocale() === 'ar' ? 'لوحة الإحصائيات' : 'Dashboard')

@section('page_actions')
    @can('campaigns.view')
        <a href="{{ route('admin.campaigns.index') }}"
            class="hidden sm:inline-flex px-4 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
            {{ app()->getLocale() === 'ar' ? 'إدارة الحملات' : 'Manage Campaigns' }}
        </a>
    @endcan

    @can('donations.view')
        <a href="{{ route('admin.donations.index') }}"
            class="inline-flex px-4 py-2 rounded-2xl border border-slate-200 text-sm font-semibold hover:bg-slate-50 transition">
            {{ app()->getLocale() === 'ar' ? 'عرض التبرعات' : 'View Donations' }}
        </a>
    @endcan
@endsection

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $n = fn($v) => number_format((float) $v, 2);

        // Labels for charts (localized)
        $statusLabels = $isAr ? ['مدفوع', 'بانتظار', 'فشل'] : ['Paid', 'Pending', 'Failed'];
        $paidLabel = $isAr ? 'المدفوع' : 'Paid';
    @endphp

    <div class="space-y-6">

        {{-- Header Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-sm text-slate-500">{{ $isAr ? 'نظرة عامة' : 'Overview' }}</div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mt-1 truncate">
                        {{ $isAr ? 'إحصائيات التبرعات والحملات' : 'Donations & Campaigns Insights' }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-2">
                        {{ $isAr ? 'ملخص سريع وآخر نشاطات المنصة.' : 'Quick summary and latest activity.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    @can('finance_reports.view')
                        <a href="{{ route('admin.finance_reports.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 text-sm font-semibold hover:bg-slate-50 transition">
                            <span class="text-slate-700">📈</span>
                            {{ $isAr ? 'التقارير المالية' : 'Finance Reports' }}
                        </a>
                    @endcan

                    @can('reports.view')
                        <a href="{{ route('admin.reports.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500 transition">
                            <span>🗂️</span>
                            {{ $isAr ? 'التقارير' : 'Reports' }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'إجمالي التبرعات' : 'Total donations' }}</div>
                <div class="text-3xl font-extrabold mt-2 text-slate-900">{{ number_format($totalDonations) }}</div>
                <div class="text-xs text-slate-400 mt-2">{{ $isAr ? 'كل السجلات' : 'All records' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'إجمالي المدفوع' : 'Total paid' }}</div>
                <div class="text-3xl font-extrabold mt-2 text-slate-900">
                    {{ $n($totalPaid) }} <span class="text-base text-slate-600">USD</span>
                </div>
                <div class="text-xs text-slate-400 mt-2">{{ $isAr ? 'حسب المدفوع فقط' : 'Paid only' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'اليوم (مدفوع)' : 'Today (paid)' }}</div>
                <div class="text-3xl font-extrabold mt-2 text-slate-900">
                    {{ $n($todayPaid) }} <span class="text-base text-slate-600">USD</span>
                </div>
                <div class="text-xs text-slate-400 mt-2">{{ $isAr ? 'آخر 24 ساعة' : 'Last 24 hours' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'هذا الشهر (مدفوع)' : 'This month (paid)' }}</div>
                <div class="text-3xl font-extrabold mt-2 text-slate-900">
                    {{ $n($monthPaid) }} <span class="text-base text-slate-600">USD</span>
                </div>
                <div class="text-xs text-slate-400 mt-2">{{ $isAr ? 'من بداية الشهر' : 'Month to date' }}</div>
            </div>
        </div>

        {{-- 3 columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Campaign summary --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-slate-500 text-sm">{{ $isAr ? 'الحملات النشطة' : 'Active campaigns' }}</div>
                        <div class="text-2xl font-bold mt-2 text-slate-900">{{ number_format($activeCampaigns) }}</div>
                    </div>
                    <div
                        class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                        {{ $isAr ? 'نشطة' : 'Live' }}
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t border-slate-200">
                    <div class="text-slate-500 text-sm">{{ $isAr ? 'أعلى حملة جمعًا' : 'Top campaign' }}</div>
                    <div class="text-lg font-semibold mt-1 text-slate-900">
                        {{ $isAr ? $topCampaign?->title_ar ?? ($topCampaign?->title ?? '-') : $topCampaign?->title ?? ($topCampaign?->title_ar ?? '-') }}
                    </div>
                    <div class="text-sm text-slate-600 mt-2">
                        {{ $isAr ? 'المجموع' : 'Collected' }}:
                        <span class="font-semibold text-slate-900">{{ $n($topCampaign?->total_collected ?? 0) }} USD</span>
                    </div>
                </div>
            </div>

            {{-- Statuses + doughnut --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'حالات التبرعات' : 'Donation statuses' }}</div>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-slate-200 p-3">
                        <div class="text-xs text-slate-500">{{ $isAr ? 'مدفوع' : 'Paid' }}</div>
                        <div class="text-xl font-bold mt-1 text-slate-900">{{ number_format($statusCounts['paid'] ?? 0) }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-3">
                        <div class="text-xs text-slate-500">{{ $isAr ? 'بانتظار' : 'Pending' }}</div>
                        <div class="text-xl font-bold mt-1 text-slate-900">
                            {{ number_format($statusCounts['pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-3">
                        <div class="text-xs text-slate-500">{{ $isAr ? 'فشل' : 'Failed' }}</div>
                        <div class="text-xl font-bold mt-1 text-slate-900">
                            {{ number_format($statusCounts['failed'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t border-slate-200">
                    <div class="text-slate-500 text-sm">{{ $isAr ? 'توزيع الحالات' : 'Status distribution' }}</div>
                    <div class="mt-3">
                        <canvas id="statusChart" height="160"></canvas>
                    </div>
                </div>
            </div>

            {{-- Trend + Top list --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm">{{ $isAr ? 'آخر 14 يوم (مدفوع)' : 'Last 14 days (paid)' }}</div>
                <div class="mt-4">
                    <canvas id="dailyChart" height="190"></canvas>
                </div>

                <div class="mt-5 pt-5 border-t border-slate-200">
                    <div class="text-slate-500 text-sm">
                        {{ $isAr ? 'أعلى 5 حملات (مدفوع)' : 'Top 5 campaigns (paid)' }}
                    </div>

                    <div class="mt-3 space-y-2">
                        @foreach ($topCampaigns as $c)
                            <div class="flex items-center justify-between text-sm gap-3">
                                <div class="font-semibold truncate">
                                    {{ $isAr ? $c->title_ar ?? $c->title : $c->title ?? $c->title_ar }}
                                </div>
                                <div class="text-slate-700 font-semibold shrink-0">
                                    {{ $n($c->total_collected ?? 0) }} USD
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        {{-- Latest donations --}}
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
            <div class="p-6 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $isAr ? 'آخر التبرعات' : 'Latest donations' }}</h2>
                    <div class="text-sm text-slate-500 mt-1">
                        {{ $isAr ? 'أحدث السجلات مع الحالة' : 'Newest records with status' }}
                    </div>
                </div>

                @can('donations.view')
                    <a href="{{ route('admin.donations.index') }}"
                        class="px-4 py-2 rounded-2xl border border-slate-200 text-sm font-semibold hover:bg-slate-50 transition">
                        {{ $isAr ? 'عرض الكل' : 'View all' }}
                    </a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-{{ $isAr ? 'right' : 'left' }} px-6 py-3 font-semibold">
                                {{ $isAr ? 'الحملة' : 'Campaign' }}</th>
                            <th class="text-{{ $isAr ? 'right' : 'left' }} px-6 py-3 font-semibold">
                                {{ $isAr ? 'المتبرع' : 'Donor' }}</th>
                            <th class="text-{{ $isAr ? 'right' : 'left' }} px-6 py-3 font-semibold">
                                {{ $isAr ? 'المبلغ' : 'Amount' }}</th>
                            <th class="text-{{ $isAr ? 'right' : 'left' }} px-6 py-3 font-semibold">
                                {{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th class="text-{{ $isAr ? 'right' : 'left' }} px-6 py-3 font-semibold">
                                {{ $isAr ? 'التاريخ' : 'Date' }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestDonations as $d)
                            @php
                                $status = $d->status;
                                $badge = match ($status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                                $statusLabel = $isAr
                                    ? match ($status) {
                                        'paid' => 'مدفوع',
                                        'pending' => 'بانتظار',
                                        'failed' => 'فشل',
                                        default => $status,
                                    }
                                    : ucfirst($status);
                            @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $isAr ? $d->campaign?->title_ar ?? ($d->campaign?->title ?? '-') : $d->campaign?->title ?? ($d->campaign?->title_ar ?? '-') }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $d->donor_name ?: ($isAr ? 'مجهول' : 'Anonymous') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $n($d->amount) }} {{ $d->currency }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $badge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $d->created_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    {{ $isAr ? 'لا توجد تبرعات بعد' : 'No donations yet' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>

    {{-- Charts data --}}
    <script>
        window.__dashboard = {
            dailyLabels: @json($dailySeries['labels'] ?? []),
            dailyValues: @json($dailySeries['values'] ?? []),

            // Use localized labels in UI:
            statusLabels: @json($statusLabels),
            statusValues: @json($statusSeries['values'] ?? []),

            paidLabel: @json($paidLabel),
            isRtl: {{ $isAr ? 'true' : 'false' }},
        };
    </script>
@endsection
