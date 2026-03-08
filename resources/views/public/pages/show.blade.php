@extends('layouts.public')

@section('title', $page->metaTitle() ?? $page->title())
@section('meta_description', $page->metaDescription() ?? '')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $rtl = app()->isLocale('ar');

        $urlHome = locale_route('home');
        $urlDonate = locale_route('donate');
        $urlCampaigns = locale_route('campaigns.index');
        $urlTransparency = locale_route('transparency');

        $title = $page->title();
        $desc = $page->metaDescription();
        $updated = $page->updated_at?->format('Y-m-d');

        $trust = [
            [
                'title' => $isEn ? 'Verified campaigns' : 'حملات موثوقة',
                'desc' => $isEn ? 'Clear goals, progress, and proof documents.' : 'أهداف واضحة وتقدّم موثق ومستندات.',
                'icon' => '✓',
            ],
            [
                'title' => $isEn ? 'Transparency reports' : 'تقارير شفافية',
                'desc' => $isEn ? 'Periodic updates and documented spending.' : 'تحديثات دورية وتوثيق للصرف.',
                'icon' => '📄',
            ],
            [
                'title' => $isEn ? 'Privacy & security' : 'خصوصية وأمان',
                'desc' => $isEn ? 'We respect privacy and protect user data.' : 'نحترم الخصوصية ونحمي البيانات.',
                'icon' => '🔒',
            ],
        ];

        $contentHtml = (string) ($page->content() ?? '');
        $shareLabel = $isEn ? 'Copy page link' : 'نسخ رابط الصفحة';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
        $printLabel = $isEn ? 'Print' : 'طباعة';
    @endphp

    <section class="relative overflow-hidden rounded-[28px] border border-border bg-surface">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -right-20 -top-24 h-80 w-80 rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, rgba(var(--brand),.18), transparent 60%);"></div>
        <div class="pointer-events-none absolute -left-24 -bottom-24 h-80 w-80 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, rgba(var(--brand2),.16), transparent 60%);"></div>

        <div class="px-6 sm:px-10 py-9 sm:py-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-subtext">
                    <a href="{{ $urlHome }}" class="hover:underline underline-offset-4">
                        {{ $isEn ? 'Home' : 'الرئيسية' }}
                    </a>
                    <span class="mx-2 text-subtext/50">/</span>
                    <span class="text-text font-semibold">{{ $title }}</span>
                </div>

                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface text-xs text-subtext">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold">{{ $isEn ? 'Trusted platform' : 'منصة موثوقة' }}</span>
                    <span class="text-subtext/40">•</span>
                    <span>{{ $isEn ? 'Clear & documented' : 'واضحة وموثقة' }}</span>
                </div>
            </div>

            <div class="mt-7 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8">
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-text leading-tight">
                        {{ $title }}
                    </h1>

                    @if (!empty($desc))
                        <p class="mt-3 text-subtext leading-relaxed max-w-3xl">
                            {{ $desc }}
                        </p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $urlDonate }}" class="btn btn-primary">
                            {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                            <span aria-hidden="true">→</span>
                        </a>

                        <a href="{{ $urlCampaigns }}" class="btn btn-secondary">
                            {{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}
                        </a>

                        <a href="{{ $urlTransparency }}" class="btn btn-secondary">
                            {{ $isEn ? 'Transparency' : 'الشفافية' }}
                        </a>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-subtext">
                        @if ($updated)
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-muted">
                                <span class="font-semibold">{{ $isEn ? 'Last updated:' : 'آخر تحديث:' }}</span>
                                <span class="font-black text-text">{{ $updated }}</span>
                            </span>
                        @endif

                        <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface hover:bg-muted transition font-semibold"
                            onclick="navigator.clipboard.writeText(window.location.href); this.querySelector('span').textContent='{{ $copiedLabel }}';">
                            <span>{{ $shareLabel }}</span>
                            <span aria-hidden="true">↗</span>
                        </button>

                        <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface hover:bg-muted transition font-semibold"
                            onclick="window.print()">
                            <span>{{ $printLabel }}</span>
                            <span aria-hidden="true">🖨</span>
                        </button>
                    </div>

                    <div class="mt-6 card-muted p-4">
                        <div class="text-sm font-black text-text">
                            {{ $isEn ? 'Donor note' : 'ملاحظة للمتبرعين' }}
                        </div>
                        <div class="mt-1 text-sm text-subtext leading-relaxed">
                            {{ $isEn
                                ? 'We publish progress and reports to keep donations transparent and measurable.'
                                : 'ننشر التقدم والتقارير لضمان شفافية التبرعات وقابليتها للقياس.' }}
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-4">
                    <div class="grid gap-3">
                        @foreach ($trust as $item)
                            <div class="rounded-2xl border border-border bg-surface/70 backdrop-blur px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-10 w-10 rounded-2xl grid place-items-center border border-border bg-muted text-lg">
                                        <span aria-hidden="true">{{ $item['icon'] }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-black text-text leading-tight">
                                            {{ $item['title'] }}
                                        </div>
                                        <div class="mt-1 text-sm text-subtext leading-relaxed">
                                            {{ $item['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-black text-text">
                                {{ $isEn ? 'On this page' : 'محتوى الصفحة' }}
                            </div>
                            <button type="button" class="text-xs font-semibold text-subtext hover:text-text transition"
                                onclick="document.getElementById('toc').scrollIntoView({behavior:'smooth', block:'start'});">
                                {{ $isEn ? 'Jump' : 'انتقل' }}
                            </button>
                        </div>

                        <div id="toc" class="mt-3">
                            <div class="text-sm text-subtext">
                                {{ $isEn ? 'Loading sections…' : 'تحميل العناوين…' }}
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-subtext">
                            {{ $isEn ? 'Sections are generated automatically from headings.' : 'يتم توليد الفهرس تلقائيًا من العناوين.' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-border bg-gradient-to-br from-muted to-bg p-5">
                        <div class="text-sm font-black text-text">
                            {{ $isEn ? 'Need help?' : 'هل تحتاج مساعدة؟' }}
                        </div>
                        <div class="mt-1 text-sm text-subtext leading-relaxed">
                            {{ $isEn ? 'Contact us and we’ll guide you.' : 'تواصل معنا وسنساعدك خطوة بخطوة.' }}
                        </div>
                        <a href="{{ $urlTransparency }}"
                            class="mt-3 inline-flex text-sm font-black text-brand hover:underline underline-offset-4">
                            {{ $isEn ? 'See transparency hub' : 'الذهاب لمركز الشفافية' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8">
                <div class="card p-6 sm:p-8">
                    <article id="page-content"
                        class="
                            prose prose-slate max-w-none
                            prose-headings:font-black prose-headings:tracking-tight
                            prose-h1:text-text prose-h2:text-text prose-h3:text-text
                            prose-p:text-subtext
                            prose-strong:text-text
                            prose-a:text-brand prose-a:font-bold prose-a:no-underline hover:prose-a:underline
                            prose-blockquote:border-border prose-blockquote:bg-muted prose-blockquote:rounded-2xl prose-blockquote:px-5 prose-blockquote:py-4
                            prose-hr:border-border
                            prose-li:marker:text-subtext/60
                            prose-table:text-sm
                            prose-img:rounded-3xl prose-img:border prose-img:border-border prose-img:shadow-sm
                            {{ $rtl ? 'prose-p:leading-8 prose-li:leading-8 prose-blockquote:text-subtext' : 'prose-p:leading-7 prose-li:leading-7' }}
                        ">
                        {!! $contentHtml !!}
                    </article>
                </div>

                <div
                    class="mt-6 rounded-[28px] border border-border bg-gradient-to-br from-muted to-bg p-6 sm:p-8 relative overflow-hidden">
                    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full blur-3xl opacity-20"
                        style="background: radial-gradient(circle, rgba(var(--brand),.16), transparent 60%);"></div>
                    <div class="pointer-events-none absolute -left-24 -bottom-24 h-64 w-64 rounded-full blur-3xl opacity-18"
                        style="background: radial-gradient(circle, rgba(var(--brand2),.14), transparent 60%);"></div>

                    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-text">
                                {{ $isEn ? 'Ready to make an impact?' : 'جاهز لتكون جزءًا من الأثر؟' }}
                            </div>
                            <div class="mt-2 text-subtext leading-relaxed max-w-2xl">
                                {{ $isEn
                                    ? 'Choose a trusted campaign and follow progress with updates and reports.'
                                    : 'اختر حملة موثوقة وتابع التقدم عبر التحديثات والتقارير.' }}
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ $urlDonate }}" class="btn btn-primary">
                                {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ $urlCampaigns }}" class="btn btn-secondary">
                                {{ $isEn ? 'Explore campaigns' : 'استعراض الحملات' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Quick actions' : 'إجراءات سريعة' }}</div>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                        <a href="{{ $urlDonate }}" class="btn btn-primary justify-center">
                            {{ $isEn ? 'Donate now' : 'تبرّع الآن' }}
                        </a>
                        <a href="{{ $urlTransparency }}" class="btn btn-secondary justify-center">
                            {{ $isEn ? 'Transparency' : 'الشفافية' }}
                        </a>
                        <a href="{{ $urlCampaigns }}" class="btn btn-secondary justify-center">
                            {{ $isEn ? 'Campaigns' : 'الحملات' }}
                        </a>
                    </div>

                    <div class="mt-4 text-xs text-subtext leading-relaxed">
                        {{ $isEn ? 'Your donation is tracked with reports and updates.' : 'تبرعك يتم تتبعه عبر التقارير والتحديثات.' }}
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Reading tips' : 'نصائح قراءة' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>•
                            {{ $isEn ? 'Use the table of contents to jump to sections.' : 'استخدم الفهرس للانتقال للأقسام.' }}
                        </li>
                        <li>• {{ $isEn ? 'Copy link to share this page.' : 'انسخ الرابط لمشاركة الصفحة.' }}</li>
                        <li>• {{ $isEn ? 'Print-friendly layout included.' : 'التخطيط مناسب للطباعة.' }}</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    @push('head')
        <style>
            @media print {

                header,
                footer,
                details,
                .no-print {
                    display: none !important;
                }

                main {
                    padding: 0 !important;
                }

                .card,
                .card-muted {
                    box-shadow: none !important;
                }

                a {
                    text-decoration: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                const root = document.getElementById('page-content');
                const toc = document.getElementById('toc');
                if (!root || !toc) return;

                const headings = root.querySelectorAll('h2, h3');

                if (!headings.length) {
                    toc.innerHTML =
                        `<div class="text-sm text-subtext">{{ $isEn ? 'No sections found.' : 'لا توجد أقسام.' }}</div>`;
                    return;
                }

                const ul = document.createElement('ul');
                ul.className = 'space-y-2 text-sm';

                headings.forEach((heading, index) => {
                    if (!heading.id) {
                        const base = (heading.textContent || 'section')
                            .trim()
                            .toLowerCase()
                            .replace(/\s+/g, '-')
                            .replace(/[^\w\-ء-ي]+/g, '');

                        heading.id = (base ? base : 'section') + '-' + (index + 1);
                    }

                    const li = document.createElement('li');
                    const link = document.createElement('a');

                    link.href = `#${heading.id}`;
                    link.textContent = heading.textContent || '';
                    link.className =
                        'block rounded-xl border border-border bg-surface hover:bg-muted transition px-3 py-2 ' +
                        (heading.tagName.toLowerCase() === 'h3' ? 'ms-4 text-subtext' : 'font-semibold text-text');

                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const el = document.getElementById(heading.id);
                        if (!el) return;

                        el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        history.replaceState(null, '', `#${heading.id}`);
                    });

                    li.appendChild(link);
                    ul.appendChild(li);
                });

                toc.innerHTML = '';
                toc.appendChild(ul);
            })();
        </script>
    @endpush
@endsection
