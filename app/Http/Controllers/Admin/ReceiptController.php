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
        $this->middleware('permission:receipts.view')->only(['index', 'download']);
        $this->middleware('permission:receipts.create')->only(['regenerate']);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:30'],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $query = Receipt::query()->with('donation');

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

        if (!$receipt->pdf_path || !$disk->exists($receipt->pdf_path)) {
            abort(404);
        }

        $filename = $receipt->receipt_no
            ? "receipt-{$receipt->receipt_no}.pdf"
            : "receipt-{$receipt->id}.pdf";

        return $disk->download($receipt->pdf_path, $filename);
    }

    public function regenerate(Receipt $receipt, ReceiptPdfService $pdfService)
    {
        DB::transaction(function () use ($receipt, $pdfService) {
            $oldPath = $receipt->pdf_path;

            // يبني ويحفظ ملف جديد
            $newPath = $pdfService->buildAndStore($receipt);

            $receipt->update(['pdf_path' => $newPath]);

            // احذف القديم بعد نجاح التحديث
            if ($oldPath && $oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
        });

        return back()->with('success', 'تم إعادة توليد الإيصال بنجاح.');
    }
}
