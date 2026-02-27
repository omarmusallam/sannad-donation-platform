@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')
@section('page_title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')

@section('page_actions')
    @can('roles.manage')
        <a href="{{ route('admin.roles.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            <span aria-hidden="true">＋</span>
            <span>{{ app()->getLocale() === 'ar' ? 'إضافة دور' : 'Add role' }}</span>
        </a>
    @endcan
@endsection

@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="text-sm text-slate-500">
                {{ $isAr ? 'إدارة أدوار وصلاحيات النظام' : 'Manage system roles & permissions' }}
            </div>
            <div class="text-lg font-extrabold mt-1 text-slate-900">
                {{ $isAr ? 'قائمة الأدوار' : 'Roles list' }}
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الدور' : 'Role' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الصلاحيات' : 'Permissions' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'المستخدمون' : 'Users' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }} font-semibold">
                                {{ $isAr ? 'إجراءات' : 'Actions' }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($roles as $r)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">{{ $r->name }}</div>
                                    @if ($r->name === 'super_admin')
                                        <div class="text-xs text-slate-500 mt-1">{{ $isAr ? 'محمي' : 'Protected' }}</div>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($r->permissions->count())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($r->permissions->take(8) as $p)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full border border-slate-200 bg-white text-slate-700 font-semibold">
                                                    {{ $p->name }}
                                                </span>
                                            @endforeach

                                            @if ($r->permissions->count() > 8)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                    +{{ $r->permissions->count() - 8 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">
                                            {{ $isAr ? 'بدون صلاحيات' : 'No permissions' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm font-semibold text-slate-900">{{ $r->users_count }}</span>
                                </td>

                                <td class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }}">
                                    <div class="flex items-center justify-{{ $isAr ? 'start' : 'end' }} gap-2">
                                        @if ($r->name !== 'super_admin')
                                            @can('roles.manage')
                                                <a href="{{ route('admin.roles.edit', $r) }}"
                                                    class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-semibold text-slate-700">
                                                    {{ $isAr ? 'تعديل' : 'Edit' }}
                                                </a>

                                                <form method="POST" action="{{ route('admin.roles.destroy', $r) }}"
                                                    onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition font-semibold">
                                                        {{ $isAr ? 'حذف' : 'Delete' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span
                                                    class="text-xs text-slate-500">{{ $isAr ? 'لا توجد صلاحيات' : 'No permissions' }}</span>
                                            @endcan
                                        @else
                                            <span class="text-xs text-slate-500">
                                                {{ $isAr ? 'غير قابل للتعديل' : 'Locked' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                    {{ $isAr ? 'لا يوجد أدوار بعد.' : 'No roles yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
