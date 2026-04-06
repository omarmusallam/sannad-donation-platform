@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Security settings' : 'إعدادات الأمان')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $accountUrl = locale_route('donor.dashboard');
        $profileUrl = locale_route('donor.profile');
        $updateUrl = locale_route('donor.security.update');
    @endphp

    <div class="max-w-7xl mx-auto space-y-8">
        <section class="relative overflow-hidden rounded-[32px] border border-border bg-surface p-6 sm:p-8 lg:p-10">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="text-sm text-subtext">
                        <a class="hover:underline underline-offset-4" href="{{ $accountUrl }}">
                            {{ $isEn ? 'Account' : 'الحساب' }}
                        </a>
                        <span class="mx-2">/</span>
                        <span class="font-semibold text-text">
                            {{ $isEn ? 'Security settings' : 'إعدادات الأمان' }}
                        </span>
                    </div>

                    <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-text">
                        {{ $isEn ? 'Security settings' : 'إعدادات الأمان' }}
                    </h1>

                    <p class="mt-2 text-subtext leading-relaxed max-w-2xl">
                        {{ $isEn
                            ? 'Protect your donor account by updating your password regularly.'
                            : 'قم بحماية حسابك عبر تحديث كلمة المرور بشكل دوري.' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ $profileUrl }}" class="btn btn-secondary">
                        {{ $isEn ? 'Profile settings' : 'إعدادات الحساب' }}
                    </a>
                    <a href="{{ $accountUrl }}" class="btn btn-primary">
                        {{ $isEn ? 'Back to account' : 'العودة للحساب' }}
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <aside class="xl:col-span-1 space-y-4">
                @include('donor.partials.account-nav')

                <div class="card-muted p-5">
                    <div class="font-black text-text">
                        {{ $isEn ? 'Security tips' : 'نصائح الأمان' }}
                    </div>

                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>• {{ $isEn ? 'Use a strong password.' : 'استخدم كلمة مرور قوية.' }}</li>
                        <li>• {{ $isEn ? 'Do not share your login details.' : 'لا تشارك بيانات الدخول.' }}</li>
                        <li>• {{ $isEn ? 'Update your password regularly.' : 'حدّث كلمة المرور بشكل دوري.' }}</li>
                    </ul>
                </div>
            </aside>

            <div class="xl:col-span-3">
                <div class="card p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-black text-text">
                            {{ $isEn ? 'Change password' : 'تغيير كلمة المرور' }}
                        </h2>
                        <p class="mt-1 text-sm text-subtext">
                            {{ $isEn ? 'Enter your current password and choose a new secure one.' : 'أدخل كلمة المرور الحالية ثم اختر كلمة مرور جديدة وآمنة.' }}
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $updateUrl }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-black mb-2 text-text">
                                {{ $isEn ? 'Current password' : 'كلمة المرور الحالية' }}
                            </label>
                            <input type="password" name="current_password" class="input" required>
                            @error('current_password')
                                <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'New password' : 'كلمة المرور الجديدة' }}
                                </label>
                                <input type="password" name="password" class="input" required>
                                @error('password')
                                    <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Confirm new password' : 'تأكيد كلمة المرور الجديدة' }}
                                </label>
                                <input type="password" name="password_confirmation" class="input" required>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button class="btn btn-primary" type="submit">
                                {{ $isEn ? 'Update password' : 'تحديث كلمة المرور' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
