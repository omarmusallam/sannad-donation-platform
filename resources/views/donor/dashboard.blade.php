@extends('layouts.public')

@section('title', app()->isLocale('en') ? 'My account' : 'حسابي')

@section('content')
    @php
        $isEn = app()->isLocale('en');
        $isAr = !$isEn;
        $homeUrl = locale_route('home');
        $donationsUrl = locale_route('donor.donations');
        $donateUrl = locale_route('donate');
        $profileUrl = locale_route('donor.profile');
        $title = $isEn ? 'My account' : 'حسابي';
        $subtitle = $isEn
            ? 'Track your donations, receipts, and supported campaigns in one calm, professional dashboard.'
            : 'تابع تبرعاتك وإيصالاتك والحملات التي دعمتها من خلال لوحة هادئة واحترافية وواضحة.';
        $donorName = $donor->name ?: ($isEn ? 'Donor' : 'متبرع');
        $donorEmail = $donor->email ?? null;
        $donorInitial = mb_strtoupper(mb_substr($donorName, 0, 1));
        $fmtMoney = function ($amount, $currency = null) {
            $amount = number_format((float) $amount, 2);
            return $currency ? $amount . ' ' . $currency : $amount;
        };
        $statusCount = function (string $key) use ($statusSummary) {
            return (int) ($statusSummary[$key] ?? 0);
        };
        $lastDonationDate = !empty($stats['last_donation_at']) ? \Illuminate\Support\Carbon::parse($stats['last_donation_at'])->format('Y-m-d') : null;
        $campaignTitle = function ($campaign) use ($isEn) {
            if (!$campaign) {
                return $isEn ? 'Unknown campaign' : 'حملة غير معروفة';
            }
            return $isEn ? ($campaign->title_en ?: $campaign->title_ar) : ($campaign->title_ar ?: $campaign->title_en);
        };
    @endphp

    <div class="max-w-7xl mx-auto space-y-8">
        <section class="section-shell">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">
                <div class="min-w-0 flex-1">
                    <div class="text-sm text-subtext">
                        <a class="hover:underline underline-offset-4" href="{{ $homeUrl }}">{{ $isEn ? 'Home' : 'الرئيسية' }}</a>
                        <span class="mx-2">/</span>
                        <span class="font-semibold text-text">{{ $title }}</span>
                    </div>

                    <div class="mt-5 flex items-start gap-4">
                        <div class="h-16 w-16 rounded-3xl border border-border bg-muted grid place-items-center text-2xl font-black text-text shrink-0">{{ $donorInitial }}</div>
                        <div class="min-w-0">
                            <div class="eyebrow">{{ $isEn ? 'Donor account' : 'حساب المتبرع' }}</div>
                            <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-text">{{ $donorName }}</h1>
                            @if ($donorEmail)
                                <div class="mt-2 text-sm text-subtext break-all">{{ $donorEmail }}</div>
                            @endif
                        </div>
                    </div>

                    <p class="mt-5 text-subtext leading-relaxed max-w-3xl">{{ $subtitle }}</p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="badge">{{ $isEn ? 'Receipts' : 'إيصالات' }}</span>
                        <span class="badge">{{ $isEn ? 'History' : 'السجل' }}</span>
                        <span class="badge">{{ $isEn ? 'Status tracking' : 'تتبع الحالات' }}</span>
                        <span class="badge">{{ $isEn ? 'Secure account' : 'حساب آمن' }}</span>
                    </div>
                </div>

                <div class="xl:w-[360px] shrink-0">
                    <div class="card-muted p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm text-subtext">{{ $isEn ? 'Quick overview' : 'نظرة سريعة' }}</div>
                                <div class="mt-1 text-xl font-black text-text">{{ $isEn ? 'Your account at a glance' : 'حسابك بنظرة واحدة' }}</div>
                            </div>
                            <span class="badge">{{ $isEn ? 'Live' : 'مباشر' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-border bg-surface p-4"><div class="text-subtext">{{ $isEn ? 'Paid' : 'مدفوع' }}</div><div class="mt-1 text-xl font-black text-text">{{ $statusCount('paid') }}</div></div>
                            <div class="rounded-2xl border border-border bg-surface p-4"><div class="text-subtext">{{ $isEn ? 'Pending' : 'قيد الانتظار' }}</div><div class="mt-1 text-xl font-black text-text">{{ $statusCount('pending') }}</div></div>
                        </div>

                        @if ($lastDonationDate)
                            <div class="rounded-2xl border border-border bg-surface p-4 text-sm"><div class="text-subtext">{{ $isEn ? 'Last donation' : 'آخر تبرع' }}</div><div class="mt-1 font-black text-text">{{ $lastDonationDate }}</div></div>
                        @endif

                        <div class="grid grid-cols-1 gap-3">
                            <a class="btn btn-primary w-full justify-center" href="{{ $donateUrl }}">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }} <span aria-hidden="true">→</span></a>
                            <a class="btn btn-secondary w-full justify-center" href="{{ $donationsUrl }}">{{ $isEn ? 'View all donations' : 'عرض كل التبرعات' }}</a>
                            <a class="btn btn-secondary w-full justify-center" href="{{ $profileUrl }}">{{ $isEn ? 'Profile settings' : 'إعدادات الحساب' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <aside class="xl:col-span-1 space-y-4">
                @include('donor.partials.account-nav')

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Trust note' : 'ملاحظة ثقة' }}</div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Your account keeps donation history, receipts, and status visibility in one protected place.' : 'حسابك يجمع سجل التبرعات والإيصالات ووضوح الحالة في مكان واحد ومحمي.' }}</div>
                </div>

                <div class="card-muted p-5">
                    <div class="font-black text-text">{{ $isEn ? 'Professional basics' : 'أساسيات احترافية' }}</div>
                    <div class="mt-2 text-sm text-subtext leading-relaxed">{{ $isEn ? 'Amounts remain in USD, receipts stay easy to access, and recent activity is organized clearly.' : 'المبالغ تبقى بالدولار، والإيصالات سهلة الوصول، والنشاط الأخير منظم بوضوح.' }}</div>
                </div>
            </aside>

            <div class="xl:col-span-3 space-y-6">
                <section class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-5">
                    <div class="kpi-tile"><div class="text-sm text-subtext font-semibold">{{ $isEn ? 'Total donations' : 'عدد التبرعات' }}</div><div class="mt-3 text-3xl font-black text-text">{{ (int) ($stats['total_donations'] ?? 0) }}</div></div>
                    <div class="kpi-tile"><div class="text-sm text-subtext font-semibold">{{ $isEn ? 'Paid donations' : 'التبرعات المدفوعة' }}</div><div class="mt-3 text-3xl font-black text-text">{{ (int) ($stats['paid_donations'] ?? 0) }}</div></div>
                    <div class="kpi-tile"><div class="text-sm text-subtext font-semibold">{{ $isEn ? 'Total amount' : 'إجمالي المبلغ' }}</div><div class="mt-3 text-3xl font-black text-text">{{ $fmtMoney($stats['total_amount'] ?? 0, 'USD') }}</div></div>
                    <div class="kpi-tile"><div class="text-sm text-subtext font-semibold">{{ $isEn ? 'Campaigns supported' : 'الحملات المدعومة' }}</div><div class="mt-3 text-3xl font-black text-text">{{ (int) ($stats['campaigns_supported'] ?? 0) }}</div></div>
                </section>

                <section class="grid grid-cols-1 2xl:grid-cols-3 gap-6">
                    <div class="2xl:col-span-2 card p-6 sm:p-7">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <div class="eyebrow">{{ $isEn ? 'Status overview' : 'ملخص الحالات' }}</div>
                                <h2 class="mt-3 text-xl font-black text-text">{{ $isEn ? 'Donation status summary' : 'ملخص حالات التبرعات' }}</h2>
                                <p class="mt-1 text-sm text-subtext">{{ $isEn ? 'A fast view of your donation activity and current payment states.' : 'نظرة سريعة على نشاط تبرعاتك وحالات الدفع الحالية.' }}</p>
                            </div>
                            <a class="btn btn-secondary" href="{{ $donationsUrl }}">{{ $isEn ? 'Open donations page' : 'فتح صفحة التبرعات' }}</a>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="rounded-2xl border border-border bg-muted p-5"><div class="text-sm text-subtext">{{ $isEn ? 'Paid' : 'مدفوع' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $statusCount('paid') }}</div></div>
                            <div class="rounded-2xl border border-border bg-muted p-5"><div class="text-sm text-subtext">{{ $isEn ? 'Pending' : 'قيد الانتظار' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $statusCount('pending') }}</div></div>
                            <div class="rounded-2xl border border-border bg-muted p-5"><div class="text-sm text-subtext">{{ $isEn ? 'Failed' : 'فشل' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $statusCount('failed') }}</div></div>
                            <div class="rounded-2xl border border-border bg-muted p-5"><div class="text-sm text-subtext">{{ $isEn ? 'Refunded' : 'مسترد' }}</div><div class="mt-2 text-2xl font-black text-text">{{ $statusCount('refunded') }}</div></div>
                        </div>
                    </div>

                    <div class="card p-6 sm:p-7">
                        <div class="eyebrow">{{ $isEn ? 'Latest confirmed donation' : 'آخر تبرع مكتمل' }}</div>

                        @if ($latestPaid)
                            <div class="mt-4 rounded-3xl border border-border bg-muted p-5">
                                <div class="text-sm text-subtext">{{ $isEn ? 'Campaign' : 'الحملة' }}</div>
                                <div class="mt-1 text-lg font-black text-text">{{ $campaignTitle($latestPaid->campaign) }}</div>
                                <div class="mt-4 text-sm text-subtext">{{ $isEn ? 'Amount' : 'المبلغ' }}</div>
                                <div class="mt-1 text-2xl font-black text-text">{{ $fmtMoney($latestPaid->amount, $latestPaid->currency) }}</div>
                                <div class="mt-4 text-sm text-subtext">{{ $isEn ? 'Date' : 'التاريخ' }}</div>
                                <div class="mt-1 font-semibold text-text">{{ optional($latestPaid->paid_at ?? $latestPaid->created_at)->format('Y-m-d') }}</div>

                                @if ($latestPaid->receipt)
                                    <a class="mt-5 btn btn-secondary w-full justify-center" href="{{ locale_route('receipt.verify', ['receipt' => $latestPaid->receipt]) }}">{{ $isEn ? 'Open receipt' : 'فتح الإيصال' }}</a>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 rounded-3xl border border-border bg-muted p-5 text-sm text-subtext leading-relaxed">{{ $isEn ? 'No successful donations yet. Once you complete one, the latest confirmed donation will appear here with direct receipt access.' : 'لا توجد تبرعات ناجحة بعد. عند إكمال أول تبرع سيظهر هنا آخر تبرع مكتمل مع وصول مباشر إلى الإيصال.' }}</div>
                            <a class="mt-4 btn btn-primary w-full justify-center" href="{{ $donateUrl }}">{{ $isEn ? 'Make your first donation' : 'ابدأ أول تبرع' }}</a>
                        @endif
                    </div>
                </section>

                <section class="card p-6 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                        <div>
                            <div class="eyebrow">{{ $isEn ? 'Recent activity' : 'أحدث النشاطات' }}</div>
                            <h2 class="mt-3 text-xl font-black text-text">{{ $isEn ? 'Recent donations' : 'أحدث التبرعات' }}</h2>
                            <p class="mt-1 text-sm text-subtext">{{ $isEn ? 'Your latest activity with quick access to receipts and status visibility.' : 'أحدث نشاطاتك مع وصول سريع إلى الإيصالات ووضوح في الحالة.' }}</p>
                        </div>

                        <a class="btn btn-secondary" href="{{ $donationsUrl }}">{{ $isEn ? 'View all donations' : 'عرض كل التبرعات' }}</a>
                    </div>

                    @if ($donations->count())
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-sm">
                                <thead>
                                    <tr class="border-b text-subtext">
                                        <th class="py-3 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $isEn ? 'Campaign' : 'الحملة' }}</th>
                                        <th class="py-3 text-center">{{ $isEn ? 'Amount' : 'المبلغ' }}</th>
                                        <th class="py-3 text-center">{{ $isEn ? 'Status' : 'الحالة' }}</th>
                                        <th class="py-3 text-center">{{ $isEn ? 'Date' : 'التاريخ' }}</th>
                                        <th class="py-3 {{ $isAr ? 'text-left' : 'text-right' }}">{{ $isEn ? 'Receipt' : 'الإيصال' }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($donations as $donation)
                                        @php
                                            $statusText = match ((string) $donation->status) {
                                                'paid' => $isEn ? 'Paid' : 'مدفوع',
                                                'pending' => $isEn ? 'Pending' : 'قيد الانتظار',
                                                'failed' => $isEn ? 'Failed' : 'فشل',
                                                'refunded' => $isEn ? 'Refunded' : 'مسترد',
                                                default => (string) $donation->status,
                                            };

                                            $statusClass = match ((string) $donation->status) {
                                                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'refunded' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };

                                            $date = optional($donation->paid_at ?? $donation->created_at)->format('Y-m-d');
                                        @endphp

                                        <tr class="border-b last:border-0">
                                            <td class="py-4 font-semibold text-text">{{ $campaignTitle($donation->campaign) }}</td>
                                            <td class="py-4 text-center font-black text-text">{{ $fmtMoney($donation->amount, $donation->currency) }}</td>
                                            <td class="py-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span></td>
                                            <td class="py-4 text-center text-subtext">{{ $date }}</td>
                                            <td class="py-4 {{ $isAr ? 'text-left' : 'text-right' }}">
                                                @if ($donation->receipt)
                                                    <a class="btn btn-secondary text-xs" href="{{ locale_route('receipt.verify', ['receipt' => $donation->receipt]) }}">{{ $isEn ? 'Open receipt' : 'فتح الإيصال' }}</a>
                                                @else
                                                    <span class="text-xs text-subtext/70">{{ $isEn ? 'No receipt' : 'لا يوجد إيصال' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-3xl border border-border bg-muted p-10 text-center">
                            <div class="text-xl font-black text-text">{{ $isEn ? 'No donations yet' : 'لا توجد تبرعات بعد' }}</div>
                            <div class="mt-2 text-sm text-subtext">{{ $isEn ? 'Start supporting campaigns and your activity will appear here in an organized way.' : 'ابدأ بدعم الحملات وستظهر نشاطاتك هنا بشكل منظم وواضح.' }}</div>
                            <div class="mt-5"><a class="btn btn-primary" href="{{ $donateUrl }}">{{ $isEn ? 'Donate now' : 'تبرّع الآن' }}</a></div>
                        </div>
                    @endif
                </section>
            </div>
        </section>
    </div>
@endsection
