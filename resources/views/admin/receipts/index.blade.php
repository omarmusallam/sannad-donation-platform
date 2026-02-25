@extends('layouts.admin')

@section('title', 'إدارة الإيصالات')
@section('page_title', 'إدارة الإيصالات')

@section('content')

    <div class="mx-auto max-w-7xl">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">الإيصالات</h1>
                <p class="text-sm text-slate-500 mt-1">إدارة جميع إيصالات التبرعات.</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white border border-slate-200 rounded-3xl p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث برقم الإيصال / الإيميل"
                    class="rounded-2xl border-slate-200 text-sm">

                <select name="status" class="rounded-2xl border-slate-200 text-sm">
                    <option value="">كل الحالات</option>
                    <option value="issued" @selected(request('status') == 'issued')>Issued</option>
                    <option value="void" @selected(request('status') == 'void')>Void</option>
                </select>

                <select name="currency" class="rounded-2xl border-slate-200 text-sm">
                    <option value="">كل العملات</option>
                    <option value="USD">USD</option>
                    <option value="GBP">GBP</option>
                </select>

                <button class="bg-slate-900 text-white rounded-2xl text-sm font-semibold px-4">
                    فلترة
                </button>
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="p-4 text-right">#</th>
                            <th class="p-4 text-right">رقم الإيصال</th>
                            <th class="p-4 text-right">المتبرع</th>
                            <th class="p-4 text-right">المبلغ</th>
                            <th class="p-4 text-right">الحالة</th>
                            <th class="p-4 text-right">تاريخ الإصدار</th>
                            <th class="p-4 text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr class="border-t hover:bg-slate-50 transition">
                                <td class="p-4">{{ $receipt->id }}</td>

                                <td class="p-4 font-mono text-xs">
                                    {{ $receipt->receipt_no }}
                                </td>

                                <td class="p-4">
                                    {{ $receipt->donor_name ?? '-' }}
                                    <div class="text-xs text-slate-500">
                                        {{ $receipt->donor_email }}
                                    </div>
                                </td>

                                <td class="p-4 font-semibold">
                                    {{ number_format($receipt->amount, 2) }}
                                    {{ $receipt->currency }}
                                </td>

                                <td class="p-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $receipt->status === 'issued' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $receipt->status }}
                                    </span>
                                </td>

                                <td class="p-4">
                                    {{ $receipt->issued_at?->format('Y-m-d') }}
                                </td>

                                <td class="p-4">
                                    <div class="flex gap-2 flex-wrap">

                                        <a href="{{ route('admin.receipts.download', $receipt) }}"
                                            class="text-xs px-3 py-1 rounded-xl bg-slate-900 text-white">
                                            PDF
                                        </a>

                                        <a href="{{ route('receipt.verify', $receipt->uuid) }}" target="_blank"
                                            class="text-xs px-3 py-1 rounded-xl border border-slate-200">
                                            تحقق
                                        </a>

                                        <form method="POST" action="{{ route('admin.receipts.regenerate', $receipt) }}">
                                            @csrf
                                            <button class="text-xs px-3 py-1 rounded-xl bg-amber-500 text-white">
                                                إعادة توليد
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-500">
                                    لا توجد إيصالات.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $receipts->links() }}
            </div>
        </div>

    </div>

@endsection
