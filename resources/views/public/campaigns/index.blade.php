@extends('layouts.public')
@section('title', __('ui.campaigns'))

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        $subtitle = $isEn
            ? 'Explore active campaigns and donate with confidence.'
            : 'استعرض الحملات النشطة وتبرّع بثقة.';

        $t = fn($c) => $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);
        $d = fn($c) => $isEn ? ($c->description_en ?: $c->description_ar) : ($c->description_ar ?: $c->description_en);

        $money = fn($v) => number_format((float) $v, 2);

        $statusLabel = function ($st) use ($isEn) {
            return match ((string) $st) {
                'active' => $isEn ? 'Active' : 'نشطة',
                'paused' => $isEn ? 'Paused' : 'متوقفة مؤقتًا',
                default => (string) $st,
            };
        };

        $statusBadge = function ($st) {
            return match ((string) $st) {
                'active' => 'bg-success/10 text-success border-success/25',
                'paused' => 'bg-warning/10 text-warning border-warning/25',
                default => 'bg-muted text-subtext border-border',
            };
        };

        // values from controller (or fallback)
        $q = $q ?? request('q', '');
        $status = $status ?? request('status');
        $featured = $featured ?? request('featured');
        $sort = $sort ?? request('sort', 'featured');

        $hasFilters = (bool) ($q || $status || $featured === '1' || $sort !== 'featured');

        $urlDonate = url($base . '/donate');
        $urlTransparency = url($base . '/transparency');
        $urlReset = url($base . '/campaigns');
    @endphp

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-text">
                {{ __('ui.campaigns') }}
            </h1>
            <p class="mt-2 text-subtext">{{ $subtitle }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ $urlDonate }}" class="btn btn-primary">
                {{ __('ui.donate_now') }}
                <span aria-hidden="true">→</span>
            </a>

            <a href="{{ $urlTransparency }}" class="btn btn-secondary">
                {{ $isEn ? 'Transparency' : 'الشفافية' }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="get" class="card p-5 sm:p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-5">
                <label class="text-xs font-black text-subtext">{{ $isEn ? 'Search' : 'بحث' }}</label>
                <input name="q" value="{{ $q }}" class="input mt-1"
                    placeholder="{{ $isEn ? 'Search by title or slug…' : 'ابحث بالعنوان أو الرابط…' }}">
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-black text-subtext">{{ $isEn ? 'Status' : 'الحالة' }}</label>
                <select name="status" class="input mt-1">
                    <option value="">{{ $isEn ? 'All' : 'الكل' }}</option>
                    <option value="active" @selected($status === 'active')>{{ $isEn ? 'Active' : 'نشطة' }}</option>
                    <option value="paused" @selected($status === 'paused')>{{ $isEn ? 'Paused' : 'متوقفة' }}</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-black text-subtext">{{ $isEn ? 'Sort' : 'الترتيب' }}</label>
                <select name="sort" class="input mt-1">
                    <option value="featured" @selected($sort === 'featured')>{{ $isEn ? 'Featured' : 'الأبرز' }}</option>
                    <option value="new" @selected($sort === 'new')>{{ $isEn ? 'Newest' : 'الأحدث' }}</option>
                    <option value="progress" @selected($sort === 'progress')>
                        {{ $isEn ? 'Highest progress' : 'الأعلى تقدّماً' }}</option>
                    <option value="goal" @selected($sort === 'goal')>{{ $isEn ? 'Highest goal' : 'الأعلى هدفاً' }}
                    </option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-black text-subtext">{{ $isEn ? 'Featured only' : 'مميزة فقط' }}</label>
                <div class="mt-1 flex items-center gap-2">
                    <input id="featured" type="checkbox" name="featured" value="1" @checked($featured === '1')
                        class="h-5 w-5 rounded border-border text-brand focus:ring-2 focus:ring-[rgba(var(--brand),.25)]">
                    <label for="featured" class="text-sm text-subtext font-semibold">
                        {{ $isEn ? 'Show featured' : 'عرض المميزة' }}
                    </label>
                </div>
            </div>

            <div class="lg:col-span-1 flex gap-2">
                <button type="submit" class="w-full btn btn-primary px-4 py-3">
                    {{ $isEn ? 'Apply' : 'تطبيق' }}
                </button>
            </div>
        </div>

        {{-- active filters chips --}}
        <div class="mt-4 flex flex-wrap gap-2">
            @if ($q)
                <span class="badge">
                    {{ $isEn ? 'Query:' : 'بحث:' }} <span class="font-black text-text">{{ $q }}</span>
                </span>
            @endif
            @if ($status)
                <span class="badge">
                    {{ $isEn ? 'Status:' : 'الحالة:' }} <span
                        class="font-black text-text">{{ $statusLabel($status) }}</span>
                </span>
            @endif
            @if ($featured === '1')
                <span class="badge"
                    style="border-color: rgba(var(--brand),.25); color: rgb(var(--brand)); background: rgba(var(--brand),.08);">
                    {{ $isEn ? 'Featured only' : 'مميزة فقط' }}
                </span>
            @endif
            @if ($sort && $sort !== 'featured')
                <span class="badge">
                    {{ $isEn ? 'Sort:' : 'الترتيب:' }} <span class="font-black text-text">{{ $sort }}</span>
                </span>
            @endif

            @if ($hasFilters)
                <a href="{{ $urlReset }}" class="badge hover:bg-muted transition">
                    {{ $isEn ? 'Reset' : 'إعادة ضبط' }}
                </a>
            @endif
        </div>
    </form>

    {{-- Grid --}}
    @if ($campaigns->count())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($campaigns as $c)
                @php
                    $img = $c->cover_url ?? ($c->cover_image_path ? asset('storage/' . $c->cover_image_path) : null);
                    $progress = (int) ($c->progress_percent ?? 0);
                    $progress = max(0, min(100, $progress));
                @endphp

                <a href="{{ url($base . '/campaigns/' . $c->slug) }}"
                    class="group block rounded-[26px] border border-border bg-surface overflow-hidden hover:bg-muted transition">

                    <div class="relative h-44 bg-muted">
                        @if ($img)
                            <img src="{{ $img }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-muted to-bg"></div>
                        @endif

                        <div class="absolute top-3 {{ $isEn ? 'right-3' : 'left-3' }} flex items-center gap-2">
                            @if ($c->is_featured)
                                <span class="text-[11px] px-3 py-1 rounded-full border font-black"
                                    style="border-color: rgba(var(--brand),.25); color: rgb(var(--brand)); background: rgba(var(--brand),.08);">
                                    {{ $isEn ? 'Featured' : 'مميزة' }}
                                </span>
                            @endif

                            <span
                                class="text-[11px] px-3 py-1 rounded-full border font-black {{ $statusBadge($c->status) }}">
                                {{ $statusLabel($c->status) }}
                            </span>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/15 via-transparent to-transparent"></div>
                    </div>

                    <div class="p-6">
                        <h2 class="text-lg font-black text-text leading-snug line-clamp-2">
                            {{ $t($c) }}
                        </h2>

                        <p class="mt-2 text-sm text-subtext leading-relaxed line-clamp-2">
                            {{ $d($c) ?: ($isEn ? 'No description yet.' : 'لا يوجد وصف بعد.') }}
                        </p>

                        <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="card-muted p-3">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Raised' : 'تم جمع' }}</div>
                                <div class="mt-1 font-black text-text">
                                    {{ $money($c->current_amount) }} {{ $c->currency }}
                                </div>
                            </div>

                            <div class="card-muted p-3">
                                <div class="text-xs text-subtext">{{ $isEn ? 'Goal' : 'الهدف' }}</div>
                                <div class="mt-1 font-black text-text">
                                    {{ $money($c->goal_amount) }} {{ $c->currency }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="flex justify-between text-xs text-subtext mb-2">
                                <span class="font-black text-text">{{ $progress }}%</span>
                                <span>{{ $isEn ? 'Donors' : 'متبرعون' }}: {{ (int) ($c->donors_count ?? 0) }}</span>
                            </div>

                            <div class="h-2 bg-muted rounded-full overflow-hidden border border-border">
                                <div class="h-2 rounded-full transition-all duration-700"
                                    style="width: {{ $progress }}%; background: linear-gradient(135deg, rgb(var(--brand)), rgb(var(--brand2)));">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-sm text-subtext">{{ $isEn ? 'Open details' : 'فتح التفاصيل' }}</span>
                            <span
                                class="inline-flex items-center gap-2 text-sm font-black text-text group-hover:underline underline-offset-4">
                                {{ $isEn ? 'View' : 'عرض' }}
                                <span class="text-subtext">↗</span>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $campaigns->links() }}
        </div>
    @else
        <div class="card-muted p-10 text-center">
            <div class="text-lg font-black text-text">
                {{ $isEn ? 'No campaigns found.' : 'لا توجد حملات مطابقة.' }}
            </div>
            <div class="mt-2 text-sm text-subtext">
                {{ $isEn ? 'Try changing filters or check back later.' : 'جرّب تغيير الفلاتر أو عد لاحقًا.' }}
            </div>
            <div class="mt-5">
                <a href="{{ $urlReset }}" class="btn btn-secondary">
                    {{ $isEn ? 'Reset' : 'إعادة ضبط' }}
                </a>
            </div>
        </div>
    @endif
@endsection
