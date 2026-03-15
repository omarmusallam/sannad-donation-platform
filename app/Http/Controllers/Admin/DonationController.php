<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:donations.view')->only(['index', 'show']);
        $this->middleware('permission:receipts.create')->only([
            'generateReceipt',
            'confirmCrypto',
            'rejectCrypto',
        ]);
    }

    public function index(Request $request)
    {
        $q = Donation::query()->with('campaign');

        $validated = $request->validate([
            'status' => ['nullable', 'in:paid,pending,pending_crypto_review,failed,refunded'],
            'campaign_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        if (!empty($validated['campaign_id'])) {
            $q->where('campaign_id', $validated['campaign_id']);
        }

        if (!empty($validated['search'])) {
            $s = trim($validated['search']);

            $q->where(function ($qq) use ($s) {
                $qq->where('donor_name', 'like', "%{$s}%")
                    ->orWhere('donor_email', 'like', "%{$s}%")
                    ->orWhere('crypto_tx_hash', 'like', "%{$s}%");

                if (ctype_digit($s)) {
                    $qq->orWhere('id', (int) $s);
                }
            });
        }

        $donations = $q->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.donations.index', compact('donations'));
    }

    public function show(Donation $donation)
    {
        $donation->load(['campaign', 'receipt']);

        return view('admin.donations.show', compact('donation'));
    }

    public function generateReceipt(Donation $donation, ReceiptService $service)
    {
        abort_unless($donation->status === 'paid', 422, 'لا يمكن إنشاء إيصال لتبرع غير مدفوع.');

        $service->generate($donation);

        return back()->with('success', 'تم إنشاء الإيصال بنجاح');
    }

    public function confirmCrypto(Request $request, Donation $donation, ReceiptService $service)
    {
        abort_unless($donation->payment_method === 'usdt_trc20', 422, 'هذه العملية ليست دفعة USDT.');

        $data = $request->validate([
            'admin_payment_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $donationId = $donation->id;
        $alreadyPaid = false;

        DB::transaction(function () use ($donationId, $data, &$alreadyPaid) {
            $donation = Donation::with(['campaign', 'receipt'])
                ->lockForUpdate()
                ->findOrFail($donationId);

            if ($donation->status === 'paid') {
                $alreadyPaid = true;
                return;
            }

            abort_unless(
                in_array($donation->status, ['pending', 'pending_crypto_review'], true),
                422,
                'لا يمكن اعتماد هذه العملية في حالتها الحالية.'
            );

            $donation->update([
                'status' => 'paid',
                'provider' => 'wallet',
                'provider_ref' => $donation->provider_ref ?: $donation->crypto_tx_hash,
                'fees' => 0,
                'net_amount' => $donation->amount,
                'paid_at' => now(),
                'admin_payment_note' => trim((string) ($data['admin_payment_note'] ?? '')) ?: null,
            ]);

            if ($donation->campaign) {
                $donation->campaign()->increment('current_amount', $donation->amount);
            }
        });

        if ($alreadyPaid) {
            return back()->with('success', 'تم اعتماد هذه الدفعة مسبقًا.');
        }

        $service->generate(Donation::findOrFail($donationId));

        return back()->with('success', 'تم اعتماد دفعة USDT وإنشاء الإيصال بنجاح.');
    }

    public function rejectCrypto(Request $request, Donation $donation)
    {
        abort_unless($donation->payment_method === 'usdt_trc20', 422, 'هذه العملية ليست دفعة USDT.');

        $data = $request->validate([
            'admin_payment_note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($donation, $data) {
            $donation = Donation::lockForUpdate()->findOrFail($donation->id);

            abort_unless(
                in_array($donation->status, ['pending', 'pending_crypto_review'], true),
                422,
                'لا يمكن رفض هذه العملية في حالتها الحالية.'
            );

            $donation->update([
                'status' => 'failed',
                'admin_payment_note' => trim((string) ($data['admin_payment_note'] ?? '')) ?: null,
            ]);
        });

        return back()->with('success', 'تم رفض دفعة USDT بنجاح.');
    }
}
