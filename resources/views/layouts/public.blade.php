{{-- resources/views/layouts/public.blade.php --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}" class="scrollbar-thin">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $isAr = app()->isLocale('ar');

        // Shared settings from AppServiceProvider
        $settings = $appSettings ?? [];

        // Brand
        $siteName = (string) ($settings['site.name'] ?? config('app.name', 'GazaSannad'));
        $tagline = (string) ($settings['site.tagline'] ?? '');

        // SEO defaults
        $seoTitle = (string) ($settings['seo.meta_title'] ?? $siteName);
        $seoDesc =
            (string) ($settings['seo.meta_description'] ??
                ($tagline ?: ($isAr ? 'منصة حملات وتبرعات' : 'Campaigns & Donations Platform')));

        // Per-page overrides
        $pageTitle = trim($__env->yieldContent('title', ''));
        $fullTitle = $pageTitle ? $pageTitle . ' — ' . $seoTitle : $seoTitle;

        $pageDesc = trim($__env->yieldContent('meta_description', ''));
        $finalDesc = $pageDesc ?: $seoDesc;

        // Assets
        $faviconPath = $settings['site.favicon'] ?? null;
        $logoPath = $settings['site.logo'] ?? null;

        $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : null;
        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;

        // Theme
        $themeColor = (string) ($settings['site.theme_color'] ?? '#0f172a');

        // Canonical / hreflang
        $canonical = url()->current();

        $path = trim(request()->path(), '/');
        $isEnPath = str_starts_with($path, 'en');
        $cleanPath = $isEnPath ? preg_replace('/^en(\/)?/', '', $path) : $path;

        $arUrl = url('/' . ltrim($cleanPath, '/'));
        $enUrl = url('/en' . ($cleanPath ? '/' . ltrim($cleanPath, '/') : ''));

        $arUrl = rtrim($arUrl, '/');
        if ($arUrl === '') {
            $arUrl = url('/');
        }

        $enUrl = rtrim($enUrl, '/');
        if ($enUrl === '') {
            $enUrl = url('/en');
        }

        // Contact
        $contactEmail = $settings['contact.email'] ?? null;
        $contactPhone = $settings['contact.phone'] ?? null;
        $contactWhats = $settings['contact.whatsapp'] ?? null;

        // Social
        $socialLinks = $settings['social.links'] ?? [];
        if (!is_array($socialLinks)) {
            $socialLinks = [];
        }

        $normalizeUrl = function ($url) {
            if (!is_string($url)) {
                return null;
            }

            $url = trim($url);
            if ($url === '') {
                return null;
            }

            $url = ltrim($url, '/');

            if (!preg_match('~^https?://~i', $url)) {
                $url = 'https://' . $url;
            }

            return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
        };

        $social = [
            'facebook' => $normalizeUrl($socialLinks['facebook'] ?? null),
            'x' => $normalizeUrl($socialLinks['x'] ?? null),
            'instagram' => $normalizeUrl($socialLinks['instagram'] ?? null),
            'youtube' => $normalizeUrl($socialLinks['youtube'] ?? null),
        ];

        $hasSocial = !empty(array_filter($social));

        // Main localized links
        $urlHome = locale_route('home');
        $urlCampaigns = locale_route('campaigns.index');
        $urlTransparency = locale_route('transparency');
        $urlDonate = locale_route('donate');
        $urlReports = locale_route('reports.index');

        // Public pages
        $publicPages = $publicPages ?? collect();

        $staticLinks = [
            ['label' => $isAr ? 'الرئيسية' : 'Home', 'url' => $urlHome],
            ['label' => $isAr ? 'الحملات' : 'Campaigns', 'url' => $urlCampaigns],
            ['label' => $isAr ? 'التبرع' : 'Donate', 'url' => $urlDonate],
            ['label' => $isAr ? 'الشفافية' : 'Transparency', 'url' => $urlTransparency],
            ['label' => $isAr ? 'التقارير' : 'Reports', 'url' => $urlReports],
        ];

        $pageLinks = [];
        foreach ($publicPages as $p) {
            $title = method_exists($p, 'title') ? $p->title() : ($isAr ? $p->title_ar : $p->title_en ?? $p->title_ar);

            $pageLinks[] = [
                'label' => $title,
                'url' => locale_route('pages.show', ['page' => $p->slug]),
            ];
        }

        $footerLinks = array_merge($staticLinks, $pageLinks);

        // Active helpers
        $isActive = function ($pattern) {
            return request()->is($pattern) || request()->is(trim($pattern, '/') . '/*');
        };

        // Alignment helpers
        $edgeStart = $isAr ? 'right' : 'left';
        $edgeEnd = $isAr ? 'left' : 'right';
        $donateMargin = $isAr ? 'mr-2' : 'ml-2';

        // Search
        $searchQ = (string) request()->query('q', '');

        // Donor auth
        $donor = null;
        try {
            $donor = auth('donor')->user();
        } catch (\Throwable $e) {
            $donor = null;
        }

        $donorName = $donor?->name ?? '';
        $donorInitial = $donorName ? mb_strtoupper(mb_substr($donorName, 0, 1)) : 'U';

        $localizedDonorLoginRoute = app()->isLocale('en') ? 'en.donor.login' : 'donor.login';
        $localizedDonorRegisterRoute = app()->isLocale('en') ? 'en.donor.register' : 'donor.register';
        $localizedDonorLogoutRoute = app()->isLocale('en') ? 'en.donor.logout' : 'donor.logout';
        $localizedDonorDashboardRoute = app()->isLocale('en') ? 'en.donor.dashboard' : 'donor.dashboard';

        $hasDonorLogin = \Illuminate\Support\Facades\Route::has($localizedDonorLoginRoute);
        $hasDonorRegister = \Illuminate\Support\Facades\Route::has($localizedDonorRegisterRoute);
        $hasDonorLogout = \Illuminate\Support\Facades\Route::has($localizedDonorLogoutRoute);
        $hasDonorDashboard = \Illuminate\Support\Facades\Route::has($localizedDonorDashboardRoute);

        $donorDashboardUrl = $hasDonorDashboard
            ? locale_route('donor.dashboard')
            : ($isAr
                ? url('/account')
                : url('/en/account'));

        // Simple icons
        $icon = function (string $k) {
            return match ($k) {
                'facebook' => '<span aria-hidden="true" class="font-black">f</span>',
                'x' => '<span aria-hidden="true" class="font-black">X</span>',
                'instagram' => '<span aria-hidden="true" class="font-black">◎</span>',
                'youtube' => '<span aria-hidden="true" class="font-black">▶</span>',
                'sun' => '<span aria-hidden="true" class="font-black">☀</span>',
                'moon' => '<span aria-hidden="true" class="font-black">☾</span>',
                'search' => '<span aria-hidden="true" class="font-black">⌕</span>',
                default => '',
            };
        };
    @endphp

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $finalDesc }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="{{ $themeColor }}">

    {{-- hreflang --}}
    <link rel="alternate" href="{{ $arUrl }}" hreflang="ar">
    <link rel="alternate" href="{{ $enUrl }}" hreflang="en">
    <link rel="alternate" href="{{ $canonical }}" hreflang="x-default">

    {{-- OpenGraph --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $finalDesc }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="{{ $isAr ? 'ar_AR' : 'en_US' }}">
    @if ($logoUrl)
        <meta property="og:image" content="{{ $logoUrl }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $finalDesc }}">
    @if ($logoUrl)
        <meta name="twitter:image" content="{{ $logoUrl }}">
    @endif

    {{-- Favicons --}}
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%230f172a'/%3E%3Ctext x='50' y='62' font-size='56' text-anchor='middle' fill='white' font-family='Arial'%3E{{ rawurlencode(mb_substr($siteName, 0, 1)) }}%3C/text%3E%3C/svg%3E">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')

    {{-- Theme init --}}
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = saved || (prefersDark ? 'dark' : 'light');

                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
