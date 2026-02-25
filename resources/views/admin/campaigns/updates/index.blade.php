@extends('layouts.admin')

@section('title', 'تحديثات الحملة')
@section('page_title', 'تحديثات: ' . $campaign->title_ar)

@section('page_actions')
    <a href="{{ route('admin.campaigns.updates.create', $campaign) }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
        إضافة تحديث
    </a>
@endsection

@section('content')
    @php
        $boolBadge = fn(bool $v) => $v
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-slate-50 text-slate-600 border-slate-200';
    @endphp

    <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
        <div
            class="p-4 md:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <div class="space-y-1">
                <div class="text-sm text-slate-600">
                    إجمالي التحديثات:
                    <span class="font-semibold text-slate-900">{{ $updates->total() }}</span>
                </div>
                <div class="text-xs text-slate-500">
                    إدارة تحديثات الحملة ونشرها للزوار عند الحاجة.
                </div>
            </div>

            <a href="{{ route('admin.campaigns.edit', $campaign) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← العودة لتعديل الحملة
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white">
                    <tr class="text-right border-b border-slate-200">
                        <th class="p-4 md:p-5 font-semibold text-slate-700">العنوان</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">عام</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">التاريخ</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">إجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($updates as $u)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 md:p-5">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900 truncate">{{ $u->title_ar }}</div>
                                    <div class="text-xs text-slate-500 mt-1 truncate">{{ $u->title_en }}</div>
                                </div>
                            </td>

                            <td class="p-4 md:p-5">
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $boolBadge((bool) $u->is_public) }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                    {{ $u->is_public ? 'عام' : 'خاص' }}
                                </span>
                            </td>

                            <td class="p-4 md:p-5 text-slate-600">
                                @php
                                    $dt = optional($u->published_at) ?? $u->created_at;
                                @endphp
                                <div class="font-medium text-slate-900">{{ $dt->format('Y-m-d') }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $dt->format('H:i') }}</div>
                            </td>

                            <td class="p-4 md:p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.campaigns.updates.edit', [$campaign, $u]) }}"
                                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        تعديل
                                    </a>

                                    <form method="POST"
                                        action="{{ route('admin.campaigns.updates.destroy', [$campaign, $u]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف التحديث؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10">
                                <div
                                    class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/60 p-8 text-center">
                                    <div class="text-slate-900 font-semibold">لا توجد تحديثات بعد</div>
                                    <div class="text-sm text-slate-500 mt-1">ابدأ بإضافة تحديث جديد للحملة.</div>
                                    <div class="mt-5">
                                        <a href="{{ route('admin.campaigns.updates.create', $campaign) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition">
                                            إضافة تحديث
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $updates->links() }}
    </div>
@endsection
