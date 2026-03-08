<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function verify(Receipt $receipt)
    {
        // علاقات آمنة + مفيدة لصفحة التحقق
        $receipt->loadMissing([
            'donation.campaign',
            'donation.donor', // ✅ مفيد لاحقاً (بدون ما نعرضه إن كان anonymous)
        ]);

        // اختياري: اخفاء غير الصالح بالكامل
        // abort_if($receipt->status !== 'issued', 404);

        return view('public.receipts.verify', compact('receipt'));
    }

    public function download(Request $request, Receipt $receipt)
    {
        // (Route already has signed middleware) ✅
        // حماية إضافية:
        abort_unless($receipt->status === 'issued', 403);
        abort_unless(!empty($receipt->pdf_path), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($receipt->pdf_path), 404);

        $filename = $receipt->receipt_no
            ? "{$receipt->receipt_no}.pdf"
            : "receipt-{$receipt->id}.pdf";

        return $disk->download($receipt->pdf_path, $filename);
    }
}
