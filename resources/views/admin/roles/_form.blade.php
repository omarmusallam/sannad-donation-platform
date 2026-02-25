@php
    $isAr = app()->getLocale() === 'ar';
    $isEdit = isset($role);
    $selected = $selected ?? old('permissions', []);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Role Info --}}
    <div class="lg:col-span-1 bg-white border border-gray-100 rounded-3xl p-6 space-y-4">
        <div>
            <div class="text-sm text-gray-500">{{ $isAr ? 'بيانات الدور' : 'Role info' }}</div>
            <div class="text-lg font-bold mt-1">{{ $isAr ? 'الدور' : 'Role' }}</div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">{{ $isAr ? 'اسم الدور' : 'Role name' }}</label>
            <input name="name" value="{{ old('name', $role->name ?? '') }}"
                class="w-full border border-gray-200 rounded-2xl p-3 focus:ring-2 focus:ring-black focus:outline-none transition"
                placeholder="admin / viewer ...">
            @error('name')
                <div class="text-red-600 text-xs mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-xs text-gray-500 leading-relaxed">
            {{ $isAr ? 'استخدم أسماء بسيطة مثل: admin, viewer, manager.' : 'Use simple names like: admin, viewer, manager.' }}
        </div>
    </div>

    {{-- Permissions --}}
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-3xl p-6 space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-sm text-gray-500">
                    {{ $isAr ? 'حدد صلاحيات هذا الدور' : 'Select permissions for this role' }}</div>
                <div class="text-lg font-bold mt-1">{{ $isAr ? 'الصلاحيات' : 'Permissions' }}</div>
            </div>

            <div class="flex gap-2">
                <button type="button" class="px-3 py-2 rounded-2xl border hover:bg-gray-50 text-sm font-semibold"
                    onclick="document.querySelectorAll('input[name=&quot;permissions[]&quot;]').forEach(i=>i.checked=true)">
                    {{ $isAr ? 'تحديد الكل' : 'Select all' }}
                </button>
                <button type="button" class="px-3 py-2 rounded-2xl border hover:bg-gray-50 text-sm font-semibold"
                    onclick="document.querySelectorAll('input[name=&quot;permissions[]&quot;]').forEach(i=>i.checked=false)">
                    {{ $isAr ? 'إلغاء الكل' : 'Clear' }}
                </button>
            </div>
        </div>

        <div class="space-y-5">
            @foreach ($groups as $group => $perms)
                <div class="border border-gray-100 rounded-3xl p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="font-semibold text-gray-900">
                            {{ strtoupper($group) }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $perms->count() }}
                            {{ $isAr ? 'صلاحية' : 'permissions' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach ($perms as $p)
                            <label
                                class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="permissions[]" value="{{ $p->name }}"
                                        class="rounded border-gray-300" @checked(in_array($p->name, $selected, true))>
                                    <span class="text-sm font-semibold text-gray-900">{{ $p->name }}</span>
                                </div>

                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ $isAr ? 'إذن' : 'Perm' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @error('permissions.*')
            <div class="text-red-600 text-xs">{{ $message }}</div>
        @enderror
    </div>

</div>
