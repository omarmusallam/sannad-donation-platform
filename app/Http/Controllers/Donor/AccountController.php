<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $donor = auth('donor')->user();

        $baseQuery = Donation::query()->where('donor_id', $donor->id);

        $donations = (clone $baseQuery)
            ->with(['campaign', 'receipt'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_donations' => (clone $baseQuery)->count(),

            'paid_donations' => (clone $baseQuery)
                ->where('status', 'paid')
                ->count(),

            'total_amount' => (clone $baseQuery)
                ->where('status', 'paid')
                ->sum('amount'),

            'campaigns_supported' => (clone $baseQuery)
                ->whereNotNull('campaign_id')
                ->distinct('campaign_id')
                ->count('campaign_id'),

            'last_donation_at' => (clone $baseQuery)->max('paid_at')
                ?: (clone $baseQuery)->max('created_at'),
        ];

        $statusSummary = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $latestPaid = (clone $baseQuery)
            ->with(['campaign', 'receipt'])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->first();

        return view('donor.dashboard', compact(
            'donor',
            'donations',
            'stats',
            'statusSummary',
            'latestPaid'
        ));
    }

    public function donations(Request $request)
    {
        $donor = auth('donor')->user();

        $search = trim((string) $request->get('q', ''));
        $activeStatus = trim((string) $request->get('status', ''));

        $baseQuery = Donation::query()->where('donor_id', $donor->id);

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'paid' => (clone $baseQuery)->where('status', 'paid')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'failed' => (clone $baseQuery)->where('status', 'failed')->count(),
            'refunded' => (clone $baseQuery)->where('status', 'refunded')->count(),
        ];

        $donationsQuery = Donation::query()
            ->with(['campaign', 'receipt'])
            ->where('donor_id', $donor->id);

        if ($activeStatus !== '') {
            $donationsQuery->where('status', $activeStatus);
        }

        if ($search !== '') {
            $donationsQuery->where(function ($query) use ($search) {
                $query->whereHas('campaign', function ($campaignQuery) use ($search) {
                    $campaignQuery
                        ->where('title_ar', 'like', '%' . $search . '%')
                        ->orWhere('title_en', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            });
        }

        $donations = $donationsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('donor.donations', compact(
            'donor',
            'donations',
            'summary',
            'search',
            'activeStatus'
        ));
    }

    public function profile()
    {
        $donor = auth('donor')->user();

        return view('donor.profile', compact('donor'));
    }

    public function updateProfile(Request $request)
    {
        $donor = auth('donor')->user();

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('donors', 'email')->ignore($donor->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $donor->update($data);

        return back()->with('success', app()->isLocale('en')
            ? 'Profile updated successfully.'
            : 'تم تحديث بيانات الحساب بنجاح.');
    }

    public function security()
    {
        $donor = auth('donor')->user();

        return view('donor.security', compact('donor'));
    }

    public function updatePassword(Request $request)
    {
        $donor = auth('donor')->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($data['current_password'], $donor->password)) {
            return back()->withErrors([
                'current_password' => app()->isLocale('en')
                    ? 'Current password is incorrect.'
                    : 'كلمة المرور الحالية غير صحيحة.',
            ]);
        }

        $donor->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', app()->isLocale('en')
            ? 'Password updated successfully.'
            : 'تم تحديث كلمة المرور بنجاح.');
    }
}
