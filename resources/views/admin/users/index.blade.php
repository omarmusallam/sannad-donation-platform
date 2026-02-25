@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'المستخدمون' : 'Users')
@section('page_title', app()->getLocale() === 'ar' ? 'المستخدمون' : 'Users')

@section('page_actions')
    <a href="{{ route('admin.users.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition">
        <span>＋</span>
        <span>{{ app()->getLocale() === 'ar' ? 'إضافة مستخدم' : 'Add user' }}</span>
    </a>
@endsection

@section('content')
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <div class="space-y-4">

        {{-- Header card --}}
        <div class="bg-white border border-gray-100 rounded-3xl p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="text-sm text-gray-500">
                        {{ $isAr ? 'إدارة حسابات لوحة التحكم' : 'Manage admin panel accounts' }}
                    </div>
                    <div class="text-lg font-bold mt-1">
                        {{ $isAr ? 'قائمة المستخدمين' : 'Users list' }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        {{ $isAr ? 'الإجمالي' : 'Total' }}: {{ $users->total() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
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

                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">#{{ $u->id }}</div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="text-gray-900">{{ $u->email }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $u->email_verified_at ? ($isAr ? 'موثق' : 'Verified') : ($isAr ? 'غير موثق' : 'Unverified') }}
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
                                                    class="text-xs px-3 py-1 rounded-full border border-gray-200 bg-white text-gray-700 font-semibold">
                                                    {{ $r }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">
                                            {{ $isAr ? 'بدون دور' : 'No role' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="text-gray-700">{{ optional($u->created_at)->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ optional($u->created_at)->format('H:i') }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-{{ $isAr ? 'left' : 'right' }}">
                                    <div class="flex items-center justify-{{ $isAr ? 'start' : 'end' }} gap-2">

                                        <a href="{{ route('admin.users.edit', $u) }}"
                                            class="px-3 py-2 rounded-2xl border border-gray-200 hover:bg-gray-50 transition text-sm font-semibold">
                                            {{ $isAr ? 'تعديل' : 'Edit' }}
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                            onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?' }}')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="px-3 py-2 rounded-2xl bg-red-50 text-red-700 hover:bg-red-100 transition text-sm font-semibold"
                                                {{ auth()->id() === $u->id ? 'disabled' : '' }}
                                                title="{{ auth()->id() === $u->id ? ($isAr ? 'لا يمكنك حذف حسابك' : 'You cannot delete yourself') : '' }}">
                                                {{ $isAr ? 'حذف' : 'Delete' }}
                                            </button>
                                        </form>

                                    </div>

                                    @if (auth()->id() === $u->id)
                                        <div class="text-xs text-gray-500 mt-2">
                                            {{ $isAr ? 'ملاحظة: لا يمكنك حذف حسابك الحالي.' : 'Note: You cannot delete your current account.' }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                    {{ $isAr ? 'لا يوجد مستخدمون بعد.' : 'No users yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

    </div>
@endsection