</head>

<body class="bg-bg text-text selection:bg-text selection:text-bg">

    {{-- Skip link --}}
    <a href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:{{ $edgeStart }}-4 focus:z-50 focus:btn focus:btn-secondary">
        {{ $isAr ? 'تخطي إلى المحتوى' : 'Skip to content' }}
    </a>

    {{-- Background --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute inset-x-0 -top-28 h-[560px] bg-gradient-to-b from-muted via-bg to-transparent"></div>

        <div class="absolute -left-48 top-24 h-80 w-80 rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, rgba(var(--brand),.18), transparent 60%);"></div>

        <div class="absolute -right-48 top-16 h-80 w-80 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, rgba(var(--brand2),.16), transparent 60%);"></div>

        <div class="absolute inset-x-0 top-[420px] h-px bg-gradient-to-r from-transparent via-border/70 to-transparent">
        </div>
    </div>

    {{-- Top bar --}}
    <div class="hidden md:block border-b border-border/70 bg-bg/70 backdrop-blur">
        <div class="container-app py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs text-subtext">
            <div class="flex flex-wrap items-center gap-4">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-success"></span>
                    <span class="font-black">
                        {{ $isAr ? 'هوية واضحة وتقارير موثقة' : 'Clear identity and verified reporting' }}
                    </span>
                </span>

                @if ($contactEmail)
                    <span class="text-subtext/40">•</span>
                    <a class="hover:underline underline-offset-4"
                        href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                @endif

                @if ($contactPhone)
                    <span class="text-subtext/40">•</span>
                    <a class="hover:underline underline-offset-4"
                        href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2">
                <button id="themeToggle"
                    class="px-2.5 py-1 rounded-lg border border-border hover:bg-muted transition font-black"
                    type="button" aria-label="Toggle theme">
                    <span id="themeIcon">{!! $icon('moon') !!}</span>
                </button>

                @if ($hasSocial)
                    @foreach ($social as $key => $url)
                        @continue(empty($url))
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                            class="px-2 py-1 rounded-lg hover:bg-muted border border-transparent hover:border-border/70 transition"
                            aria-label="{{ $key }}">
                            {!! $icon($key) !!}
                        </a>
                    @endforeach
                @endif

                <a class="px-2.5 py-1 rounded-lg border border-border hover:bg-muted transition font-black"
                    href="{{ switch_locale_url() }}" rel="nofollow">
                    {{ $isEnPath ? 'AR' : 'EN' }}
                </a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 bg-bg/85 backdrop-blur border-b border-border/70">
        <div class="container-app py-3.5 flex items-center justify-between gap-3 sm:gap-4">

            {{-- Brand --}}
            <a href="{{ $urlHome }}" class="flex items-center gap-3 min-w-0">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}"
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl border border-border object-cover bg-surface shrink-0 shadow-sm"
                        loading="eager" decoding="async" referrerpolicy="no-referrer">
                @else
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl border border-border bg-muted grid place-items-center shrink-0 shadow-sm">
                        <span class="font-black text-subtext text-lg">{{ mb_substr($siteName, 0, 1) }}</span>
                    </div>
                @endif

                <div class="min-w-0 leading-tight">
                    <div class="font-black text-base sm:text-lg truncate">{{ $siteName }}</div>
                    @if (!empty($tagline))
                        <div class="text-xs text-subtext truncate">{{ $tagline }}</div>
                    @endif
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden xl:flex items-center gap-3">
                <nav class="flex items-center gap-1 text-sm">
                    @php
                        $activeCampaigns = $isActive($isEnPath ? 'en/campaigns' : 'campaigns');
                        $activeTransparency = $isActive($isEnPath ? 'en/transparency' : 'transparency');
                        $activeReports = $isActive($isEnPath ? 'en/transparency/reports' : 'transparency/reports');
                    @endphp

                    <a href="{{ $urlCampaigns }}"
                        class="navlink {{ $activeCampaigns ? 'navlink-active' : 'navlink-idle' }}">
                        {{ $isAr ? 'الحملات' : 'Campaigns' }}
                    </a>

                    <a href="{{ $urlTransparency }}"
                        class="navlink {{ $activeTransparency ? 'navlink-active' : 'navlink-idle' }}">
                        {{ $isAr ? 'الشفافية' : 'Transparency' }}
                    </a>

                    <a href="{{ $urlReports }}"
                        class="navlink {{ $activeReports ? 'navlink-active' : 'navlink-idle' }}">
                        {{ $isAr ? 'التقارير' : 'Reports' }}
                    </a>

                    @if ($publicPages->count())
                        <details class="relative">
                            <summary
                                class="navlink navlink-idle list-none cursor-pointer select-none [&::-webkit-details-marker]:hidden">
                                {{ $isAr ? 'المزيد' : 'More' }}
                            </summary>

                            <div
                                class="absolute {{ $edgeEnd }}-0 mt-2 w-72 bg-surface border border-border rounded-2xl shadow-soft overflow-hidden">
                                @foreach ($publicPages as $p)
                                    @php
                                        $t = method_exists($p, 'title')
                                            ? $p->title()
                                            : ($isAr
                                                ? $p->title_ar
                                                : $p->title_en ?? $p->title_ar);
                                    @endphp

                                    <a href="{{ locale_route('pages.show', ['page' => $p->slug]) }}"
                                        class="block px-4 py-3 text-sm hover:bg-muted font-semibold text-subtext">
                                        {{ $t }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </nav>

                <form action="{{ $urlCampaigns }}" method="get" class="relative shrink-0">
                    <span class="absolute {{ $edgeStart }}-3 top-1/2 -translate-y-1/2 text-subtext/70 text-sm">
                        {!! $icon('search') !!}
                    </span>
                    <input name="q" value="{{ $searchQ }}" class="input ps-9 w-[260px] 2xl:w-[300px]"
                        placeholder="{{ $isAr ? 'ابحث عن حملة...' : 'Search campaigns...' }}">
                </form>

                @if (!empty($donor))
                    <details class="relative group">
                        <summary
                            class="list-none [&::-webkit-details-marker]:hidden cursor-pointer select-none inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-border bg-surface hover:bg-muted transition font-black text-sm">
                            <span
                                class="w-8 h-8 rounded-xl grid place-items-center border border-border bg-muted text-subtext">
                                {{ $donorInitial }}
                            </span>
                            <span class="max-w-[160px] truncate text-text">{{ $donorName }}</span>
                            <span class="text-subtext/70 transition group-open:rotate-180">▾</span>
                        </summary>

                        <div
                            class="absolute {{ $edgeEnd }}-0 mt-2 w-72 rounded-2xl border border-border bg-surface shadow-soft overflow-hidden">
                            <div class="px-4 py-3">
                                <div class="text-xs text-subtext">{{ $isAr ? 'حساب المتبرع' : 'Donor account' }}</div>
                                <div class="mt-1 font-black text-text truncate">{{ $donorName }}</div>
                            </div>

                            <div class="h-px bg-border/70"></div>

                            @php
                                $donorProfileUrl = \Illuminate\Support\Facades\Route::has('donor.profile')
                                    ? locale_route('donor.profile')
                                    : null;

                                $donorSecurityUrl = \Illuminate\Support\Facades\Route::has('donor.security')
                                    ? locale_route('donor.security')
                                    : null;

                                $donorDonationsUrl = \Illuminate\Support\Facades\Route::has('donor.donations')
                                    ? locale_route('donor.donations')
                                    : null;
                            @endphp

                            <a href="{{ $donorDashboardUrl }}"
                                class="flex items-center justify-between px-4 py-3 text-sm font-semibold text-subtext hover:text-text hover:bg-muted transition">
                                <span>{{ $isAr ? 'لوحة الحساب' : 'Account dashboard' }}</span>
                                <span class="text-subtext/60">→</span>
                            </a>

                            @if ($donorDonationsUrl)
                                <a href="{{ $donorDonationsUrl }}"
                                    class="flex items-center justify-between px-4 py-3 text-sm font-semibold text-subtext hover:text-text hover:bg-muted transition">
                                    <span>{{ $isAr ? 'تبرعاتي' : 'My donations' }}</span>
                                    <span class="text-subtext/60">→</span>
                                </a>
                            @endif

                            @if ($donorProfileUrl)
                                <a href="{{ $donorProfileUrl }}"
                                    class="flex items-center justify-between px-4 py-3 text-sm font-semibold text-subtext hover:text-text hover:bg-muted transition">
                                    <span>{{ $isAr ? 'إعدادات الحساب' : 'Profile settings' }}</span>
                                    <span class="text-subtext/60">→</span>
                                </a>
                            @endif

                            @if ($donorSecurityUrl)
                                <a href="{{ $donorSecurityUrl }}"
                                    class="flex items-center justify-between px-4 py-3 text-sm font-semibold text-subtext hover:text-text hover:bg-muted transition">
                                    <span>{{ $isAr ? 'الأمان' : 'Security' }}</span>
                                    <span class="text-subtext/60">→</span>
                                </a>
                            @endif

                            <div class="h-px bg-border/70"></div>

                            @if ($hasDonorLogout)
                                <form method="POST" action="{{ locale_route('donor.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-subtext hover:text-text hover:bg-muted transition">
                                        <span>{{ $isAr ? 'تسجيل الخروج' : 'Logout' }}</span>
                                        <span class="text-subtext/60">↩</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </details>
                @else
                    @if ($hasDonorLogin)
                        <a href="{{ locale_route('donor.login') }}" class="btn btn-secondary whitespace-nowrap">
                            {{ $isAr ? 'تسجيل دخول' : 'Login' }}
                        </a>
                    @endif

                    @if ($hasDonorRegister)
                        <a href="{{ locale_route('donor.register') }}" class="btn btn-secondary whitespace-nowrap">
                            {{ $isAr ? 'إنشاء حساب' : 'Register' }}
                        </a>
                    @endif
                @endif

                <a href="{{ $urlDonate }}" class="btn btn-primary {{ $donateMargin }} whitespace-nowrap">
                    {{ $isAr ? 'تبرّع الآن' : 'Donate Now' }} <span aria-hidden="true">→</span>
                </a>
            </div>

            {{-- Mobile actions --}}
            <div class="xl:hidden flex items-center gap-2 shrink-0">
                <a href="{{ switch_locale_url() }}"
                    class="header-icon-btn text-sm font-black"
                    rel="nofollow"
                    aria-label="{{ $isEnPath ? 'Switch to Arabic' : 'Switch to English' }}">
                    {{ $isEnPath ? 'AR' : 'EN' }}
                </a>

                <button id="themeToggleMobile"
                    class="header-icon-btn font-black"
                    type="button" aria-label="Toggle theme">
                    <span id="themeIconMobile">{!! $icon('moon') !!}</span>
                </button>

                <details class="relative">
                    <summary
                        class="header-icon-btn list-none [&::-webkit-details-marker]:hidden cursor-pointer select-none font-black"
                        aria-label="{{ $isAr ? 'فتح القائمة' : 'Open menu' }}">
                        ☰
                    </summary>

                    <div
                        class="absolute {{ $edgeEnd }}-0 mt-2 w-[min(22rem,calc(100vw-2rem))] max-w-[calc(100vw-2rem)] bg-surface border border-border rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-3 space-y-2">
                            @if (!empty($donor))
                                <div class="rounded-2xl border border-border bg-surface p-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl grid place-items-center border border-border bg-muted font-black text-subtext">
                                            {{ $donorInitial }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-black text-text truncate">{{ $donorName }}</div>
                                            <div class="text-xs text-subtext">
                                                {{ $isAr ? 'حساب المتبرع' : 'Donor account' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-2">
                                        <a href="{{ $donorDashboardUrl }}" class="btn btn-secondary w-full">
                                            {{ $isAr ? 'لوحة الحساب' : 'Account dashboard' }}
                                        </a>

                                        @if ($donorDonationsUrl)
                                            <a href="{{ $donorDonationsUrl }}" class="btn btn-secondary w-full">
                                                {{ $isAr ? 'تبرعاتي' : 'My donations' }}
                                            </a>
                                        @endif

                                        @if ($donorProfileUrl)
                                            <a href="{{ $donorProfileUrl }}" class="btn btn-secondary w-full">
                                                {{ $isAr ? 'إعدادات الحساب' : 'Profile settings' }}
                                            </a>
                                        @endif

                                        @if ($donorSecurityUrl)
                                            <a href="{{ $donorSecurityUrl }}" class="btn btn-secondary w-full">
                                                {{ $isAr ? 'الأمان' : 'Security' }}
                                            </a>
                                        @endif

                                        @if ($hasDonorLogout)
                                            <form method="POST" action="{{ locale_route('donor.logout') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary w-full">
                                                    {{ $isAr ? 'تسجيل الخروج' : 'Logout' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @if ($hasDonorLogin)
                                    <a href="{{ locale_route('donor.login') }}" class="btn btn-secondary w-full">
                                        {{ $isAr ? 'تسجيل دخول' : 'Login' }}
                                    </a>
                                @endif

                                @if ($hasDonorRegister)
                                    <a href="{{ locale_route('donor.register') }}" class="btn btn-secondary w-full">
                                        {{ $isAr ? 'إنشاء حساب' : 'Register' }}
                                    </a>
                                @endif
                            @endif

                            <a href="{{ $urlDonate }}" class="btn btn-primary w-full">
                                {{ $isAr ? 'تبرّع الآن' : 'Donate Now' }}
                            </a>
                        </div>

                        <div class="border-t border-border/60"></div>

                        <div class="p-3">
                            <form action="{{ $urlCampaigns }}" method="get" class="relative">
                                <span
                                    class="absolute {{ $edgeStart }}-3 top-1/2 -translate-y-1/2 text-subtext/70 text-sm">
                                    {!! $icon('search') !!}
                                </span>
                                <input name="q" value="{{ $searchQ }}" class="input ps-9"
                                    placeholder="{{ $isAr ? 'ابحث عن حملة...' : 'Search campaigns...' }}">
                            </form>
                        </div>

                        <div class="border-t border-border/60"></div>

                        <a href="{{ $urlCampaigns }}" class="block px-4 py-3 text-sm hover:bg-muted font-semibold">
                            {{ $isAr ? 'الحملات' : 'Campaigns' }}
                        </a>
                        <a href="{{ $urlTransparency }}"
                            class="block px-4 py-3 text-sm hover:bg-muted font-semibold">
                            {{ $isAr ? 'الشفافية' : 'Transparency' }}
                        </a>
                        <a href="{{ $urlReports }}" class="block px-4 py-3 text-sm hover:bg-muted font-semibold">
                            {{ $isAr ? 'التقارير' : 'Reports' }}
                        </a>

                        @if ($publicPages->count())
                            <div class="border-t border-border/60"></div>
                            <div class="px-4 py-2 text-xs font-black text-subtext/80">
                                {{ $isAr ? 'صفحات' : 'Pages' }}
                            </div>

                            @foreach ($publicPages as $p)
                                @php
                                    $t = method_exists($p, 'title')
                                        ? $p->title()
                                        : ($isAr
                                            ? $p->title_ar
                                            : $p->title_en ?? $p->title_ar);
                                @endphp

                                <a href="{{ locale_route('pages.show', ['page' => $p->slug]) }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted">
                                    {{ $t }}
                                </a>
                            @endforeach
                        @endif

                        <div class="border-t border-border/60"></div>

                        <div class="p-3 flex items-center justify-end text-sm">
                            @if ($hasSocial)
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    @foreach ($social as $key => $url)
                                        @continue(empty($url))
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="px-3 py-2 rounded-xl border border-border hover:bg-muted text-xs font-semibold transition"
                                            aria-label="{{ $key }}">
                                            {!! $icon($key) !!}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </details>
            </div>

        </div>
    </header>

    {{-- Main --}}
    <main id="main" class="container-app py-10 sm:py-14">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-border/70 bg-bg">
        <div class="container-app py-12 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 lg:gap-10 text-sm">

            <div class="md:col-span-1">
                <div class="flex items-center gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}"
                            class="w-11 h-11 rounded-2xl border border-border object-cover bg-surface shadow-sm"
                            loading="lazy" decoding="async">
                    @else
                        <div
                            class="w-11 h-11 rounded-2xl border border-border bg-muted grid place-items-center shadow-sm">
                            <span class="font-black text-subtext">{{ mb_substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif

                    <div>
                        <div class="font-black text-text text-lg">{{ $siteName }}</div>
                        @if (!empty($tagline))
                            <div class="text-xs text-subtext">{{ $tagline }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 text-subtext leading-relaxed">
                    {{ $isAr ? 'منصة تبرعات موثقة تقدم حملات واضحة وتقارير منتظمة وتجربة متبرع هادئة وآمنة.' : 'A verified donation platform with clear campaigns, regular reporting, and a calm secure donor experience.' }}
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="badge">{{ $isAr ? 'شفافية' : 'Transparency' }}</span>
                    <span class="badge">{{ $isAr ? 'توثيق' : 'Documentation' }}</span>
                    <span class="badge">{{ $isAr ? 'بيانات آمنة' : 'Secure' }}</span>
                </div>
            </div>

            <div>
                <div class="font-black text-text mb-3">{{ $isAr ? 'روابط' : 'Links' }}</div>
                <div class="space-y-2">
                    @foreach ($footerLinks as $lnk)
                        <a class="footer-link" href="{{ $lnk['url'] }}">
                            {{ $lnk['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="font-black text-text mb-3">{{ $isAr ? 'تواصل' : 'Contact' }}</div>
                <div class="space-y-2 text-subtext">
                    @if ($contactEmail)
                        <div>
                            <a class="hover:underline underline-offset-4"
                                href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                        </div>
                    @endif

                    @if ($contactPhone)
                        <div>
                            <a class="hover:underline underline-offset-4"
                                href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                        </div>
                    @endif

                    @if ($contactWhats)
                        @php($wa = preg_replace('/\D+/', '', $contactWhats))
                        <div>
                            <a class="hover:underline underline-offset-4" target="_blank" rel="noopener"
                                href="https://wa.me/{{ $wa }}">
                                WhatsApp: {{ $contactWhats }}
                            </a>
                        </div>
                    @endif
                </div>

                @if ($hasSocial)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($social as $key => $url)
                            @continue(empty($url))
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border bg-surface hover:bg-muted text-subtext text-xs font-semibold transition">
                                {!! $icon($key) !!}
                                <span class="capitalize">{{ $key === 'x' ? 'X' : $key }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="font-black text-text mb-3">{{ $isAr ? 'دليل سريع' : 'Quick Guide' }}</div>
                <div class="text-subtext text-sm leading-relaxed">
                    {{ $isAr ? 'ابدأ من الحملات، راجع الشفافية، ثم أكمل التبرع مع الاحتفاظ بالإيصال ورابط التتبع.' : 'Start with campaigns, review transparency, then donate while keeping the receipt and tracking link.' }}
                </div>

                <div class="mt-4 flex flex-col sm:flex-row sm:flex-wrap gap-3">
                    <a href="{{ $urlCampaigns }}" class="btn btn-secondary shrink-0 px-4 py-3">
                        {{ $isAr ? 'استعراض الحملات' : 'Browse campaigns' }}
                    </a>
                    <a href="{{ $urlDonate }}" class="btn btn-primary shrink-0 px-4 py-3">
                        {{ $isAr ? 'تبرّع الآن' : 'Donate now' }}
                    </a>
                </div>

                <div class="mt-3 text-xs text-subtext/80">
                    {{ $isAr ? 'روابط الإيصالات والتتبع متاحة مباشرة داخل المنصة عند الحاجة.' : 'Receipt and tracking links remain directly available inside the platform when needed.' }}
                </div>
            </div>
        </div>

        <div class="border-t border-border/70">
            <div
                class="container-app py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-subtext text-center sm:text-start">
                <div>
                    © {{ date('Y') }} {{ $siteName }} —
                    {{ $isAr ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}
                </div>

                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-4">
                    <a href="{{ $urlTransparency }}" class="footer-meta-link">
                        {{ $isAr ? 'الشفافية' : 'Transparency' }}
                    </a>
                    <a href="{{ $urlReports }}" class="footer-meta-link">
                        {{ $isAr ? 'التقارير' : 'Reports' }}
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Cookie banner --}}
    <div id="cookieBanner" class="hidden fixed bottom-4 {{ $edgeStart }}-4 {{ $edgeEnd }}-4 z-50">
        <div class="max-w-3xl mx-auto rounded-3xl border border-border bg-surface shadow-soft p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-subtext leading-relaxed">
                    <span class="font-black text-text">{{ $isAr ? 'ملفات تعريف الارتباط' : 'Cookies' }}</span>
                    —
                    {{ $isAr ? 'نستخدم ملفات تعريف الارتباط لتحسين التجربة وتحليلات الأداء.' : 'We use cookies to improve experience and performance analytics.' }}
                </div>

                <div class="flex flex-wrap gap-2">
                    <button id="cookieAccept" class="btn btn-primary px-4 py-2.5">
                        {{ $isAr ? 'موافق' : 'Accept' }}
                    </button>
                    <button id="cookieDismiss" class="btn btn-secondary px-4 py-2.5">
                        {{ $isAr ? 'لاحقًا' : 'Later' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Theme + cookies scripts --}}
    <script>
        (function() {
            function setTheme(theme) {
                try {
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }

                    localStorage.setItem('theme', theme);

                    const icon = theme === 'dark' ? '☀' : '☾';
                    const el1 = document.getElementById('themeIcon');
                    const el2 = document.getElementById('themeIconMobile');

                    if (el1) el1.textContent = icon;
                    if (el2) el2.textContent = icon;
                } catch (e) {}
            }

            function toggleTheme() {
                const isDark = document.documentElement.classList.contains('dark');
                setTheme(isDark ? 'light' : 'dark');
            }

            try {
                const isDark = document.documentElement.classList.contains('dark');
                const icon = isDark ? '☀' : '☾';
                const el1 = document.getElementById('themeIcon');
                const el2 = document.getElementById('themeIconMobile');

                if (el1) el1.textContent = icon;
                if (el2) el2.textContent = icon;
            } catch (e) {}

            const t1 = document.getElementById('themeToggle');
            const t2 = document.getElementById('themeToggleMobile');

            if (t1) t1.addEventListener('click', toggleTheme);
            if (t2) t2.addEventListener('click', toggleTheme);

            try {
                const key = 'cookie_consent_v1';
                const banner = document.getElementById('cookieBanner');
                const accept = document.getElementById('cookieAccept');
                const dismiss = document.getElementById('cookieDismiss');

                const has = localStorage.getItem(key);
                if (!has && banner) {
                    banner.classList.remove('hidden');
                }

                if (accept) {
                    accept.addEventListener('click', function() {
                        localStorage.setItem(key, 'accepted');
                        if (banner) banner.classList.add('hidden');
                    });
                }

                if (dismiss) {
                    dismiss.addEventListener('click', function() {
                        if (banner) banner.classList.add('hidden');
                    });
                }
            } catch (e) {}
        })();
    </script>

    @stack('scripts')
</body>

</html>

