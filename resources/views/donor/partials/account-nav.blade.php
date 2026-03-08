@php
    $isEn = app()->isLocale('en');

    $navItems = [
        [
            'label' => $isEn ? 'Dashboard' : 'لوحة الحساب',
            'url' => locale_route('donor.dashboard'),
            'active' => request()->routeIs('donor.dashboard', 'en.donor.dashboard'),
        ],
        [
            'label' => $isEn ? 'My donations' : 'تبرعاتي',
            'url' => locale_route('donor.donations'),
            'active' => request()->routeIs('donor.donations', 'en.donor.donations'),
        ],
        [
            'label' => $isEn ? 'Profile settings' : 'إعدادات الحساب',
            'url' => locale_route('donor.profile'),
            'active' => request()->routeIs('donor.profile', 'en.donor.profile'),
        ],
        [
            'label' => $isEn ? 'Security' : 'الأمان',
            'url' => locale_route('donor.security'),
            'active' => request()->routeIs('donor.security', 'en.donor.security'),
        ],
    ];
@endphp

<div class="card p-4 sm:p-5">
    <div class="text-sm font-black text-text">
        {{ $isEn ? 'Account navigation' : 'التنقل داخل الحساب' }}
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
        @foreach ($navItems as $item)
            <a href="{{ $item['url'] }}"
                class="{{ $item['active'] ? 'btn btn-primary justify-center' : 'btn btn-secondary justify-center' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
