@extends('layouts.admin')

@section('content')
    @php
        $isAr = app()->isLocale('ar');
    @endphp

    <div class="space-y-6">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">
                {{ $isAr ? 'تقرير حملة' : 'Campaign Report' }}
            </div>
            <div class="text-sm text-slate-600">
                {{ $isAr ? 'فلترة بالحملة أو عرض ترتيب الحملات حسب الإجمالي.' : 'Filter by campaign or view campaign ranking.' }}
            </div>
        </div>

        @include('admin.finance-reports._filter_dates', [
            'slot' => view()->make('admin.finance-reports._campaign_select', compact('campaigns')),
        ])
    </div>

    {{-- KPIs --}}
    @if (isset($kpis))
        @include('admin.finance-reports._kpis', ['kpis' => $kpis])
    @endif

    {{-- Table (only when no specific campaign selected) --}}
    @if (!$campaignId)
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-slate-900">
                {{ $isAr ? 'حسب الحملات (مدفوع فقط)' : 'By Campaigns (Paid only)' }}
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الحملة' : 'Campaign' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'عدد التبرعات' : 'Count' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($rows ?? collect()) as $r)
                            @php
                                $c = $r->campaign;
                                $title = $c ? ($isAr ? $c->title_ar : $c->title_en ?? $c->title_ar) : 'N/A';
                            @endphp

                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $title }}</td>
                                <td class="px-4 py-3">{{ (int) $r->donations_count }}</td>
                                <td class="px-4 py-3 font-semibold">{{ number_format((float) $r->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr class="border-t border-slate-100">
                                <td colspan="3" class="px-4 py-10 text-center text-slate-500">
                                    {{ $isAr ? 'لا يوجد بيانات.' : 'No data.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.finance_reports.index') }}"
            class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4">
            {{ $isAr ? '← رجوع للتقارير' : '← Back to reports' }}
        </a>
    </div>
@endsection
