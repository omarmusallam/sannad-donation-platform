@extends('layouts.admin')

@cannot('finance_reports.view')
    @php abort(403); @endphp
@endcannot

@section('title', app()->isLocale('ar') ? 'تقرير حالات الدفع' : 'Status Report')
@section('page_title', app()->isLocale('ar') ? 'تقرير حالات الدفع' : 'Status Report')

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $statusLabel = function ($status) use ($isAr) {
            return match ($status) {
                'paid' => $isAr ? 'مدفوع' : 'Paid',
                'pending' => $isAr ? 'قيد الانتظار' : 'Pending',
                'pending_crypto_review' => $isAr ? 'بانتظار مراجعة الكريبتو' : 'Pending Crypto Review',
                'failed' => $isAr ? 'فشل' : 'Failed',
                'refunded' => $isAr ? 'مسترجع' : 'Refunded',
                null, '' => $isAr ? 'غير محدد' : 'Unspecified',
                default => $status,
            };
        };

        $statusBadge = function ($status) {
            return match ($status) {
                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                'pending_crypto_review' => 'bg-violet-50 text-violet-700 border-violet-200',
                'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                'refunded' => 'bg-sky-50 text-sky-700 border-sky-200',
                default => 'bg-slate-50 text-slate-700 border-slate-200',
            };
        };
    @endphp

    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
            <div class="text-2xl font-extrabold text-slate-900">
                {{ $isAr ? 'تقرير حالات الدفع' : 'Status Report' }}
            </div>
            <div class="text-sm text-slate-600 mt-1">
                {{ $isAr ? 'تجميع حسب حالة الدفع.' : 'Grouped by payment status.' }}
            </div>
        </div>

        @include('admin.finance-reports._filter_dates')

        <div class="rounded-[28px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-slate-100 font-extrabold text-slate-900">
                {{ $isAr ? 'حسب الحالة' : 'By Status' }}
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'عدد العمليات' : 'Count' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $statusBadge($r->status) }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                        {{ $statusLabel($r->status) }}
                                    </span>
                                </td>
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

        <a href="{{ route('admin.finance_reports.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-slate-900 hover:underline underline-offset-4">
            <span aria-hidden="true">←</span>
            {{ $isAr ? 'رجوع للتقارير' : 'Back to reports' }}
        </a>
    </div>
@endsection
