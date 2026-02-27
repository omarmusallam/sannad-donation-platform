@extends('layouts.admin')

@section('title', 'الحملات')
@section('page_title', 'الحملات')

@section('page_actions')
    @can('campaigns.create')
        <a href="{{ route('admin.campaigns.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 active:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            إضافة حملة
        </a>
    @endcan
@endsection

@section('content')
    @php
        $statusMeta = function (string $status) {
            return match ($status) {
                'active' => ['label' => 'نشطة', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'paused' => ['label' => 'موقوفة', 'cls' => 'bg-amber-50 text-amber-800 border-amber-200'],
                'draft' => ['label' => 'مسودة', 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
                'ended' => ['label' => 'منتهية', 'cls' => 'bg-sky-50 text-sky-700 border-sky-200'],
                'archived' => ['label' => 'مؤرشفة', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                default => ['label' => $status, 'cls' => 'bg-slate-50 text-slate-700 border-slate-200'],
            };
        };
    @endphp

    <div class="bg-white border border-slate-200 rounded-[28px] overflow-hidden shadow-sm">
        {{-- Header --}}
        <div class="p-5 md:p-6 border-b border-slate-200 bg-slate-50/70">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1">
                    <div class="text-sm text-slate-600">
                        إجمالي الحملات:
                        <span class="font-semibold text-slate-900">{{ $campaigns->total() }}</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        إدارة الحملات، متابعة التقدم، وتعديل المحتوى بسرعة.
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.campaigns.index') }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 12a9 9 0 10-3.3 6.9" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M21 12v-7m0 7h-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        تحديث
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white">
                    <tr class="text-right border-b border-slate-200">
                        <th class="p-4 md:p-5 font-semibold text-slate-700">الحملة</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">التقدم</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">الحالة</th>
                        <th class="p-4 md:p-5 font-semibold text-slate-700">إجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($campaigns as $c)
                        @php
                            $pct =
                                $c->goal_amount > 0
                                    ? (int) max(
                                        0,
                                        min(100, round(((float) $c->current_amount / (float) $c->goal_amount) * 100)),
                                    )
                                    : 0;

                            $meta = $statusMeta($c->status);
                        @endphp

                        <tr class="hover:bg-slate-50/50 transition">
                            {{-- Campaign --}}
                            <td class="p-4 md:p-5">
                                <div class="flex items-center gap-3.5">
                                    {{-- Thumb --}}
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden shrink-0 ring-1 ring-slate-200">
                                        @if (!empty($c->cover_url))
                                            <img src="{{ $c->cover_url }}" class="w-full h-full object-cover"
                                                alt="">
                                        @else
                                            <div class="w-full h-full grid place-items-center text-slate-400">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path
                                                        d="M21 19V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2z"
                                                        stroke="currentColor" stroke-width="2" />
                                                    <path d="M8.5 11a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" stroke="currentColor"
                                                        stroke-width="2" />
                                                    <path d="M21 15l-5-5L5 21" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2.5">
                                            <div class="font-semibold text-slate-900 truncate">
                                                {{ $c->title_ar }}
                                            </div>

                                            @if ($c->is_featured)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-slate-900 text-white">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white/80"></span>
                                                    مميزة
                                                </span>
                                            @endif
                                        </div>

                                        <div
                                            class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                            <span>
                                                Slug:
                                                <span class="font-mono text-slate-600">{{ $c->slug }}</span>
                                            </span>

                                            @if (!empty($c->priority))
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                    أولوية: <span
                                                        class="text-slate-600 font-semibold">{{ $c->priority }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Progress --}}
                            <td class="p-4 md:p-5">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-700 font-semibold">
                                            {{ number_format((float) $c->current_amount, 2) }} {{ $c->currency }}
                                        </span>
                                        <span class="text-slate-500">
                                            الهدف: {{ number_format((float) $c->goal_amount, 2) }} {{ $c->currency }}
                                        </span>
                                    </div>

                                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden ring-1 ring-slate-200">
                                        <div class="h-2.5 bg-slate-900 rounded-full" style="width: {{ $pct }}%">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <span>{{ $pct }}%</span>
                                        @if ($c->goal_amount > 0)
                                            <span>
                                                المتبقي:
                                                <span class="text-slate-700 font-semibold">
                                                    {{ number_format(max(0, (float) $c->goal_amount - (float) $c->current_amount), 2) }}
                                                    {{ $c->currency }}
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="p-4 md:p-5">
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $meta['cls'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="p-4 md:p-5">
                                <div class="flex flex-wrap items-center gap-2">

                                    @can('campaigns.edit')
                                        <a href="{{ route('admin.campaigns.edit', $c) }}"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 20h9" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                                <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                            تعديل
                                        </a>
                                    @endcan

                                    @can('campaign_updates.view')
                                        <a href="{{ route('admin.campaigns.updates.index', $c) }}"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 19h16M7 16V7m5 9V5m5 11v-8" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                            </svg>
                                            تحديثات
                                        </a>
                                    @endcan

                                    @can('campaigns.delete')
                                        <form method="POST" action="{{ route('admin.campaigns.destroy', $c) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف الحملة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M3 6h18" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                    <path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                    <path d="M6 6l1 16h10l1-16" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" />
                                                </svg>
                                                حذف
                                            </button>
                                        </form>
                                    @endcan

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10">
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

                                    <div class="mt-4 text-slate-900 font-semibold">لا توجد حملات بعد</div>
                                    <div class="mt-1 text-sm text-slate-500">ابدأ بإضافة حملة جديدة ثم تابع التقدم من هنا.
                                    </div>

                                    @can('campaigns.create')
                                        <div class="mt-5">
                                            <a href="{{ route('admin.campaigns.create') }}"
                                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                                إضافة حملة
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
    </div>

    <div class="mt-5">
        {{ $campaigns->links() }}
    </div>
@endsection
