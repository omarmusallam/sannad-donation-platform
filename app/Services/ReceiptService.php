<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptService
{
    public function __construct(
        protected ReceiptPdfService $pdfService
    ) {}

    public function generate(Donation $donation): Receipt
    {
        if ($donation->status !== 'paid') {
            throw new \RuntimeException('Donation must be paid to generate a receipt.');
        }

        return DB::transaction(function () use ($donation) {

            // Receipt موجود؟
            $receipt = Receipt::where('donation_id', $donation->id)->first();

            if (!$receipt) {
                $receipt = Receipt::create([
                    'receipt_no'  => $this->generateReceiptNumber(),
                    'uuid'        => (string) Str::uuid(),
                    'donation_id' => $donation->id,
                    'donor_name'  => $donation->donor_name,
                    'donor_email' => $donation->donor_email,
                    'amount'      => $donation->amount,
                    'currency'    => $donation->currency,
                    'status'      => 'issued',
                    'issued_at'   => now(),
                ]);
            }

            // PDF موجود وملفه موجود فعلاً؟
            if (!$receipt->pdf_path || !Storage::disk('public')->exists($receipt->pdf_path)) {
                try {
                    $path = $this->pdfService->buildAndStore($receipt);
                    $receipt->update(['pdf_path' => $path]);
                } catch (\Throwable $e) {

                    Log::error('Receipt PDF generation failed', [
                        'receipt_id'  => $receipt->id,
                        'donation_id' => $receipt->donation_id,
                        'error'       => $e->getMessage(),
                        'class'       => get_class($e),
                    ]);

                    // أثناء التطوير اعرض السبب الحقيقي بدل الرسالة العامة
                    if (app()->isLocal()) {
                        throw $e;
                    }

                    throw new \RuntimeException('Failed to generate receipt PDF.');
                }
            }

            return $receipt->fresh();
        });
    }

    protected function generateReceiptNumber(): string
    {
        // رقم واضح ومضمون (تاريخ + تسلسل)
        $seq = str_pad((string) (Receipt::max('id') + 1), 6, '0', STR_PAD_LEFT);
        return 'RC-' . now()->format('Ymd') . '-' . $seq;
    }
}
