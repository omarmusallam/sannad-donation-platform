<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Services\ReceiptPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::query()->with('donation');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('donor_email', 'like', "%{$search}%")
                    ->orWhere('donor_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Currency filter
        if ($currency = $request->get('currency')) {
            $query->where('currency', $currency);
        }

        $receipts = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.receipts.index', compact('receipts'));
    }

    public function download(Receipt $receipt)
    {
        if (!$receipt->pdf_path || !Storage::disk('public')->exists($receipt->pdf_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($receipt->pdf_path);
    }

    public function regenerate(Receipt $receipt, ReceiptPdfService $pdfService)
    {
        $path = $pdfService->buildAndStore($receipt);
        $receipt->update(['pdf_path' => $path]);

        return back()->with('success', 'تم إعادة توليد الإيصال بنجاح.');
    }
}
