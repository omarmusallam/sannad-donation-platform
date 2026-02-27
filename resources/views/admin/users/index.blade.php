@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'المستخدمون' : 'Users')
@section('page_title', app()->getLocale() === 'ar' ? 'المستخدمون' : 'Users')

@section('page_actions')
    @can('users.manage')
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            <span aria-hidden="true">＋</span>
            <span>{{ app()->getLocale() === 'ar' ? 'إضافة مستخدم' : 'Add user' }}</span>
        </a>
    @endcan
@endsection

@section('content')
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <div class="space-y-4">

        {{-- Header card --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="text-sm text-slate-500">
                        {{ $isAr ? 'إدارة حسابات لوحة التحكم' : 'Manage admin panel accounts' }}
                    </div>
                    <div class="text-lg font-extrabold mt-1 text-slate-900">
                        {{ $isAr ? 'قائمة المستخدمين' : 'Users list' }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $isAr ? 'الإجمالي' : 'Total' }}: <span class="font-semibold">{{ $users->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الاسم' : 'Name' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'البريد' : 'Email' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'الأدوار' : 'Roles' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'right' : 'left' }} font-semibold">
                                {{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}
                            </th>
                            <th class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }} font-semibold">
                                {{ $isAr ? 'إجراءات' : 'Actions' }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 mt-1">#{{ $u->id }}</div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="text-slate-900">{{ $u->email }}</div>
                                    <div class="text-xs mt-1">
                                        @if ($u->email_verified_at)
                                            <span
                                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full border text-[11px] font-semibold bg-emerald-50 text-emerald-700 border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                                {{ $isAr ? 'موثق' : 'Verified' }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full border text-[11px] font-semibold bg-amber-50 text-amber-800 border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                                {{ $isAr ? 'غير موثق' : 'Unverified' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $roles = $u->roles->pluck('name')->values();
                                    @endphp

                                    @if ($roles->count())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($roles as $r)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full border border-slate-200 bg-white text-slate-700 font-semibold">
                                                    {{ $r }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">
                                            {{ $isAr ? 'بدون دور' : 'No role' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="text-slate-700">{{ optional($u->created_at)->format('Y-m-d') }}</div>
                                    <div class="text-xs text-slate-500 mt-1">{{ optional($u->created_at)->format('H:i') }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }}">
                                    <div class="flex items-center justify-{{ $isAr ? 'start' : 'end' }} gap-2">

                                        @can('users.manage')
                                            <a href="{{ route('admin.users.edit', $u) }}"
                                                class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-semibold text-slate-700">
                                                {{ $isAr ? 'تعديل' : 'Edit' }}
                                            </a>

                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?' }}')">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ auth()->id() === $u->id ? 'disabled' : '' }}
                                                    title="{{ auth()->id() === $u->id ? ($isAr ? 'لا يمكنك حذف حسابك' : 'You cannot delete yourself') : '' }}">
                                                    {{ $isAr ? 'حذف' : 'Delete' }}
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="text-xs text-slate-500">{{ $isAr ? 'لا توجد صلاحيات' : 'No permissions' }}</span>
                                        @endcan

                                    </div>

                                    @if (auth()->id() === $u->id)
                                        <div class="text-xs text-slate-500 mt-2">
                                            {{ $isAr ? 'ملاحظة: لا يمكنك حذف حسابك الحالي.' : 'Note: You cannot delete your current account.' }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                    {{ $isAr ? 'لا يوجد مستخدمون بعد.' : 'No users yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        </div>

    </div>
@endsection
