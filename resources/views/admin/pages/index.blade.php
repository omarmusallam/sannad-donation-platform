@extends('layouts.admin')

@section('title', 'الصفحات')
@section('page_title', 'الصفحات الثابتة')

@section('page_actions')
    @can('pages.create')
        <a href="{{ route('admin.pages.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm font-semibold bg-black text-white hover:opacity-90">
            <span>+</span> <span>إضافة صفحة</span>
        </a>
    @endcan
@endsection

@section('content')
    <div class="bg-white border border-slate-200/70 rounded-3xl shadow-sm overflow-hidden">

        {{-- header --}}
        <div class="p-6 border-b border-slate-200/70 flex items-start justify-between gap-4">
            <div>
                <div class="text-xs text-slate-500">CMS</div>
                <div class="text-xl font-extrabold text-slate-900">إدارة الصفحات الثابتة</div>
                <div class="mt-1 text-sm text-slate-600">
                    أنشئ صفحات مثل: من نحن، سياسة الخصوصية، الشروط… وستظهر تلقائيًا في الموقع.
                </div>
            </div>

            <div class="text-xs px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-700">
                إجمالي: {{ $pages->total() }}
            </div>
        </div>

        {{-- table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-5 py-3 font-bold">الصفحة</th>
                        <th class="text-right px-5 py-3 font-bold">Slug</th>
                        <th class="text-right px-5 py-3 font-bold">الحالة</th>
                        <th class="text-right px-5 py-3 font-bold">الترتيب</th>
                        <th class="text-right px-5 py-3 font-bold">إجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($pages as $page)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $page->title() }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    الرابط: <span class="font-semibold">/p/{{ $page->slug }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-slate-700 font-mono text-xs">
                                {{ $page->slug }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($page->is_public)
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs
                                                bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        منشورة
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs
                                                bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        مخفية
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-slate-700">
                                {{ $page->sort_order }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
                                        class="px-3 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-800">
                                        عرض
                                    </a>

                                    @can('pages.edit')
                                        <a href="{{ route('admin.pages.edit', $page) }}"
                                            class="px-3 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-800">
                                            تعديل
                                        </a>
                                    @endcan

                                    @can('pages.delete')
                                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف الصفحة؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-2 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 text-xs font-semibold">
                                                حذف
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12">
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
                                    <div class="mt-4 text-slate-900 font-semibold">لا توجد صفحات حتى الآن</div>
                                    <div class="mt-1 text-sm text-slate-500">ابدأ بإضافة صفحة ثابتة جديدة.</div>
                                    @can('pages.create')
                                        <div class="mt-5">
                                            <a href="{{ route('admin.pages.create') }}"
                                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                                                إضافة صفحة
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

        <div class="p-4 border-t border-slate-200/70">
            {{ $pages->links() }}
        </div>
    </div>
@endsection
