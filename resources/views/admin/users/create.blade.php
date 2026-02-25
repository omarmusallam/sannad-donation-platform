@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مستخدم' : 'Add user')
@section('page_title', app()->getLocale() === 'ar' ? 'إضافة مستخدم' : 'Add user')

@section('page_actions')
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-gray-200 hover:bg-gray-50 transition text-sm font-semibold">
        <span>←</span>
        <span>{{ app()->getLocale() === 'ar' ? 'العودة' : 'Back' }}</span>
    </a>
@endsection

@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        @include('admin.users._form', ['roles' => $roles])

        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <button class="px-6 py-3 rounded-2xl bg-black text-white font-semibold hover:opacity-95 transition">
                {{ $isAr ? 'إنشاء المستخدم' : 'Create user' }}
            </button>

            <a href="{{ route('admin.users.index') }}"
                class="px-6 py-3 rounded-2xl border border-gray-200 hover:bg-gray-50 transition text-center font-semibold">
                {{ $isAr ? 'إلغاء' : 'Cancel' }}
            </a>
        </div>
    </form>
@endsection
