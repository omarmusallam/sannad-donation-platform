@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Login' : 'تسجيل الدخول')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $siteName = (string) ($appSettings['site.name'] ?? config('app.name', 'GazaSannad'));

        $urlHome = locale_route('home');
        $urlDonate = locale_route('donate');
        $urlLoginSubmit = locale_route('donor.login.store');
        $urlRegister = locale_route('donor.register');

        $googleLoginUrl = locale_route('donor.social.redirect', ['provider' => 'google']);
        $facebookLoginUrl = locale_route('donor.social.redirect', ['provider' => 'facebook']);

        $emailError = $errors->has('email');
        $passwordError = $errors->has('password');

        $formErrors = collect($errors->all())->unique()->values();
    @endphp

    <div class="max-w-5xl mx-auto">
        <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface p-7 sm:p-10 mb-8">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <div class="text-sm text-subtext">
                        <a class="hover:underline underline-offset-4" href="{{ $urlHome }}">
                            {{ $isEn ? 'Home' : 'الرئيسية' }}
                        </a>
                        <span class="mx-2">/</span>
                        <span class="text-text font-semibold">
                            {{ $isEn ? 'Donor login' : 'دخول المتبرع' }}
                        </span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">
                        {{ $isEn ? 'Welcome back' : 'مرحباً بعودتك' }}
                    </h1>

                    <p class="mt-2 text-subtext leading-relaxed max-w-2xl">
                        {{ $isEn
                            ? 'Login to track your donations, download receipts, and manage your account.'
                            : 'سجّل دخولك لتتبع تبرعاتك، تحميل الإيصالات، وإدارة حسابك.' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="badge">{{ $isEn ? 'Receipts' : 'الإيصالات' }}</span>
                        <span class="badge">{{ $isEn ? 'History' : 'السجل' }}</span>
                        <span class="badge">{{ $isEn ? 'Faster checkout' : 'تبرع أسرع' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-surface/70 p-5 text-sm max-w-md">
                    <div class="font-black text-text mb-2">{{ $isEn ? 'Tip' : 'معلومة' }}</div>
                    <div class="text-subtext leading-relaxed">
                        {{ $isEn
                            ? 'If you donated before without an account, you can still create one later using the same email.'
                            : 'إذا تبرعت سابقاً بدون حساب، يمكنك إنشاء حساب لاحقاً بنفس البريد.' }}
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6 sm:p-8 space-y-5">
                <div>
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Secure access' : 'دخول آمن' }}
                    </div>
                    <div class="mt-1 text-2xl font-black text-text">
                        {{ $isEn ? 'Login to your account' : 'الدخول إلى حسابك' }}
                    </div>
                </div>

                @if ($errors->has('social'))
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('social') }}
                    </div>
                @endif

                @if ($formErrors->isNotEmpty() && !$errors->has('social'))
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                        <div class="text-sm font-black text-red-800">
                            {{ $isEn ? 'Please review the highlighted fields.' : 'يرجى مراجعة الحقول المظللة بالأخطاء.' }}
                        </div>

                        <ul class="mt-2 space-y-1 text-sm text-red-700 list-disc ps-5">
                            @foreach ($formErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ $googleLoginUrl }}"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl border border-border bg-white px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 shadow-sm">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                            <path fill="#EA4335"
                                d="M12 10.2v3.9h5.5c-.2 1.3-.8 2.3-1.7 3.1l2.8 2.2c1.6-1.5 2.5-3.7 2.5-6.4 0-.6-.1-1.2-.2-1.8H12z" />
                            <path fill="#34A853"
                                d="M12 21c2.4 0 4.5-.8 6-2.2l-2.8-2.2c-.8.5-1.8.9-3.2.9-2.4 0-4.5-1.7-5.2-3.9l-2.9 2.2C5.4 18.8 8.4 21 12 21z" />
                            <path fill="#4A90E2"
                                d="M6.8 13.6c-.2-.5-.3-1-.3-1.6s.1-1.1.3-1.6L3.9 8.2C3.3 9.4 3 10.7 3 12s.3 2.6.9 3.8l2.9-2.2z" />
                            <path fill="#FBBC05"
                                d="M12 6.5c1.3 0 2.4.4 3.3 1.3l2.5-2.5C16.5 3.9 14.4 3 12 3 8.4 3 5.4 5.2 3.9 8.2l2.9 2.2C7.5 8.2 9.6 6.5 12 6.5z" />
                        </svg>
                        <span>{{ $isEn ? 'Continue with Google' : 'المتابعة عبر Google' }}</span>
                    </a>

                    <a href="{{ $facebookLoginUrl }}"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-white transition border border-transparent"
                        style="background-color: #1877F2;" onmouseover="this.style.backgroundColor='#166FE5'"
                        onmouseout="this.style.backgroundColor='#1877F2'">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                            <path
                                d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.1V12h2.1V9.8c0-2.1 1.2-3.3 3.2-3.3.9 0 1.9.2 1.9.2v2.1h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.5l-.4 2.9h-2.1v7A10 10 0 0 0 22 12z" />
                        </svg>
                        <span>{{ $isEn ? 'Continue with Facebook' : 'المتابعة عبر Facebook' }}</span>
                    </a>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-border"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-surface px-3 text-subtext">
                            {{ $isEn ? 'or continue with email' : 'أو تابع باستخدام البريد' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ $urlLoginSubmit }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Email' : 'البريد الإلكتروني' }}
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="255"
                            autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" dir="ltr"
                            placeholder="name@example.com" required
                            class="input {{ $emailError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                            aria-invalid="{{ $emailError ? 'true' : 'false' }}"
                            @if ($emailError) aria-describedby="email-error" @endif>

                        @error('email')
                            <div id="email-error" class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Password' : 'كلمة المرور' }}
                        </label>

                        <input id="password" type="password" name="password" maxlength="128"
                            autocomplete="current-password" dir="ltr"
                            placeholder="{{ $isEn ? 'Enter your password' : 'أدخل كلمة المرور' }}" required
                            class="input {{ $passwordError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                            aria-invalid="{{ $passwordError ? 'true' : 'false' }}"
                            @if ($passwordError) aria-describedby="password-error" @endif>

                        @error('password')
                            <div id="password-error" class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <label for="remember" class="flex items-center gap-2 text-sm">
                            <input id="remember" type="checkbox" name="remember" value="1"
                                {{ old('remember') ? 'checked' : '' }}
                                class="h-5 w-5 rounded border-border text-brand focus:ring-2 focus:ring-[rgba(var(--brand),.25)]">
                            <span class="font-black text-text">
                                {{ $isEn ? 'Remember me' : 'تذكرني' }}
                            </span>
                        </label>

                        {{-- <span class="text-xs text-subtext">
                            {{ $isEn ? 'Forgot password? (soon)' : 'نسيت كلمة المرور؟ (قريباً)' }}
                        </span> --}}
                    </div>

                    <button class="w-full btn btn-primary" type="submit">
                        {{ $isEn ? 'Login' : 'تسجيل الدخول' }}
                        <span aria-hidden="true">{{ $isEn ? '→' : '←' }}</span>
                    </button>
                </form>

                <div class="text-sm text-subtext text-center pt-2">
                    {{ $isEn ? "Don't have an account?" : 'ليس لديك حساب؟' }}
                    <a class="font-black text-text hover:underline underline-offset-4" href="{{ $urlRegister }}">
                        {{ $isEn ? 'Create one' : 'أنشئ حساباً' }}
                    </a>
                </div>
            </div>

            <aside class="h-fit">
                <div class="card p-6 sm:p-8 space-y-4">
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Why login?' : 'لماذا تسجيل الدخول؟' }}
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Track donations' : 'تتبع التبرعات' }}</div>
                            <div class="text-subtext mt-1">
                                {{ $isEn ? 'See all your donations in one place.' : 'شاهد كل تبرعاتك في مكان واحد.' }}
                            </div>
                        </div>

                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Access receipts' : 'الوصول إلى الإيصالات' }}
                            </div>
                            <div class="text-subtext mt-1">
                                {{ $isEn ? 'Open and download official receipts any time.' : 'افتح وحمّل الإيصالات الرسمية في أي وقت.' }}
                            </div>
                        </div>

                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Faster next time' : 'أسرع في المرات القادمة' }}
                            </div>
                            <div class="text-subtext mt-1">
                                {{ $isEn ? 'Complete future donations with less effort.' : 'أكمل التبرعات القادمة بسهولة أكبر.' }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ $urlDonate }}" class="btn btn-secondary w-full text-center">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                    </a>

                    <a href="{{ $urlHome }}" class="btn btn-secondary w-full text-center">
                        {{ $isEn ? 'Back to home' : 'العودة للرئيسية' }}
                    </a>

                    <div class="card-muted p-4 text-sm">
                        <div class="font-black text-text mb-1">{{ $siteName }}</div>
                        <div class="text-subtext leading-relaxed">
                            {{ $isEn
                                ? 'Your account keeps receipts and donation history organized in one secure place.'
                                : 'حسابك يحفظ الإيصالات وسجل التبرعات بشكل منظم وآمن في مكان واحد.' }}
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
