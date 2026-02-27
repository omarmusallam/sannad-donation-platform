@extends('layouts.admin')

@section('title', 'إضافة صفحة')
@section('page_title', 'إضافة صفحة ثابتة')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إضافة صفحة ثابتة</h1>
                <p class="text-sm text-slate-500 mt-1">أنشئ صفحة جديدة مثل: من نحن، سياسة الخصوصية…</p>
            </div>

            <a href="{{ route('admin.pages.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('admin.pages.store') }}">
            @include('admin.pages._form', ['page' => null])
        </form>
    </div>
@endsection
