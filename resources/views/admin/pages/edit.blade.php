@extends('layouts.admin')
@section('title', 'تعديل صفحة')
@section('page_title', 'تعديل صفحة ثابتة')

@section('content')
    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf @method('PUT')
        @include('admin.pages._form', ['page' => $page])
    </form>
@endsection
