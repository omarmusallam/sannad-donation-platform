<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $isAr = app()->isLocale('ar');

        $setting = function (string $key, $default = null) {
            try {
                return function_exists('setting') ? setting($key, $default) : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        };

        $siteName = (string) $setting('site.name', config('app.name', 'GazaSannad'));
        $faviconPath = $setting('site.favicon');
        $logoPath = $setting('site.logo');
    @endphp

    <title>{{ $isAr ? 'تسجيل دخول الإدارة' : 'Admin Login' }} - {{ $siteName }}</title>

    @if (!empty($faviconPath))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $faviconPath) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            {{-- Card --}}
            <div class="bg-white/95 backdrop-blur rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="p-6 border-b border-slate-200/70">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-2xl overflow-hidden shrink-0 border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 grid place-items-center">
                            @if (!empty($logoPath))
                                <img src="{{ asset('storage/' . $logoPath) }}" class="w-full h-full object-cover"
                                    alt="{{ $siteName }}">
                            @else
                                <span class="text-sm font-extrabold text-slate-800">
                                    {{ mb_substr($siteName, 0, 1) }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="text-xs text-slate-500">
                                {{ $isAr ? 'لوحة التحكم' : 'Admin Panel' }}
                            </div>
                            <div class="text-lg font-extrabold tracking-tight truncate">
                                {{ $siteName }}
                            </div>
                        </div>

                        <span
                            class="ms-auto text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200/70">
                            {{ $isAr ? 'إدارة' : 'Admin' }}
                        </span>
                    </div>

                    <h1 class="mt-5 text-xl font-extrabold tracking-tight">
                        {{ $isAr ? 'تسجيل الدخول' : 'Sign in' }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $isAr ? 'ادخل بياناتك للوصول إلى لوحة التحكم.' : 'Enter your credentials to access the dashboard.' }}
                    </p>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <div class="font-bold">{{ $isAr ? 'تحقق من المدخلات' : 'Please check your inputs' }}</div>
                            <ul class="text-sm mt-2 list-disc px-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                {{ $isAr ? 'البريد الإلكتروني' : 'Email' }}
                            </label>
                            <input name="email" type="email" autocomplete="email" value="{{ old('email') }}"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none
                                       focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                            @error('email')
                                <div class="text-sm text-rose-700 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                {{ $isAr ? 'كلمة المرور' : 'Password' }}
                            </label>
                            <input name="password" type="password" autocomplete="current-password" required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none
                                       focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                            @error('password')
                                <div class="text-sm text-rose-700 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember --}}
                        <div class="flex items-center justify-between gap-3">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="remember"
                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-200">
                                <span>{{ $isAr ? 'تذكرني' : 'Remember me' }}</span>
                            </label>

                            {{-- Optional placeholder for future "Forgot password" --}}
                            {{-- <a href="#" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                {{ $isAr ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                            </a> --}}
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full rounded-2xl py-3 text-sm font-extrabold text-white shadow-sm
                                   bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800
                                   hover:opacity-95 active:opacity-90 transition">
                            {{ $isAr ? 'دخول' : 'Sign in' }}
                        </button>

                        <div class="pt-2 text-center">
                            <a href="{{ route('home') }}"
                                class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                {{ $isAr ? 'العودة للموقع' : 'Back to site' }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Footer --}}
            <p class="mt-4 text-center text-xs text-slate-500">
                {{ $isAr ? 'هذه الصفحة مخصصة للمدراء فقط.' : 'This area is for administrators only.' }}
            </p>
        </div>
    </div>
</body>

</html>
