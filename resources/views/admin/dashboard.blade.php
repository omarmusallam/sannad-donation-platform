@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'الداشبورد' : 'Dashboard')
@section('page_title', app()->getLocale() === 'ar' ? 'لوحة الإحصائيات' : 'Dashboard')

@section('page_actions')
    <a href="{{ route('admin.campaigns.index') }}"
        class="hidden sm:inline-flex px-4 py-2 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-90">
        {{ app()->getLocale() === 'ar' ? 'إدارة الحملات' : 'Manage Campaigns' }}
    </a>
    <a href="{{ route('admin.donations.index') }}"
        class="inline-flex px-4 py-2 rounded-2xl border text-sm font-semibold hover:bg-gray-50">
        {{ app()->getLocale() === 'ar' ? 'عرض التبرعات' : 'View Donations' }}
    </a>
@endsection

@section('content')
    @php
        $isAr = app()->getLocale() === 'ar';
        $n = fn($v) => number_format((float) $v, 2);
    @endphp

    <div class="space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'إجمالي التبرعات' : 'Total donations' }}</div>
                <div class="text-3xl font-extrabold mt-2">{{ number_format($totalDonations) }}</div>
                <div class="text-xs text-gray-400 mt-2">{{ $isAr ? 'كل السجلات' : 'All records' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'إجمالي المدفوع' : 'Total paid' }}</div>
                <div class="text-3xl font-extrabold mt-2">{{ $n($totalPaid) }} <span class="text-base">USD</span></div>
                <div class="text-xs text-gray-400 mt-2">{{ $isAr ? 'حسب المدفوع فقط' : 'Paid only' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'اليوم (مدفوع)' : 'Today (paid)' }}</div>
                <div class="text-3xl font-extrabold mt-2">{{ $n($todayPaid) }} <span class="text-base">USD</span></div>
                <div class="text-xs text-gray-400 mt-2">{{ $isAr ? 'آخر 24 ساعة' : 'Last 24 hours' }}</div>
            </div>

            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'هذا الشهر (مدفوع)' : 'This month (paid)' }}</div>
                <div class="text-3xl font-extrabold mt-2">{{ $n($monthPaid) }} <span class="text-base">USD</span></div>
                <div class="text-xs text-gray-400 mt-2">{{ $isAr ? 'من بداية الشهر' : 'Month to date' }}</div>
            </div>
        </div>

        {{-- 3 columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Campaign summary --}}
            <div class="bg-white p-6 rounded-3xl border">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-gray-500 text-sm">{{ $isAr ? 'الحملات النشطة' : 'Active campaigns' }}</div>
                        <div class="text-2xl font-bold mt-2">{{ number_format($activeCampaigns) }}</div>
                    </div>
                    <div class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold">
                        {{ $isAr ? 'Live' : 'Live' }}
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t">
                    <div class="text-gray-500 text-sm">{{ $isAr ? 'أعلى حملة جمعًا' : 'Top campaign' }}</div>
                    <div class="text-lg font-semibold mt-1">
                        {{ $topCampaign?->title_ar ?? ($topCampaign?->title ?? '-') }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        {{ $isAr ? 'المجموع' : 'Collected' }}:
                        <span class="font-semibold text-gray-900">{{ $n($topCampaign?->total_collected ?? 0) }} USD</span>
                    </div>
                </div>
            </div>

            {{-- Statuses + doughnut --}}
            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'حالات التبرعات' : 'Donation statuses' }}</div>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border p-3">
                        <div class="text-xs text-gray-500">{{ $isAr ? 'مدفوع' : 'Paid' }}</div>
                        <div class="text-xl font-bold mt-1">{{ number_format($statusCounts['paid'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-2xl border p-3">
                        <div class="text-xs text-gray-500">{{ $isAr ? 'بانتظار' : 'Pending' }}</div>
                        <div class="text-xl font-bold mt-1">{{ number_format($statusCounts['pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-2xl border p-3">
                        <div class="text-xs text-gray-500">{{ $isAr ? 'فشل' : 'Failed' }}</div>
                        <div class="text-xl font-bold mt-1">{{ number_format($statusCounts['failed'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t">
                    <div class="text-gray-500 text-sm">{{ $isAr ? 'توزيع الحالات' : 'Status distribution' }}</div>
                    <div class="mt-3">
                        <canvas id="statusChart" height="140"></canvas>
                    </div>
                </div>
            </div>

            {{-- Trend + Top list --}}
            <div class="bg-white p-6 rounded-3xl border">
                <div class="text-gray-500 text-sm">{{ $isAr ? 'آخر 14 يوم (مدفوع)' : 'Last 14 days (paid)' }}</div>
                <div class="mt-4">
                    <canvas id="dailyChart" height="180"></canvas>
                </div>

                <div class="mt-5 pt-5 border-t">
                    <div class="text-gray-500 text-sm">{{ $isAr ? 'أعلى 5 حملات (مدفوع)' : 'Top 5 campaigns (paid)' }}
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach ($topCampaigns as $c)
                            <div class="flex items-center justify-between text-sm">
                                <div class="font-semibold truncate max-w-[70%]">
                                    {{ $isAr ? $c->title_ar ?? $c->title : $c->title ?? $c->title_ar }}
                                </div>
                                <div class="text-gray-700 font-semibold">
                                    {{ $n($c->total_collected ?? 0) }} USD
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        {{-- Latest donations --}}
        <div class="bg-white border rounded-3xl overflow-hidden">
            <div class="p-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">{{ $isAr ? 'آخر التبرعات' : 'Latest donations' }}</h2>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ $isAr ? 'أحدث السجلات مع الحالة' : 'Newest records with status' }}
                    </div>
                </div>

                <a href="{{ route('admin.donations.index') }}"
                    class="px-4 py-2 rounded-2xl border text-sm font-semibold hover:bg-gray-50">
                    {{ $isAr ? 'عرض الكل' : 'View all' }}
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
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

                    <tbody class="divide-y">
                        @forelse ($latestDonations as $d)
                            @php
                                $status = $d->status;
                                $badge = match ($status) {
                                    'paid' => 'bg-green-50 text-green-700 border-green-200',
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                    'failed' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200',
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

                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold">
                                    {{ $d->campaign?->title_ar ?? ($d->campaign?->title ?? '-') }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $d->donor_name ?: ($isAr ? 'مجهول' : 'Anonymous') }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $n($d->amount) }} {{ $d->currency }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $badge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $d->created_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
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
            statusLabels: @json($statusSeries['labels'] ?? []),
            statusValues: @json($statusSeries['values'] ?? []),
            isRtl: {{ $isAr ? 'true' : 'false' }},
        };
    </script>
@endsection
