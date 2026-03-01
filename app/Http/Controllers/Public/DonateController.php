<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Receipt;
use App\Services\ReceiptPdfService;
use Illuminate\Support\Str;

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

    public function submit(Request $request, ReceiptPdfService $pdfService)
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

        $campaign = Campaign::findOrFail($data['campaign_id']);
        $data['currency'] = $campaign->currency;

        if ($data['is_anonymous']) {
            $data['donor_name'] = null;
            $data['donor_email'] = null;
        }

        // 1️⃣ Create donation
        $donation = Donation::create([
            ...$data,
            'payment_method' => 'mock',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // 2️⃣ Create receipt record
        $receipt = Receipt::create([
            'uuid' => (string) Str::uuid(),
            'receipt_no' => 'RC-' . now()->format('Ymd') . '-' . str_pad($donation->id, 6, '0', STR_PAD_LEFT),
            'donation_id' => $donation->id,
            'donor_name' => $donation->donor_name,
            'donor_email' => $donation->donor_email,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        // 3️⃣ Generate PDF
        $pdfPath = $pdfService->buildAndStore($receipt);
        $receipt->update(['pdf_path' => $pdfPath]);

        $base = app()->getLocale() === 'en' ? '/en' : '';

        return redirect()->to(url($base . '/donate/success?d=' . $donation->id));
    }

    // public function success(Request $request)
    // {
    //     $donation = Donation::with('campaign')->findOrFail($request->get('d'));
    //     return view('public.donate_success', compact('donation'));
    // }

    public function success(Request $request)
    {
        $donation = Donation::with('campaign')->findOrFail($request->get('d'));

        // Prefer relation if exists, fallback query
        $receipt = $donation->receipt
            ?? \App\Models\Receipt::where('donation_id', $donation->id)->latest()->first();

        $receiptUrl = $receipt ? route('receipt.verify', $receipt) : null;

        $downloadUrl = $receipt
            ? \URL::temporarySignedRoute(
                'receipt.download.public',
                now()->addMinutes(30),
                ['receipt' => $receipt] // ✅ matches {receipt:uuid}
            )
            : null;

        return view('public.donate_success', compact('donation', 'receiptUrl', 'downloadUrl'));
    }
}
