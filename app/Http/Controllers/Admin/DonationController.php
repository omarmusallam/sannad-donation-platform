<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class DonationController extends Controller
{

    public function generateReceipt(Donation $donation, ReceiptService $service)
    {
        $receipt = $service->generate($donation);

        return back()->with('success', 'تم إنشاء الإيصال بنجاح');
    }

    public function index(Request $request)
    {
        $q = Donation::query()->with('campaign');

        if ($request->filled('status')) {
            $q->where('status', $request->get('status'));
        }

        if ($request->filled('campaign_id')) {
            $q->where('campaign_id', $request->get('campaign_id'));
        }

        if ($request->filled('search')) {
            $s = $request->get('search');
            $q->where(function ($qq) use ($s) {
                $qq->where('donor_name', 'like', "%{$s}%")
                    ->orWhere('donor_email', 'like', "%{$s}%")
                    ->orWhere('id', $s);
            });
        }

        // $donations = $q->latest()->paginate(20)->withQueryString();
        $donations = $q->latest()
            ->paginate(20)
            ->appends($request->query());
        // $donations = $q->latest()->paginate(20)->appends($request->query());

        return view('admin.donations.index', compact('donations'));
    }

    public function show(Donation $donation)
    {
        $donation->load(['campaign', 'receipt']);
        return view('admin.donations.show', compact('donation'));
    }
}
