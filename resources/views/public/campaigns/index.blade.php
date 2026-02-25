@extends('layouts.public')
@section('title', __('ui.campaigns'))

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $base = $isEn ? '/en' : '';

        $subtitle = $isEn
            ? 'Explore active campaigns and donate with confidence.'
            : 'استعرض الحملات النشطة وتبرع بثقة.';

        $t = fn($c) => $isEn ? ($c->title_en ?: $c->title_ar) : ($c->title_ar ?: $c->title_en);
        $d = fn($c) => $isEn ? ($c->description_en ?: $c->description_ar) : ($c->description_ar ?: $c->description_en);

        $money = fn($v) => number_format((float) $v, 2);

        $statusLabel = function (string $st) use ($isEn) {
            return match ($st) {
                'active' => $isEn ? 'Active' : 'نشطة',
                'paused' => $isEn ? 'Paused' : 'متوقفة مؤقتًا',
                default => $st,
            };
        };

        $badge = function (string $st) {
            return match ($st) {
                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'paused' => 'bg-amber-50 text-amber-800 border-amber-200',
                default => 'bg-slate-50 text-slate-700 border-slate-200',
            };
        };

        // values from controller (or fallback)
        $q = $q ?? request('q', '');
        $status = $status ?? request('status');
        $featured = $featured ?? request('featured');
        $sort = $sort ?? request('sort', 'featured');

        $qsBase = request()->except('page');
    @endphp

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-950">
                {{ __('ui.campaigns') }}
            </h1>
            <p class="mt-2 text-slate-600">{{ $subtitle }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ url($base . '/donate') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-white shadow-sm hover:shadow transition"
                style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                {{ __('ui.donate_now') }}
                <span aria-hidden="true">→</span>
            </a>

            <a href="{{ url($base . '/transparency') }}"
                class="inline-flex items-center justify-center px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold text-slate-800">
                {{ $isEn ? 'Transparency' : 'الشفافية' }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="get" class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-5">
                <label class="text-xs font-bold text-slate-600">{{ $isEn ? 'Search' : 'بحث' }}</label>
                <input name="q" value="{{ $q }}"
                    class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
                    placeholder="{{ $isEn ? 'Search by title or slug…' : 'ابحث بالعنوان أو الرابط…' }}">
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-bold text-slate-600">{{ $isEn ? 'Status' : 'الحالة' }}</label>
                <select name="status"
                    class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
                    <option value="">{{ $isEn ? 'All' : 'الكل' }}</option>
                    <option value="active" @selected($status === 'active')>{{ $isEn ? 'Active' : 'نشطة' }}</option>
                    <option value="paused" @selected($status === 'paused')>{{ $isEn ? 'Paused' : 'متوقفة' }}</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-bold text-slate-600">{{ $isEn ? 'Sort' : 'الترتيب' }}</label>
                <select name="sort"
                    class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
                    <option value="featured" @selected($sort === 'featured')>{{ $isEn ? 'Featured' : 'الأبرز' }}</option>
                    <option value="new" @selected($sort === 'new')>{{ $isEn ? 'Newest' : 'الأحدث' }}</option>
                    <option value="progress" @selected($sort === 'progress')>
                        {{ $isEn ? 'Highest progress' : 'الأعلى تقدماً' }}</option>
                    <option value="goal" @selected($sort === 'goal')>{{ $isEn ? 'Highest goal' : 'الأعلى هدفاً' }}
                    </option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-bold text-slate-600">{{ $isEn ? 'Featured only' : 'مميزة فقط' }}</label>
                <div class="mt-1 flex items-center gap-2">
                    <input id="featured" type="checkbox" name="featured" value="1" @checked($featured === '1')
                        class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200">
                    <label for="featured" class="text-sm text-slate-700">
                        {{ $isEn ? 'Show featured' : 'عرض المميزة' }}
                    </label>
                </div>
            </div>

            <div class="lg:col-span-1 flex gap-2">
                <button type="submit" class="w-full px-4 py-3 rounded-2xl font-extrabold text-white transition"
                    style="background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                    {{ $isEn ? 'Apply' : 'تطبيق' }}
                </button>
            </div>
        </div>

        {{-- active filters chips --}}
        <div class="mt-4 flex flex-wrap gap-2">
            @if ($q)
                <span class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700">
                    {{ $isEn ? 'Query:' : 'بحث:' }} <b>{{ $q }}</b>
                </span>
            @endif
            @if ($status)
                <span class="px-3 py-1 rounded-full text-xs bg-slate-50 border border-slate-200 text-slate-700">
                    {{ $isEn ? 'Status:' : 'الحالة:' }} <b>{{ $statusLabel($status) }}</b>
                </span>
            @endif
            @if ($featured === '1')
                <span
                    class="px-3 py-1 rounded-full text-xs bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold">
                    {{ $isEn ? 'Featured only' : 'مميزة فقط' }}
                </span>
            @endif

            @if ($q || $status || $featured === '1' || $sort !== 'featured')
                <a href="{{ url($base . '/campaigns') }}"
                    class="px-3 py-1 rounded-full text-xs bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-bold">
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
                @endphp

                <a href="{{ url($base . '/campaigns/' . $c->slug) }}"
                    class="group block rounded-3xl border border-slate-200 bg-white overflow-hidden hover:shadow-sm hover:border-slate-300 transition">

                    <div class="relative h-44 bg-slate-100">
                        @if ($img)
                            <img src="{{ $img }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-100 to-white"></div>
                        @endif

                        <div class="absolute top-3 {{ $isEn ? 'right-3' : 'left-3' }} flex items-center gap-2">
                            @if ($c->is_featured)
                                <span
                                    class="text-[11px] px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 font-bold">
                                    {{ $isEn ? 'Featured' : 'مميزة' }}
                                </span>
                            @endif
                            <span class="text-[11px] px-3 py-1 rounded-full border font-bold {{ $badge($c->status) }}">
                                {{ $statusLabel($c->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h2 class="text-lg font-extrabold text-slate-950 leading-snug line-clamp-2">
                            {{ $t($c) }}
                        </h2>

                        <p class="mt-2 text-sm text-slate-600 leading-relaxed line-clamp-2">
                            {{ $d($c) ?: ($isEn ? 'No description yet.' : 'لا يوجد وصف بعد.') }}
                        </p>

                        <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Raised' : 'تم جمع' }}</div>
                                <div class="mt-1 font-bold text-slate-950">
                                    {{ $money($c->current_amount) }} {{ $c->currency }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <div class="text-xs text-slate-500">{{ $isEn ? 'Goal' : 'الهدف' }}</div>
                                <div class="mt-1 font-bold text-slate-950">
                                    {{ $money($c->goal_amount) }} {{ $c->currency }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="flex justify-between text-xs text-slate-500 mb-2">
                                <span class="font-bold text-slate-700">{{ $c->progress_percent }}%</span>
                                <span>{{ $isEn ? 'Donors' : 'متبرعون' }}: {{ $c->donors_count ?? 0 }}</span>
                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $c->progress_percent }}%; background: linear-gradient(135deg, rgb(79,70,229), rgb(16,185,129));">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-sm text-slate-500">{{ $isEn ? 'Open details' : 'فتح التفاصيل' }}</span>
                            <span
                                class="inline-flex items-center gap-2 text-sm font-extrabold text-slate-950 group-hover:underline underline-offset-4">
                                {{ $isEn ? 'View' : 'عرض' }}
                                <span class="text-slate-400">↗</span>
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
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
            <div class="text-lg font-extrabold text-slate-950">
                {{ $isEn ? 'No campaigns found.' : 'لا توجد حملات مطابقة.' }}
            </div>
            <div class="mt-2 text-sm text-slate-600">
                {{ $isEn ? 'Try changing filters or check back later.' : 'جرّب تغيير الفلاتر أو عد لاحقًا.' }}
            </div>
            <div class="mt-5">
                <a href="{{ url($base . '/campaigns') }}"
                    class="inline-flex px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-bold">
                    {{ $isEn ? 'Reset' : 'إعادة ضبط' }}
                </a>
            </div>
        </div>
    @endif
@endsection
