<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonateController extends Controller
{
    public function show(Request $request)
    {
        $campaign = null;

        if ($request->filled('campaign')) {
            $campaign = Campaign::query()
                ->where('slug', $request->string('campaign'))
                ->whereIn('status', ['active', 'paused'])
                ->first();
        }

        $campaigns = Campaign::query()
            ->whereIn('status', ['active', 'paused'])
            ->orderByDesc('is_featured')
            ->orderByDesc('priority')
            ->get(['id', 'title_ar', 'title_en', 'slug', 'currency']);

        $amount = (float) $request->get('amount', 25);
        $amount = max(1, min(100000, $amount));

        return view('public.donate', compact('campaign', 'campaigns', 'amount'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
            'currency' => ['required', 'string', 'size:3', Rule::in(['USD', 'EUR', 'ILS'])],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $data['is_anonymous'] = $request->boolean('is_anonymous');

        // ✅ حماية: تأكد أن العملة مطابقة للحملة (حتى لا يرسل Currency غلط)
        $campaign = Campaign::query()->findOrFail($data['campaign_id']);
        $data['currency'] = $campaign->currency ?: $data['currency'];

        // ✅ لو مجهول: لا نخزن اسم/ايميل (اختياري لكن أنظف)
        if ($data['is_anonymous']) {
            $data['donor_name'] = null;
            $data['donor_email'] = null;
        }

        // Mock paid
        $donation = Donation::create([
            ...$data,
            'payment_method' => 'mock',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $base = app()->getLocale() === 'en' ? '/en' : '';
        return redirect()->to(url($base . '/donate/success?d=' . $donation->id));
    }

    public function success(Request $request)
    {
        $donation = Donation::with('campaign')->findOrFail($request->get('d'));
        return view('public.donate_success', compact('donation'));
    }
}
