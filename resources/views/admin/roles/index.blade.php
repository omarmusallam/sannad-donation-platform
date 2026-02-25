@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')
@section('page_title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')

@section('page_actions')
    <a href="{{ route('admin.roles.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition">
        <span>＋</span>
        <span>{{ app()->getLocale() === 'ar' ? 'إضافة دور' : 'Add role' }}</span>
    </a>
@endsection

@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="space-y-4">
        <div class="bg-white border border-gray-100 rounded-3xl p-6">
            <div class="text-sm text-gray-500">
                {{ $isAr ? 'إدارة أدوار وصلاحيات النظام' : 'Manage system roles & permissions' }}</div>
            <div class="text-lg font-bold mt-1">{{ $isAr ? 'قائمة الأدوار' : 'Roles list' }}</div>
        </div>

        <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الدور' : 'Role' }}</th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الصلاحيات' : 'Permissions' }}</th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'المستخدمون' : 'Users' }}</th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }} font-semibold">
                                {{ $isAr ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($roles as $r)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900">{{ $r->name }}</div>
                                    @if ($r->name === 'super_admin')
                                        <div class="text-xs text-gray-500 mt-1">{{ $isAr ? 'محمي' : 'Protected' }}</div>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($r->permissions->count())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($r->permissions->take(8) as $p)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full border border-gray-200 bg-white text-gray-700 font-semibold">
                                                    {{ $p->name }}
                                                </span>
                                            @endforeach
                                            @if ($r->permissions->count() > 8)
                                                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    +{{ $r->permissions->count() - 8 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span
                                            class="text-xs text-gray-500">{{ $isAr ? 'بدون صلاحيات' : 'No permissions' }}</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm font-semibold text-gray-900">{{ $r->users_count }}</span>
                                </td>

                                <td class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }}">
                                    <div class="flex items-center justify-{{ $isAr ? 'start' : 'end' }} gap-2">
                                        @if ($r->name !== 'super_admin')
                                            <a href="{{ route('admin.roles.edit', $r) }}"
                                                class="px-3 py-2 rounded-2xl border border-gray-200 hover:bg-gray-50 transition font-semibold">
                                                {{ $isAr ? 'تعديل' : 'Edit' }}
                                            </a>

                                            <form method="POST" action="{{ route('admin.roles.destroy', $r) }}"
                                                onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="px-3 py-2 rounded-2xl bg-red-50 text-red-700 hover:bg-red-100 transition font-semibold">
                                                    {{ $isAr ? 'حذف' : 'Delete' }}
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="text-xs text-gray-500">{{ $isAr ? 'غير قابل للتعديل' : 'Locked' }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">
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
