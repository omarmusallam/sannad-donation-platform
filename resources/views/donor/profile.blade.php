@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Profile settings' : 'إعدادات الحساب')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $accountUrl = locale_route('donor.dashboard');
        $securityUrl = locale_route('donor.security');
        $updateUrl = locale_route('donor.profile.update');

        $donorName = $donor->name ?: ($isEn ? 'Donor' : 'متبرع');
        $donorInitial = mb_strtoupper(mb_substr($donorName, 0, 1));
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
                            {{ $isEn ? 'Profile settings' : 'إعدادات الحساب' }}
                        </span>
                    </div>

                    <div class="mt-5 flex items-center gap-4">
                        <div
                            class="h-16 w-16 rounded-3xl border border-border bg-muted grid place-items-center text-2xl font-black text-text shrink-0">
                            {{ $donorInitial }}
                        </div>

                        <div>
                            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-text">
                                {{ $isEn ? 'Profile settings' : 'إعدادات الحساب' }}
                            </h1>
                            <p class="mt-2 text-subtext leading-relaxed max-w-2xl">
                                {{ $isEn
                                    ? 'Manage your personal details and keep your donor account updated.'
                                    : 'قم بإدارة بياناتك الشخصية والحفاظ على تحديث حساب المتبرع الخاص بك.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ $accountUrl }}" class="btn btn-secondary">
                        {{ $isEn ? 'Back to account' : 'العودة للحساب' }}
                    </a>
                    <a href="{{ $securityUrl }}" class="btn btn-primary">
                        {{ $isEn ? 'Security settings' : 'إعدادات الأمان' }}
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <aside class="xl:col-span-1 space-y-4">
                @include('donor.partials.account-nav')

                <div class="card-muted p-5">
                    <div class="font-black text-text">
                        {{ $isEn ? 'Account note' : 'ملاحظة الحساب' }}
                    </div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">
                        {{ $isEn
                            ? 'Keep your email and phone updated so your account stays organized and reachable.'
                            : 'احرص على تحديث البريد والهاتف ليبقى حسابك منظمًا ويسهل التواصل معك عند الحاجة.' }}
                    </div>
                </div>
            </aside>

            <div class="xl:col-span-3">
                <div class="card p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-black text-text">
                            {{ $isEn ? 'Personal information' : 'البيانات الشخصية' }}
                        </h2>
                        <p class="mt-1 text-sm text-subtext">
                            {{ $isEn ? 'Update your account details below.' : 'قم بتحديث بيانات حسابك من النموذج التالي.' }}
                        </p>
                    </div>

                    @if (session('success'))
                        <div
                            class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $updateUrl }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Full name' : 'الاسم الكامل' }}
                                </label>
                                <input type="text" name="name" value="{{ old('name', $donor->name) }}"
                                    class="input">
                                @error('name')
                                    <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Email address' : 'البريد الإلكتروني' }}
                                </label>
                                <input type="email" name="email" value="{{ old('email', $donor->email) }}"
                                    class="input" required>
                                @error('email')
                                    <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Phone' : 'الهاتف' }}
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $donor->phone) }}"
                                    class="input">
                                @error('phone')
                                    <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-black mb-2 text-text">
                                    {{ $isEn ? 'Country' : 'الدولة' }}
                                </label>
                                <input type="text" name="country" value="{{ old('country', $donor->country) }}"
                                    class="input">
                                @error('country')
                                    <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-3 flex flex-col sm:flex-row gap-3">
                            <button class="btn btn-primary" type="submit">
                                {{ $isEn ? 'Save changes' : 'حفظ التغييرات' }}
                            </button>

                            <a href="{{ $securityUrl }}" class="btn btn-secondary">
                                {{ $isEn ? 'Go to security' : 'الانتقال إلى الأمان' }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
