<?php

namespace App\Http\Controllers;

use App\Models\Receipt;

class ReceiptController extends Controller
{
    public function verify(string $uuid)
    {
        $receipt = Receipt::query()
            ->where('uuid', $uuid)
            ->with('donation.campaign')
            ->first();

        if (!$receipt) {
            return response()
                ->view('public.receipts.verify_not_found', [], 404);
        }

        return view('public.receipts.verify', compact('receipt'));
    }

    // ✅ تحميل PDF عبر uuid (عام)
    public function download(string $uuid)
    {
        $receipt = Receipt::where('uuid', $uuid)->firstOrFail();

        abort_unless($receipt->pdf_path, 404);

        $fullPath = storage_path('app/public/' . $receipt->pdf_path);
        abort_unless(file_exists($fullPath), 404);

        return response()->download($fullPath, $receipt->receipt_no . '.pdf');
    }
}
