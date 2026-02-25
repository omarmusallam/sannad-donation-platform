{{-- resources/views/layouts/public.blade.php --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $isAr = app()->isLocale('ar');

        // From View Composer (AppServiceProvider)
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
        $fullTitle = $pageTitle ? $pageTitle . ' - ' . $seoTitle : $seoTitle;

        $pageDesc = trim($__env->yieldContent('meta_description', ''));
        $finalDesc = $pageDesc ?: $seoDesc;

        // Assets
        $faviconPath = $settings['site.favicon'] ?? null;
        $logoPath = $settings['site.logo'] ?? null;

        $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : null;
        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;

        // Theme color
        $themeColor = (string) ($settings['site.theme_color'] ?? '#111827'); // gray-900 fallback

        // Canonical
        $canonical = url()->current();

        // Hreflang alternates
        $path = trim(request()->path(), '/');
        $isEnPath = str_starts_with($path, 'en');
        $cleanPath = $isEnPath ? preg_replace('/^en(\/)?/', '', $path) : $path;

        $arUrl = url('/' . ltrim($cleanPath, '/'));
        $enUrl = url('/en' . ($cleanPath ? '/' . ltrim($cleanPath, '/') : ''));

        $arUrl = rtrim($arUrl, '/');
        $enUrl = rtrim($enUrl, '/');
        if ($arUrl === '') {
            $arUrl = url('/');
        }
        if ($enUrl === '') {
            $enUrl = url('/en');
        }

        // Language switch link
        $targetPath = $isEnPath ? $cleanPath : 'en/' . $cleanPath;
        $targetPath = trim($targetPath, '/');
        $langSwitchUrl = $targetPath === '' ? '/' : '/' . $targetPath;

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

        // Main links
        $urlHome = $isEnPath ? url('/en') : url('/');
        $urlCampaigns = $isEnPath ? url('/en/campaigns') : route('campaigns.index');
        $urlTransparency = $isEnPath ? url('/en/transparency') : route('transparency');
        $urlDonate = $isEnPath ? url('/en/donate') : route('donate');
        $urlReports = $isEnPath ? url('/en/transparency/reports') : route('reports.index');

        // Public pages from DB (composer)
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
            $pageLinks[] = ['label' => $title, 'url' => route('pages.show', $p->slug)];
        }

        $footerLinks = array_merge($staticLinks, $pageLinks);

        $isActive = function ($pattern) {
            return request()->is($pattern) || request()->is(trim($pattern, '/') . '/*');
        };

        // Simple text icons
        $socialIcon = function (string $key) {
            return match ($key) {
                'facebook' => '<span aria-hidden="true" class="font-black">f</span>',
                'x' => '<span aria-hidden="true" class="font-black">𝕏</span>',
                'instagram' => '<span aria-hidden="true" class="font-black">◎</span>',
                'youtube' => '<span aria-hidden="true" class="font-black">▶</span>',
                default => '',
            };
        };

        // Logo sizes
        $brandLogoSize = 'w-11 h-11 sm:w-12 sm:h-12'; // nicer & clearer in header
        $footerLogoSize = 'w-11 h-11';
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

    {{-- Favicons (best practice) --}}
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @else
        {{-- safe fallback --}}
        <link rel="icon"
            href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%23111827'/%3E%3Ctext x='50' y='62' font-size='56' text-anchor='middle' fill='white' font-family='Arial'%3E{{ rawurlencode(mb_substr($siteName, 0, 1)) }}%3C/text%3E%3C/svg%3E">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Cairo', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        :root {
            --brand: 79 70 229;
            /* indigo-600 */
            --brand2: 16 185 129;
            /* emerald-500 */
            --ink: 15 23 42;
            /* slate-900 */
        }

        /* nicer default focus for keyboard users */
        :focus-visible {
            outline: 2px solid rgba(var(--brand), .35);
            outline-offset: 2px;
            border-radius: 14px;
        }
    </style>

    @stack('head')
</head>

<body class="bg-white text-slate-900 selection:bg-slate-900 selection:text-white">

    {{-- Background polish --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute inset-x-0 -top-28 h-[560px] bg-gradient-to-b from-slate-50 via-white to-transparent"></div>

        <div class="absolute -left-44 top-28 h-80 w-80 rounded-full blur-3xl opacity-35"
            style="background: radial-gradient(circle, rgba(var(--brand),.22), transparent 60%);"></div>

        <div class="absolute -right-44 top-16 h-80 w-80 rounded-full blur-3xl opacity-30"
            style="background: radial-gradient(circle, rgba(var(--brand2),.20), transparent 60%);"></div>

        <div
            class="absolute inset-x-0 top-[420px] h-px bg-gradient-to-r from-transparent via-slate-200/70 to-transparent">
        </div>
    </div>

    {{-- Top bar --}}
    <div class="hidden md:block border-b border-slate-200/60 bg-white/70 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5 flex items-center justify-between text-xs text-slate-600">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span
                        class="font-semibold">{{ $isAr ? 'شفافية وتقارير دورية' : 'Transparency with periodic reports' }}</span>
                </span>

                @if ($contactEmail)
                    <a class="hover:underline underline-offset-4"
                        href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                @endif

                @if ($contactPhone)
                    <span class="text-slate-300">•</span>
                    <a class="hover:underline underline-offset-4"
                        href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if (!empty(array_filter($social)))
                    @foreach ($social as $key => $url)
                        @continue(empty($url))
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                            class="px-2 py-1 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200/60 transition">
                            {!! $socialIcon($key) !!}
                            <span class="sr-only">{{ $key }}</span>
                        </a>
                    @endforeach
                @endif

                <a class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 transition font-extrabold"
                    href="{{ url($langSwitchUrl) }}" rel="nofollow">
                    {{ $isEnPath ? 'AR' : 'EN' }}
                </a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4">

            {{-- Brand --}}
            <a href="{{ $urlHome }}" class="flex items-center gap-3 min-w-0">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}"
                        class="{{ $brandLogoSize }} rounded-2xl border border-slate-200/80 object-cover bg-white shrink-0 shadow-sm"
                        loading="eager" decoding="async" referrerpolicy="no-referrer">
                @else
                    <div
                        class="{{ $brandLogoSize }} rounded-2xl border border-slate-200 bg-slate-50 grid place-items-center shrink-0 shadow-sm">
                        <span class="font-extrabold text-slate-700 text-lg">{{ mb_substr($siteName, 0, 1) }}</span>
                    </div>
                @endif

                <div class="min-w-0 leading-tight">
                    <div class="font-extrabold text-base sm:text-lg truncate">{{ $siteName }}</div>
                    @if (!empty($tagline))
                        <div class="text-xs text-slate-500 truncate">{{ $tagline }}</div>
                    @endif
                </div>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1 text-sm">
                @php
                    $activeCampaigns = $isActive($isEnPath ? 'en/campaigns' : 'campaigns');
                    $activeTransparency = $isActive($isEnPath ? 'en/transparency' : 'transparency');
                @endphp

                <a href="{{ $urlCampaigns }}"
                    class="px-3 py-2 rounded-xl transition font-semibold
                    {{ $activeCampaigns ? 'text-indigo-700 bg-indigo-50 ring-1 ring-indigo-100' : 'text-slate-700 hover:bg-slate-50' }}">
                    {{ $isAr ? 'الحملات' : 'Campaigns' }}
                </a>

                <a href="{{ $urlTransparency }}"
                    class="px-3 py-2 rounded-xl transition font-semibold
                    {{ $activeTransparency ? 'text-indigo-700 bg-indigo-50 ring-1 ring-indigo-100' : 'text-slate-700 hover:bg-slate-50' }}">
                    {{ $isAr ? 'الشفافية' : 'Transparency' }}
                </a>

                {{-- Donate primary --}}
                <a href="{{ $urlDonate }}"
                    class="ml-1 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition active:scale-[.99]"
                    style="background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                    <span>{{ $isAr ? 'تبرّع الآن' : 'Donate Now' }}</span>
                    <span aria-hidden="true">→</span>
                </a>
            </nav>

            {{-- Mobile menu --}}
            <details class="md:hidden relative">
                <summary
                    class="list-none cursor-pointer select-none px-3.5 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 transition font-extrabold"
                    aria-label="{{ $isAr ? 'فتح القائمة' : 'Open menu' }}">
                    ☰
                </summary>

                <div
                    class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-3">
                        <a href="{{ $urlDonate }}"
                            class="block text-center px-4 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                            style="background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                            {{ $isAr ? 'تبرّع الآن' : 'Donate Now' }}
                        </a>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <a href="{{ $urlCampaigns }}" class="block px-4 py-3 text-sm hover:bg-slate-50 font-semibold">
                        {{ $isAr ? 'الحملات' : 'Campaigns' }}
                    </a>

                    <a href="{{ $urlTransparency }}" class="block px-4 py-3 text-sm hover:bg-slate-50 font-semibold">
                        {{ $isAr ? 'الشفافية' : 'Transparency' }}
                    </a>

                    @if ($publicPages->count())
                        <div class="border-t border-slate-100"></div>
                        <div class="px-4 py-2 text-xs font-bold text-slate-500">
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
                            <a href="{{ route('pages.show', $p->slug) }}"
                                class="block px-4 py-3 text-sm hover:bg-slate-50">
                                {{ $t }}
                            </a>
                        @endforeach
                    @endif

                    <div class="border-t border-slate-100"></div>
                    <div class="p-3 flex items-center justify-between text-sm">
                        <a href="{{ url($langSwitchUrl) }}"
                            class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition font-extrabold"
                            rel="nofollow">
                            {{ $isEnPath ? 'AR' : 'EN' }}
                        </a>

                        @if (!empty(array_filter($social)))
                            <div class="flex items-center gap-2">
                                @foreach ($social as $key => $url)
                                    @continue(empty($url))
                                    <a href="{{ $url }}" target="_blank" rel="noopener"
                                        class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-semibold transition">
                                        {!! $socialIcon($key) !!}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </details>

        </div>
    </header>

    {{-- Main --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200/70 bg-white">
        {{-- CTA band --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-12">
            <div
                class="rounded-3xl p-6 sm:p-8 border border-slate-200 bg-gradient-to-br from-slate-50 to-white overflow-hidden relative">
                <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full blur-3xl opacity-35"
                    style="background: radial-gradient(circle, rgba(var(--brand),.22), transparent 60%);"></div>
                <div class="absolute -left-16 -bottom-16 h-48 w-48 rounded-full blur-3xl opacity-30"
                    style="background: radial-gradient(circle, rgba(var(--brand2),.20), transparent 60%);"></div>

                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="text-lg sm:text-xl font-extrabold text-slate-900">
                            {{ $isAr ? 'كن جزءاً من الأثر' : 'Be part of the impact' }}
                        </div>
                        <div class="mt-1 text-sm text-slate-600 leading-relaxed max-w-2xl">
                            {{ $isAr ? 'ساهم في حملات موثوقة مع تحديثات وتقارير دورية. تبرعك يُحدث فرقاً.' : 'Support trusted campaigns with periodic updates and reports. Your donation makes a difference.' }}
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ $urlDonate }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition active:scale-[.99]"
                            style="background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                            {{ $isAr ? 'تبرّع الآن' : 'Donate Now' }}
                            <span aria-hidden="true">→</span>
                        </a>

                        <a href="{{ $urlTransparency }}"
                            class="inline-flex items-center justify-center px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-extrabold text-slate-800">
                            {{ $isAr ? 'اطّلع على الشفافية' : 'View transparency' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main footer grid --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-10 text-sm">

            {{-- Brand --}}
            <div class="md:col-span-1">
                <div class="flex items-center gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}"
                            class="{{ $footerLogoSize }} rounded-2xl border border-slate-200/80 object-cover bg-white shadow-sm"
                            loading="lazy" decoding="async">
                    @else
                        <div
                            class="{{ $footerLogoSize }} rounded-2xl border border-slate-200 bg-slate-50 grid place-items-center shadow-sm">
                            <span class="font-extrabold text-slate-700">{{ mb_substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif

                    <div>
                        <div class="font-extrabold text-slate-900 text-lg">{{ $siteName }}</div>
                        @if (!empty($tagline))
                            <div class="text-xs text-slate-500">{{ $tagline }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 text-slate-600 leading-relaxed">
                    {{ $isAr ? 'منصة تبرعات تربط الداعمين بالحملات عبر تقارير وتحديثات مستمرة.' : 'A donation platform connecting supporters with campaigns through continuous updates and reports.' }}
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700">
                        {{ $isAr ? 'شفافية' : 'Transparency' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700">
                        {{ $isAr ? 'توثيق' : 'Documentation' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700">
                        {{ $isAr ? 'بيانات آمنة' : 'Secure Data' }}
                    </span>
                </div>
            </div>

            {{-- Links --}}
            <div>
                <div class="font-extrabold text-slate-900 mb-3">{{ $isAr ? 'روابط' : 'Links' }}</div>
                <div class="space-y-2">
                    @foreach ($footerLinks as $lnk)
                        <a class="block text-slate-600 hover:text-slate-900 hover:underline underline-offset-4"
                            href="{{ $lnk['url'] }}">
                            {{ $lnk['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Contact --}}
            <div>
                <div class="font-extrabold text-slate-900 mb-3">{{ $isAr ? 'تواصل' : 'Contact' }}</div>
                <div class="space-y-2 text-slate-600">
                    @if ($contactEmail)
                        <div>
                            <a class="hover:underline underline-offset-4" href="mailto:{{ $contactEmail }}">
                                {{ $contactEmail }}
                            </a>
                        </div>
                    @endif

                    @if ($contactPhone)
                        <div>
                            <a class="hover:underline underline-offset-4" href="tel:{{ $contactPhone }}">
                                {{ $contactPhone }}
                            </a>
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

                @if (!empty(array_filter($social)))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($social as $key => $url)
                            @continue(empty($url))
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                                {!! $socialIcon($key) !!}
                                <span class="capitalize">{{ $key === 'x' ? 'X' : $key }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Newsletter --}}
            <div>
                <div class="font-extrabold text-slate-900 mb-3">{{ $isAr ? 'ابقَ على اطلاع' : 'Stay updated' }}</div>
                <div class="text-slate-600 text-sm leading-relaxed">
                    {{ $isAr ? 'اشترك لتصلك تحديثات وتقارير جديدة. (يمكنك ربطها لاحقًا بنظام بريد)' : 'Subscribe to receive new updates and reports. (You can wire this to email later)' }}
                </div>

                <form action="#" method="post" class="mt-4 flex gap-2">
                    <input type="email" name="email" required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
                        placeholder="{{ $isAr ? 'بريدك الإلكتروني' : 'Your email' }}">
                    <button type="submit"
                        class="shrink-0 px-4 py-3 rounded-2xl font-extrabold text-white transition active:scale-[.99]"
                        style="background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                        {{ $isAr ? 'اشتراك' : 'Subscribe' }}
                    </button>
                </form>

                <div class="mt-3 text-xs text-slate-500">
                    {{ $isAr ? 'لن نرسل رسائل مزعجة.' : 'No spam. Unsubscribe anytime.' }}
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-slate-200/70">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    © {{ date('Y') }} {{ $siteName }} —
                    {{ $isAr ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ $urlTransparency }}" class="hover:text-slate-700 hover:underline underline-offset-4">
                        {{ $isAr ? 'الشفافية' : 'Transparency' }}
                    </a>
                    <a href="{{ $urlReports }}" class="hover:text-slate-700 hover:underline underline-offset-4">
                        {{ $isAr ? 'التقارير' : 'Reports' }}
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
