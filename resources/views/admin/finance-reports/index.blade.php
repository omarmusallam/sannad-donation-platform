@extends('layouts.admin')

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
                't' => $isAr ? 'تقرير بوابة' : 'Gateway Report',
                'd' => $isAr ? 'حسب provider' : 'Grouped by provider',
                'r' => route('admin.finance_reports.gateway'),
            ],
            [
                't' => $isAr ? 'تقرير عملة' : 'Currency Report',
                'd' => $isAr ? 'حسب العملات' : 'Grouped by currency',
                'r' => route('admin.finance_reports.currency'),
            ],
            [
                't' => $isAr ? 'تقرير الحالة' : 'Status Report',
                'd' => $isAr ? 'paid/pending/failed/refunded' : 'By status',
                'r' => route('admin.finance_reports.status'),
            ],
            [
                't' => $isAr ? 'تقرير طريقة الدفع' : 'Payment Method',
                'd' => $isAr ? 'manual/mock...' : 'By payment_method',
                'r' => route('admin.finance_reports.paymentMethod'),
            ],
        ];
    @endphp

    <div class="space-y-6">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $isAr ? 'التقارير المالية' : 'Finance Reports' }}</div>
            <div class="text-sm text-slate-600">
                {{ $isAr ? 'ملخصات ديناميكية مباشرة من التبرعات.' : 'Dynamic summaries based on donations.' }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($cards as $c)
                <a href="{{ $c['r'] }}"
                    class="group rounded-3xl border border-slate-200 bg-white p-5 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-extrabold text-slate-900">{{ $c['t'] }}</div>
                        <div class="text-slate-400 group-hover:text-slate-700 transition">→</div>
                    </div>
                    <div class="mt-2 text-sm text-slate-600">{{ $c['d'] }}</div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
