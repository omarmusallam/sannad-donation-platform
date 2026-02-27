@extends('layouts.admin')

@section('title', 'تعديل صفحة')
@section('page_title', 'تعديل صفحة ثابتة')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">تعديل صفحة ثابتة</h1>
                <p class="text-sm text-slate-500 mt-1">قم بتحديث البيانات ثم احفظ التغييرات.</p>
            </div>

            <a href="{{ route('admin.pages.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('admin.pages.update', $page) }}">
            @csrf
            @method('PUT')

            @include('admin.pages._form', ['page' => $page])
        </form>
    </div>
@endsection
