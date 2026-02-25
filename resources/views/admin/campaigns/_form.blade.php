@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-5 md:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <div class="text-base font-semibold text-slate-900">بيانات الحملة</div>
                    <div class="text-xs text-slate-500 mt-1">العناوين والوصف والمحتوى الأساسي.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Title AR --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (عربي)</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $campaign->title_ar ?? '') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    @error('title_ar')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Title EN --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">العنوان (English)</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $campaign->title_en ?? '') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    @error('title_en')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Description AR --}}
            <div class="mt-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">الوصف (عربي)</label>
                <textarea name="description_ar" rows="5"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">{{ old('description_ar', $campaign->description_ar ?? '') }}</textarea>
                @error('description_ar')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description EN --}}
            <div class="mt-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">الوصف (English)</label>
                <textarea name="description_en" rows="5"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">{{ old('description_en', $campaign->description_en ?? '') }}</textarea>
                @error('description_en')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Cover --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-5 md:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <div class="text-base font-semibold text-slate-900">صورة الغلاف</div>
                    <div class="text-xs text-slate-500 mt-1">يفضل صورة واضحة بنسبة 16:9.</div>
                </div>
            </div>

            <input type="file" name="cover_image"
                class="block w-full text-sm text-slate-600
                       file:mr-3 file:rounded-2xl file:border-0 file:bg-black file:px-4 file:py-2.5 file:text-white file:text-sm file:font-semibold
                       hover:file:opacity-95 transition">

            @if (isset($campaign) && $campaign->cover_url)
                <div class="mt-5 flex items-center gap-4">
                    <img src="{{ $campaign->cover_url }}"
                        class="w-48 h-28 object-cover rounded-2xl border border-slate-200" alt="">
                    <div class="text-xs text-slate-500">
                        الصورة الحالية موجودة — رفع صورة جديدة سيستبدلها.
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-5 md:p-6 shadow-sm">
            <div class="text-base font-semibold text-slate-900">الإعدادات المالية</div>
            <div class="text-xs text-slate-500 mt-1 mb-5">الهدف والعملة والحالة.</div>

            <div class="space-y-4">
                {{-- Goal --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">الهدف المالي</label>
                    <input type="number" step="0.01" name="goal_amount"
                        value="{{ old('goal_amount', $campaign->goal_amount ?? 0) }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    @error('goal_amount')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Currency --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">العملة</label>
                    <select name="currency"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                        @foreach (['USD', 'EUR', 'ILS'] as $cur)
                            <option value="{{ $cur }}" @selected(old('currency', $campaign->currency ?? 'USD') === $cur)>{{ $cur }}
                            </option>
                        @endforeach
                    </select>
                    @error('currency')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">الحالة</label>
                    <select name="status"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                        @foreach (['draft', 'active', 'paused', 'ended', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $campaign->status ?? 'draft') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-5 md:p-6 shadow-sm">
            <div class="text-base font-semibold text-slate-900">خصائص إضافية</div>
            <div class="text-xs text-slate-500 mt-1 mb-5">تمييز الحملة وترتيبها.</div>

            <div class="space-y-4">
                {{-- Featured --}}
                <label
                    class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                    <div>
                        <div class="text-sm font-semibold text-slate-800">حملة مميزة</div>
                        <div class="text-xs text-slate-500 mt-0.5">تظهر ضمن الحملات المميزة.</div>
                    </div>
                    <input type="checkbox" name="is_featured" value="1"
                        class="w-5 h-5 rounded border-slate-300 text-black focus:ring-black/10"
                        @checked(old('is_featured', $campaign->is_featured ?? false))>
                </label>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">الأولوية</label>
                    <input type="number" name="priority" value="{{ old('priority', $campaign->priority ?? 0) }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                               focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                    @error('priority')
                        <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Footer actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="inline-flex justify-center items-center gap-2 w-full px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:opacity-95 transition shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h11l5 5v9a2 2 0 01-2 2z" stroke="currentColor"
                        stroke-width="2" />
                    <path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2" />
                    <path d="M7 5v5h8" stroke="currentColor" stroke-width="2" />
                </svg>
                حفظ
            </button>
        </div>
    </div>
</div>
