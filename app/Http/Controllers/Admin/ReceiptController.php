<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Services\ReceiptPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function __construct()
    {
        // ✅ حدد guard admin لمنع أي تعارض لاحقاً مع donor permissions
        $this->middleware('permission:receipts.view,admin')->only(['index', 'download']);
        $this->middleware('permission:receipts.create,admin')->only(['regenerate']);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:30'],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $query = Receipt::query()
            ->with([
                'donation:id,campaign_id,donor_id,donor_name,donor_email,is_anonymous,amount,currency,status,paid_at',
                'donation.campaign:id,title_ar,title_en,slug',
                // 'donation.donor:id,name,email', // اختياري للعرض الإداري
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('donor_email', 'like', "%{$search}%")
                    ->orWhere('donor_name', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['currency'])) {
            $query->where('currency', $validated['currency']);
        }

        $receipts = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.receipts.index', compact('receipts'));
    }

    public function download(Receipt $receipt)
    {
        $disk = Storage::disk('public');

        abort_unless(!empty($receipt->pdf_path), 404);
        abort_unless($disk->exists($receipt->pdf_path), 404);

        $filename = $receipt->receipt_no
            ? "receipt-{$receipt->receipt_no}.pdf"
            : "receipt-{$receipt->id}.pdf";

        return $disk->download($receipt->pdf_path, $filename);
    }

    public function regenerate(Receipt $receipt, ReceiptPdfService $pdfService)
    {
        DB::transaction(function () use ($receipt, $pdfService) {

            // تأكد من وجود donation لأن pdf غالباً يعتمد عليه
            $receipt->loadMissing('donation.campaign');

            $oldPath = $receipt->pdf_path;

            $newPath = $pdfService->buildAndStore($receipt);

            $receipt->update([
                'pdf_path' => $newPath,
                // اختياري: تحديث وقت الإصدار عند إعادة التوليد
                // 'issued_at' => now(),
            ]);

            if ($oldPath && $oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
        });

        return back()->with('success', 'تم إعادة توليد الإيصال بنجاح.');
    }
}
