@php($isAr = app()->isLocale('ar'))

<form method="GET" class="rounded-2xl border border-slate-200 bg-white p-4">
    <div class="flex flex-col lg:flex-row lg:items-end gap-3">
        <div class="w-full lg:w-56">
            <label class="block text-xs font-bold text-slate-600 mb-1">{{ $isAr ? 'من' : 'From' }}</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
        </div>

        <div class="w-full lg:w-56">
            <label class="block text-xs font-bold text-slate-600 mb-1">{{ $isAr ? 'إلى' : 'To' }}</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
        </div>

        {{-- Extra filters slot --}}
        {!! $slot ?? '' !!}

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 rounded-xl font-extrabold text-white"
                style="background: linear-gradient(135deg, rgb(79 70 229), rgb(16 185 129));">
                {{ $isAr ? 'تطبيق' : 'Apply' }}
            </button>

            <a href="{{ url()->current() }}"
                class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-slate-700">
                {{ $isAr ? 'مسح' : 'Reset' }}
            </a>
        </div>
    </div>
</form>
