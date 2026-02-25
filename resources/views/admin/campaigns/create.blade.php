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
        </div>

        <form method="POST" action="{{ route('admin.campaigns.store') }}" enctype="multipart/form-data">
            @include('admin.campaigns._form')
        </form>
    </div>
@endsection
