@extends('layouts.admin')
@section('title', 'إضافة صفحة')
@section('page_title', 'إضافة صفحة ثابتة')

@section('content')
    <form method="POST" action="{{ route('admin.pages.store') }}">
        @csrf
        @include('admin.pages._form', ['page' => null])
    </form>
@endsection
