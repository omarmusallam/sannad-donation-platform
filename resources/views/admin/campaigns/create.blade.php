@extends('layouts.admin')

@section('title', 'إضافة حملة')
@section('page_title', 'إضافة حملة')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-6 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">إضافة حملة</h1>
                <p class="text-sm text-slate-500 mt-1">املأ البيانات الأساسية ثم احفظ لإظهار الحملة.</p>
            </div>

            <a href="{{ route('admin.campaigns.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                رجوع
            </a>
        </div>

        @can('campaigns.create')
            <form method="POST" action="{{ route('admin.campaigns.store') }}" enctype="multipart/form-data">
                @include('admin.campaigns._form')
            </form>
        @else
            <div class="rounded-3xl border border-rose-200 bg-rose-50 p-6 text-rose-900">
                <div class="font-bold">غير مسموح</div>
                <div class="text-sm mt-1">لا تملك صلاحية إنشاء حملات.</div>
            </div>
        @endcan
    </div>
@endsection
