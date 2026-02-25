<?php

namespace App\Services;

use App\Models\Receipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ReceiptPdfService
{
    public function buildAndStore(Receipt $receipt): string
    {
        $verifyUrl = route('receipt.verify', $receipt->uuid);

        $html = view('pdf.receipt', [
            'receipt'   => $receipt,
            'verifyUrl' => $verifyUrl,
        ])->render();

        // ---- Paths (fonts + temp) ----
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        if (!is_writable($tempDir)) {
            throw new \RuntimeException("mPDF tempDir is not writable: {$tempDir}");
        }

        $fontPathRegular = storage_path('fonts/Cairo-Regular.ttf');
        $fontPathBold    = storage_path('fonts/Cairo-Bold.ttf');

        if (!file_exists($fontPathRegular) || !file_exists($fontPathBold)) {
            throw new \RuntimeException(
                "Cairo font files not found. Expected:\n- {$fontPathRegular}\n- {$fontPathBold}"
            );
        }

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $defaultFonts  = (new FontVariables())->getDefaults();

        $fontDir = array_merge($defaultConfig['fontDir'], [
            storage_path('fonts'),
        ]);

        $fontData = $defaultFonts['fontdata'] + [
            'cairo' => [
                'R' => 'Cairo-Regular.ttf',
                'B' => 'Cairo-Bold.ttf',
            ],
        ];

        try {
            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 12,
                'margin_right'  => 12,
                'margin_top'    => 12,
                'margin_bottom' => 12,
                'tempDir'       => $tempDir,
                'fontDir'       => $fontDir,
                'fontdata'      => $fontData,
                'default_font'  => 'cairo',
            ]);

            // ---- Arabic / RTL ----
            $mpdf->SetDirectionality('rtl');
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont   = true;
            $mpdf->useSubstitutions = true;

            // ملاحظة مهمة:
            // ❌ لا تستخدم $mpdf->useOTL (غير موجود في 8.2.7 وسيكسر التطبيق)

            $mpdf->WriteHTML($html);

            $path = "receipts/{$receipt->receipt_no}.pdf";
            Storage::disk('public')->put($path, $mpdf->Output('', 'S'));

            return $path;
        } catch (\Throwable $e) {
            Log::error('Receipt PDF generation failed', [
                'receipt_id'  => $receipt->id,
                'receipt_no'  => $receipt->receipt_no,
                'donation_id' => $receipt->donation_id,
                'error'       => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to generate receipt PDF.');
        }
    }
}
