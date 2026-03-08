@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Receipt not found' : 'إيصال غير موجود')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $homeUrl = locale_route('home');
    @endphp

    <div class="max-w-xl mx-auto">
        <div class="card p-8 text-center">
            <div class="text-2xl font-black text-text">
                {{ $isEn ? 'Receipt not found' : 'الإيصال غير موجود' }}
            </div>

            <p class="mt-3 text-subtext leading-relaxed">
                {{ $isEn ? 'The link is invalid or the receipt was removed.' : 'الرابط غير صحيح أو تم حذف الإيصال.' }}
            </p>

            <a href="{{ $homeUrl }}" class="mt-6 inline-flex justify-center btn btn-primary">
                {{ $isEn ? 'Back to website' : 'العودة للموقع' }}
            </a>
        </div>
    </div>
@endsection
