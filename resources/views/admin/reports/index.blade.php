@extends('layouts.admin')

@section('title', 'التقارير')
@section('page_title', 'التقارير')

@section('page_actions')
    @can('reports.create')
        <a href="{{ route('admin.reports.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            إضافة تقرير
        </a>
    @endcan
@endsection

@section('content')
    @php
        $isAr = app()->isLocale('ar');

        $boolBadge = fn(bool $v) => $v
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-slate-50 text-slate-600 border-slate-200';

        $fmtPeriod = function ($r) {
            if (!$r->period_year) {
                return '-';
            }
            $m = $r->period_month ? str_pad((string) $r->period_month, 2, '0', STR_PAD_LEFT) : '01';
            return $r->period_year . '-' . $m;
        };
    @endphp

    <div class="mx-auto max-w-7xl space-y-5">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">التقارير</h1>
                <p class="text-sm text-slate-500 mt-1">إدارة تقارير الأداء وملفات PDF المرتبطة بها.</p>
            </div>

            <a href="{{ route('admin.reports.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M21 12a9 9 0 10-3.3 6.9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M21 12v-7m0 7h-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                تحديث
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    العدد المعروض:
                    <span class="font-semibold text-slate-900">{{ $reports->count() }}</span>
                    <span class="text-slate-400">/</span>
                    الإجمالي:
                    <span class="font-semibold text-slate-900">{{ $reports->total() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white">
                        <tr class="text-right border-b border-slate-200">
                            <th class="p-4 md:p-5 font-semibold text-slate-700">العنوان</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">الفترة</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">الحملة</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">عام</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">PDF</th>
                            <th class="p-4 md:p-5 font-semibold text-slate-700">إجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $r)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 md:p-5">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 truncate">{{ $r->title_ar }}</div>
                                        <div class="text-xs text-slate-500 mt-1 truncate">{{ $r->title_en }}</div>
                                    </div>
                                </td>

                                <td class="p-4 md:p-5 text-slate-600">
                                    <div class="font-medium text-slate-900">{{ $fmtPeriod($r) }}</div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 truncate">
                                            {{ $r->campaign?->title_ar ?? '-' }}
                                        </div>
                                        @if ($r->campaign_id)
                                            <div class="text-xs text-slate-500 mt-1">
                                                Campaign ID: <span class="font-mono">{{ $r->campaign_id }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-4 md:p-5">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $boolBadge((bool) $r->is_public) }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                        {{ $r->is_public ? 'عام' : 'خاص' }}
                                    </span>
                                </td>

                                <td class="p-4 md:p-5">
                                    @if ($r->pdf_path)
                                        <a href="{{ asset('storage/' . $r->pdf_path) }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            فتح
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                <td class="p-4 md:p-5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @can('reports.edit')
                                            <a href="{{ route('admin.reports.edit', $r) }}"
                                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                                تعديل
                                            </a>
                                        @endcan

                                        @can('reports.delete')
                                            <form method="POST" action="{{ route('admin.reports.destroy', $r) }}"
                                                onsubmit="return confirm('هل أنت متأكد من حذف التقرير؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition">
                                                    حذف
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10">
                                    <div
                                        class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/60 p-8 text-center">
                                        <div
                                            class="mx-auto w-12 h-12 rounded-2xl bg-white border border-slate-200 grid place-items-center text-slate-400">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                                <path d="M21 12a9 9 0 10-18 0 9 9 0 0018 0z" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-slate-900 font-semibold">لا توجد تقارير بعد</div>
                                        <div class="mt-1 text-sm text-slate-500">ابدأ بإضافة تقرير جديد وسيظهر هنا.</div>
                                        @can('reports.create')
                                            <div class="mt-5">
                                                <a href="{{ route('admin.reports.create') }}"
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                                    إضافة تقرير
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 md:p-5 border-t border-slate-200">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
@endsection
