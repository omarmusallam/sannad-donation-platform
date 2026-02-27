@php($isAr = app()->isLocale('ar'))

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-xs text-slate-500">{{ $isAr ? 'إجمالي المبالغ' : 'Total Amount' }}</div>
        <div class="mt-1 text-2xl font-extrabold text-slate-900">
            {{ number_format((float) ($kpis['total_amount'] ?? 0), 2) }}
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-xs text-slate-500">{{ $isAr ? 'عدد التبرعات' : 'Donations Count' }}</div>
        <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ (int) ($kpis['donations_count'] ?? 0) }}</div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-xs text-slate-500">{{ $isAr ? 'متوسط التبرع' : 'Average Donation' }}</div>
        <div class="mt-1 text-2xl font-extrabold text-slate-900">
            {{ number_format((float) ($kpis['avg_donation'] ?? 0), 2) }}
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-xs text-slate-500">{{ $isAr ? 'عدد المتبرعين' : 'Unique Donors' }}</div>
        <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ (int) ($kpis['unique_donors'] ?? 0) }}</div>
    </div>
</div>
