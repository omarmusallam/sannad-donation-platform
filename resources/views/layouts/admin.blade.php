<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $isAr = app()->isLocale('ar');

        // Minimal safe fallback: prefer a real `setting()` helper in backend.
        $setting = function (string $key, $default = null) {
            try {
                return function_exists('setting') ? setting($key, $default) : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        };

        $siteName = (string) $setting('site.name', config('app.name', 'GazaSannad'));
        $metaTitle = (string) $setting('seo.meta_title', $siteName);
        $metaDesc = (string) $setting('seo.meta_description', $isAr ? 'لوحة تحكم المنصة' : 'Admin panel');
        $faviconPath = $setting('site.favicon');
        $logoPath = $setting('site.logo');

        $pageTitle = trim($__env->yieldContent('title', $isAr ? 'لوحة التحكم' : 'Admin'));
        $fullTitle = trim($pageTitle . ' - ' . $metaTitle);

        /**
         * NAV as "route base" (important):
         * - For resources: admin.roles (will match admin.roles.*)
         * - For single routes: admin.home
         */
        $nav = [
            [
                'label' => $isAr ? 'الداشبورد' : 'Dashboard',
                'base' => 'admin.home',
                'href' => 'admin.home',
                'icon' => 'home',
            ],

            [
                'label' => $isAr ? 'الحملات' : 'Campaigns',
                'base' => 'admin.campaigns',
                'href' => 'admin.campaigns.index',
                'icon' => 'flag',
            ],
            [
                'label' => $isAr ? 'التبرعات' : 'Donations',
                'base' => 'admin.donations',
                'href' => 'admin.donations.index',
                'icon' => 'money',
            ],
            [
                'label' => $isAr ? 'الإيصالات' : 'Receipts',
                'base' => 'admin.receipts',
                'href' => 'admin.receipts.index',
                'icon' => 'receipt',
                'can' => 'receipts.view',
            ],
            [
                'label' => $isAr ? 'التقارير' : 'Reports',
                'base' => 'admin.reports',
                'href' => 'admin.reports.index',
                'icon' => 'doc',
            ],
            [
                'label' => $isAr ? 'التقارير المالية' : 'Finance Reports',
                'base' => 'admin.finance_reports',
                'href' => 'admin.finance_reports.index',
                'icon' => 'chart',
                'can' => 'finance_reports.view',
            ],
            [
                'label' => $isAr ? 'المستخدمون' : 'Users',
                'base' => 'admin.users',
                'href' => 'admin.users.index',
                'icon' => 'users',
                'can' => 'users.manage',
            ],
            [
                'label' => $isAr ? 'الأدوار' : 'Roles',
                'base' => 'admin.roles',
                'href' => 'admin.roles.index',
                'icon' => 'shield',
                'can' => 'roles.manage',
            ],
            [
                'label' => $isAr ? 'الصفحات' : 'Pages',
                'base' => 'admin.pages',
                'href' => 'admin.pages.index',
                'icon' => 'pages',
                'can' => 'pages.view',
            ],
            [
                'label' => $isAr ? 'الإعدادات' : 'Settings',
                'base' => 'admin.settings',
                'href' => 'admin.settings.edit',
                'icon' => 'settings',
                'can' => 'settings.manage',
            ],
        ];

        // Active check: matches ANY nested route under base
        $isActive = function (string $base): bool {
            // home is a single route, keep it exact
            if ($base === 'admin.home') {
                return request()->routeIs('admin.home');
            }
            return request()->routeIs($base . '.*') || request()->routeIs($base);
        };

        $icon = function (string $name) {
            return match ($name) {
                'home'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
                'flag'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 3v18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M6 4h10l-1.5 3L16 10H6V4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
                'money'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M7 12h.01M17 12h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 14.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.6"/></svg>',
                'doc'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v4h4" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.5 12h7M8.5 16h7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
                'receipt' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3h10a2 2 0 0 1 2 2v16l-2-1-2 1-2-1-2 1-2-1-2 1-2-1-2 1V5a2 2 0 0 1 2-2Z"
                                    stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="M9 7h6M9 11h6M9 15h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>',
                'settings'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.6"/><path d="M19.4 15a7.8 7.8 0 0 0 .1-6l-2 .6a6.2 6.2 0 0 0-1.1-1.2l.9-1.9a7.9 7.9 0 0 0-6-2.5l-.3 2a6.4 6.4 0 0 0-1.6.6L7 4.9a7.9 7.9 0 0 0-4 5.1l2 .3a6.4 6.4 0 0 0 0 1.8l-2 .3a7.9 7.9 0 0 0 4 5.1l1.4-1.5c.5.3 1 .5 1.6.6l.3 2a7.9 7.9 0 0 0 6-2.5l-.9-1.9c.4-.4.8-.8 1.1-1.2l2 .6Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>',
                'users'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.6"/><path d="M22 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
                'shield'
                    => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2 20 6v6c0 5-3.4 9.4-8 10-4.6-.6-8-5-8-10V6l8-4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
                'pages' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M9 7h6M9 11h6M9 15h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>',
                'chart' => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 19V5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M4 19h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M8 17v-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M12 17V9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M16 17v-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>',
                default => '',
            };
        };
    @endphp

    <title>{{ $fullTitle }}</title>

    <meta name="description" content="{{ $metaDesc }}">
    <meta name="robots" content="noindex,nofollow">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:type" content="website">

    @if (!empty($faviconPath))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $faviconPath) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 selection:bg-slate-900 selection:text-white">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false, userMenu: false }">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden" x-transition.opacity
            @click="sidebarOpen=false" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside
            class="fixed lg:static z-40 top-0 {{ $isAr ? 'right-0' : 'left-0' }} h-full w-80 lg:w-72 bg-white/95 backdrop-blur border-slate-200 {{ $isAr ? 'border-l' : 'border-r' }} shadow-sm"
            :class="sidebarOpen ? 'translate-x-0' : '{{ $isAr ? 'translate-x-full' : '-translate-x-full' }} lg:translate-x-0'"
            x-transition>
            {{-- Brand --}}
            <div class="p-6 border-b border-slate-200/70">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 rounded-2xl overflow-hidden shrink-0 border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 grid place-items-center">
                            @if (!empty($logoPath))
                                <img src="{{ asset('storage/' . $logoPath) }}" class="w-full h-full object-cover"
                                    alt="{{ $siteName }}">
                            @else
                                <span
                                    class="text-sm font-extrabold text-slate-800">{{ mb_substr($siteName, 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="text-xs text-slate-500">{{ $isAr ? 'منصة' : 'Platform' }}</div>
                            <div class="text-lg font-extrabold tracking-tight truncate">{{ $siteName }}</div>
                        </div>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200/70">
                        {{ $isAr ? 'إدارة' : 'Admin' }}
                    </span>
                </div>

                <button type="button"
                    class="mt-4 lg:hidden w-full px-3 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 text-sm font-semibold"
                    @click="sidebarOpen=false">
                    {{ $isAr ? 'إغلاق القائمة' : 'Close menu' }}
                </button>
            </div>

            {{-- Nav --}}
            <nav class="p-4 space-y-1" aria-label="{{ $isAr ? 'قائمة الإدارة' : 'Admin navigation' }}">
                @foreach ($nav as $item)
                    @php
                        $active = $isActive($item['base']);
                    @endphp

                    @if (empty($item['can']))
                        <a href="{{ route($item['href']) }}" @class([
                            'group flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm transition relative overflow-hidden',
                            // Active style (global, premium look)
                            'text-white shadow-sm' => $active,
                            // Inactive style
                            'text-slate-700 hover:bg-slate-50' => !$active,
                        ])
                            @if ($active) aria-current="page" @endif>
                            @if ($active)
                                <span
                                    class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800"></span>
                                <span
                                    class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_30%_20%,white,transparent_40%)]"></span>
                            @endif

                            <span
                                class="relative inline-flex items-center justify-center w-9 h-9 rounded-2xl
                            {{ $active ? 'bg-white/10 ring-1 ring-white/15' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                                <span
                                    class="{{ $active ? 'text-white' : 'text-slate-700' }}">{!! $icon($item['icon']) !!}</span>
                            </span>

                            <span class="relative font-semibold">{{ $item['label'] }}</span>

                            @if ($active)
                                <span
                                    class="relative ms-auto text-xs px-2 py-1 rounded-full bg-white/10 ring-1 ring-white/15">
                                    {{ $isAr ? 'نشط' : 'Active' }}
                                </span>
                            @endif
                        </a>
                    @else
                        @can($item['can'])
                            <a href="{{ route($item['href']) }}" @class([
                                'group flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm transition relative overflow-hidden',
                                'text-white shadow-sm' => $active,
                                'text-slate-700 hover:bg-slate-50' => !$active,
                            ])
                                @if ($active) aria-current="page" @endif>
                                @if ($active)
                                    <span
                                        class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800"></span>
                                    <span
                                        class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_30%_20%,white,transparent_40%)]"></span>
                                @endif

                                <span
                                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-2xl
                                {{ $active ? 'bg-white/10 ring-1 ring-white/15' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                                    <span
                                        class="{{ $active ? 'text-white' : 'text-slate-700' }}">{!! $icon($item['icon']) !!}</span>
                                </span>

                                <span class="relative font-semibold">{{ $item['label'] }}</span>

                                @if ($active)
                                    <span
                                        class="relative ms-auto text-xs px-2 py-1 rounded-full bg-white/10 ring-1 ring-white/15">
                                        {{ $isAr ? 'نشط' : 'Active' }}
                                    </span>
                                @endif
                            </a>
                        @endcan
                    @endif
                @endforeach
            </nav>

            {{-- Footer actions --}}
            <div class="p-4 border-t border-slate-200/70 mt-4 space-y-3">
                <a href="{{ route('home') }}"
                    class="flex items-center justify-between px-3 py-2.5 rounded-2xl text-sm border border-slate-200 hover:bg-slate-50 transition">
                    <span class="font-semibold">{{ $isAr ? 'العودة للموقع' : 'Back to site' }}</span>
                    <span class="text-slate-500" aria-hidden="true">↗</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-3 py-2.5 rounded-2xl text-sm bg-rose-50 text-rose-700 hover:bg-rose-100 transition font-semibold border border-rose-200/60">
                        {{ $isAr ? 'تسجيل الخروج' : 'Logout' }}
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <main class="flex-1 lg:{{ $isAr ? 'mr-72' : 'ml-72' }} min-w-0">

            {{-- Top bar --}}
            <header class="sticky top-0 z-20">
                <div class="bg-white/80 backdrop-blur border-b border-slate-200">
                    <div class="px-4 sm:px-6 py-4 flex items-center justify-between gap-3">

                        <div class="flex items-center gap-3 min-w-0">
                            <button type="button"
                                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-2xl border border-slate-200 hover:bg-slate-50"
                                @click="sidebarOpen=true" aria-label="{{ $isAr ? 'فتح القائمة' : 'Open menu' }}">
                                ☰
                            </button>

                            <div class="min-w-0">
                                <div class="text-xs text-slate-500">{{ $isAr ? 'لوحة التحكم' : 'Admin Panel' }}</div>
                                <div class="text-lg font-extrabold tracking-tight truncate">
                                    @yield('page_title', $isAr ? 'مرحبًا' : 'Welcome')
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @yield('page_actions')

                            {{-- User menu --}}
                            <div class="relative">
                                <button type="button"
                                    class="px-3 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 text-sm font-semibold"
                                    @click="userMenu=!userMenu" :aria-expanded="userMenu.toString()"
                                    aria-haspopup="menu">
                                    {{ auth()->user()->name ?? ($isAr ? 'المدير' : 'Admin') }} ▾
                                </button>

                                <div x-show="userMenu" @click.outside="userMenu=false" x-transition
                                    class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden"
                                    role="menu">
                                    <a href="{{ route('home') }}" class="block px-4 py-3 text-sm hover:bg-slate-50"
                                        role="menuitem">
                                        {{ $isAr ? 'زيارة الموقع' : 'Visit site' }}
                                    </a>

                                    @can('settings.manage')
                                        <a href="{{ route('admin.settings.edit') }}"
                                            class="block px-4 py-3 text-sm hover:bg-slate-50" role="menuitem">
                                            {{ $isAr ? 'إعدادات الموقع' : 'Site Settings' }}
                                        </a>
                                    @endcan

                                    <div class="border-t border-slate-200"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-{{ $isAr ? 'right' : 'left' }} px-4 py-3 text-sm hover:bg-rose-50 text-rose-700"
                                            role="menuitem">
                                            {{ $isAr ? 'تسجيل الخروج' : 'Logout' }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="p-4 sm:p-6 lg:p-8">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                        <div class="font-bold">{{ $isAr ? 'تم بنجاح' : 'Success' }}</div>
                        <div class="text-sm mt-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
                        <div class="font-bold">{{ $isAr ? 'حدث خطأ' : 'Error' }}</div>
                        <div class="text-sm mt-1">{{ session('error') }}</div>
                    </div>
                @endif

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

                @yield('content')
            </div>
        </main>

    </div>

    @stack('scripts')
</body>

</html>
