@php($isAr = app()->isLocale('ar'))
<div class="w-full lg:w-72">
    <label class="block text-xs font-bold text-slate-600 mb-1">{{ $isAr ? 'حملة' : 'Campaign' }}</label>
    <select name="campaign_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <option value="">{{ $isAr ? 'كل الحملات' : 'All campaigns' }}</option>
        @foreach ($campaigns as $c)
            @php($t = $isAr ? $c->title_ar : $c->title_en ?? $c->title_ar)
            <option value="{{ $c->id }}" @selected(request('campaign_id') == $c->id)>{{ $t }}</option>
        @endforeach
    </select>
</div>
