@extends('layouts.admin')

@cannot('finance_reports.view')
    @php abort(403); @endphp
@endcannot

@section('title', app()->isLocale('ar') ? 'تقرير بوابة الدفع' : 'Gateway Report')
@section('page_title', app()->isLocale('ar') ? 'تقرير بوابة الدفع' : 'Gateway Report')

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $providerLabel = function ($provider) use ($isAr) {
            return match ($provider) {
                'stripe' => $isAr ? 'Stripe' : 'Stripe',
                'wallet' => $isAr ? 'محفظة / كريبتو' : 'Wallet / Crypto',
                null, '' => $isAr ? 'غير محدد' : 'Unspecified',
                default => $provider,
            };
        };
    @endphp

    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
            <div class="text-2xl font-extrabold text-slate-900">
                {{ $isAr ? 'تقرير بوابة الدفع' : 'Gateway Report' }}
            </div>
            <div class="text-sm text-slate-600 mt-1">
                {{ $isAr ? 'تجميع حسب مزود الدفع / بوابة الدفع.' : 'Grouped by payment provider.' }}
            </div>
        </div>

        @include('admin.finance-reports._filter_dates')
        @include('admin.finance-reports._kpis', ['kpis' => $kpis])

        <div class="rounded-[28px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-slate-100 font-extrabold text-slate-900">
                {{ $isAr ? 'حسب البوابة (مدفوع فقط)' : 'By Gateway (Paid only)' }}
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'البوابة' : 'Provider' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'عدد التبرعات' : 'Count' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    {{ $providerLabel($r->provider) }}
                                </td>
                                <td class="px-4 py-3">{{ (int) $r->donations_count }}</td>
                                <td class="px-4 py-3 font-semibold">
                                    {{ number_format((float) $r->total_amount, 2) }}
                                </td>
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

        <a href="{{ route('admin.finance_reports.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-slate-900 hover:underline underline-offset-4">
            <span aria-hidden="true">←</span>
            {{ $isAr ? 'رجوع للتقارير' : 'Back to reports' }}
        </a>
    </div>
@endsection
