@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'Create account' : 'إنشاء حساب')

@section('content')
    @php
        $isEn = app()->isLocale('en');

        $siteName = (string) ($appSettings['site.name'] ?? config('app.name', 'GazaSannad'));

        $urlHome = locale_route('home');
        $urlDonate = locale_route('donate');
        $urlRegisterSubmit = locale_route('donor.register.store');
        $urlLogin = locale_route('donor.login');

        $googleLoginUrl = locale_route('donor.social.redirect', ['provider' => 'google']);
        $facebookLoginUrl = locale_route('donor.social.redirect', ['provider' => 'facebook']);

        $nameError = $errors->has('name');
        $emailError = $errors->has('email');
        $passwordError = $errors->has('password');
        $passwordConfirmationError = $errors->has('password_confirmation');

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
                            {{ $isEn ? 'Create donor account' : 'إنشاء حساب متبرع' }}
                        </span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">
                        {{ $isEn ? 'Create your account' : 'أنشئ حسابك' }}
                    </h1>

                    <p class="mt-2 text-subtext leading-relaxed max-w-2xl">
                        {{ $isEn
                            ? 'A donor account helps you track donations, download receipts, and donate faster next time.'
                            : 'حساب المتبرع يساعدك على تتبع التبرعات، تحميل الإيصالات، والتبرع بسرعة في المرات القادمة.' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="badge">{{ $isEn ? 'Donation history' : 'سجل التبرعات' }}</span>
                        <span class="badge">{{ $isEn ? 'Receipts' : 'الإيصالات' }}</span>
                        <span class="badge">{{ $isEn ? 'Privacy controls' : 'الخصوصية' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-surface/70 p-5 text-sm max-w-md">
                    <div class="font-black text-text mb-2">{{ $isEn ? 'Note' : 'تنبيه' }}</div>
                    <div class="text-subtext leading-relaxed">
                        {{ $isEn
                            ? 'We use your email to connect your receipts and confirmations. You can still donate anonymously at any time.'
                            : 'نستخدم بريدك الإلكتروني لربط الإيصالات ورسائل التأكيد. ويمكنك التبرع كمجهول في أي وقت.' }}
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6 sm:p-8 space-y-5">
                <div>
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Create account securely' : 'إنشاء حساب بشكل آمن' }}
                    </div>
                    <div class="mt-1 text-2xl font-black text-text">
                        {{ $isEn ? 'Get started' : 'ابدأ الآن' }}
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
                            {{ $isEn ? 'or create with email' : 'أو أنشئ الحساب بالبريد الإلكتروني' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ $urlRegisterSubmit }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-black mb-2 text-text">
                            {{ $isEn ? 'Name (optional)' : 'الاسم (اختياري)' }}
                        </label>

                        <input id="name" type="text" name="name" value="{{ old('name') }}" maxlength="120"
                            autocomplete="name" placeholder="{{ $isEn ? 'Your full name' : 'اسمك الكامل' }}"
                            class="input {{ $nameError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                            aria-invalid="{{ $nameError ? 'true' : 'false' }}"
                            @if ($nameError) aria-describedby="name-error" @endif>

                        @error('name')
                            <div id="name-error" class="text-red-600 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

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

                    <div class="rounded-2xl border border-border bg-muted/40 p-4 text-sm">
                        <div class="font-black text-text mb-1">
                            {{ $isEn ? 'Password requirements' : 'متطلبات كلمة المرور' }}
                        </div>
                        <div class="text-subtext leading-relaxed">
                            {{ $isEn
                                ? 'Use at least 10 characters, including uppercase and lowercase letters, at least one number, and at least one symbol.'
                                : 'استخدم 10 أحرف على الأقل، مع حرف كبير وحرف صغير ورقم واحد على الأقل ورمز واحد على الأقل.' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-black mb-2 text-text">
                                {{ $isEn ? 'Password' : 'كلمة المرور' }}
                            </label>

                            <input id="password" type="password" name="password" minlength="10" autocomplete="new-password"
                                dir="ltr"
                                placeholder="{{ $isEn ? 'Create a strong password' : 'أنشئ كلمة مرور قوية' }}" required
                                class="input {{ $passwordError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                                aria-invalid="{{ $passwordError ? 'true' : 'false' }}"
                                @if ($passwordError) aria-describedby="password-error" @endif>

                            @error('password')
                                <div id="password-error" class="text-red-600 text-xs mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-black mb-2 text-text">
                                {{ $isEn ? 'Confirm password' : 'تأكيد كلمة المرور' }}
                            </label>

                            <input id="password_confirmation" type="password" name="password_confirmation"
                                minlength="10" autocomplete="new-password" dir="ltr"
                                placeholder="{{ $isEn ? 'Re-enter your password' : 'أعد إدخال كلمة المرور' }}" required
                                class="input {{ $passwordConfirmationError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                                aria-invalid="{{ $passwordConfirmationError ? 'true' : 'false' }}"
                                @if ($passwordConfirmationError) aria-describedby="password-confirmation-error" @endif>

                            @error('password_confirmation')
                                <div id="password-confirmation-error" class="text-red-600 text-xs mt-2">{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-muted p-4 text-sm">
                        <div class="font-black text-text mb-1">{{ $isEn ? 'Privacy' : 'الخصوصية' }}</div>
                        <div class="text-subtext leading-relaxed">
                            {{ $isEn
                                ? 'You can still donate anonymously at any time from the donation page.'
                                : 'يمكنك التبرع كمجهول في أي وقت من صفحة التبرع.' }}
                        </div>
                    </div>

                    <button class="w-full btn btn-primary" type="submit">
                        {{ $isEn ? 'Create account' : 'إنشاء حساب' }}
                        <span aria-hidden="true">{{ $isEn ? '→' : '←' }}</span>
                    </button>
                </form>

                <div class="text-sm text-subtext text-center pt-2">
                    {{ $isEn ? 'Already have an account?' : 'لديك حساب مسبقاً؟' }}
                    <a class="font-black text-text hover:underline underline-offset-4" href="{{ $urlLogin }}">
                        {{ $isEn ? 'Login' : 'تسجيل الدخول' }}
                    </a>
                </div>
            </div>

            <aside class="h-fit">
                <div class="card p-6 sm:p-8 space-y-4">
                    <div class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Why create an account?' : 'لماذا إنشاء حساب؟' }}
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Track donations' : 'تتبع التبرعات' }}</div>
                            <div class="text-subtext mt-1">
                                {{ $isEn ? 'See your full donation history in one place.' : 'اعرض سجل تبرعاتك الكامل في مكان واحد.' }}
                            </div>
                        </div>

                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Download receipts' : 'تحميل الإيصالات' }}</div>
                            <div class="text-subtext mt-1">
                                {{ $isEn
                                    ? 'Access your receipt PDFs quickly whenever you need them.'
                                    : 'الوصول إلى ملفات الإيصالات PDF بسهولة عند الحاجة.' }}
                            </div>
                        </div>

                        <div class="card-muted p-4">
                            <div class="font-black text-text">{{ $isEn ? 'Faster donations' : 'تبرع أسرع' }}</div>
                            <div class="text-subtext mt-1">
                                {{ $isEn
                                    ? 'Use your saved account details for a smoother donation flow next time.'
                                    : 'استخدم بياناتك المحفوظة لتجربة تبرع أسرع وأسهل في المرات القادمة.' }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ $urlDonate }}" class="btn btn-secondary w-full text-center">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                    </a>

                    <div class="card-muted p-4 text-sm">
                        <div class="font-black text-text mb-1">{{ $siteName }}</div>
                        <div class="text-subtext leading-relaxed">
                            {{ $isEn
                                ? 'You stay in control: donate with your name or anonymously.'
                                : 'أنت المتحكم: يمكنك التبرع باسمك أو كمجهول.' }}
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
