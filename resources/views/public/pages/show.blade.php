@extends('layouts.public')

@section('title', $page->metaTitle() ?? $page->title())
@section('meta_description', $page->metaDescription() ?? '')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $rtl = app()->isLocale('ar');
        $pageType = $pageType ?? 'general';

        $urlHome = locale_route('home');
        $urlDonate = locale_route('donate');
        $urlCampaigns = locale_route('campaigns.index');
        $urlTransparency = locale_route('transparency');

        $title = $page->title();
        $desc = $page->metaDescription();
        $updated = $page->updated_at?->format('Y-m-d');
        $contentHtml = (string) ($page->content() ?? '');

        $profiles = [
            'privacy' => [
                'eyebrow' => $isEn ? 'Privacy policy' : 'سياسة الخصوصية',
                'summary' => $isEn ? 'This page explains how donor and visitor data is collected, protected, and used.' : 'توضح هذه الصفحة كيفية جمع بيانات المتبرعين والزوار وحمايتها واستخدامها.',
                'note' => $isEn ? 'Read this page to understand donor privacy, receipt delivery, and account-related communication.' : 'راجع هذه الصفحة لفهم خصوصية المتبرع وإيصال الإيصالات والتواصل المرتبط بالحساب.',
            ],
            'terms' => [
                'eyebrow' => $isEn ? 'Terms and conditions' : 'الشروط والأحكام',
                'summary' => $isEn ? 'This page defines platform rules, donor expectations, and the boundaries of service use.' : 'تحدد هذه الصفحة قواعد المنصة وتوقعات المتبرع وحدود استخدام الخدمة.',
                'note' => $isEn ? 'Review this page before donating or relying on a process, timeline, or platform feature.' : 'راجع هذه الصفحة قبل التبرع أو الاعتماد على أي إجراء أو مدة أو ميزة داخل المنصة.',
            ],
            'refund' => [
                'eyebrow' => $isEn ? 'Refund policy' : 'سياسة الاسترداد',
                'summary' => $isEn ? 'This page explains when a donation may be reviewed, declined, corrected, or refunded.' : 'تشرح هذه الصفحة متى يمكن مراجعة التبرع أو رفضه أو تصحيحه أو استرداده.',
                'note' => $isEn ? 'Use it to understand payment outcomes, review windows, and receipt status after intervention.' : 'استخدمها لفهم نتائج الدفع وفترات المراجعة ووضع الإيصال بعد أي تدخل.',
            ],
            'cookies' => [
                'eyebrow' => $isEn ? 'Cookie notice' : 'سياسة ملفات الارتباط',
                'summary' => $isEn ? 'This page explains how cookies support security, preferences, and performance across the platform.' : 'توضح هذه الصفحة كيف تدعم ملفات الارتباط الأمان والتفضيلات والأداء عبر المنصة.',
                'note' => $isEn ? 'It distinguishes between what is needed for secure operation and what improves the browsing experience.' : 'تميّز هذه الصفحة بين ما هو ضروري للتشغيل الآمن وما يحسن تجربة الاستخدام.',
            ],
            'about' => [
                'eyebrow' => $isEn ? 'About the platform' : 'عن المنصة',
                'summary' => $isEn ? 'This page presents the platform mission, identity, and trust approach.' : 'تعرض هذه الصفحة رسالة المنصة وهويتها ومنهجها في بناء الثقة.',
                'note' => $isEn ? 'It helps visitors understand who the platform serves and how public trust is maintained.' : 'تساعد الزائر على فهم من تخدمه المنصة وكيف يتم الحفاظ على الثقة العامة.',
            ],
            'general' => [
                'eyebrow' => $isEn ? 'Verified public page' : 'صفحة عامة موثقة',
                'summary' => $isEn ? 'This page is part of the platform public record and donor guidance library.' : 'هذه الصفحة جزء من السجل العام للمنصة ومكتبة الإرشاد الخاصة بالمتبرعين.',
                'note' => $isEn ? 'We publish important information in a structured and reviewable way to support trust.' : 'ننشر المعلومات المهمة بطريقة منظمة وقابلة للمراجعة لدعم الثقة.',
            ],
        ];

        $profile = $profiles[$pageType] ?? $profiles['general'];

        $trust = [
            [
                'title' => $isEn ? 'Clear identity' : 'هوية واضحة',
                'desc' => $isEn ? 'This page is aligned with the platform mission, donation flow, and transparency model.' : 'هذه الصفحة منسجمة مع رسالة المنصة ومسار التبرع ونموذج الشفافية.',
                'icon' => 'ID',
            ],
            [
                'title' => $isEn ? 'Reviewable information' : 'معلومات قابلة للمراجعة',
                'desc' => $isEn ? 'Key donor-facing details are published in one structured and accessible place.' : 'يتم نشر التفاصيل المهمة للمتبرع في مكان واحد منظم وسهل الوصول.',
                'icon' => 'DOC',
            ],
            [
                'title' => $isEn ? 'Security-aware operation' : 'تشغيل واع أمنيًا',
                'desc' => $isEn ? 'The platform protects sessions, receipts, and payment visibility with safer defaults.' : 'تحمي المنصة الجلسات والإيصالات ووضوح الدفع بإعدادات أكثر أمانًا.',
                'icon' => 'SAFE',
            ],
        ];

        $legalChecklist = [
            $isEn ? 'Check the last updated date before relying on any operational detail.' : 'راجع تاريخ آخر تحديث قبل الاعتماد على أي تفصيل تشغيلي.',
            $isEn ? 'Use the transparency hub for reports and campaign-level proof.' : 'استخدم مركز الشفافية للوصول إلى التقارير والإثباتات الخاصة بالحملات.',
            $isEn ? 'Keep receipt and status links when donating as a guest.' : 'احتفظ بروابط الإيصال والحالة عند التبرع كضيف.',
        ];

        $shareLabel = $isEn ? 'Copy page link' : 'نسخ رابط الصفحة';
        $copiedLabel = $isEn ? 'Copied!' : 'تم النسخ!';
        $printLabel = $isEn ? 'Print' : 'طباعة';
    @endphp

    <section class="section-shell relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-muted via-bg to-transparent"></div>
        <div class="pointer-events-none absolute -right-20 -top-24 h-80 w-80 rounded-full blur-3xl opacity-25" style="background: radial-gradient(circle, rgba(var(--brand),.18), transparent 60%);"></div>
        <div class="pointer-events-none absolute -left-24 -bottom-24 h-80 w-80 rounded-full blur-3xl opacity-20" style="background: radial-gradient(circle, rgba(var(--brand2),.16), transparent 60%);"></div>

        <div class="px-6 sm:px-10 py-9 sm:py-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-subtext">
                    <a href="{{ $urlHome }}" class="hover:underline underline-offset-4">{{ $isEn ? 'Home' : 'الرئيسية' }}</a>
                    <span class="mx-2 text-subtext/50">/</span>
                    <span class="text-text font-semibold">{{ $title }}</span>
                </div>

                <div class="eyebrow">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $profile['eyebrow'] }}</span>
                </div>
            </div>

            <div class="mt-7 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8">
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-text leading-tight">{{ $title }}</h1>

                    <p class="mt-3 text-subtext leading-relaxed max-w-3xl">{{ $profile['summary'] }}</p>

                    @if (!empty($desc))
                        <p class="mt-3 text-subtext leading-relaxed max-w-3xl">{{ $desc }}</p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $urlTransparency }}" class="btn btn-primary">{{ $isEn ? 'Transparency hub' : 'مركز الشفافية' }}</a>
                        <a href="{{ $urlCampaigns }}" class="btn btn-secondary">{{ $isEn ? 'Browse campaigns' : 'استعراض الحملات' }}</a>
                        <a href="{{ $urlDonate }}" class="btn btn-secondary">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }}</a>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-subtext">
                        @if ($updated)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-muted">
                                <span class="font-semibold">{{ $isEn ? 'Last updated:' : 'آخر تحديث:' }}</span>
                                <span class="font-black text-text">{{ $updated }}</span>
                            </span>
                        @endif

                        <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface hover:bg-muted transition font-semibold" onclick="navigator.clipboard.writeText(window.location.href); this.querySelector('span').textContent='{{ $copiedLabel }}';">
                            <span>{{ $shareLabel }}</span>
                            <span aria-hidden="true">↗</span>
                        </button>

                        <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-surface hover:bg-muted transition font-semibold" onclick="window.print()">
                            <span>{{ $printLabel }}</span>
                            <span aria-hidden="true">⌘</span>
                        </button>
                    </div>

                    <div class="mt-6 card-muted p-5">
                        <div class="text-sm font-black text-text">{{ $isEn ? 'Page note' : 'ملاحظة الصفحة' }}</div>
                        <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $profile['note'] }}</div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-4">
                    <div class="grid gap-3">
                        @foreach ($trust as $item)
                            <div class="rounded-2xl border border-border bg-surface/70 backdrop-blur px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-2xl grid place-items-center border border-border bg-muted text-[11px] font-black text-text">{{ $item['icon'] }}</div>
                                    <div class="min-w-0">
                                        <div class="font-black text-text leading-tight">{{ $item['title'] }}</div>
                                        <div class="mt-1 text-sm text-subtext leading-relaxed">{{ $item['desc'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card p-5">
                        <div class="font-black text-text">{{ $isEn ? 'Reading checklist' : 'قائمة مراجعة' }}</div>
                        <ul class="mt-3 space-y-2 text-sm text-subtext">
                            @foreach ($legalChecklist as $item)
                                <li>• {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-border bg-gradient-to-br from-muted to-bg p-5">
                        <div class="text-sm font-black text-text">{{ $isEn ? 'Need more proof?' : 'تحتاج إلى إثباتات إضافية؟' }}</div>
                        <div class="mt-1 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Use the transparency hub and campaign pages for reports, updates, and donation-facing proof.' : 'استخدم مركز الشفافية وصفحات الحملات للوصول إلى التقارير والتحديثات والإثباتات الموجهة للمتبرع.' }}</div>
                        <a href="{{ $urlTransparency }}" class="mt-3 inline-flex text-sm font-black text-brand hover:underline underline-offset-4">{{ $isEn ? 'Open transparency hub' : 'فتح مركز الشفافية' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8">
                <div class="card p-6 sm:p-8">
                    <article id="page-content" class="prose prose-slate max-w-none prose-headings:font-black prose-headings:tracking-tight prose-h1:text-text prose-h2:text-text prose-h3:text-text prose-p:text-subtext prose-strong:text-text prose-a:text-brand prose-a:font-bold prose-a:no-underline hover:prose-a:underline prose-blockquote:border-border prose-blockquote:bg-muted prose-blockquote:rounded-2xl prose-blockquote:px-5 prose-blockquote:py-4 prose-hr:border-border prose-li:marker:text-subtext/60 prose-table:text-sm prose-img:rounded-3xl prose-img:border prose-img:border-border prose-img:shadow-sm {{ $rtl ? 'prose-p:leading-8 prose-li:leading-8 prose-blockquote:text-subtext' : 'prose-p:leading-7 prose-li:leading-7' }}">
                        {!! $contentHtml !!}
                    </article>
                </div>

                <div class="mt-6 rounded-[28px] border border-border bg-gradient-to-br from-muted to-bg p-6 sm:p-8 relative overflow-hidden">
                    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full blur-3xl opacity-20" style="background: radial-gradient(circle, rgba(var(--brand),.16), transparent 60%);"></div>
                    <div class="pointer-events-none absolute -left-24 -bottom-24 h-64 w-64 rounded-full blur-3xl opacity-18" style="background: radial-gradient(circle, rgba(var(--brand2),.14), transparent 60%);"></div>

                    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-text">{{ $isEn ? 'Ready to donate with confidence?' : 'جاهز للتبرع بثقة؟' }}</div>
                            <div class="mt-2 text-subtext leading-relaxed max-w-2xl">{{ $isEn ? 'Choose a trusted campaign and follow progress through updates, reports, receipts, and secure status visibility.' : 'اختر حملة موثوقة وتابع التقدم عبر التحديثات والتقارير والإيصالات ووضوح الحالة الآمن.' }}</div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ $urlDonate }}" class="btn btn-primary">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }}</a>
                            <a href="{{ $urlCampaigns }}" class="btn btn-secondary">{{ $isEn ? 'Explore campaigns' : 'استعراض الحملات' }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
                <div class="card p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Quick actions' : 'إجراءات سريعة' }}</div>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                        <a href="{{ $urlTransparency }}" class="btn btn-primary justify-center">{{ $isEn ? 'Transparency' : 'الشفافية' }}</a>
                        <a href="{{ $urlCampaigns }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Campaigns' : 'الحملات' }}</a>
                        <a href="{{ $urlDonate }}" class="btn btn-secondary justify-center">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }}</a>
                    </div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'How to use this page' : 'كيفية استخدام هذه الصفحة' }}</div>
                    <ul class="mt-3 space-y-2 text-sm text-subtext">
                        <li>• {{ $isEn ? 'Use the table of contents to move between sections quickly.' : 'استخدم الفهرس للانتقال السريع بين الأقسام.' }}</li>
                        <li>• {{ $isEn ? 'Copy the link if you need to share this page with a donor or partner.' : 'انسخ الرابط إذا احتجت إلى مشاركة الصفحة مع متبرع أو جهة شريكة.' }}</li>
                        <li>• {{ $isEn ? 'Use print mode when you need a clean offline review.' : 'استخدم وضع الطباعة عندما تحتاج إلى مراجعة نظيفة دون اتصال.' }}</li>
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
                    toc.innerHTML = `<div class="text-sm text-subtext">{{ $isEn ? 'No sections found.' : 'لا توجد أقسام.' }}</div>`;
                    return;
                }

                const ul = document.createElement('ul');
                ul.className = 'space-y-2 text-sm';

                headings.forEach((heading, index) => {
                    if (!heading.id) {
                        const base = (heading.textContent || 'section').trim().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-ء-ي]+/g, '');
                        heading.id = (base ? base : 'section') + '-' + (index + 1);
                    }

                    const li = document.createElement('li');
                    const link = document.createElement('a');

                    link.href = `#${heading.id}`;
                    link.textContent = heading.textContent || '';
                    link.className = 'block rounded-xl border border-border bg-surface hover:bg-muted transition px-3 py-2 ' + (heading.tagName.toLowerCase() === 'h3' ? 'ms-4 text-subtext' : 'font-semibold text-text');

                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const el = document.getElementById(heading.id);
                        if (!el) return;

                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
