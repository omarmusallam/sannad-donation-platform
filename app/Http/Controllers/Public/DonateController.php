<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Receipt;
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
            'payment_method' => ['required', Rule::in(['card', 'usdt_trc20'])],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $data['is_anonymous'] = $request->boolean('is_anonymous');

        $campaign = Campaign::query()
            ->whereKey($data['campaign_id'])
            ->whereIn('status', ['active', 'paused'])
            ->firstOrFail();

        $donor = auth('donor')->user();

        if ($data['is_anonymous']) {
            $data['donor_name'] = null;
            $data['donor_email'] = null;
        } else {
            $data['donor_name'] = $data['donor_name'] ?: $donor?->name;
            $data['donor_email'] = $data['donor_email'] ?: $donor?->email;
        }

        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'donor_id' => $donor?->id,
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'],
            'is_anonymous' => $data['is_anonymous'],

            'amount' => $data['amount'],
            'fees' => 0,
            'net_amount' => null,
            'currency' => $data['currency'],

            'payment_method' => $data['payment_method'],
            'status' => 'pending',
            'provider' => $data['payment_method'] === 'card' ? 'stripe' : 'wallet',
            'provider_ref' => null,

            'paid_at' => null,
            'refunded_at' => null,
        ]);

        if ($data['payment_method'] === 'usdt_trc20') {
            return redirect()->to(locale_route('donate.crypto', ['donation' => $donation->id]));
        }

        return app(\App\Services\Payments\StripeCheckoutService::class)
            ->redirectToCheckout($donation);
    }

    public function success(Donation $donation)
    {
        $donation->loadMissing(['campaign', 'receipt']);

        $receipt = $donation->receipt
            ?? Receipt::where('donation_id', $donation->id)->latest()->first();

        $receiptUrl = $receipt ? route('receipt.verify', $receipt) : null;

        $downloadUrl = $receipt
            ? \URL::temporarySignedRoute(
                'receipt.download.public',
                now()->addMinutes(30),
                ['receipt' => $receipt]
            )
            : null;

        return view('public.donate_success', compact('donation', 'receiptUrl', 'downloadUrl'));
    }

    public function cancel(Donation $donation)
    {
        return redirect()
            ->to(locale_route('donate', ['campaign' => $donation->campaign->slug ?? null]))
            ->with('error', app()->isLocale('en')
                ? 'Payment was canceled.'
                : 'تم إلغاء عملية الدفع.');
    }

    public function crypto(Donation $donation)
    {
        abort_unless($donation->payment_method === 'usdt_trc20', 404);

        return view('public.donate_crypto', compact('donation'));
    }
}
