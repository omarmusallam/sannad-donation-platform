@extends('layouts.admin')

@cannot('finance_reports.view')
    @php abort(403); @endphp
@endcannot

@section('title', app()->isLocale('ar') ? 'التقارير المالية' : 'Finance Reports')
@section('page_title', app()->isLocale('ar') ? 'التقارير المالية' : 'Finance Reports')

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $cards = [
            [
                't' => $isAr ? 'تقرير شهري' : 'Monthly Report',
                'd' => $isAr ? 'حسب الأشهر' : 'Grouped by months',
                'r' => route('admin.finance_reports.monthly'),
            ],
            [
                't' => $isAr ? 'تقرير حملة' : 'Campaign Report',
                'd' => $isAr ? 'حسب الحملات + KPIs' : 'By campaigns + KPIs',
                'r' => route('admin.finance_reports.campaign'),
            ],
            [
                't' => $isAr ? 'تقرير بوابة الدفع' : 'Gateway Report',
                'd' => $isAr ? 'حسب بوابة الدفع' : 'Grouped by provider',
                'r' => route('admin.finance_reports.gateway'),
            ],
            [
                't' => $isAr ? 'تقرير العملات' : 'Currency Report',
                'd' => $isAr ? 'حسب العملات' : 'Grouped by currency',
                'r' => route('admin.finance_reports.currency'),
            ],
            [
                't' => $isAr ? 'تقرير الحالة' : 'Status Report',
                'd' => $isAr ? 'حسب حالة الدفع' : 'Grouped by status',
                'r' => route('admin.finance_reports.status'),
            ],
            [
                't' => $isAr ? 'تقرير طرق الدفع' : 'Payment Methods',
                'd' => $isAr ? 'حسب طريقة الدفع' : 'Grouped by payment_method',
                'r' => route('admin.finance_reports.paymentMethod'),
            ],
        ];
    @endphp

    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm">
            <div class="text-sm text-slate-500">
                {{ $isAr ? 'ملخصات ديناميكية مباشرة من التبرعات.' : 'Dynamic summaries based on donations.' }}
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1">
                {{ $isAr ? 'لوحة التقارير المالية' : 'Finance Reports Hub' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($cards as $c)
                <a href="{{ $c['r'] }}"
                    class="group rounded-[28px] border border-slate-200 bg-white p-5 hover:bg-slate-50/60 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-extrabold text-slate-900">{{ $c['t'] }}</div>
                        <div class="text-slate-400 group-hover:text-slate-700 transition" aria-hidden="true">→</div>
                    </div>
                    <div class="mt-2 text-sm text-slate-600">{{ $c['d'] }}</div>

                    <div class="mt-4 text-xs font-semibold text-slate-500">
                        {{ $isAr ? 'فتح التقرير' : 'Open report' }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
