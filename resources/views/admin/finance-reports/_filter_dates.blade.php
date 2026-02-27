@php($isAr = app()->isLocale('ar'))

<form method="GET" class="rounded-[28px] border border-slate-200 bg-white p-4 md:p-5 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-end gap-3">
        <div class="w-full lg:w-56">
            <label class="block text-xs font-bold text-slate-600 mb-1">{{ $isAr ? 'من' : 'From' }}</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm
                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
        </div>

        <div class="w-full lg:w-56">
            <label class="block text-xs font-bold text-slate-600 mb-1">{{ $isAr ? 'إلى' : 'To' }}</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm
                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
        </div>

        {{-- Extra filters slot --}}
        @isset($slot)
            {!! $slot !!}
        @endisset

        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2.5 rounded-2xl font-extrabold text-white bg-black hover:opacity-95 transition">
                {{ $isAr ? 'تطبيق' : 'Apply' }}
            </button>

            <a href="{{ url()->current() }}"
                class="px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition font-extrabold text-slate-700">
                {{ $isAr ? 'مسح' : 'Reset' }}
            </a>
        </div>
    </div>
</form>
