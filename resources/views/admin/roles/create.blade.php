@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة دور' : 'Add role')
@section('page_title', app()->getLocale() === 'ar' ? 'إضافة دور' : 'Add role')

@section('page_actions')
    <a href="{{ route('admin.roles.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm font-semibold text-slate-700">
        ← {{ app()->getLocale() === 'ar' ? 'العودة' : 'Back' }}
    </a>
@endsection

@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    @can('roles.manage')
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
            @csrf

            @include('admin.roles._form', ['groups' => $groups])

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-sm">
                    {{ $isAr ? 'إنشاء الدور' : 'Create role' }}
                </button>

                <a href="{{ route('admin.roles.index') }}"
                    class="px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition text-center font-semibold text-slate-700">
                    {{ $isAr ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    @else
        <div class="rounded-3xl border border-rose-200 bg-rose-50 p-6 text-rose-900">
            <div class="font-bold">{{ $isAr ? 'غير مسموح' : 'Not allowed' }}</div>
            <div class="text-sm mt-1">
                {{ $isAr ? 'لا تملك صلاحية إدارة الأدوار.' : 'You do not have permission to manage roles.' }}</div>
        </div>
    @endcan
@endsection
