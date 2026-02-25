@extends('layouts.admin')

@section('title', 'التقارير')
@section('page_title', 'التقارير')

@section('content')
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">التقارير</h1>
            <p class="text-sm text-slate-500 mt-1">
                إدارة تقارير الأداء الشهرية والسنوية وملفات PDF المرتبطة بها.
            </p>
        </div>

        <a href="{{ route('admin.reports.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
            إضافة تقرير
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
        <div class="p-4 md:p-5 border-b border-slate-200 bg-slate-50/70 text-sm text-slate-600">
            إجمالي التقارير:
            <span class="font-semibold text-slate-900">{{ $reports->total() }}</span>
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
                            <td class="p-4 md:p-5 font-semibold text-slate-900">
                                {{ $r->title_ar }}
                            </td>

                            <td class="p-4 md:p-5 text-slate-600">
                                {{ $r->period_year ? $r->period_year . '-' . str_pad($r->period_month, 2, '0', STR_PAD_LEFT) : '-' }}
                            </td>

                            <td class="p-4 md:p-5">
                                {{ $r->campaign?->title_ar ?? '-' }}
                            </td>

                            <td class="p-4 md:p-5">
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold
                                    {{ $r->is_public ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ $r->is_public ? 'عام' : 'خاص' }}
                                </span>
                            </td>

                            <td class="p-4 md:p-5">
                                @if ($r->pdf_path)
                                    <a href="{{ asset('storage/' . $r->pdf_path) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        فتح
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            <td class="p-4 md:p-5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.reports.edit', $r) }}"
                                        class="px-3 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('admin.reports.destroy', $r) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف التقرير؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-slate-500">
                                لا توجد تقارير بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $reports->links() }}
    </div>
@endsection
