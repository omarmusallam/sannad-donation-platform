<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:donations.view')->only(['index', 'show']);
        $this->middleware('permission:receipts.create')->only(['generateReceipt']);
    }

    public function index(Request $request)
    {
        $q = Donation::query()->with('campaign');

        $validated = $request->validate([
            'status' => ['nullable', 'in:paid,pending,failed'],
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
                    ->orWhere('donor_email', 'like', "%{$s}%");

                // لو البحث رقم فقط (id)
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
        // اختيارياً: تأمين بسيط (منطقي)
        // إذا بدك تمنع إنشاء إيصال لتبرع غير مدفوع:
        // abort_unless($donation->status === 'paid', 422, 'لا يمكن إنشاء إيصال لتبرع غير مدفوع');

        $service->generate($donation);

        return back()->with('success', 'تم إنشاء الإيصال بنجاح');
    }
}
