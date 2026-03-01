<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function verify(Receipt $receipt)
    {
        // Load relations safely
        $receipt->loadMissing('donation.campaign');

        // If you want to hide cancelled receipts completely, you can 404:
        // abort_if($receipt->status !== 'issued', 404);

        return view('public.receipts.verify', compact('receipt'));
    }

    public function download(Request $request, Receipt $receipt)
    {
        abort_unless($receipt->pdf_path, 404);

        $fullPath = storage_path('app/public/' . $receipt->pdf_path);
        abort_unless(file_exists($fullPath), 404);

        // Optional: also require issued status
        // abort_unless($receipt->status === 'issued', 403);

        return response()->download($fullPath, $receipt->receipt_no . '.pdf');
    }
}
