@extends('layouts.admin')

@section('content')
    @php($isAr = app()->isLocale('ar'))

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <div>
                <div class="text-2xl font-extrabold text-slate-900">{{ $isAr ? 'تقرير شهري' : 'Monthly Report' }}</div>
                <div class="text-sm text-slate-600">
                    {{ $isAr ? 'ملخص التبرعات المدفوعة حسب الأشهر.' : 'Paid donations grouped by month.' }}</div>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <select name="year" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    @for ($y = now()->year; $y >= now()->year - 6; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endfor
                </select>
                <button class="px-4 py-2 rounded-xl font-extrabold text-white"
                    style="background: linear-gradient(135deg, rgb(79 70 229), rgb(16 185 129));">
                    {{ $isAr ? 'عرض' : 'View' }}
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-extrabold text-slate-900">
                {{ $isAr ? 'النتائج' : 'Results' }}
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الشهر' : 'Month' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'عدد التبرعات' : 'Count' }}</th>
                            <th class="text-start px-4 py-3 font-bold">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $r->month }}</td>
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

        <div>
            <a href="{{ route('admin.finance_reports.index') }}"
                class="text-sm font-bold text-indigo-700 hover:underline underline-offset-4">
                {{ $isAr ? '← رجوع للتقارير' : '← Back to reports' }}
            </a>
        </div>
    </div>
@endsection
