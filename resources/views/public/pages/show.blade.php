@extends('layouts.public')

@section('title', $page->metaTitle() ?? $page->title())
@section('meta_description', $page->metaDescription() ?? '')

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        $urlHome = url($base ?: '/');
        $urlDonate = url($base . '/donate');
        $urlCampaigns = url($base . '/campaigns');
        $urlTransparency = url($base . '/transparency');

        $title = $page->title();
        $desc = $page->metaDescription();
        $rtl = app()->isLocale('ar');

        $updated = $page->updated_at?->format('Y-m-d');

        // Quick trust items (localized)
        $trust = [
            [
                'title' => $isEn ? 'Verified campaigns' : 'حملات موثوقة',
                'desc' => $isEn
                    ? 'Clear details, goals, and progress tracking.'
                    : 'تفاصيل واضحة وأهداف ومتابعة للتقدم.',
                'icon' => '✓',
            ],
            [
                'title' => $isEn ? 'Transparency reports' : 'تقارير شفافية',
                'desc' => $isEn ? 'Periodic updates and documented spending.' : 'تحديثات دورية وتوثيق للصرف.',
                'icon' => '📄',
            ],
            [
                'title' => $isEn ? 'Privacy & security' : 'خصوصية وأمان',
                'desc' => $isEn
                    ? 'We respect privacy and protect user data.'
                    : 'نحترم الخصوصية ونحمي بيانات المستخدمين.',
                'icon' => '🔒',
            ],
        ];
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-transparent"></div>
        <div class="absolute -right-20 -top-24 h-80 w-80 rounded-full blur-3xl opacity-35"
            style="background: radial-gradient(circle, rgba(79,70,229,.18), transparent 60%);"></div>
        <div class="absolute -left-24 -bottom-24 h-80 w-80 rounded-full blur-3xl opacity-30"
            style="background: radial-gradient(circle, rgba(16,185,129,.16), transparent 60%);"></div>

        <div class="px-6 sm:px-10 py-10 sm:py-12">
            {{-- Breadcrumb + Trust pill --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-slate-500">
                    <a href="{{ $urlHome }}" class="hover:underline underline-offset-4">
                        {{ $isEn ? 'Home' : 'الرئيسية' }}
                    </a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-700 font-semibold">{{ $title }}</span>
                </div>

                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 bg-white text-xs text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold">{{ $isEn ? 'Trusted platform' : 'منصة موثوقة' }}</span>
                    <span class="text-slate-300">•</span>
                    <span>{{ $isEn ? 'Clear & documented' : 'واضحة وموثقة' }}</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Left: text --}}
                <div class="lg:col-span-8">
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-950 leading-tight">
                        {{ $title }}
                    </h1>

                    @if (!empty($desc))
                        <p class="mt-3 text-slate-600 leading-relaxed max-w-3xl">
                            {{ $desc }}
                        </p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $urlDonate }}"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition active:scale-[.99]"
                            style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                            {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                            <span aria-hidden="true">→</span>
                        </a>

                        <a href="{{ $urlCampaigns }}"
                            class="px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-extrabold text-slate-800">
                            {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                        </a>

                        <a href="{{ $urlTransparency }}"
                            class="px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-700">
                            {{ $isEn ? 'Transparency' : 'الشفافية' }}
                        </a>
                    </div>

                    @if ($updated)
                        <div class="mt-4 text-xs text-slate-500">
                            {{ $isEn ? 'Last updated:' : 'آخر تحديث:' }}
                            <span class="font-semibold text-slate-700">{{ $updated }}</span>
                        </div>
                    @endif
                </div>

                {{-- Right: trust cards --}}
                <div class="lg:col-span-4">
                    <div class="grid gap-3">
                        @foreach ($trust as $t)
                            <div class="rounded-2xl border border-slate-200 bg-white/70 backdrop-blur px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-10 w-10 rounded-2xl grid place-items-center border border-slate-200 bg-slate-50 text-lg">
                                        <span aria-hidden="true">{{ $t['icon'] }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 leading-tight">
                                            {{ $t['title'] }}
                                        </div>
                                        <div class="mt-1 text-sm text-slate-600 leading-relaxed">
                                            {{ $t['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
                        <div class="text-sm font-extrabold text-slate-900">
                            {{ $isEn ? 'Need help?' : 'هل تحتاج مساعدة؟' }}
                        </div>
                        <div class="mt-1 text-sm text-slate-600 leading-relaxed">
                            {{ $isEn ? 'Contact us and we’ll guide you.' : 'تواصل معنا وسنساعدك خطوة بخطوة.' }}
                        </div>
                        <a href="{{ $urlTransparency }}"
                            class="mt-3 inline-flex text-sm font-extrabold text-indigo-700 hover:underline underline-offset-4">
                            {{ $isEn ? 'See how we ensure transparency' : 'اطّلع على آلية الشفافية' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="mt-8">
        <div class="max-w-4xl mx-auto">
            <article
                class="
                    prose prose-slate max-w-none
                    prose-headings:font-extrabold
                    prose-headings:tracking-tight
                    prose-h2:text-slate-950 prose-h3:text-slate-950
                    prose-p:text-slate-700
                    prose-strong:text-slate-950
                    prose-a:text-indigo-700 prose-a:font-bold prose-a:no-underline hover:prose-a:underline
                    prose-blockquote:border-slate-200 prose-blockquote:bg-slate-50/60 prose-blockquote:rounded-2xl prose-blockquote:px-5 prose-blockquote:py-4
                    prose-hr:border-slate-200
                    prose-li:marker:text-slate-400
                    prose-table:text-sm
                    prose-img:rounded-3xl prose-img:border prose-img:border-slate-200 prose-img:shadow-sm
                    {{ $rtl ? 'prose-p:leading-8 prose-li:leading-8 prose-blockquote:text-slate-700' : 'prose-p:leading-7 prose-li:leading-7' }}
                ">
                {!! $page->content() !!}
            </article>
        </div>
    </section>

    {{-- CTA BOTTOM --}}
    <section class="mt-10">
        <div
            class="max-w-5xl mx-auto rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full blur-3xl opacity-30"
                style="background: radial-gradient(circle, rgba(79,70,229,.16), transparent 60%);"></div>
            <div class="absolute -left-24 -bottom-24 h-64 w-64 rounded-full blur-3xl opacity-25"
                style="background: radial-gradient(circle, rgba(16,185,129,.14), transparent 60%);"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-950">
                        {{ $isEn ? 'Ready to make an impact?' : 'جاهز لتكون جزءًا من الأثر؟' }}
                    </div>
                    <div class="mt-2 text-slate-600 leading-relaxed max-w-2xl">
                        {{ $isEn ? 'Choose a trusted campaign and follow progress with updates and reports.' : 'اختر حملة موثوقة وتابع التقدم عبر التحديثات والتقارير.' }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ $urlDonate }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition active:scale-[.99]"
                        style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                        {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ $urlCampaigns }}"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-extrabold text-slate-800">
                        {{ $isEn ? 'Explore campaigns' : 'استعراض الحملات' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
