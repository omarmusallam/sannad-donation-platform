@php
    $isAr = app()->getLocale() === 'ar';
    $isEdit = isset($user) && $user;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: User info --}}
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 space-y-5 shadow-sm">

        <div>
            <div class="text-sm text-slate-500">
                {{ $isEdit ? ($isAr ? 'تعديل بيانات المستخدم' : 'Edit user details') : ($isAr ? 'إنشاء مستخدم جديد' : 'Create a new user') }}
            </div>
            <div class="text-lg font-extrabold mt-1 text-slate-900">
                {{ $isAr ? 'البيانات الأساسية' : 'Basic info' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">
                    {{ $isAr ? 'الاسم' : 'Name' }}
                </label>
                <input name="name" value="{{ old('name', $user->name ?? '') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                @error('name')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">
                    {{ $isAr ? 'البريد الإلكتروني' : 'Email' }}
                </label>
                <input name="email" type="email" value="{{ old('email', $user->email ?? '') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                           focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition">
                @error('email')
                    <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">
                {{ $isEdit ? ($isAr ? 'كلمة المرور (اختياري)' : 'Password (optional)') : ($isAr ? 'كلمة المرور' : 'Password') }}
            </label>

            <input name="password" type="password"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm
                       focus:ring-2 focus:ring-black/10 focus:border-black/30 focus:outline-none transition"
                placeholder="{{ $isEdit ? ($isAr ? 'اتركها فارغة إذا لا تريد تغييرها' : 'Leave empty to keep current password') : '' }}">

            @error('password')
                <div class="text-rose-600 text-xs mt-2">{{ $message }}</div>
            @enderror

            <div class="text-xs text-slate-500 mt-2">
                {{ $isAr ? 'يفضل 8 أحرف على الأقل.' : 'Prefer at least 8 characters.' }}
            </div>
        </div>

    </div>

    {{-- Right: Roles --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-4 shadow-sm">

        <div>
            <div class="text-sm text-slate-500">{{ $isAr ? 'صلاحيات الوصول' : 'Access control' }}</div>
            <div class="text-lg font-extrabold mt-1 text-slate-900">{{ $isAr ? 'الأدوار' : 'Roles' }}</div>
        </div>

        @php
            $selectedRoles = old('roles', isset($user) ? $user->roles->pluck('name')->toArray() : []);
        @endphp

        <div class="space-y-2">
            @forelse($roles as $role)
                <label
                    class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            class="rounded border-slate-300 text-black focus:ring-black/10"
                            @checked(in_array($role->name, $selectedRoles, true))>
                        <span class="font-semibold text-sm text-slate-900">{{ $role->name }}</span>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $isAr ? 'دور' : 'Role' }}
                    </span>
                </label>
            @empty
                <div class="text-sm text-slate-500">
                    {{ $isAr ? 'لا يوجد أدوار بعد. شغّل Seeder أو أنشئ دور.' : 'No roles found. Run the seeder or create roles.' }}
                </div>
            @endforelse
        </div>

        <div class="text-xs text-slate-500 leading-relaxed">
            {{ $isAr
                ? 'تنبيه: المستخدم بدون أدوار قد لا يستطيع دخول لوحة التحكم حسب إعدادات routes.'
                : 'Note: a user without roles may not access the admin panel depending on your route middleware.' }}
        </div>

    </div>
</div>
