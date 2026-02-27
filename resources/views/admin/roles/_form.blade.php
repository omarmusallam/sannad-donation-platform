@php
    $isAr = app()->getLocale() === 'ar';
    $isEdit = isset($role);
    $selected = $selected ?? old('permissions', []);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Role Info --}}
    <div class="lg:col-span-1 bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">
        <div>
            <div class="text-sm text-slate-500">{{ $isAr ? 'بيانات الدور' : 'Role info' }}</div>
            <div class="text-lg font-extrabold mt-1 text-slate-900">{{ $isAr ? 'الدور' : 'Role' }}</div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">
                {{ $isAr ? 'اسم الدور' : 'Role name' }}
            </label>
            <input name="name" value="{{ old('name', $role->name ?? '') }}"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                placeholder="admin / viewer / manager ...">
            @error('name')
                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-xs text-slate-500 leading-relaxed">
            {{ $isAr ? 'استخدم أسماء بسيطة مثل: admin, viewer, manager.' : 'Use simple names like: admin, viewer, manager.' }}
        </div>

        @if ($isEdit && ($role->name ?? null) === 'super_admin')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                <div class="font-bold text-sm">{{ $isAr ? 'تنبيه' : 'Notice' }}</div>
                <div class="text-xs mt-1">
                    {{ $isAr ? 'دور super_admin محمي عادةً من التعديل.' : 'The super_admin role is usually protected from editing.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- Permissions --}}
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 space-y-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm text-slate-500">
                    {{ $isAr ? 'حدد صلاحيات هذا الدور' : 'Select permissions for this role' }}
                </div>
                <div class="text-lg font-extrabold mt-1 text-slate-900">{{ $isAr ? 'الصلاحيات' : 'Permissions' }}</div>
            </div>

            <div class="flex gap-2">
                <button type="button"
                    class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold text-slate-700 transition"
                    onclick="document.querySelectorAll('input[name=&quot;permissions[]&quot;]').forEach(i=>i.checked=true)">
                    {{ $isAr ? 'تحديد الكل' : 'Select all' }}
                </button>

                <button type="button"
                    class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold text-slate-700 transition"
                    onclick="document.querySelectorAll('input[name=&quot;permissions[]&quot;]').forEach(i=>i.checked=false)">
                    {{ $isAr ? 'إلغاء الكل' : 'Clear' }}
                </button>
            </div>
        </div>

        <div class="space-y-5">
            @foreach ($groups as $group => $perms)
                <div class="border border-slate-200 rounded-3xl p-4 bg-slate-50/40">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="font-semibold text-slate-900">
                            {{ strtoupper($group) }}
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ $perms->count() }} {{ $isAr ? 'صلاحية' : 'permissions' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach ($perms as $p)
                            <label
                                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="permissions[]" value="{{ $p->name }}"
                                        class="rounded border-slate-300 text-black focus:ring-black/10"
                                        @checked(in_array($p->name, $selected, true))>
                                    <span class="text-sm font-semibold text-slate-900">{{ $p->name }}</span>
                                </div>

                                <span
                                    class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $isAr ? 'إذن' : 'Perm' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @error('permissions.*')
            <div class="text-rose-600 text-xs">{{ $message }}</div>
        @enderror
    </div>

</div>
